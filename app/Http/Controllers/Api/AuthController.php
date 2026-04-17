<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create([
            ...$request->validated(),
            'role' => User::ROLE_USER,
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            'User registered successfully.',
            [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
            201,
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            return $this->errorResponse(
                'Invalid credentials provided.',
                ['email' => ['The provided credentials are incorrect.']],
                422,
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('Login successful.', [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->successResponse('Logout successful.');
    }
}
