<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
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

        return ProductResource::collection($query->paginate(10));
    }
}
