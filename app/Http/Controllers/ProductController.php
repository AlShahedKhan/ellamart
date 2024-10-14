<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller implements HasMiddleware
{
    use JsonResponseTrait;
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
        try {
            $product = Product::with('category')->get();
            return $this->jsonResponse(200,'success', $product);
        } catch (\Exception $e) {
            //throw $th;
            \Log::error("Fetching products failed", ['error' => $e->getMessage()]);
            return $this->jsonResponse(500,'Failed');
        }
        // return Product::with('category')->get();
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
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
            
            return $this->jsonResponse(201,'Product created successfully', $product);
        } catch (\Exception $e) {
            //throw $th;
            \Log::error("Product creation failed", ['error', $e->getMessage()]);
            return $this->jsonResponse(500,'Failed');
        }
        

        // return response()->json($product, 201);
    }

    /**
     * Display a specific product.
     */
    public function show(Product $product)
    {
        try {
            $product->load('category');
            return $this->jsonResponse(200, 'Success', $product);
        } catch (\Exception $e) {
            \Log::error("Fetching product failed", ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
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

            return $this->jsonResponse(200, 'Product updated successfully', $product);
        } catch (\Exception $e) {
            \Log::error("Product update failed", ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        try {
            Gate::authorize('modify', $product);

            $product->delete();

            return $this->jsonResponse(200, 'Product deleted successfully');
        } catch (\Exception $e) {
            \Log::error("Product deletion failed", ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }
}
