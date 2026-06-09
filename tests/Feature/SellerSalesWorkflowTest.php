<?php

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Filament\Seller\Resources\Sales\Pages\CreateSale;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;
use Filament\Facades\Filament;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

beforeEach(function () {
    Filament::setCurrentPanel('seller');
});

it('creates a seller sale with calculated totals and decrements stock', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create([
        'supplier_price' => 100,
        'selling_price' => 150,
        'stock_quantity' => 5,
        'status' => 'active',
    ]);
    $client = Client::query()->create([
        'name' => 'Client Test',
        'phone_number' => '237699000000',
        'email' => 'client@example.com',
        'address' => 'Douala',
    ]);

    actingAs($seller, 'seller');

    livewire(CreateSale::class)
        ->fillForm([
            'product_id' => $product->id,
            'quantity' => 2,
            'client_id' => $client->id,
            'payment_type' => PaymentTypeEnum::ONE_TIME->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $sale = Sale::query()->firstOrFail();

    expect($sale->seller_id)->toBe($seller->id)
        ->and((float) $sale->sale_price)->toBe(300.0)
        ->and((float) $sale->profit)->toBe(100.0)
        ->and($product->refresh()->stock_quantity)->toBe(3);
});

it('rejects a seller sale when quantity is greater than stock', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 1,
        'status' => 'active',
    ]);

    actingAs($seller, 'seller');

    livewire(CreateSale::class)
        ->fillForm([
            'product_id' => $product->id,
            'quantity' => 2,
            'payment_type' => PaymentTypeEnum::ONE_TIME->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['quantity']);

    expect(Sale::query()->count())->toBe(0)
        ->and($product->refresh()->stock_quantity)->toBe(1);
});

it('finds overdue unpaid sales', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create();
    $overdueSale = createSaleForSeller($seller, $product, [
        'payment_status' => PaymentStatusEnum::PENDING,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_date_limit' => now()->subDay(),
    ]);
    createSaleForSeller($seller, $product, [
        'payment_status' => PaymentStatusEnum::PENDING,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_date_limit' => now()->addDay(),
    ]);
    createSaleForSeller($seller, $product, [
        'payment_status' => PaymentStatusEnum::PAID,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_date_limit' => now()->subDay(),
    ]);

    expect(Sale::query()->overduePayment()->pluck('id')->all())->toBe([$overdueSale->id]);
});

it('calculates remaining amount from approved payments only', function () {
    $seller = User::factory()->seller()->create();
    $product = Product::factory()->create();
    $sale = createSaleForSeller($seller, $product, [
        'sale_price' => 500,
    ]);

    SalePayment::query()->create([
        'sale_id' => $sale->id,
        'amount' => 200,
        'confirmation_status' => ConfirmationStatusEnum::APPROVED,
    ]);
    SalePayment::query()->create([
        'sale_id' => $sale->id,
        'amount' => 100,
        'confirmation_status' => ConfirmationStatusEnum::PENDING,
    ]);

    expect($sale->getRemainingAmount())->toBe(300.0);
});

it('uses french labels for payment enums', function () {
    expect(PaymentTypeEnum::ONE_TIME->getLabel())->toBe('Paiement unique')
        ->and(PaymentStatusEnum::PAID->getLabel())->toBe('Payé');
});

function createSaleForSeller(User $seller, Product $product, array $attributes = []): Sale
{
    return Sale::query()->create(array_merge([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'quantity' => 1,
        'supplier_price_at_sale' => $product->supplier_price,
        'sale_price' => $product->selling_price,
        'profit' => $product->profit_price,
        'payment_status' => PaymentStatusEnum::NOT_INITIATED,
        'payment_type' => PaymentTypeEnum::ONE_TIME,
        'sold_at' => now(),
    ], $attributes));
}
