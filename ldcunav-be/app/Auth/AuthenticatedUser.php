<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use JsonSerializable;

/**
 * AuthenticatedUser
 *
 * Laravel Authenticatable adapter around a user row read from PostgreSQL via
 * the Prisma client. This lets Laravel's auth system work without Eloquent.
 */
class AuthenticatedUser implements Authenticatable, JsonSerializable
{
    /** @var array<string, mixed> */
    private array $attributes;

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): int|string
    {
        return (int) $this->attributes['id'];
    }

    public function getAuthPasswordName(): string
    {
        return 'passwordHash';
    }

    public function getAuthPassword(): string
    {
        return (string) ($this->attributes['passwordHash'] ?? '');
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // Not used: token-based auth has no remember-token feature.
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Public (API-safe) representation of the user.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = $this->attributes;
        unset($attributes['passwordHash']);

        return $attributes;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}