<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    public function getCategoryProducts(string $categorySlug)
    {
        $products = Category::with('products')
            ->where('slug', $categorySlug)
            ->firstOrFail()
            ->products();
        return ProductResource::collection($products->paginate(5));
    }
}
