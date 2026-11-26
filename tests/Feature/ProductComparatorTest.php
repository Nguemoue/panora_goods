<?php

declare(strict_types=1);

use App\Livewire\ProductComparator;
use App\Models\Product;
use App\Models\Specification;

use function Pest\Livewire\livewire;

it('toggles up to three products and displays their specifications side by side', function () {
    $ram = Specification::factory()->create(['name' => 'RAM', 'measure' => 'GB']);

    $p1 = Product::factory()->create(['name' => 'Phone Alpha', 'status' => 'active']);
    $p2 = Product::factory()->create(['name' => 'Phone Beta', 'status' => 'active']);
    $p3 = Product::factory()->create(['name' => 'Phone Gamma', 'status' => 'active']);
    $p4 = Product::factory()->create(['name' => 'Phone Delta', 'status' => 'active']);

    $p1->specifications()->attach($ram->id, ['value' => '8']);
    $p2->specifications()->attach($ram->id, ['value' => '12']);

    livewire(ProductComparator::class)
        ->call('toggleProduct', $p1->id)
        ->call('toggleProduct', $p2->id)
        ->call('toggleProduct', $p3->id)
        ->call('toggleProduct', $p4->id)
        ->assertSet('productIds', [$p1->id, $p2->id, $p3->id])
        ->assertSee('Phone Alpha')
        ->assertSee('Phone Beta')
        ->assertSee('RAM')
        ->assertSee('8 GB')
        ->assertSee('12 GB')
        ->call('toggleProduct', $p1->id)
        ->assertSet('productIds', [$p2->id, $p3->id])
        ->call('clear')
        ->assertSet('productIds', []);
});
