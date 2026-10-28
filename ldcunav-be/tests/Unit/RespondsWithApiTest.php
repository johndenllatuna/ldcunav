<?php

namespace Tests\Unit;

use App\Support\RespondsWithApi;
use Tests\TestCase;

class RespondsWithApiTest extends TestCase
{
    private object $traitConsumer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitConsumer = new class {
            use RespondsWithApi;

            public function callSuccess(string $message, mixed $data = null, int $statusCode = 200)
            {
                return $this->successResponse($message, $data, $statusCode);
            }

            public function callError(string $message, mixed $errors = null, int $statusCode = 400)
            {
                return $this->errorResponse($message, $errors, $statusCode);
            }
        };
    }

    public function test_success_response_structure_and_defaults(): void
    {
        $response = $this->traitConsumer->callSuccess('Operation successful', ['key' => 'val']);

        $this->assertSame(200, $response->getStatusCode());
        $payload = json_decode((string) $response->getContent(), true);

        $this->assertTrue($payload['success']);
        $this->assertSame('Operation successful', $payload['message']);
        $this->assertSame(['key' => 'val'], $payload['data']);
    }

    public function test_success_response_with_custom_status_code(): void
    {
        $response = $this->traitConsumer->callSuccess('Created', ['id' => 1], 201);

        $this->assertSame(201, $response->getStatusCode());
        $payload = json_decode((string) $response->getContent(), true);

        $this->assertTrue($payload['success']);
        $this->assertSame('Created', $payload['message']);
        $this->assertSame(1, $payload['data']['id']);
    }

    public function test_error_response_structure(): void
    {
        $response = $this->traitConsumer->callError('Validation failed', ['field' => ['Invalid value']], 422);

        $this->assertSame(422, $response->getStatusCode());
        $payload = json_decode((string) $response->getContent(), true);

        $this->assertFalse($payload['success']);
        $this->assertSame('Validation failed', $payload['message']);
        $this->assertSame(['Invalid value'], $payload['errors']['field']);
        $this->assertArrayNotHasKey('data', $payload);
    }
}
