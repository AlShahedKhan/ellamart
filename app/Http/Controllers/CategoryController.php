<?php

// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CategoryController extends Controller implements HasMiddleware
{
    use JsonResponseTrait;

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }
    
    public function index()
    {
        try {
            $categories = Category::all();
            return $this->jsonResponse(200, 'Success', $categories);
        } catch (\Exception $e) {
            \Log::error('Fetching categories failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name',
            ]);

            // Set user_id to the authenticated user
            $fields['user_id'] = $request->user()->id;

            $category = Category::create($fields);

            return $this->jsonResponse(201, 'Category created successfully', $category);
        } catch (\Exception $e) {
            \Log::error('Category creation failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }


    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        try {
            return $this->jsonResponse(200, 'Success', $category);
        } catch (\Exception $e) {
            \Log::error('Fetching category failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            Gate::authorize('modify', $category);

            $fields = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            ]);

            $category->update($fields);

            return $this->jsonResponse(200, 'Category updated successfully', $category);
        } catch (\Exception $e) {
            \Log::error('Category update failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        try {
            Gate::authorize('modify', $category);

            $category->delete();

            return $this->jsonResponse(200, 'Category deleted successfully');
        } catch (\Exception $e) {
            \Log::error('Category deletion failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }
}
