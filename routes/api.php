<?php

use App\Http\Controllers\Api\AffiliateController;
use Illuminate\Support\Facades\Route;

/*
| Affiliate Store Publisher API (dealhunter365). Full paths: /api/affiliate/v1/{...}
| Auth: shared key via X-API-Key header / Authorization: Bearer / ?api_key=
| throttle:600,1 = 600 requests/minute (loosened so batch publishing won't hit 429).
*/
// Route::prefix('affiliate/v1')
//     ->middleware(['affiliate.key', 'throttle:600,1'])
//     ->group(function () {
//         Route::get('ping', [AffiliateController::class, 'ping']);
//         Route::post('import', [AffiliateController::class, 'import']);
//         Route::get('store', [AffiliateController::class, 'getStore']);
//         Route::get('categories', [AffiliateController::class, 'categories']);
//         Route::post('update', [AffiliateController::class, 'update']);
//         Route::post('post', [AffiliateController::class, 'createPostRoute']);
//     });
