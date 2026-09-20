<?php

namespace App\Services;

use App\Prisma\PrismaClient;

/**
 * LocationService
 *
 * Read access to campus locations. Keep read paths here so controllers stay
 * thin and PostgreSQL access never leaks into controllers or routes.
 */
class LocationService
{
    public function __construct(private readonly PrismaClient $prisma)
    {
    }

    /**
     * List locations, optionally filtered by category and/or a free-text query.
     *
     * @param  array<string, mixed>  $filters
     * @return list<array<string, mixed>>
     */
    public function all(array $filters = []): array
    {
        $where = [];

        if (! empty($filters['category'])) {
            $where['category'] = (string) $filters['category'];
        }

        $where = $this->applySearch($where, $filters['q'] ?? null);

        return $this->prisma->location->findMany([
            'where' => $where,
            'orderBy' => [
                ['mapOrder' => 'asc'],
                ['name' => 'asc'],
            ],
        ]);
    }

    /**
     * Free-text search across name, type and description.
     *
     * @return list<array<string, mixed>>
     */
    public function search(string $query): array
    {
        $query = trim($query);

        if ($query === '') {
            return [];
        }

        return $this->prisma->location->findMany([
            'where' => $this->applySearch([], $query),
            'orderBy' => [
                ['mapOrder' => 'asc'],
                ['name' => 'asc'],
            ],
        ]);
    }

    /**
     * Locations positioned on the campus map (mapOrder set), ordered by mapOrder.
     *
     * @return list<array<string, mixed>>
     */
    public function map(): array
    {
        return $this->prisma->location->findMany([
            'where' => ['mapOrder' => ['not' => null]],
            'orderBy' => ['mapOrder' => 'asc'],
        ]);
    }

    /**
     * Find a single location by its slug.
     *
     * @return array<string, mixed>|null
     */
    public function bySlug(string $slug): ?array
    {
        return $this->prisma->location->findUnique([
            'where' => ['slug' => $slug],
        ]);
    }

    /**
     * Build a combined case-insensitive search filter.
     *
     * @param  array<string, mixed>  $where
     * @return array<string, mixed>
     */
    private function applySearch(array $where, ?string $query): array
    {
        $query = trim((string) $query);

        if ($query === '') {
            return $where;
        }

        $insensitive = static fn ($value): array => [
            'contains' => $value,
            'mode' => 'insensitive',
        ];

        $where['OR'] = [
            ['name' => $insensitive($query)],
            ['type' => $insensitive($query)],
            ['description' => $insensitive($query)],
        ];

        return $where;
    }
}