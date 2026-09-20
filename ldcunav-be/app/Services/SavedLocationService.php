<?php

namespace App\Services;

use App\Prisma\PrismaClient;
use Illuminate\Validation\ValidationException;
use PDOException;

/**
 * SavedLocationService
 *
 * Read/save/remove saved locations. Scope is always anchored to the calling
 * user's id so one user can never touch another user's saved list.
 */
class SavedLocationService
{
    public function __construct(private readonly PrismaClient $prisma)
    {
    }

    /**
     * List the authenticated user's saved locations (with the location payload).
     *
     * @return list<array<string, mixed>>
     */
    public function listForUser(int $userId): array
    {
        return $this->prisma->savedLocation->findMany([
            'where' => ['userId' => $userId],
            'orderBy' => ['createdAt' => 'desc'],
            'include' => ['location' => true],
        ]);
    }

    /**
     * Save a location for the user (idempotent; returns the saved record).
     *
     * @return array<string, mixed>
     */
    public function save(int $userId, int $locationId): array
    {
        $location = $this->prisma->location->findUnique([
            'where' => ['id' => $locationId],
        ]);

        if ($location === null) {
            throw ValidationException::withMessages([
                'locationId' => ['The selected location does not exist.'],
            ]);
        }

        $existing = $this->prisma->savedLocation->findFirst([
            'where' => ['userId' => $userId, 'locationId' => $locationId],
        ]);

        if ($existing !== null) {
            return $this->withLocation($existing, $location);
        }

        try {
            $this->prisma->savedLocation->create([
                'data' => [
                    'userId' => $userId,
                    'locationId' => $locationId,
                ],
            ]);
        } catch (PDOException $e) {
            // Unique (userId, locationId) race: the row may have just appeared.
            if ($e->getCode() !== '23505') {
                throw $e;
            }
        }

        $saved = $this->prisma->savedLocation->findFirst([
            'where' => ['userId' => $userId, 'locationId' => $locationId],
        ]);

        return $this->withLocation($saved, $location);
    }

    /**
     * Remove a location from a user's saved list.
     *
     * @return int Number of removed rows (0 when nothing was saved).
     */
    public function remove(int $userId, int $locationId): int
    {
        return $this->prisma->savedLocation->deleteMany([
            'userId' => $userId,
            'locationId' => $locationId,
        ]);
    }

    /**
     * Attach the location payload and strip internal ids the UI does not need.
     *
     * @param  array<string, mixed>  $saved
     * @param  array<string, mixed>  $location
     * @return array<string, mixed>
     */
    private function withLocation(array $saved, array $location): array
    {
        $saved['location'] = $location;

        return $saved;
    }
}