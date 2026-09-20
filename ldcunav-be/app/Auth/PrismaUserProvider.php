<?php

namespace App\Auth;

use App\Prisma\PrismaClient;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

/**
 * PrismaUserProvider
 *
 * Laravel user provider backed by the Prisma client instead of Eloquent, used
 * by the "api" guard. Passwords are verified with Laravel's Hash (bcrypt).
 */
class PrismaUserProvider implements UserProvider
{
    public function __construct(private readonly PrismaClient $prisma)
    {
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        $user = $this->prisma->user->findUnique([
            'where' => ['id' => (int) $identifier],
        ]);

        return $user === null ? null : new AuthenticatedUser($user);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        $record = $this->prisma->personalAccessToken->findUnique([
            'where' => ['tokenHash' => hash('sha256', (string) $token)],
        ]);

        if ($record === null) {
            return null;
        }

        $user = $this->prisma->user->findUnique([
            'where' => ['id' => (int) $record['userId']],
        ]);

        return $user === null ? null : new AuthenticatedUser($user);
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        // Token-based auth, no remember-me feature.
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (! isset($credentials['email']) || $credentials['email'] === '') {
            return null;
        }

        $user = $this->prisma->user->findUnique([
            'where' => ['email' => (string) $credentials['email']],
        ]);

        return $user === null ? null : new AuthenticatedUser($user);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (! isset($credentials['password'])) {
            return false;
        }

        return Hash::check((string) $credentials['password'], $user->getAuthPassword());
    }
}