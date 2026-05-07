<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(Request $request): View
    {
        $categoryId = request()->query('category');
        $perPage = request()->query('per_page', 12);

        $query = Product::with(['category', 'brand', 'images'])
            ->where('status', 'active');

        if ($request->has('q')) {
            $searchTerm = request()->query('q');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('selling_price', 'like', "%{$searchTerm}%")
                    //->orWhere('description', 'like', "%{$searchTerm}%")
                ;
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->paginate($perPage);
        $categories = Category::withCount('products')->get();
        $selectedCategory = $categoryId ? Category::find($categoryId) : null;

        return view('welcome', compact('products', 'categories', 'selectedCategory'));
    }
}
