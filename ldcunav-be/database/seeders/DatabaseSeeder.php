<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Master orchestrator for LdcuNav database seeding.
 * Executes seeders in dependency order:
 * 1. LocationSeeder (populates campus locations)
 * 2. UserSeeder (creates synthetic demo accounts with hashed passwords)
 * 3. SavedLocationSeeder (creates bookmarks linking demo users to locations)
 *
 * All seeders interact with PostgreSQL through App\Prisma\PrismaClient and are
 * safe to execute repeatedly without duplicating records or dropping existing data.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            UserSeeder::class,
            SavedLocationSeeder::class,
        ]);
    }
}
