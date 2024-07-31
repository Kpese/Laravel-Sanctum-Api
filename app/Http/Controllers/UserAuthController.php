<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function register(){
        $requestUserData = request()->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $requestUserData['name'],
            'email' => $requestUserData['email'],
            'password' => Hash::make($requestUserData['password']),
        ]);

        return response()->json([
            'message' => 'User created successfully',
        ], 200);
    }

    public function login(){
        $loginUserData = request()->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8',
        ]);

        if(!(Auth::attempt(['password' => $loginUserData['password'], 'email' => $loginUserData['email']]))){
            response()->json([
                'message' => 'invalid credentials',
            ], 401);
        }
        $user = User::where('email', $loginUserData['email'])->first();

        $token = $user->createToken($user->name. '-AuthToken')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function get(){
        return User::get();
    }
    public function logout(){
        Auth::user()->tokens->delete();
        response()->json([
            'message' => 'Logged out'
        ]); //
    }
}
