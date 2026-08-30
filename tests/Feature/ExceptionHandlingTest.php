<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;

use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_api_404_returns_json(): void
    {
        $response = $this->getJson("/api/v1/non-existent-route");

        $response->assertStatus(404);
        $response->assertJson(["message" => "Not Found"]);
    }

    public function test_api_500_returns_json(): void
    {

        Route::get('/api/v1/internal-server-error', function () {
            throw new \Exception('Internal Server Error');
        });
        
        $response = $this->getJson("/api/v1/internal-server-error");

        $response->assertStatus(500);
        $response->assertJson(["message" => "Internal Server Error"]);
    }
}
