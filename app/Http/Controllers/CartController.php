<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartRequest;
use App\Http\Resources\CartItemResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {

        $cart = Cart::with('cartItems.product')->where('user_id', auth()->id())->firstOrFail();
        $cartItems = $cart->cartItems;

        return CartItemResource::collection($cartItems);
    }

    public function create(CartRequest $request)
    {
        $cart = Cart::with('cartItems')->where('user_id', auth()->id())->firstOrCreate(['user_id' => auth()->id()]);
        $cartItem = $cart->cartItems()->where('product_id', $request->get('product_id'))->first();

        if ($cartItem) {
            $cartItem->quantity = $cartItem->quantity + 1;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->get('product_id'),
                'quantity' => $request->get('quantity')
            ]);
        }

        return CartItemResource::make($cartItem);
    }

    public function update(CartRequest $request)
    {
        $cart = Cart::with('cartItems')->where('user_id', auth()->id())->firstOrFail();
        $cartItem = $cart->cartItems()->where('product_id', $request->get('product_id'))->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        } else {
            $cartItem->quantity = $request->get('quantity');
            $cartItem->save();
        }

        return CartItemResource::make($cartItem);
    }

    public function destroy(int $productId)
    {
        $cart = Cart::with('cartItems')->where('user_id', auth()->id())->firstOrFail();
        $cartItem = $cart->cartItems()->where('product_id', $productId)->delete();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }

        return response()->json(['message' => 'Product removed from cart']);
    }

    public function clear(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cart->cartItems()->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
