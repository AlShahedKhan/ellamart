<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Image; // Import the Image model
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Traits\JsonResponseTrait;

class AuthController extends Controller
{
    use JsonResponseTrait, ImageUploadTrait;

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate the image
        ]);
    
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']), // Hashing the password before saving
        ]);
    
        // Handle image upload for user
        $imageUrl = $this->uploadImage($request, 'image', $user, 'profile_images');
    
        $token = $user->createToken($request->name);
    
        // Fetch the image URL for the user after creation
        $userImage = $user->images()->first(); // Fetch the first image for the user if it exists
    
        return $this->jsonResponse(201, 'User registered successfully', [
            'user' => $user,
            'token' => $token->plainTextToken,
            'image' => $imageUrl ?? ($userImage ? $userImage->url : null), // Include the image URL if it exists
        ]);
    }



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->jsonResponse(401, 'The provided credentials do not match');
        }

        $token = $user->createToken($user->name);

        return $this->jsonResponse(200, 'Login successful', [
            'user' => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->jsonResponse(200, 'Logged out successfully');
    }

    // Optional: Method to update user image
    // public function updateImage(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     // Get the authenticated user
    //     $user = $request->user();

    //     // Handle image upload
    //     if ($request->hasFile('image')) {
    //         // Delete existing images (if you want to replace)
    //         $user->images()->delete();

    //         $path = $request->file('image')->store('profile_images', 'public'); // Store the image
    //         Image::create([
    //             'url' => Storage::url($path),
    //             'imageable_id' => $user->id,
    //             'imageable_type' => User::class, // Setting up polymorphic relation
    //         ]);
    //     }

    //     return $this->jsonResponse(200, 'Image updated successfully');
    // }
}
