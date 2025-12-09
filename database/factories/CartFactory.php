<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 20, 400);
        $tax = $subtotal * 0.18;
        $shipping = fake()->randomFloat(2, 0, 25);
        $discount = fake()->randomFloat(2, 0, 40);
        $grandTotal = $subtotal + $tax + $shipping - $discount;

        return [
            'user_id' => User::factory(),
            'session_id' => fake()->uuid(),
            'status' => fake()->randomElement(['open', 'converted', 'abandoned']),
            'currency' => 'USD',
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'tax_total' => $tax,
            'shipping_total' => $shipping,
            'grand_total' => max($grandTotal, 0),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+3 days'),
        ];
    }
}
