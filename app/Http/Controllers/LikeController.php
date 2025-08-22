<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;

class LikeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = $user->likedProducts();
        return ProductResource::collection($products->paginate(5));
    }

    public function show(Product $product)
    {
        $like = $product->likedUsers()
            ->where('user_id', auth()->id())
            ->exists();

        return response()->json(['liked' => $like]);
    }

    public function store(Product $product)
    {
        $toggle = $product->likedUsers()->toggle(auth()->id());
        $liked = !empty($toggle['attached']);

        return response()->json(['liked' => $liked]);
    }
}
