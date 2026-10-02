<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * GET /api/v1/health
 *
 * Tells callers (you, Postman, load balancers, monitoring) whether the
 * gateway is alive AND able to reach its database. A payment gateway that
 * is "up" but cannot write to its database must not accept payments,
 * so we report 503 in that case.
 */
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $databaseOk = $this->databaseIsReachable();

        return response()->json(
            [
                'status' => $databaseOk ? 'ok' : 'degraded',
                'checks' => [
                    'database' => $databaseOk ? 'ok' : 'unreachable',
                ],
            ],
            $databaseOk ? 200 : 503, // 503 = Service Unavailable
        );
    }

    private function databaseIsReachable(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (Throwable $e) {
            // Log details for us; never leak connection errors (hosts, users) to the caller.
            Log::error('Health check: database unreachable', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
