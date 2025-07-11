<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

class Auth extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'address' => 'string|nullable',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
        ]);

//        if (User::where('email', $credentials['email'])->exists()) {
//            return response()->json(['message' => 'Такой пользователь уже есть!'], 422);
//        }

        $user = User::create($credentials);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function logout()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        auth()->user()->currentAccessToken()->delete();
        return response()->noContent();
    }
}
