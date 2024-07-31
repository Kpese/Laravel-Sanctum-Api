<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use HttpResponses;


    public function register(){
        $requestUserData = request()->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $requestUserData['name'],
            'email' => $requestUserData['email'],
            'password' => Hash::make($requestUserData['password']),
        ]);

        return $this->success([
            'User' => $user,
            'token' => $user->createToken('API Token of '. $user->name)->plainTextToken,
        ]);
    }


    public function login(){
        $loginUserData = request()->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8',
        ]);

        if(!(Auth::attempt(['password' => $loginUserData['password'], 'email' => $loginUserData['email']]))){
            return $this->error('', 'invalid credentials', 400);
        }
        $user = User::where('email', $loginUserData['email'])->first();

        return $this->success([
            'User' => $user,
            'token' => $user->createToken('API Token of '. $user->name)->plainTextToken,
        ]);
    }

    public function logout(){
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            'message' =>'you have been logged out'
        ]);
    }
}
