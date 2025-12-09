<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

// App health check
Route::get('/', fn () => response()->json([
    'name' => config('app.name'),
    'version' => config('app.version'),
    'status' => 'ok',
]));

Route::get('/health', fn () => response()->json(['status' => 'ok']));

// Products
Route::get('/products', [ProductController::class, 'index']); // List all products
Route::get('/products/{product}', [ProductController::class, 'show']); // Single product details

// Categories
Route::get('/categories', [CategoryController::class, 'index']); // List all categories
Route::get('/categories/{category}', [CategoryController::class, 'show']); // Single category details
Route::get('/categories/{category}/products', [CategoryController::class, 'products']); // Products by category

// Cart (token-based guest carts)
Route::get('/cart', [CartController::class, 'get']); // Get cart by token (query param)
Route::post('/cart', [CartController::class, 'getOrCreate']); // Create or get cart
Route::post('/cart/items', [CartController::class, 'addItem']); // Add item to cart
Route::patch('/cart/items/{item}', [CartController::class, 'updateItem']); // Update cart item
Route::delete('/cart/items/{item}', [CartController::class, 'removeItem']); // Remove item
Route::post('/cart/clear', [CartController::class, 'clear']); // Clear cart

// Orders
Route::post('/orders/checkout', [OrderController::class, 'checkout']); // Checkout
