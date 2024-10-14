<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\JsonResponseTrait;

class AuthController extends Controller
{
    use JsonResponseTrait;

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']), // Hashing the password before saving
        ]);

        $token = $user->createToken($request->name);

        return $this->jsonResponse(201, 'User registered successfully', [
            'user' => $user,
            'token' => $token->plainTextToken
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
}
