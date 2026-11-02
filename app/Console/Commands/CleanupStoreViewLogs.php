<?php

namespace App\Console\Commands;

use App\Models\StoreViewLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanupStoreViewLogs extends Command
{
    protected $signature = 'store-views:cleanup';

    protected $description = 'Xóa lượt xem store cũ, giữ hôm qua và hôm nay (chạy mỗi 7 ngày)';

    public function handle(): int
    {
        if (!config('store_view_log.cleanup_enabled')) {
            $this->info('Đã tắt dọn lượt xem (STORE_VIEW_LOG_CLEANUP_ENABLED=false).');

            return self::SUCCESS;
        }

        $keepFrom = Carbon::yesterday()->startOfDay();
        $chunk = max(100, (int) config('store_view_log.cleanup_chunk', 1000));
        $deleted = 0;

        do {
            $ids = StoreViewLog::query()
                ->where('created_at', '<', $keepFrom)
                ->orderBy('id')
                ->limit($chunk)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $deleted += StoreViewLog::query()->whereIn('id', $ids)->delete();
        } while ($ids->count() === $chunk);

        $kept = StoreViewLog::query()->count();
        $this->info(sprintf(
            'Đã xóa %d record. Giữ lại %d record từ %s đến hôm nay.',
            $deleted,
            $kept,
            $keepFrom->format('d-m-Y')
        ));

        return self::SUCCESS;
    }
}
