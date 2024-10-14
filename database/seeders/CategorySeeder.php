<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Electronics',
            'user_id' => 1, // Replace with an actual user ID that exists in your database
        ]);
        
        Category::create([
            'name' => 'Shirts',
            'user_id' => 1, // Replace with an actual user ID that exists in your database
        ]);
    }
    
}
