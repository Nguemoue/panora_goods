<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        // On charge toutes les relations nécessaires pour une fiche détaillée
        $product->load(['category', 'brand', 'images', 'specifications']);

        // On peut aussi suggérer des produits similaires de la même catégorie
        $similarProducts = Product::with(['brand', 'images'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'similarProducts'));
    }
}
