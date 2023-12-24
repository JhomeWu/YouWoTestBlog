<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // delete old token
        $user = $request->user();
        $user->tokens()->delete();
        // create new token
        $token = $request->user()->createToken('authToken')->plainTextToken;

        return response()->json([
            'name' => $user->name,
            'token' => $token,
        ]);
    }
}
