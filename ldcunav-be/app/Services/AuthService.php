<?php

namespace App\Services;

use App\Prisma\PrismaClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PDOException;

/**
 * AuthService
 *
 * Registration, login, logout and password recovery. Passwords are hashed with
 * Laravel's Hash (bcrypt); bearer tokens are random strings stored as SHA-256
 * hashes in the "PersonalAccessToken" table.
 */
class AuthService
{
    public function __construct(private readonly PrismaClient $prisma)
    {
    }

    /**
     * Create a user and issue a bearer token.
     *
     * @param  array<string, mixed>  $data  validated input: fullName, studentId, email, password
     * @return array{user: array<string, mixed>, token: string}
     */
    public function register(array $data): array
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $studentId = trim((string) ($data['studentId'] ?? ''));

        $this->assertEmailAvailable($email);
        $this->assertStudentIdAvailable($studentId);

        try {
            $user = $this->prisma->user->create([
                'data' => [
                    'fullName' => trim((string) ($data['fullName'] ?? '')),
                    'studentId' => $studentId,
                    'email' => $email,
                    'passwordHash' => Hash::make((string) ($data['password'] ?? '')),
                ],
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23505') { // unique_violation
                $this->assertEmailAvailable($email);
                $this->assertStudentIdAvailable($studentId);
            }

            throw $e;
        }

        $user = $this->publicUser($user);

        return [
            'user' => $user,
            'token' => $this->issueToken((int) $user['id'], 'auth'),
        ];
    }

    /**
     * Authenticate by email + password.
     *
     * @return array{user: array<string, mixed>, token: string}|null
     */
    public function login(string $email, string $password): ?array
    {
        $user = $this->prisma->user->findUnique([
            'where' => ['email' => strtolower(trim($email))],
        ]);

        if ($user === null || ! Hash::check((string) $password, (string) ($user['passwordHash'] ?? ''))) {
            return null;
        }

        return [
            'user' => $this->publicUser($user),
            'token' => $this->issueToken((int) $user['id'], 'auth'),
        ];
    }

    /**
     * Revoke the given plaintext bearer token.
     */
    public function logout(string $token): void
    {
        if ($token === '') {
            return;
        }

        $this->prisma->personalAccessToken->deleteMany([
            'tokenHash' => hash('sha256', $token),
        ]);
    }

    /**
     * Password recovery. Deliberately generic: it never reveals whether the
     * email exists. No real mail transport is configured in development, so no
     * link is actually dispatched (see final report for this limitation).
     */
    public function forgotPassword(string $email): void
    {
        $user = $this->prisma->user->findUnique([
            'where' => ['email' => strtolower(trim($email))],
        ]);

        if ($user !== null) {
            // Reserved for future reset-link dispatch; nothing to do yet.
            logger()->info('Password reset requested', ['email' => $user['email']]);
        }
    }

    /**
     * Generate a token, store only its SHA-256 hash, and return the plaintext.
     */
    public function issueToken(int $userId, string $name): string
    {
        $plain = Str::random(64);

        $this->prisma->personalAccessToken->create([
            'data' => [
                'userId' => $userId,
                'name' => $name,
                'tokenHash' => hash('sha256', $plain),
                'abilities' => '["*"]',
            ],
        ]);

        return $plain;
    }

    private function assertEmailAvailable(string $email): void
    {
        if ($this->prisma->user->findUnique(['where' => ['email' => $email]]) !== null) {
            throw ValidationException::withMessages([
                'email' => ['That email is already registered.'],
            ]);
        }
    }

    private function assertStudentIdAvailable(string $studentId): void
    {
        if ($this->prisma->user->findUnique(['where' => ['studentId' => $studentId]]) !== null) {
            throw ValidationException::withMessages([
                'studentId' => ['That student ID is already registered.'],
            ]);
        }
    }

    /**
     * Strip the password hash before the user array leaves the service.
     *
     * @param  array<string, mixed>  $user
     * @return array<string, mixed>
     */
    private function publicUser(array $user): array
    {
        unset($user['passwordHash']);

        return $user;
    }
}