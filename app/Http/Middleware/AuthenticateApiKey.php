<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            return response()->json([
                'message' => 'invalid token',
            ], 401);
        }

        [$key, $secret] = $parts;

        $apiKey = ApiKey::verify($key, $secret);

        if (!$apiKey) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->attributes->set('merchant', $apiKey->merchant);

        return $next($request);
    }
}
