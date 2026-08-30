<?php

namespace Tests\Feature;

use App\Traits\ApiResponseTrait;
use Tests\TestCase;

class TraitResponseTest extends TestCase
{
    public function test_api_success_response_returns_200(): void
    {
        $response = (new TestResponseClass)->successResponse(
            ['ok'],
            'Operation completed successfully.'
        );

        $this->assertSame(200, $response->status());

        $this->assertSame([
            'message' => 'Operation completed successfully.',
            'data' => ['ok'],
        ], $response->getData(true));
    }

    public function test_api_error_response_returns_400(): void
    {
        $response = (new TestResponseClass)->errorResponse(
            'The request could not be processed.'
        );

        $this->assertSame(400, $response->status());

        $this->assertSame([
            'message' => 'The request could not be processed.',
            'errors' => null,
        ], $response->getData(true));
    }
}

class TestResponseClass
{
    use ApiResponseTrait;
}