<?php

namespace Database\Seeders;

use App\Prisma\PrismaClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 *
 * Populates realistic, synthetic demonstration accounts for testing and grading.
 * Passwords are encrypted with bcrypt (Hash::make).
 * All demo emails conform to the @liceo.edu.ph institutional domain.
 * Safe to re-run (idempotent: checks for existence by unique email).
 */
class UserSeeder extends Seeder
{
    /**
     * Documented demo credentials (all accounts share the default demo password):
     * Password: LdcuNav2026!
     *
     * @var list<array{fullName: string, studentId: string, email: string, password: string, roleNote: string}>
     */
    public const DEMO_USERS = [
        [
            'fullName' => 'Maria Clara Santos',
            'studentId' => '2024-00123',
            'email' => 'maria.santos@liceo.edu.ph',
            'password' => 'LdcuNav2026!',
            'roleNote' => 'Senior Student (College of Business)',
        ],
        [
            'fullName' => 'Juan Dela Cruz',
            'studentId' => '2024-00456',
            'email' => 'juan.delacruz@liceo.edu.ph',
            'password' => 'LdcuNav2026!',
            'roleNote' => 'Sophomore Student (College of Engineering)',
        ],
        [
            'fullName' => 'Prof. Clara Reyes',
            'studentId' => '2020-00789',
            'email' => 'clara.reyes@liceo.edu.ph',
            'password' => 'LdcuNav2026!',
            'roleNote' => 'Faculty Member (College of Arts & Sciences)',
        ],
        [
            'fullName' => 'Demo Campus Navigator',
            'studentId' => '2025-00999',
            'email' => 'demo.navigator@liceo.edu.ph',
            'password' => 'LdcuNav2026!',
            'roleNote' => 'Evaluator / General Demo Account',
        ],
    ];

    public function run(PrismaClient $prisma): void
    {
        $created = 0;
        $updated = 0;

        foreach (self::DEMO_USERS as $demo) {
            $existing = $prisma->user->findUnique([
                'where' => ['email' => strtolower($demo['email'])],
            ]);

            if ($existing === null) {
                $prisma->user->create([
                    'data' => [
                        'fullName' => $demo['fullName'],
                        'studentId' => $demo['studentId'],
                        'email' => strtolower($demo['email']),
                        'passwordHash' => Hash::make($demo['password']),
                    ],
                ]);
                $created++;
            } else {
                $prisma->user->updateMany([
                    'where' => ['id' => (int) $existing['id']],
                    'data' => [
                        'fullName' => $demo['fullName'],
                        'studentId' => $demo['studentId'],
                        'passwordHash' => Hash::make($demo['password']),
                    ],
                ]);
                $updated++;
            }
        }

        $this->command?->info("UserSeeder: {$created} created, {$updated} updated (total: " . count(self::DEMO_USERS) . ').');
    }
}
