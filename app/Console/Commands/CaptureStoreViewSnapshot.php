<?php

namespace App\Console\Commands;

use App\Services\StoreViewSnapshotService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CaptureStoreViewSnapshot extends Command
{
    protected $signature = 'store:capture-view-snapshot {--date= : Ngày snapshot d-m-Y, mặc định hôm nay}';

    protected $description = 'Lưu snapshot lượt xem store theo ngày (file JSON, không dùng DB)';

    public function handle(StoreViewSnapshotService $snapshotService): int
    {
        $dateOption = $this->option('date');
        $date = Carbon::today();

        if ($dateOption) {
            $parsed = null;
            foreach (['j-n-Y', 'd-m-Y'] as $format) {
                try {
                    $parsed = Carbon::createFromFormat($format, trim(str_replace('/', '-', $dateOption)));
                    break;
                } catch (\Throwable) {
                    continue;
                }
            }

            if (!$parsed) {
                $this->error('Tham số --date không hợp lệ. Ví dụ: --date=28-8-2026');
                return self::FAILURE;
            }

            $date = $parsed->startOfDay();
        }

        $count = $snapshotService->capture($date);
        $this->info('Đã lưu snapshot ' . $count . ' store cho ngày ' . $date->format('d-m-Y'));

        return self::SUCCESS;
    }
}
