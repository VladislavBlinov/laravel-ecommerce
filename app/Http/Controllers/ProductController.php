<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $products = Product::findOrFail($id);
        return ProductResource::make($products);
    }

    public function getProductsByCategory(string $slug)
    {
        $products = Category::where('slug', $slug)->firstOrFail()->products;
        return ProductResource::collection($products);
    }

    public function getProductsByIds()
    {
//        $cart = Cart::where('user_id', auth()->id())->first();
//        $productIds = $cart->CartItems->pluck('product_id')->toArray();
//        $product = Product::whereIn('id', $productIds)->get();

        $cart = Cart::with('cartItems.product')->where('user_id', auth()->id())->firstOrFail();
        $products = $cart->CartItems->pluck('product')->filter()->flatten();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product->price * $product->cartItems->where('product_id', $product->id)->first()->quantity;
        }

        return response()->json(['products' => ProductResource::collection($products),
            'totalPrice' => $totalPrice]);
    }

    public function getProductsByLikes()
    {
        $products = auth()->user()->likedProducts;


        return ProductResource::collection($products);

    }

    public function search(Request $request)
    {
        $query = Product::search($request->query('text'));
        $query->query(function ($builder) use ($request) {
            if ($request->filled('category')) {
                $builder->where('category_id', $request->query('category'));
            }
            if ($request->filled('price_min')) {
                $builder->where('price', '>=', $request->query('price_min'));
            }
            if ($request->filled('price_max')) {
                $builder->where('price', '<=', $request->query('price_max'));
            }
        });

        return ProductResource::collection($query->get());
    }

}
