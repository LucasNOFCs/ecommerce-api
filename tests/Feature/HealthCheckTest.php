<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_check(): void
    {
        $response = $this->get("/up");

        $response->assertJson(
            ["message" => "Application is running ok.", "data" => null],
            200,
        );
    }

    public function test_health_check_versionated_endpoint(): void
    {
        $response = $this->get("api/v1/health");

        $response->assertJson(
            ["message" => "Application is running ok.", "data" => null],
            200,
        );
    }
}
