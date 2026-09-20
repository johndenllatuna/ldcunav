<?php

namespace App\Console\Commands;

use App\Prisma\PrismaClient;
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
        $locations = [
            [
                'name' => 'North Academic Cluster',
                'slug' => 'north-academic-cluster',
                'category' => 'Academic',
                'type' => 'Academic Building',
                'description' => 'Home to the College of Arts and Sciences, the library annex, and general education classrooms.',
                'icon' => '🏢',
                'mapOrder' => 1,
            ],
            [
                'name' => 'West Academic Cluster',
                'slug' => 'west-academic-cluster',
                'category' => 'Academic',
                'type' => 'Academic Building',
                'description' => 'Houses engineering, architecture, and technology departments with modern laboratories.',
                'icon' => '🏛️',
                'mapOrder' => 2,
            ],
            [
                'name' => 'South Academic Cluster',
                'slug' => 'south-academic-cluster',
                'category' => 'Academic',
                'type' => 'Academic Building',
                'description' => 'Contains the College of Business and Accountancy and allied lecture halls.',
                'icon' => '🏫',
                'mapOrder' => null,
            ],
            [
                'name' => 'East Academic Cluster',
                'slug' => 'east-academic-cluster',
                'category' => 'Academic',
                'type' => 'Academic Building',
                'description' => 'Dedicated to education, nursing, and graduate school programs.',
                'icon' => '🎓',
                'mapOrder' => null,
            ],
            [
                'name' => 'Liceo Civic Center',
                'slug' => 'liceo-civic-center',
                'category' => 'Facilities',
                'type' => 'Campus Facility',
                'description' => 'Main event venue hosting university assemblies, cultural shows, and graduations.',
                'icon' => '🏫',
                'mapOrder' => 4,
            ],
            [
                'name' => 'Rodolsa Hall',
                'slug' => 'rodolsa-hall',
                'category' => 'Facilities',
                'type' => 'University Facility',
                'description' => 'Multi-purpose hall used for seminars, conferences, and student activities.',
                'icon' => '🏢',
                'mapOrder' => 3,
            ],
            [
                'name' => 'University Library',
                'slug' => 'university-library',
                'category' => 'Services',
                'type' => 'Library Facility',
                'description' => 'Central library with reading areas, research databases, and study rooms.',
                'icon' => '📚',
                'mapOrder' => null,
            ],
            [
                'name' => 'Health Services Clinic',
                'slug' => 'health-services-clinic',
                'category' => 'Services',
                'type' => 'Medical Facility',
                'description' => 'On-campus clinic providing first aid, consultations, and wellness services.',
                'icon' => '🏥',
                'mapOrder' => null,
            ],
            [
                'name' => 'Registrar\'s Office',
                'slug' => 'registrars-office',
                'category' => 'Offices',
                'type' => 'Administrative Office',
                'description' => 'Handles enrollment records, grades, and official documents.',
                'icon' => '🗂',
                'mapOrder' => null,
            ],
            [
                'name' => 'Cashier\'s Office',
                'slug' => 'cashiers-office',
                'category' => 'Offices',
                'type' => 'Financial Services',
                'description' => 'Processes tuition payments, fees, and other financial transactions.',
                'icon' => '💰',
                'mapOrder' => null,
            ],
            [
                'name' => 'HRMO',
                'slug' => 'hrmo',
                'category' => 'Administrative',
                'type' => 'Administrative Office',
                'description' => 'Human Resource Management Office for employee and administrative concerns.',
                'icon' => '👥',
                'mapOrder' => null,
            ],
            [
                'name' => 'IT Services Lab',
                'slug' => 'it-services-lab',
                'category' => 'Services',
                'type' => 'Technology Facility',
                'description' => 'Computer laboratory and IT support center for students and faculty.',
                'icon' => '🖥️',
                'mapOrder' => null,
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($locations as $location) {
            $existing = $prisma->location->findUnique([
                'where' => ['slug' => $location['slug']],
            ]);

            if ($existing !== null) {
                $skipped++;
                continue;
            }

            $prisma->location->create(['data' => $location]);
            $created++;
        }

        $this->info("Seeded {$created} campus locations ({$skipped} already present, skipped).");

        return self::SUCCESS;
    }
}