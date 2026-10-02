<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_returns_ok_when_database_is_reachable(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
            ->assertExactJson([
                'status' => 'ok',
                'checks' => ['database' => 'ok'],
            ]);
    }

    public function test_health_endpoint_returns_503_when_database_is_down(): void
    {
        // Simulate a database outage without actually stopping PostgreSQL.
        DB::shouldReceive('select')->andThrow(new RuntimeException('connection refused'));

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', 'degraded')
            ->assertJsonPath('checks.database', 'unreachable')
            // Internal error details must never be exposed to callers.
            ->assertDontSee('connection refused');
    }
}
