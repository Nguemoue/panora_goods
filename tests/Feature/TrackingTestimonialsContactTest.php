<?php

declare(strict_types=1);

use App\Enums\ConfirmationStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\SaleStatusEnum;
use App\Enums\UserRoleEnum;
use App\Livewire\TrackOrder;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\User;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Livewire\livewire;

it('renders the testimonials page with active navigation', function () {
    get(route('testimonials'))
        ->assertOk()
        ->assertSee('Avis Clients Vérifiés')
        ->assertSee('aria-current="page"', false);
});

it('renders and processes the redesigned flux contact page', function () {
    get(route('contact'))
        ->assertOk()
        ->assertSee('Envoyez-nous un message');

    post(route('contact.send'), [
        'name' => 'Jean Dupont',
        'email' => 'jean@example.com',
        'subject' => 'Demande de renseignement',
        'message' => 'Bonjour, je souhaite connaître la disponibilité.',
    ])->assertRedirect(route('contact'))
        ->assertSessionHas('contact_sent', true);
});

it('displays financial summary, seller reference and payment history on order tracking', function () {
    $seller = User::factory()->create([
        'name' => 'Marc Vendeur',
        'role' => UserRoleEnum::SELLER,
        'reference_number' => 'VND-2026-042',
    ]);

    $product = Product::factory()->create([
        'name' => 'iPhone 15 Pro',
        'status' => 'active',
        'selling_price' => 600000,
    ]);

    $sale = Sale::factory()->create([
        'product_id' => $product->id,
        'seller_id' => $seller->id,
        'sale_price' => 600000,
        'quantity' => 1,
        'customer_name' => 'Aline Client',
        'status' => SaleStatusEnum::SHIPPED,
        'payment_type' => PaymentTypeEnum::MANY_TIME_WITH_DEBT_ACKNOWLEDGE,
        'payment_status' => PaymentStatusEnum::PENDING,
    ]);

    SalePayment::query()->create([
        'sale_id' => $sale->id,
        'amount' => 250000,
        'confirmation_status' => ConfirmationStatusEnum::APPROVED,
    ]);

    livewire(TrackOrder::class)
        ->set('tracking_code', $sale->tracking_code)
        ->call('track')
        ->assertSee($sale->tracking_code)
        ->assertSee('iPhone 15 Pro')
        ->assertSee('Marc Vendeur')
        ->assertSee('VND-2026-042')
        ->assertSee('600 000 FCFA')
        ->assertSee('250 000 FCFA')
        ->assertSee('350 000 FCFA');
});
