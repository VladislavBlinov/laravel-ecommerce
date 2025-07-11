<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories], 200);
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->get()->first();
        return response()->json(['data' => $category], 200);
    }
}
