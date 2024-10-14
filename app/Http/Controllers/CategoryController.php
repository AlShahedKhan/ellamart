<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\JsonResponseTrait;
use App\Traits\ImageUploadTrait; // Import the ImageUploadTrait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CategoryController extends Controller implements HasMiddleware
{
    use JsonResponseTrait, ImageUploadTrait; // Use both traits

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        try {
            $categories = Category::with('images')->get(); // Include images if needed
            return $this->jsonResponse(200, 'Success', $categories);
        } catch (\Exception $e) {
            \Log::error('Fetching categories failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        try {
            $fields = $request->validate([
                'name' => 'required|string|max:100|unique:categories,name',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
            ]);

            // Set user_id to the authenticated user
            $fields['user_id'] = $request->user()->id;

            // Create the category
            $category = Category::create($fields);

            // Use the trait method to handle image upload for category
            $imageUrl = $this->uploadImage($request, 'image', $category, 'category_images');

            return $this->jsonResponse(201, 'Category created successfully', [
                'category' => $category,
                'image' => $imageUrl // Include the image URL if uploaded
            ]);
        } catch (\Exception $e) {
            \Log::error('Category creation failed', ['error' => $e->getMessage()]);
            return $this->jsonResponse(500, 'Failed');
        }
    }

    /**
     * Display the specified category.
     */
    // public function show(Category $category)
    // {
    //     try {
    //         return $this->jsonResponse(200, 'Success', $category);
    //     } catch (\Exception $e) {
    //         \Log::error('Fetching category failed', ['error' => $e->getMessage()]);
    //         return $this->jsonResponse(500, 'Failed');
    //     }
    // }
    public function show(Category $category)
    {
        try {
            $category->load('images'); // Load associated images

            return $this->jsonResponse(200, 'Success', [
                'category' => $category,
                'images' => $category->images // Include the images in the response
            ]);
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
        // Authorize the action
        Gate::authorize('modify', $category);

        // Log the incoming request data for debugging
        \Log::info('Update category request data', ['request' => $request->all()]);

        // Validate input fields
        $fields = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);

        // Update the category with validated fields
        $category->update($fields);

        // Log the image input for debugging
        \Log::info('Image field received', ['image' => $request->file('image')]);

        // Handle image upload if provided
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->uploadImage($request, 'image', $category, 'category_images');
        }

        return $this->jsonResponse(200, 'Category updated successfully', [
            'category' => $category,
            'image' => $imageUrl // Include the image URL if uploaded
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Log validation errors
        \Log::error('Category update validation failed', [
            'errors' => $e->errors(),
            'message' => $e->getMessage()
        ]);
        return $this->jsonResponse(422, 'Validation failed', $e->errors());
    } catch (\Exception $e) {
        // Log any other errors
        \Log::error('Category update failed', ['error' => $e->getMessage()]);
        return $this->jsonResponse(500, 'Failed', null);
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
