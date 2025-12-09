<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $defaultPassword = 'password';

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make($defaultPassword),
            ]
        );

        $categorySeeds = [
            [
                'name' => "Men's Clothing",
                'slug' => 'mens-clothing',
                'description' => 'Go-to staples for every modern wardrobe.',
                'image_url' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => "Women's Clothing",
                'slug' => 'womens-clothing',
                'description' => 'Elevated essentials and statement pieces.',
                'image_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Jewelry',
                'slug' => 'jewelry',
                'description' => 'Fine and fashion jewelry to complete every look.',
                'image_url' => 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=800&q=80',
            ],
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Smart tech, accessories, and everyday gadgets.',
                'image_url' => 'https://images.unsplash.com/photo-1510552776732-01acc9a4c63f?auto=format&fit=crop&w=800&q=80',
            ],
        ];

        $categories = collect($categorySeeds)->mapWithKeys(function (array $seed) {
            $category = Category::updateOrCreate(
                ['slug' => $seed['slug']],
                [
                    'name' => $seed['name'],
                    'description' => $seed['description'],
                    'image_url' => $seed['image_url'],
                    'is_active' => true,
                ]
            );

            return [$seed['slug'] => $category];
        });

        $productSeeds = [
            'mens-clothing' => [
                [
                    'name' => 'Classic Denim Jacket',
                    'sku' => 'MEN-JACKET-001',
                    'price' => 89.99,
                    'stock' => 45,
                    'description' => 'A timeless denim jacket with modern detailing and a tailored fit.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'Slim Fit Chino Pants',
                    'sku' => 'MEN-PANTS-002',
                    'price' => 64.5,
                    'stock' => 60,
                    'description' => 'Versatile chinos crafted in breathable cotton with a tapered leg.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1521579971123-1192931a1452?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1521579971123-1192931a1452?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'Heritage Crewneck Sweater',
                    'sku' => 'MEN-SWEATER-003',
                    'price' => 72.0,
                    'stock' => 38,
                    'description' => 'Soft knit crewneck sweater for effortless layering all season long.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1600180758890-6e99d6e962f0?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1600180758890-6e99d6e962f0?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
            ],
            'womens-clothing' => [
                [
                    'name' => 'Silk Wrap Dress',
                    'sku' => 'WOM-DRESS-001',
                    'price' => 128.0,
                    'stock' => 28,
                    'description' => 'Elegant midi wrap dress in luxe silk with a flattering drape.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'Everyday Linen Shirt',
                    'sku' => 'WOM-SHIRT-002',
                    'price' => 58.5,
                    'stock' => 55,
                    'description' => 'Relaxed linen shirt with utility pockets and rolled sleeves.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'High-Rise Tailored Trousers',
                    'sku' => 'WOM-PANTS-003',
                    'price' => 74.25,
                    'stock' => 42,
                    'description' => 'Impeccably tailored trousers with front pleats and ankle crop.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
            ],
            'jewelry' => [
                [
                    'name' => 'Minimalist Gold Necklace',
                    'sku' => 'JEW-NECK-001',
                    'price' => 92.0,
                    'stock' => 80,
                    'description' => '14k gold plated chain necklace with a minimalist pendant.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'Stackable Ring Set',
                    'sku' => 'JEW-RING-002',
                    'price' => 48.75,
                    'stock' => 120,
                    'description' => 'Set of three stackable rings with mixed metal finishes.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1522312346375-d1a52e2b99b3?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1520962919973-8b456906c813?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'Pearl Drop Earrings',
                    'sku' => 'JEW-EARR-003',
                    'price' => 58.0,
                    'stock' => 95,
                    'description' => 'Freshwater pearl earrings suspended from gold vermeil hoops.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1520962919973-8b456906c813?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1520962919973-8b456906c813?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
            ],
            'electronics' => [
                [
                    'name' => 'Noise-Cancelling Headphones',
                    'sku' => 'ELEC-HEAD-001',
                    'price' => 199.99,
                    'stock' => 65,
                    'description' => 'Wireless over-ear headphones with adaptive noise cancelling.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1510552776732-01acc9a4c63f?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1510552776732-01acc9a4c63f?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => 'Smart Home Speaker',
                    'sku' => 'ELEC-SPKR-002',
                    'price' => 129.95,
                    'stock' => 85,
                    'description' => 'Voice-enabled smart speaker with premium sound.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1587033410392-5e062eda3cf0?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1587033410392-5e062eda3cf0?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
                [
                    'name' => '4K Action Camera',
                    'sku' => 'ELEC-CAM-003',
                    'price' => 249.0,
                    'stock' => 40,
                    'description' => 'Waterproof action camera with 4K recording and stabilization.',
                    'thumbnail_url' => 'https://images.unsplash.com/photo-1512495967849-bf0c41b96cbb?auto=format&fit=crop&w=800&q=80',
                    'images' => [
                        'https://images.unsplash.com/photo-1512495967849-bf0c41b96cbb?auto=format&fit=crop&w=800&q=80',
                    ],
                ],
            ],
        ];

        foreach ($productSeeds as $categorySlug => $products) {
            /** @var \App\Models\Category|null $category */
            $category = $categories->get($categorySlug);

            if (!$category) {
                continue;
            }

            foreach ($products as $seed) {
                $name = $seed['name'];
                $slug = Str::slug($name);
                $thumbnail = $seed['thumbnail_url'] ?? data_get($seed, 'images.0');

                $product = Product::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'sku' => $seed['sku'] ?? Str::upper(Str::random(10)),
                        'description' => $seed['description'] ?? '',
                        'price' => $seed['price'] ?? 0,
                        'stock' => $seed['stock'] ?? 0,
                        'thumbnail_url' => $thumbnail,
                        'attributes' => $seed['attributes'] ?? [],
                        'is_active' => true,
                    ]
                );

                $product->images()->delete();

                $images = $seed['images'] ?? array_filter([$thumbnail]);
                foreach ($images as $index => $url) {
                    $product->images()->create([
                        'url' => $url,
                        'position' => $index,
                        'is_primary' => $index === 0,
                    ]);
                }
            }
        }
    }
}
