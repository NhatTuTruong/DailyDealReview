<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuspiciousIp extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_IGNORED = 'ignored';

    public const REASON_LABELS = [
        'burst_traffic' => 'Truy cập dày + tín hiệu phụ (không referer / nhiều store / bot)',
        'affiliate_spray' => 'Bấm nhiều URL affiliate liên tục',
        'rapid_clicks' => 'Click affiliate quá nhanh',
        'click_without_view' => 'Click affiliate mà không xem trang',
        'bot_signature' => 'Dấu hiệu bot / scraper',
        'abnormal_ua' => 'User-Agent bất thường',
        'ua_rotation' => 'Đổi nhiều User-Agent trên cùng IP',
        'no_interaction' => 'Xem nhiều store nhưng không tương tác',
        'ads_repeat' => 'Vào từ Google Ads nhiều lần trong thời gian ngắn',
        'short_bounce' => 'Thoát dưới 5 giây nhiều lần (không click deal)',
        'repeat_same_url' => 'Lặp lại cùng URL / store nhiều lần',
        'periodic_access' => 'Truy cập theo chu kỳ giống bot',
        'many_brands' => 'Click / xem nhiều brand trong thời gian ngắn',
    ];

    protected $fillable = [
        'ip',
        'score',
        'reasons',
        'view_count_window',
        'affiliate_count_window',
        'unique_store_count',
        'country',
        'last_user_agent',
        'status',
        'first_detected_at',
        'last_detected_at',
    ];

    protected function casts(): array
    {
        return [
            'reasons' => 'array',
            'first_detected_at' => 'datetime',
            'last_detected_at' => 'datetime',
        ];
    }

    public function reasonLabels(): array
    {
        return collect($this->reasons ?? [])
            ->map(fn ($code) => self::REASON_LABELS[$code] ?? $code)
            ->values()
            ->all();
    }
}
