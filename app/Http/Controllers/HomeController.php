<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $categoryId = request()->query('category');
        $perPage = request()->query('per_page', 12);

        $query = Product::with(['category', 'brand', 'images'])
            ->where('status', 'active');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate($perPage);
        $categories = Category::withCount('products')->get();
        $selectedCategory = $categoryId ? Category::find($categoryId) : null;

        return view('welcome', compact('products', 'categories', 'selectedCategory'));
    }
}
