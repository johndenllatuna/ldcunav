<?php

namespace Tests\Unit;

use App\Prisma\ModelQuery;
use App\Prisma\PrismaClient;
use InvalidArgumentException;
use Tests\TestCase;

class ModelQueryTest extends TestCase
{
    private ModelQuery $locationQuery;

    protected function setUp(): void
    {
        parent::setUp();
        $prisma = app(PrismaClient::class);
        $this->locationQuery = $prisma->location;
    }

    public function test_rejects_unknown_fields_in_where_clause(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown field "malicious_column"');

        $this->locationQuery->findMany([
            'where' => ['malicious_column' => 'value'],
        ]);
    }

    public function test_rejects_unknown_fields_in_order_by(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown field "non_existent_column"');

        $this->locationQuery->findMany([
            'orderBy' => ['non_existent_column' => 'desc'],
        ]);
    }

    public function test_count_returns_non_negative_integer(): void
    {
        $count = $this->locationQuery->count();

        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }
}
