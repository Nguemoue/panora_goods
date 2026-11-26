<?php

declare(strict_types=1);

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Filament\Admin\Pages\PaymentSettingPage;
use App\Filament\Admin\Resources\Sales\Pages\EditSale;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Widgets\Admin\LowStockProducts;
use App\Filament\Admin\Widgets\Admin\PendingSalePaymentsWidget;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('approves pending payment atomically and blocks over-approval of remaining balance', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $client = Client::factory()->create();
    $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 5]);

    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'client_id' => $client->id,
        'sale_price' => 100000,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_status' => PaymentStatusEnum::NOT_INITIATED,
        'status' => SaleStatusEnum::PENDING,
    ]);

    $firstPayment = SalePayment::create([
        'sale_id' => $sale->id,
        'amount' => 100000,
        'confirmation_status' => ConfirmationStatusEnum::PENDING,
        'notes' => 'OM-REF-100',
    ]);

    $excessPayment = SalePayment::create([
        'sale_id' => $sale->id,
        'amount' => 50000,
        'confirmation_status' => ConfirmationStatusEnum::PENDING,
        'notes' => 'OM-REF-EXCESS',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(PendingSalePaymentsWidget::class)
        ->assertCanSeeTableRecords([$firstPayment, $excessPayment])
        ->callAction(TestAction::make('approveSalePayment')->table($firstPayment))
        ->assertNotified();

    expect($firstPayment->fresh()->confirmation_status)->toBe(ConfirmationStatusEnum::APPROVED)
        ->and($sale->fresh()->payment_status)->toBe(PaymentStatusEnum::PAID)
        ->and($seller->notifications()->count())->toBe(1);

    // Approving a second payment now that remaining balance is 0 must be blocked
    livewire(PendingSalePaymentsWidget::class)
        ->callAction(TestAction::make('approveSalePayment')->table($excessPayment))
        ->assertNotified();

    expect($excessPayment->fresh()->confirmation_status)->toBe(ConfirmationStatusEnum::PENDING);
});

it('rejects a payment with a reason saved to notes and sent to seller notification', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $sale = Sale::factory()->create(['seller_id' => $seller->id, 'sale_price' => 80000]);

    $payment = SalePayment::create([
        'sale_id' => $sale->id,
        'amount' => 40000,
        'confirmation_status' => ConfirmationStatusEnum::PENDING,
        'notes' => 'MOMO-1234',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(PendingSalePaymentsWidget::class)
        ->callAction(TestAction::make('reject')->table($payment), [
            'reason' => 'Référence introuvable sur le relevé',
        ])
        ->assertNotified();

    $payment->refresh();
    $notification = $seller->notifications()->latest()->first();

    expect($payment->confirmation_status)->toBe(ConfirmationStatusEnum::REJECTED)
        ->and($payment->notes)->toContain('MOMO-1234')
        ->and($payment->notes)->toContain('[Rejet] Référence introuvable sur le relevé')
        ->and($notification)->not->toBeNull()
        ->and((string) data_get($notification->data, 'body'))->toContain('Référence introuvable sur le relevé');
});

it('restores stock once when cancelling a sale and hides delete action for paid or delivered sales', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 4]);

    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'quantity' => 2,
        'status' => SaleStatusEnum::PENDING,
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(EditSale::class, ['record' => $sale->id])
        ->assertActionVisible(DeleteAction::class)
        ->fillForm(['status' => SaleStatusEnum::CANCELLED->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->fresh()->stock_quantity)->toBe(6)
        ->and($sale->fresh()->status)->toBe(SaleStatusEnum::CANCELLED)
        ->and($seller->notifications()->count())->toBe(1);

    // Re-saving as CANCELLED must NOT restore stock a second time
    livewire(EditSale::class, ['record' => $sale->id])
        ->fillForm(['status' => SaleStatusEnum::CANCELLED->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->fresh()->stock_quantity)->toBe(6);

    // A sale with an approved payment cannot be deleted
    SalePayment::create([
        'sale_id' => $sale->id,
        'amount' => 10000,
        'confirmation_status' => ConfirmationStatusEnum::APPROVED,
    ]);

    livewire(EditSale::class, ['record' => $sale->id])
        ->assertActionHidden(DeleteAction::class);
});

it('fixes user reference_number editing, photo avatar_url resolution and admin self-delete protection', function () {
    $admin = User::factory()->create([
        'role' => UserRoleEnum::ADMIN,
        'status' => 'active',
        'reference_number' => 'PG-26-ADM1',
        'photo' => 'avatars/admin-pic.jpg',
    ]);

    $seller = User::factory()->create([
        'role' => UserRoleEnum::SELLER,
        'status' => 'active',
        'reference_number' => 'PG-26-SLR1',
    ]);

    expect($admin->avatar_url)->toContain('storage/avatars/admin-pic.jpg');

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    // Admin cannot see DeleteAction on their own profile
    livewire(EditUser::class, ['record' => $admin->id])
        ->assertActionHidden(DeleteAction::class);

    // Editing a user with an existing reference_number must succeed (ignoreRecord: true)
    livewire(EditUser::class, ['record' => $seller->id])
        ->assertActionVisible(DeleteAction::class)
        ->fillForm([
            'name' => 'Vendeur Modifié',
            'reference_number' => 'PG-26-SLR1',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($seller->fresh()->name)->toBe('Vendeur Modifié');
});

it('renders payment settings page cleanly and allows quick restock from low stock widget', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $activeLow = Product::factory()->create(['name' => 'Phone Low', 'status' => 'active', 'stock_quantity' => 2]);
    $inactiveLow = Product::factory()->create(['name' => 'Phone Inactive', 'status' => 'inactive', 'stock_quantity' => 1]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(PaymentSettingPage::class)
        ->assertOk()
        ->assertSee('Simulation de répartition');

    livewire(LowStockProducts::class)
        ->assertCanSeeTableRecords([$activeLow])
        ->assertCanNotSeeTableRecords([$inactiveLow])
        ->callAction(TestAction::make('restock')->table($activeLow), [
            'quantity_to_add' => 5,
        ])
        ->assertNotified();

    expect($activeLow->fresh()->stock_quantity)->toBe(7);
});
