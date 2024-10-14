<?php

use App\Models\User;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::post('/user/image', [AuthController::class, 'updateImage'])->middleware('auth:sanctum');


Route::apiResource('/products', ProductController::class);
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/orders', OrderController::class);

// Route::post('/images/upload', [ImageController::class, 'upload']);
// Route::get('/images/{imageable_type}/{imageable_id}', [ImageController::class, 'getImages']);
// Route::delete('/images/{id}', [ImageController::class, 'deleteImage']);

// Route::post('/test-upload-image', function (Request $request) {
//     $request->validate([
//         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
//     ]);

//     $user = User::first(); // Get the first user for testing

//     if ($request->hasFile('image')) {
//         $path = $request->file('image')->store('profile_images', 'public');
//         Image::create([
//             'url' => Storage::url($path),
//             'imageable_id' => $user->id,
//             'imageable_type' => User::class,
//         ]);
//         return response()->json(['message' => 'Image uploaded successfully']);
//     }

//     return response()->json(['message' => 'No image provided'], 400);
// });
