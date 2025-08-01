<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginAuthRequest $request)
    {
        if (auth()->attempt($request->all())) {
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
