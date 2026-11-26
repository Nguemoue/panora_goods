<?php

declare(strict_types=1);

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Filament\Admin\Pages\FinancialDistributionPage;
use App\Filament\Admin\Resources\Sales\Pages\ListSales as AdminListSales;
use App\Filament\Admin\Widgets\Admin\AdminStatsOverview;
use App\Filament\Admin\Widgets\Admin\PendingSalePaymentsWidget;
use App\Filament\Admin\Widgets\Admin\SalesChart;
use App\Filament\Admin\Widgets\Admin\SellerPerformanceWidget;
use App\Filament\Seller\Pages\MyCommissionsPage;
use App\Filament\Seller\Resources\Clients\Pages\ListClients as SellerListClients;
use App\Filament\Seller\Resources\Sales\Pages\ListSales as SellerListSales;
use App\Filament\Seller\Widgets\Seller\SellerOverdueSalesWidget;
use App\Filament\Seller\Widgets\Seller\SellerStatsOverview;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('strictly isolates seller sales, overdue reminders, clients and commissions', function () {
    $sellerA = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);
    $sellerB = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active']);

    $clientA = Client::factory()->create(['name' => 'Client Alpha', 'phone_number' => '+237690000001']);
    $clientB = Client::factory()->create(['name' => 'Client Beta', 'phone_number' => '+237690000002']);

    $product = Product::factory()->create(['name' => 'iPhone 16', 'status' => 'active', 'selling_price' => 500000, 'supplier_price' => 400000]);

    $saleA = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $sellerA->id,
        'client_id' => $clientA->id,
        'customer_name' => $clientA->name,
        'sale_price' => 500000,
        'supplier_price_at_sale' => 400000,
        'profit' => 100000,
        'status' => SaleStatusEnum::PENDING,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_status' => PaymentStatusEnum::PENDING,
        'payment_date_limit' => today()->subDay(),
        'sold_at' => now(),
    ]);

    $saleB = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $sellerB->id,
        'client_id' => $clientB->id,
        'customer_name' => $clientB->name,
        'sale_price' => 500000,
        'supplier_price_at_sale' => 400000,
        'profit' => 100000,
        'status' => SaleStatusEnum::PENDING,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_status' => PaymentStatusEnum::PENDING,
        'payment_date_limit' => today()->subDay(),
        'sold_at' => now(),
    ]);

    Filament::setCurrentPanel(Filament::getPanel('seller'));
    actingAs($sellerA, 'seller');

    livewire(SellerListSales::class)
        ->assertCanSeeTableRecords([$saleA])
        ->assertCanNotSeeTableRecords([$saleB]);

    livewire(SellerOverdueSalesWidget::class)
        ->assertCanSeeTableRecords([$saleA])
        ->assertCanNotSeeTableRecords([$saleB]);

    livewire(SellerListClients::class)
        ->assertCanSeeTableRecords([$clientA])
        ->assertCanNotSeeTableRecords([$clientB]);

    livewire(SellerStatsOverview::class)->assertOk();
    livewire(MyCommissionsPage::class)->assertOk();
});

it('renders admin tracking widgets, pending payment queue, financial distribution and notifications', function () {
    $admin = User::factory()->create(['role' => UserRoleEnum::ADMIN, 'status' => 'active']);
    $seller = User::factory()->create(['role' => UserRoleEnum::SELLER, 'status' => 'active', 'reference_number' => 'VND-999']);
    $product = Product::factory()->create(['status' => 'active', 'stock_quantity' => 2]);

    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'sale_price' => 300000,
        'supplier_price_at_sale' => 220000,
        'profit' => 80000,
        'status' => SaleStatusEnum::PENDING,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_status' => PaymentStatusEnum::PENDING,
        'sold_at' => now(),
    ]);

    $payment = SalePayment::query()->create([
        'sale_id' => $sale->id,
        'amount' => 100000,
        'confirmation_status' => ConfirmationStatusEnum::PENDING,
    ]);

    // Creating a pending payment notifies active admins in database
    expect($admin->fresh()->notifications()->count())->toBeGreaterThanOrEqual(1);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    actingAs($admin);

    livewire(AdminStatsOverview::class)->assertOk();
    livewire(SalesChart::class)->assertOk();
    livewire(SellerPerformanceWidget::class)->assertCanSeeTableRecords([$seller]);
    livewire(PendingSalePaymentsWidget::class)->assertCanSeeTableRecords([$payment]);
    livewire(AdminListSales::class)->assertCanSeeTableRecords([$sale]);
    livewire(FinancialDistributionPage::class)
        ->assertOk()
        ->assertSee('Répartition du bénéfice');
});
