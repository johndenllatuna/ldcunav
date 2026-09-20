<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\UserService;
use App\Support\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly UserService $userService)
    {
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse('Authenticated user.', $request->user()->toArray());
    }

    public function show(Request $request): JsonResponse
    {
        return $this->successResponse('Profile retrieved.', $request->user()->toArray());
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile(
            (int) $request->user()->getAuthIdentifier(),
            $request->validated()
        );

        return $this->successResponse('Profile updated successfully.', $user);
    }
}