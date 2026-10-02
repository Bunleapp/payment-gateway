<?php

use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Support\Facades\Route;

/*
| Every route in this file is automatically prefixed with /api
| (configured in bootstrap/app.php) and uses the stateless "api" middleware
| group (no sessions, no cookies, no CSRF; merchants are servers, not browsers).
|
| We version the API (/api/v1) so we can release /api/v2 later without
| breaking schools that already integrated against v1.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', HealthController::class)->name('health');
});
