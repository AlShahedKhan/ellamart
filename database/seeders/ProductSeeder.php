<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'user_id' => 1, // Replace with an actual user ID that exists in your database
            'name' => 'Sample Product 1',
            'description' => 'A high-quality sample product',
            'vendor' => 'Vendor 1',
            'sku' => 'SP1001',
            'price' => 59.99,
            'availability' => true,
            'size' => 'M',
            'color' => 'Red',
            'category_id' => 1, // Assuming category_id 1 exists
        ]);
    
        Product::create([
            'user_id' => 1, // Replace with an actual user ID that exists in your database
            'name' => 'Sample Product 2',
            'description' => 'Another sample product with great features',
            'vendor' => 'Vendor 2',
            'sku' => 'SP1002',
            'price' => 89.99,
            'availability' => true,
            'size' => 'L',
            'color' => 'Blue',
            'category_id' => 2, // Assuming category_id 2 exists
        ]);
    }
}
