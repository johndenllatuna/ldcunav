<?php

namespace App\Prisma;

use PDO;
use PDOException;
use RuntimeException;

/**
 * PrismaClient
 *
 * Project-local, Prisma-flavoured data-access client for LDCUNav.
 *
 * Prisma (the CLI / schema / migrations) is the single source of truth for the
 * PostgreSQL schema. At runtime Laravel talks to PostgreSQL through this small
 * client (PDO prepared statements) because there is no maintained PHP client for
 * Prisma. It mirrors a small, useful subset of the Prisma query API:
 *
 *   $client->user->findUnique(['where' => ['email' => ...]])
 *   $client->location->findMany(['where' => [...], 'orderBy' => [...]])
 *   $client->savedLocation->create(['data' => [...]])
 *
 * All values are bound as prepared-statement parameters; column identifiers are
 * validated against a fixed field map (never taken from user input), so the
 * client is injection-safe by construction.
 */
class PrismaClient
{
    private const OPTIONS = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    /** @var array<string, mixed> Connection options. */
    private array $config;

    private ?PDO $connection = null;

    public ModelQuery $user;

    public ModelQuery $location;

    public ModelQuery $savedLocation;

    public ModelQuery $personalAccessToken;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $this->user = new ModelQuery($this, 'User', [
            'id', 'fullName', 'studentId', 'email', 'passwordHash', 'createdAt', 'updatedAt',
        ], dateFields: ['createdAt', 'updatedAt']);

        $this->location = new ModelQuery($this, 'Location', [
            'id', 'name', 'slug', 'category', 'type', 'description', 'icon', 'mapOrder', 'createdAt', 'updatedAt',
        ], dateFields: ['createdAt', 'updatedAt']);

        $this->savedLocation = new ModelQuery($this, 'SavedLocation', [
            'id', 'userId', 'locationId', 'createdAt',
        ], dateFields: ['createdAt']);

        $this->personalAccessToken = new ModelQuery($this, 'PersonalAccessToken', [
            'id', 'userId', 'name', 'tokenHash', 'abilities', 'lastUsedAt', 'expiresAt', 'createdAt',
        ], dateFields: ['lastUsedAt', 'expiresAt', 'createdAt']);
    }

    /**
     * Lazily connect to PostgreSQL.
     */
    public function connection(): PDO
    {
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $this->config['host'] ?? '127.0.0.1',
            $this->config['port'] ?? '5432',
            $this->config['database'] ?? 'ldcunav'
        );

        try {
            $pdo = new PDO(
                $dsn,
                (string) ($this->config['username'] ?? 'postgres'),
                (string) ($this->config['password'] ?? ''),
                self::OPTIONS
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Unable to connect to the database: '.$e->getMessage(), 0, $e);
        }

        $pdo->exec("SET search_path TO public");

        return $this->connection = $pdo;
    }

    /**
     * Quote an identifier (table / column name) for PostgreSQL.
     */
    public function quoteIdent(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }

    /**
     * Quote a value using the native PDO quoter (escaped literal).
     */
    public function quoteValue(mixed $value): string
    {
        return $this->connection()->quote(match (true) {
            $value === null => 'NULL',
            is_bool($value) => $value ? 'TRUE' : 'FALSE',
            is_int($value), is_float($value) => (string) $value,
            default => (string) $value,
        });
    }

    /**
     * Convert a value into a UTC-naive timestamp string that matches how the
     * Prisma-managed timestamp(3) columns store values.
     */
    public function toDatabaseDateTime(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        }

        // Timestamps in Prisma-managed columns are stored as UTC.
        $timestamp = strtotime((string) $value);

        return $timestamp === false
            ? null
            : gmdate('Y-m-d H:i:s', $timestamp).'.'.gmdate('v', $timestamp);
    }

    /**
     * Convert a stored timestamp into an ISO-8601 string (Prisma wire format).
     */
    public function fromDatabaseDateTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $timestamp = is_numeric($value) ? (int) $value : strtotime((string) $value);

        return $timestamp === false
            ? (string) $value
            : gmdate('Y-m-d\TH:i:s.v\Z', $timestamp);
    }

    public function beginTransaction(): void
    {
        $this->connection()->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection()->commit();
    }

    public function rollBack(): void
    {
        $this->connection()->rollBack();
    }

    /**
     * Run a callable inside a transaction, rolling back on failure.
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();

            return $result;
        } catch (\Throwable $e) {
            if ($this->connection()->inTransaction()) {
                $this->rollBack();
            }

            throw $e;
        }
    }
}