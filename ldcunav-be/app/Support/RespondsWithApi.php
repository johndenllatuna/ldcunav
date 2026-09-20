<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * RespondsWithApi
 *
 * Single source of truth for the consistent API envelope used by every
 * endpoint: { success, message, data } on success and { success, message,
 * errors } on failure.
 */
trait RespondsWithApi
{
    protected function successResponse(
        string $message = 'Success',
        mixed $data = null,
        int $status = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(
        string $message = 'Request failed',
        array $errors = [],
        int $status = 400,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}