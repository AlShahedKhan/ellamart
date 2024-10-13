<?php

// app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CategoryController extends Controller implements HasMiddleware
{

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
        return Category::all();
    }

    /**
     * Store a newly created category in storage.
     */
    // app/Http/Controllers/CategoryController.php

    public function store(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
        ]);

        // Set user_id to the authenticated user
        $fields['user_id'] = $request->user()->id;

        $category = Category::create($fields);

        return response()->json($category, 201);
    }


    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        Gate::authorize('modify', $category);

        $fields = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
        ]);

        $category->update($fields);

        return response()->json($category);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        Gate::authorize('modify', $category);

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
