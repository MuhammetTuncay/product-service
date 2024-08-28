<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockLocation;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories
        $categories = Category::factory(10)->create();

        // Create Stock Locations
        $stockLocations = StockLocation::factory(5)->create();

        // Create Products and associate them with Categories and Stock Locations
        Product::factory(50)->create()->each(function ($product) use ($categories, $stockLocations) {
            // Attach categories (randomly pick 1 to 3 categories)
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Attach stock locations with random quantity (randomly pick 1 to 3 stock locations)
            $stockLocations->random(rand(1, 3))->each(function ($stockLocation) use ($product) {
                $product->stockLocations()->attach($stockLocation->id, ['quantity' => rand(10, 100)]);
            });
        });
    }
}

