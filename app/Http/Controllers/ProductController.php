<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(5);
        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return ProductResource::make($product);
    }
}
