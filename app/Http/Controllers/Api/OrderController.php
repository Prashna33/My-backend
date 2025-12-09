<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
	public function checkout(Request $request)
	{
		try {
			Log::info('Checkout request received', [
				'cart_token' => $request->input('cart_token'),
				'has_customer' => $request->has('customer'),
				'has_shipping' => $request->has('shipping'),
			]);
			
			$data = $request->validate([
				'cart_token' => 'required|string',
				'customer' => 'required|array',
				'customer.name' => 'required|string|max:255',
				'customer.email' => 'required|email',
				'customer.phone' => 'nullable|string|max:50',
				'shipping' => 'required|array',
				'shipping.address' => 'required|string|max:500',
				'shipping.city' => 'required|string|max:255',
				'shipping.postcode' => 'required|string|max:50',
				'shipping.country' => 'required|string|max:2',
			]);
		} catch (ValidationException $e) {
			Log::error('Checkout validation failed', [
				'errors' => $e->errors(),
				'request_data' => $request->all()
			]);
			return response()->json([
				'message' => 'Validation failed',
				'errors' => $e->errors()
			], 422);
		}

		$cart = Cart::query()->where('session_id', $data['cart_token'])->where('status', 'open')->with('items.product')->first();
		
		if (!$cart) {
			return response()->json([
				'message' => 'Cart not found or already converted to order',
				'errors' => ['cart_token' => ['Cart not found']]
			], 404);
		}
		
		if ($cart->items->isEmpty()) {
			return response()->json([
				'message' => 'Cart is empty',
				'errors' => ['cart' => ['Cart is empty. Please add items before checkout.']]
			], 400);
		}

		return DB::transaction(function () use ($cart, $data) {
			$order = Order::create([
				'order_number' => 'ORD-' . now()->format('YmdHis') . '-' . $cart->id,
				'user_id' => $cart->user_id,
				'cart_id' => $cart->id,
				'status' => 'pending',
				'currency' => $cart->currency,
				'subtotal' => $cart->subtotal,
				'discount_total' => $cart->discount_total,
				'tax_total' => $cart->tax_total,
				'shipping_total' => $cart->shipping_total,
				'grand_total' => $cart->grand_total,
				'shipping_address' => [
					'name' => $data['customer']['name'],
					'email' => $data['customer']['email'],
					'phone' => $data['customer']['phone'] ?? null,
					'address' => $data['shipping']['address'],
					'city' => $data['shipping']['city'],
					'postcode' => $data['shipping']['postcode'],
					'country' => $data['shipping']['country'],
				],
				'billing_address' => null,
				'placed_at' => now(),
			]);

			foreach ($cart->items as $item) {
				if (!$item->product) {
					return response()->json([
						'message' => 'Product not found for cart item',
						'errors' => ['product' => ["Product ID {$item->product_id} not found"]]
					], 400);
				}
				
				if ($item->product->stock < $item->quantity) {
					return response()->json([
						'message' => 'Insufficient stock',
						'errors' => ['quantity' => ["Insufficient stock for {$item->product->name}. Available: {$item->product->stock}, Requested: {$item->quantity}"]]
					], 400);
				}
				$itemTotal = $item->quantity * $item->unit_price;
				OrderItem::create([
					'order_id' => $order->id,
					'product_id' => $item->product_id,
					'product_name' => $item->product->name,
					'unit_price' => $item->unit_price,
					'quantity' => $item->quantity,
					'total_price' => $itemTotal,
				]);
				$item->product->decrement('stock', $item->quantity);
			}

			$cart->status = 'converted';
			$cart->save();

			return response()->json(['order' => $order->load('items')], 201);
		});
	}
}


