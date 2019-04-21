<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\User as UserResource;
use App\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['logout', 'me']);
    }

    public function register(RegisterRequest $request)
    {
        $validatedRequest = $request->validated();

        $user = User::create([
            'name' => $validatedRequest['name'],
            'email' => $validatedRequest['email'],
            'password' => bcrypt($validatedRequest['password']),
            'role' => 'user',
            'balance' => 500
        ]);

        $user->generateToken();

        return new UserResource($user);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (auth()->attempt(array_merge($credentials, ['banned' => 0]))) {
            auth()->user()->generateToken();
            return new UserResource(auth()->user());
        }

        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    public function me() {
        return new UserResource(auth()->user());
    }

    public function logout()
    {
        auth()->user()->token()->revoke();

        return response()->json(['success' => true], 200);
    }
}
