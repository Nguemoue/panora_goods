<?php

declare(strict_types=1);

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Filament\Seller\Resources\Sales\Pages\CreateSale;
use App\Filament\Seller\Widgets\Seller\SellerSalesChart;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('enforces server-side seller_id, prices, statuses and creates optional initial payment atomically', function () {
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $otherSeller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $client = Client::factory()->create(['name' => 'Marie Client', 'phone_number' => '+237699112233']);

    $product = Product::factory()->create([
        'name' => 'Galaxy S25',
        'status' => 'active',
        'supplier_price' => 300000,
        'selling_price' => 450000,
        'stock_quantity' => 5,
    ]);

    Filament::setCurrentPanel(Filament::getPanel('seller'));
    actingAs($seller, 'seller');

    livewire(CreateSale::class)
        ->fillForm([
            'product_id' => $product->id,
            'quantity' => 2,
            'client_id' => $client->id,
            'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE->value,
            'payment_date_limit' => today()->addDays(15)->toDateString(),
            'initial_payment_amount' => 200000,
            'initial_payment_notes' => 'OM-998877',
            // Attempted tampering fields
            'seller_id' => $otherSeller->id,
            'supplier_price_at_sale' => 1,
            'status' => SaleStatusEnum::DELIVERED->value,
            'payment_status' => PaymentStatusEnum::PAID->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $sale = Sale::query()->latest('id')->firstOrFail();

    expect($sale->seller_id)->toBe($seller->id)
        ->and((float) $sale->supplier_price_at_sale)->toBe(300000.0)
        ->and((float) $sale->sale_price)->toBe(900000.0)
        ->and((float) $sale->profit)->toBe(300000.0)
        ->and($sale->status)->toBe(SaleStatusEnum::PENDING)
        ->and($sale->payment_status)->toBe(PaymentStatusEnum::NOT_INITIATED)
        ->and($sale->customer_name)->toBe('Marie Client')
        ->and($product->fresh()->stock_quantity)->toBe(3);

    $payment = SalePayment::query()->where('sale_id', $sale->id)->first();
    expect($payment)->not->toBeNull()
        ->and((float) $payment->amount)->toBe(200000.0)
        ->and($payment->notes)->toBe('OM-998877')
        ->and($payment->confirmation_status)->toBe(ConfirmationStatusEnum::PENDING)
        ->and($sale->getAvailablePaymentAmount())->toBe(700000.0);
});

it('requires payment_date_limit for installment sales and blocks sequential ID enumeration on invoice PDF', function () {
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $client = Client::factory()->create();
    $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 3]);

    Filament::setCurrentPanel(Filament::getPanel('seller'));
    actingAs($seller, 'seller');

    livewire(CreateSale::class)
        ->fillForm([
            'product_id' => $product->id,
            'quantity' => 1,
            'client_id' => $client->id,
            'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE->value,
            'payment_date_limit' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['payment_date_limit' => 'required']);

    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'client_id' => $client->id,
    ]);

    // Sequential numeric ID must NOT resolve the invoice PDF
    get("/sales/{$sale->id}/pdf")->assertNotFound();

    // Tracking code route resolves cleanly
    get(route('invoice.download.pdf', ['sale' => $sale]))->assertOk();
});

it('renders seller monthly sales chart cleanly without SQL errors', function () {
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    Sale::factory()->create([
        'seller_id' => $seller->id,
        'sale_price' => 250000,
        'profit' => 50000,
        'sold_at' => now(),
    ]);

    Filament::setCurrentPanel(Filament::getPanel('seller'));
    actingAs($seller, 'seller');

    livewire(SellerSalesChart::class)->assertOk();
});
