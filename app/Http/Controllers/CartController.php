<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrCreate(['user_id' => auth()->id()]);
        $cartItems = $cart->cartItems->load('product');

        return CartResource::collection($cartItems);
    }

    public function create(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrCreate();
        $cartItem = $cart->cartItems;
        $productIds = $cart->cartItems->pluck('product_id')->toArray();
        if (in_array($request->get('product_id'), $productIds)) {
            $this->update($request);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->get('product_id'),
                'quantity' => $request->get('quantity')
            ]);
        }

        return response()->json(['data' => $cartItem]);
    }

    public function update(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrCreate();
        $cartItem = $cart->cartItems;
        $productIds = $cart->cartItems->pluck('product_id')->toArray();
        if (!in_array($request->get('product_id'), $productIds)) {
            return response()->json(['message' => 'Product already added to cart'], 400);
        } else {
            foreach ($cartItem as $item) {
                if ($item->product_id == $request->get('product_id')) {
                    $item->quantity = $request->get('quantity');
                    $item->save();
                    break;
                }
            }
        }

        return response()->json(['data' => $cartItem]);
    }

    public function destroy(Request $request, string $productId)
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cartItem = $cart->cartItems;
        foreach ($cartItem as $item) {
            if ($item->product_id == $productId) {
                $item->delete();
                break;
            }
        }
        return response()->json(['data' => $cartItem]);
    }

    public function clear(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->delete();

        return response()->json(['data' => $cart]);
    }
}
