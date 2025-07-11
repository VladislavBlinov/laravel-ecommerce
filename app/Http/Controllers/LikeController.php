<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = $user->likedProducts;

        return ProductResource::collection($products);
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        $like = $product->likedUsers()->exists(auth()->id());

        return response()->json(['data' => $like]);
    }

    public function store(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $like = $product->likedUsers()->toggle(auth()->id());

        return response()->json(['data' => $like], 200);
    }
}
