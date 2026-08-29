<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiVersioningTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function api_versioning_test_returns_ok(): void
    {
        $response = $this->get('/api/v1/');

        $response->assertStatus(200)->assertJson(["status" => "ok"]);
    }
}
