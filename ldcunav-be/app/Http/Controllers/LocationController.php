<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Support\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use RespondsWithApi;

    public function __construct(private readonly LocationService $locationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $locations = $this->locationService->all([
            'category' => $request->query('category'),
            'q' => $request->query('q'),
        ]);

        return $this->successResponse('Locations retrieved.', $locations);
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return $this->errorResponse('A search query is required.', [
                'q' => ['The "q" query parameter is required.'],
            ], 422);
        }

        return $this->successResponse('Search results.', $this->locationService->search($query));
    }

    public function map(): JsonResponse
    {
        return $this->successResponse('Campus map locations retrieved.', $this->locationService->map());
    }

    public function show(Request $request): JsonResponse
    {
        $slug = (string) $request->route('slug');

        $location = $this->locationService->bySlug($slug);

        if ($location === null) {
            return $this->errorResponse('Location not found.', ['slug' => ['No location matches that slug.']], 404);
        }

        return $this->successResponse('Location retrieved.', $location);
    }
}