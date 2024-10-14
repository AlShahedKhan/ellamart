<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'imageable_type' => 'required|string',
    //         'imageable_id' => 'required|integer',
    //     ]);

    //     // Store the image
    //     $path = $request->file('image')->store('images', 'public');

    //     // Create the image record
    //     $image = Image::create([
    //         'url' => Storage::url($path),
    //         'imageable_id' => $request->imageable_id,
    //         'imageable_type' => $request->imageable_type,
    //     ]);

    //     return response()->json([
    //         'status' => 201,
    //         'message' => 'Image uploaded successfully',
    //         'data' => $image,
    //     ]);
    // }

    // public function getImages($imageableType, $imageableId)
    // {
    //     $images = Image::where('imageable_type', $imageableType)
    //         ->where('imageable_id', $imageableId)
    //         ->get();

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Success',
    //         'data' => $images,
    //     ]);
    // }

    // public function deleteImage($id)
    // {
    //     $image = Image::findOrFail($id);
    //     Storage::disk('public')->delete($image->url); // Delete the image from storage
    //     $image->delete(); // Delete the record from the database

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Image deleted successfully',
    //     ]);
    // }
}
