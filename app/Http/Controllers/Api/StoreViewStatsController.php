<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StoreViewSnapshotService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreViewStatsController extends Controller
{
    public function index(Request $request, StoreViewSnapshotService $snapshotService): JsonResponse
    {
        $date = $this->parseDay($request->get('day'));
        if ($date === null) {
            return response()->json([
                'ok' => false,
                'message' => 'Tham số day không hợp lệ. Ví dụ: day=28-8-2026',
            ], 422);
        }

        $weekly = filter_var($request->get('week', false), FILTER_VALIDATE_BOOLEAN);
        $result = $snapshotService->compare($date, $weekly);

        return response()->json($result, ($result['ok'] ?? false) ? 200 : 404);
    }

    private function parseDay(?string $day): ?Carbon
    {
        if ($day === null || trim($day) === '') {
            return Carbon::today();
        }

        $day = trim(str_replace('/', '-', $day));
        $formats = ['j-n-Y', 'd-m-Y', 'j-m-Y', 'd-n-Y'];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $day);
                if ($date !== false) {
                    return $date->startOfDay();
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
