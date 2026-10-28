<?php

namespace App\Console\Commands;

use App\Prisma\PrismaClient;
use Database\Seeders\LocationSeeder;
use Illuminate\Console\Command;

/**
 * SeedLocations
 *
 * Seeds the campus catalog (locations) through the Prisma client. Idempotent:
 * locations are upserted by slug, so the command can be re-run safely.
 */
class SeedLocations extends Command
{
    protected $signature = 'ldcunav:seed-locations';

    protected $description = 'Seed the campus locations used by the LDCUNav app';

    public function handle(PrismaClient $prisma): int
    {
        $locations = LocationSeeder::LOCATIONS;

        $created = 0;
        $updated = 0;

        foreach ($locations as $location) {
            $existing = $prisma->location->findUnique([
                'where' => ['slug' => $location['slug']],
            ]);

            if ($existing === null) {
                $prisma->location->create(['data' => $location]);
                $created++;
            } else {
                $prisma->location->updateMany([
                    'where' => ['id' => (int) $existing['id']],
                    'data' => [
                        'name' => $location['name'],
                        'category' => $location['category'],
                        'type' => $location['type'],
                        'description' => $location['description'],
                        'icon' => $location['icon'],
                        'mapOrder' => $location['mapOrder'],
                    ],
                ]);
                $updated++;
            }
        }

        $this->info("Campus locations seeded: {$created} created, {$updated} updated (total: " . count($locations) . ').');

        return self::SUCCESS;
    }
}
