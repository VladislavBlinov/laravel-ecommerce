<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(string $id)
    {
        $reviews = Product::findOrFail($id)->reviews->load('user')->toArray();
        return response()->json($reviews);
    }

    public function store(Request $request, string $id)
    {
        $review = Review::create([
            'product_id' => $id,
            'user_id' => auth()->id(),
            'rating' => $request->input('rating'),
            'review' => $request->input('comment'),
        ]);

        $product = Product::findOrFail($id);
        $product->rating = $review->avg('rating');
        $product->save();

        return response()->json($review->load('user')->toArray());
    }

    public function update(Request $request, string $id)
    {
        $review = Review::findOrFail($id);
        $review->update([
            'rating' => $request->input('rating'),
            'review' => $request->input('comment'),
        ]);
        return response()->json($review->load('user')->toArray());
    }
}
