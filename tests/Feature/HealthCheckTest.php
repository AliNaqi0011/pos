<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HealthCheckTest extends TestCase
{
    public function test_health_check_returns_success()
    {
        $response = $this->get('/api/health');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'status',
                    'timestamp',
                    'checks' => [
                        'database' => ['status', 'message'],
                        'cache' => ['status', 'message'],
                        'storage' => ['status', 'message']
                    ]
                ]);
    }

    public function test_api_documentation_returns_success()
    {
        $response = $this->get('/api/docs');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'name',
                    'version',
                    'description',
                    'endpoints'
                ]);
    }
}