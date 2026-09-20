<?php

namespace App\Providers;

use App\Auth\AuthenticatedUser;
use App\Auth\PrismaUserProvider;
use App\Prisma\PrismaClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PrismaClient::class, function () {
            return new PrismaClient([
                'host' => (string) env('DB_HOST', '127.0.0.1'),
                'port' => (string) env('DB_PORT', '5432'),
                'database' => (string) env('DB_DATABASE', 'ldcunav'),
                'username' => (string) env('DB_USERNAME', 'postgres'),
                'password' => (string) env('DB_PASSWORD', ''),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Guard used by "api" guard: resolve the user from the bearer token
        // that is stored (hashed) in the "PersonalAccessToken" table.
        Auth::viaRequest('ldcunav-token', function (Request $request) {
            $token = (string) $request->bearerToken();

            if ($token === '') {
                return null;
            }

            $prisma = $this->app->make(PrismaClient::class);

            $record = $prisma->personalAccessToken->findUnique([
                'where' => ['tokenHash' => hash('sha256', $token)],
            ]);

            if ($record === null) {
                return null;
            }

            if ($record['expiresAt'] !== null) {
                $expiresAt = strtotime((string) $record['expiresAt']);

                if ($expiresAt === false || $expiresAt < time()) {
                    return null;
                }
            }

            $user = $prisma->user->findUnique([
                'where' => ['id' => (int) $record['userId']],
            ]);

            if ($user === null) {
                return null;
            }

            // Best-effort "last used" tracking (never fail the request on this).
            $prisma->personalAccessToken->updateMany([
                'where' => ['id' => (int) $record['id']],
                'data' => ['lastUsedAt' => new \DateTimeImmutable('now', new \DateTimeZone('UTC'))],
            ]);

            return new AuthenticatedUser($user);
        });

        // User provider backed by the Prisma client (no Eloquent).
        Auth::provider('prisma', function ($app, array $config) {
            return new PrismaUserProvider($app->make(PrismaClient::class));
        });
    }
}