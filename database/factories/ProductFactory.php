<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 5, 250);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-#####')),
            'description' => fake()->paragraph(),
            'price' => $price,
            'stock' => fake()->numberBetween(0, 150),
            'thumbnail_url' => fake()->optional()->imageUrl(600, 600, 'product', true),
            'attributes' => [
                'color' => fake()->optional()->safeColorName(),
                'material' => fake()->optional()->word(),
            ],
            'is_active' => true,
        ];
    }
}
