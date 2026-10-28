<?php

namespace Database\Seeders;

use App\Prisma\PrismaClient;
use Illuminate\Database\Seeder;

/**
 * SavedLocationSeeder
 *
 * Establishes realistic user-location bookmarks for demo accounts.
 * Enforces referential integrity by looking up verified User and Location IDs.
 * Idempotent: checks for existing (userId, locationId) pairings before creating.
 */
class SavedLocationSeeder extends Seeder
{
    /**
     * Map of demo user email => list of location slugs to bookmark.
     *
     * @var array<string, list<string>>
     */
    public const USER_BOOKMARKS = [
        'maria.santos@liceo.edu.ph' => [
            'north-academic-cluster',
            'university-library',
            'it-services-lab',
            'cashiers-office',
        ],
        'juan.delacruz@liceo.edu.ph' => [
            'west-academic-cluster',
            'rodolsa-hall',
            'university-gymnasium',
            'university-cafeteria',
        ],
        'demo.navigator@liceo.edu.ph' => [
            'liceo-civic-center',
            'health-services-clinic',
            'registrars-office',
            'guidance-center',
        ],
        'clara.reyes@liceo.edu.ph' => [
            'north-academic-cluster',
            'rodolsa-hall',
            'hrmo',
            'research-and-publications',
        ],
    ];

    public function run(PrismaClient $prisma): void
    {
        $created = 0;
        $skipped = 0;

        foreach (self::USER_BOOKMARKS as $email => $slugs) {
            $user = $prisma->user->findUnique([
                'where' => ['email' => strtolower($email)],
            ]);

            if ($user === null) {
                $this->command?->warn("SavedLocationSeeder: User {$email} not found. Skipping.");
                continue;
            }

            $userId = (int) $user['id'];

            foreach ($slugs as $slug) {
                $location = $prisma->location->findUnique([
                    'where' => ['slug' => $slug],
                ]);

                if ($location === null) {
                    $this->command?->warn("SavedLocationSeeder: Location {$slug} not found. Skipping.");
                    continue;
                }

                $locationId = (int) $location['id'];

                $existing = $prisma->savedLocation->findFirst([
                    'where' => [
                        'userId' => $userId,
                        'locationId' => $locationId,
                    ],
                ]);

                if ($existing !== null) {
                    $skipped++;
                    continue;
                }

                $prisma->savedLocation->create([
                    'data' => [
                        'userId' => $userId,
                        'locationId' => $locationId,
                    ],
                ]);
                $created++;
            }
        }

        $this->command?->info("SavedLocationSeeder: {$created} saved locations created ({$skipped} already present, skipped).");
    }
}
