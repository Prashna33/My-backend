<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 30, 600);
        $discount = fake()->randomFloat(2, 0, 50);
        $tax = round($subtotal * 0.18, 2);
        $shipping = fake()->randomFloat(2, 0, 30);
        $grand = $subtotal + $tax + $shipping - $discount;

        return [
            'order_number' => strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'cart_id' => Cart::factory(),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_status' => fake()->randomElement(['unpaid', 'pending', 'paid', 'refunded']),
            'currency' => 'USD',
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'tax_total' => $tax,
            'shipping_total' => $shipping,
            'grand_total' => max($grand, 0),
            'shipping_address' => [
                'line1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => fake()->countryCode(),
                'phone' => fake()->phoneNumber(),
            ],
            'billing_address' => [
                'line1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => fake()->countryCode(),
                'phone' => fake()->phoneNumber(),
            ],
            'payment_method' => fake()->randomElement(['cod', 'card', 'upi']),
            'payment_reference' => fake()->optional()->uuid(),
            'placed_at' => fake()->dateTimeBetween('-2 weeks', 'now'),
            'paid_at' => fake()->optional()->dateTimeBetween('-2 weeks', 'now'),
            'customer_notes' => fake()->optional()->sentence(),
        ];
    }
}
