<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSavedLocationRequest;
use App\Services\SavedLocationService;
use App\Support\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedLocationController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly SavedLocationService $savedLocationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $saved = $this->savedLocationService->listForUser((int) $request->user()->getAuthIdentifier());

        return $this->successResponse('Saved locations retrieved.', $saved);
    }

    public function store(StoreSavedLocationRequest $request): JsonResponse
    {
        $saved = $this->savedLocationService->save(
            (int) $request->user()->getAuthIdentifier(),
            (int) $request->validated('locationId')
        );

        return $this->successResponse('Location saved.', $saved, 201);
    }

    public function destroy(Request $request, int $locationId): JsonResponse
    {
        $removed = $this->savedLocationService->remove(
            (int) $request->user()->getAuthIdentifier(),
            $locationId
        );

        if ($removed === 0) {
            return $this->errorResponse('Location was not in your saved list.', ['locationId' => ['Nothing to remove.']], 404);
        }

        return $this->successResponse('Location removed from saved.');
    }
}