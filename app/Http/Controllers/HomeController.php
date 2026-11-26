<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CatalogCacheService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private CatalogCacheService $catalogCache) {}

    public function __invoke(): View
    {
        $featuredProducts = $this->catalogCache->getFeaturedProducts(3);
        $categories = $this->catalogCache->getCategoriesWithActiveCount()
            ->sortByDesc('products_count')
            ->values();

        return view('welcome', compact('featuredProducts', 'categories'));
    }
}
