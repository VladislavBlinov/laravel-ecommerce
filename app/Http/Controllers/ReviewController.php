<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(int $productId)
    {
        $reviews = Review::with('user:id,name')
            ->where('product_id', $productId)
            ->latest()
            ->paginate(10);
        return ReviewResource::collection($reviews);
    }

    public function store(ReviewRequest $request, int $productId)
    {
        if (Review::where('product_id', $productId)
            ->where('user_id', auth()->id())
            ->exists()) {
            return response()->json(['error' => 'Вы уже оставили отзыв на этот товар'], 422);
        }
        return DB::transaction(function () use ($request, $productId) {
            $review = Review::create([
                'product_id' => $productId,
                'user_id' => auth()->id(),
                'rating' => $request->input('rating'),
                'review' => $request->input('comment'),
            ]);

            $avg = Review::where('product_id', $productId)->avg('rating');
            $review->product()->update(['rating' => $avg]);
            $review->load('user:id,name');

            return ReviewResource::make($review);
        });
    }

    public function update(ReviewRequest $request, int $reviewId)
    {
        $review = Review::with('user:id,name')->findOrFail($reviewId);

        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $review->update([
            'rating' => $request->input('rating'),
            'review' => $request->input('comment'),
        ]);

        $avg = Review::where('product_id', $review->product_id)->avg('rating');
        $review->product()->update(['rating' => $avg]);

        return ReviewResource::make($review);
    }
}
