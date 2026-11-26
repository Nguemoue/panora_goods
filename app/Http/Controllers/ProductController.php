<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CatalogCacheService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private CatalogCacheService $catalogCache) {}

    public function show(Product $product): View
    {
        $product->load([
            'category',
            'brand',
            'primaryImage',
            'images' => fn ($query) => $query->orderByDesc('is_primary')->orderBy('id'),
            'specifications',
        ]);

        $similarProducts = $this->catalogCache->getSimilarProducts($product, 4);

        return view('products.show', compact('product', 'similarProducts'));
    }
}
