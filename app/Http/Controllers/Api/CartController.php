<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
	protected function findCartByToken(?string $token): ?Cart
	{
		if (!$token) {
			return null;
		}
		return Cart::query()->where('session_id', $token)->where('status', 'open')->first();
	}

	public function get(Request $request)
	{
		$token = $request->query('cart_token');
		$cart = $this->findCartByToken($token);
		
		if (!$cart) {
			return response()->json(['cart' => null, 'message' => 'Cart not found'], 404);
		}
		
		$cart->load('items.product');
		return response()->json([
			'cart_token' => $cart->session_id,
			'cart' => $cart,
		]);
	}

	public function getOrCreate(Request $request)
	{
		$token = $request->string('cart_token') ?: null;
		$cart = $this->findCartByToken($token);
		if (!$cart) {
			$cart = Cart::create([
				'session_id' => Str::uuid()->toString(),
			]);
		}
		$cart->load('items.product');
		return response()->json([
			'cart_token' => $cart->session_id,
			'cart' => $cart,
		]);
	}

	public function addItem(Request $request)
	{
		$data = $request->validate([
			'cart_token' => 'required|string',
			'product_id' => 'required|integer|exists:products,id',
			'quantity' => 'required|integer|min:1',
		]);

		$cart = $this->findCartByToken($data['cart_token']);
		if (!$cart) {
			throw ValidationException::withMessages(['cart_token' => 'Cart not found']);
		}

		$product = Product::findOrFail($data['product_id']);
		if (!$product->is_active || $product->stock < $data['quantity']) {
			throw ValidationException::withMessages(['quantity' => 'Insufficient stock']);
		}

		$item = $cart->items()->firstOrNew(['product_id' => $product->id]);
		$item->quantity = ($item->exists ? $item->quantity : 0) + $data['quantity'];
		$item->unit_price = $product->price;
		$item->total_price = $item->quantity * $item->unit_price;
		$item->product_snapshot = [
			'name' => $product->name,
			'sku' => $product->sku,
			'thumbnail_url' => $product->thumbnail_url,
			'price' => $product->price,
			'description' => $product->description,
		];
		$item->save();

		$this->recalculateTotals($cart);

		$cart->load('items.product');
		return response()->json($cart);
	}

	public function updateItem(Request $request, CartItem $item)
	{
		$data = $request->validate([
			'cart_token' => 'required|string',
			'quantity' => 'required|integer|min:1',
		]);
		$cart = $this->findCartByToken($data['cart_token']);
		if (!$cart || $item->cart_id !== $cart->id) {
			throw ValidationException::withMessages(['cart_token' => 'Cart mismatch']);
		}
		if ($item->product->stock < $data['quantity']) {
			throw ValidationException::withMessages(['quantity' => 'Insufficient stock']);
		}
		$item->quantity = $data['quantity'];
		$item->unit_price = $item->product->price;
		$item->total_price = $item->quantity * $item->unit_price;
		$item->product_snapshot = [
			'name' => $item->product->name,
			'sku' => $item->product->sku,
			'thumbnail_url' => $item->product->thumbnail_url,
			'price' => $item->product->price,
			'description' => $item->product->description,
		];
		$item->save();

		$this->recalculateTotals($cart);
		$cart->load('items.product');
		return response()->json($cart);
	}

	public function removeItem(Request $request, CartItem $item)
	{
		$data = $request->validate([
			'cart_token' => 'required|string',
		]);
		$cart = $this->findCartByToken($data['cart_token']);
		if (!$cart || $item->cart_id !== $cart->id) {
			throw ValidationException::withMessages(['cart_token' => 'Cart mismatch']);
		}
		$item->delete();
		$this->recalculateTotals($cart);
		$cart->load('items.product');
		return response()->json($cart);
	}

	public function clear(Request $request)
	{
		$data = $request->validate([
			'cart_token' => 'required|string',
		]);
		$cart = $this->findCartByToken($data['cart_token']);
		if ($cart) {
			$cart->items()->delete();
			$this->recalculateTotals($cart);
			$cart->load('items.product');
		}
		return response()->json($cart);
	}

	protected function recalculateTotals(Cart $cart): void
	{
		$subtotal = $cart->items()->sum('total_price');
		$cart->subtotal = $subtotal;
		$cart->discount_total = 0;
		$cart->tax_total = 0;
		$cart->shipping_total = 0;
		$cart->grand_total = $subtotal;
		$cart->save();
	}
}


