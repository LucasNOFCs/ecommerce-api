<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiVersioningTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_api_versioning_returns_ok(): void
    {
        $response = $this->get('/api/v1/');

        $response->assertStatus(200)->assertJson(["status" => "ok"]);
    }
}
