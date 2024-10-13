<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller implements HasMiddleware
{
    /**
     * Apply middleware to routes.
     */
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of all products.
     */
    public function index()
    {
        return Product::with('category')->get();
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'vendor' => 'required|max:100',
            'sku' => 'required|unique:products,sku|max:50',
            'price' => 'required|numeric|min:0',
            'availability' => 'boolean',
            'size' => 'nullable|in:XS,S,M,L,XL',
            'color' => 'nullable|max:50',
            'category_id' => 'required|exists:categories,id', // Validate category_id
        ]);

        $product = $request->user()->products()->create($fields);

        return response()->json($product, 201);
    }

    /**
     * Display a specific product.
     */
    public function show(Product $product)
    {
        return $product->load('category');
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('modify', $product);

        $fields = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'vendor' => 'required|max:100',
            'sku' => 'required|unique:products,sku,' . $product->id . '|max:50',
            'price' => 'required|numeric|min:0',
            'availability' => 'boolean',
            'size' => 'nullable|in:XS,S,M,L,XL',
            'color' => 'nullable|max:50',
            'category_id' => 'required|exists:categories,id', // Validate category_id
        ]);

        $product->update($fields);

        return response()->json($product);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('modify', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
