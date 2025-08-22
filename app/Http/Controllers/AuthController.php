<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterAuthRequest;
use App\Mail\RegisteredEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $user = User::create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;
        Mail::to($user->email)->queue(new RegisteredEmail($user));
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
