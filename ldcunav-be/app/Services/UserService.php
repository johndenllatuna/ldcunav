<?php

namespace App\Services;

use App\Prisma\PrismaClient;
use Illuminate\Validation\ValidationException;

/**
 * UserService
 *
 * Profile read/update operations for the authenticated user.
 */
class UserService
{
    public function __construct(private readonly PrismaClient $prisma)
    {
    }

    /**
     * Update the authenticated user's editable profile fields.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateProfile(int $userId, array $data): array
    {
        $user = $this->prisma->user->update([
            'where' => ['id' => $userId],
            'data' => [
                'fullName' => trim((string) ($data['fullName'] ?? '')),
            ],
        ]);

        if ($user === null) {
            throw ValidationException::withMessages(['id' => ['User not found.']]);
        }

        unset($user['passwordHash']);

        return $user;
    }
}