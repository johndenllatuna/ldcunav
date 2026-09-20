<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Support\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->successResponse('Account created successfully.', $result, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $result = $this->authService->login($credentials['email'], $credentials['password']);

        if ($result === null) {
            return $this->errorResponse(
                'These credentials do not match our records.',
                ['email' => ['These credentials do not match our records.']],
                401
            );
        }

        return $this->successResponse('Logged in successfully.', $result);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout((string) $request->bearerToken());

        return $this->successResponse('Logged out successfully.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->forgotPassword($request->validated('email'));

        return $this->successResponse('If that email is registered, a reset link will be sent.');
    }
}