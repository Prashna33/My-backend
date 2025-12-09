<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * List all products with optional filters and pagination
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $perPage = max(1, min(100, $perPage));

        $query = Product::with(['category', 'images'])
            ->where('is_active', true);

        // Filter by category_id
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by category_slug
        if ($request->filled('category_slug')) {
            $slug = $request->input('category_slug');
            $query->whereHas('category', function ($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        // Search query
        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
            });
        }

        $products = $query->paginate($perPage);

        // Format products for frontend
        $formattedProducts = $products->map(function ($product) {
            $primaryImage = $product->thumbnail_url;
            if (!$primaryImage && $product->images->isNotEmpty()) {
                $primaryImage = $product->images->first()->url;
            }
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'title' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'stock' => $product->stock,
                'thumbnail_url' => $primaryImage,
                'image' => $primaryImage,
                'category' => [
                    'id' => $product->category?->id,
                    'name' => $product->category?->name,
                    'slug' => $product->category?->slug,
                ],
                'category_id' => $product->category_id,
                'images' => $product->images->map(fn ($img) => ['url' => $img->url]),
            ];
        });

        // Return paginated response with consistent JSON
        return response()->json([
            'data' => $formattedProducts,
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'last_page' => $products->lastPage(),
        ]);
    }

    /**
     * Show single product details
     */
    public function show(Product $product)
    {
        $product->load(['category', 'images']);

        $primaryImage = $product->thumbnail_url;
        if (!$primaryImage && $product->images->isNotEmpty()) {
            $primaryImage = $product->images->first()->url;
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'title' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'stock' => $product->stock,
            'thumbnail_url' => $primaryImage,
            'image' => $primaryImage,
            'category' => [
                'id' => $product->category?->id,
                'name' => $product->category?->name,
                'slug' => $product->category?->slug,
            ],
            'category_id' => $product->category_id,
            'images' => $product->images->map(fn ($img) => $img->url),
            'is_active' => $product->is_active,
        ]);
    }
}
