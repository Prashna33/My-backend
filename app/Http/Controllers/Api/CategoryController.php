<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        
        $response = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'image_url' => $category->image_url,
                'is_active' => $category->is_active,
            ];
        });

        return response()->json($response);
    }

    
    public function show(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'image_url' => $category->image_url,
            'is_active' => $category->is_active,
        ]);
    }

    
    public function products(Category $category, Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $perPage = max(1, min(100, $perPage));

        $products = $category->products()
            ->with(['category', 'images'])
            ->where('is_active', true)
            ->paginate($perPage);

        
        $data = $products->items();
        return response()->json([
            'data' => $data,
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'last_page' => $products->lastPage(),
        ]);
    }
}
