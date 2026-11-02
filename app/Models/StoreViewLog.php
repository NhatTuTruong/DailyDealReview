<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreViewLog extends Model
{
    public const TYPE_VIEW = 'view';
    public const TYPE_AFFILIATE_CLICK = 'affiliate_click';
    public const TYPE_LEAVE = 'leave';

    public const TYPE_LABELS = [
        self::TYPE_VIEW => 'Xem store',
        self::TYPE_AFFILIATE_CLICK => 'Click deal',
        self::TYPE_LEAVE => 'Rời trang',
    ];

    public const TYPE_BADGES = [
        self::TYPE_VIEW => 'badge-info',
        self::TYPE_AFFILIATE_CLICK => 'badge-warning',
        self::TYPE_LEAVE => 'badge-secondary',
    ];

    public const VISITOR_HUMAN = 'human';
    public const VISITOR_BOT = 'bot';
    public const VISITOR_ALL = 'all';

    public const VISITOR_LABELS = [
        self::VISITOR_HUMAN => 'Người dùng',
        self::VISITOR_BOT => 'Bot',
        self::VISITOR_ALL => 'Tất cả',
    ];

    protected $fillable = [
        'store_id',
        'store_name',
        'event_type',
        'ip',
        'device',
        'browser',
        'os',
        'country',
        'country_code',
        'user_agent',
        'target_url',
        'referer',
        'gclid',
        'from_google_ads',
        'duration_seconds',
    ];

    protected function casts(): array
    {
        return [
            'from_google_ads' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeVisitor($query, string $visitor)
    {
        if ($visitor === self::VISITOR_BOT) {
            return $query->where('device', 'Bot');
        }

        if ($visitor === self::VISITOR_ALL) {
            return $query;
        }

        return $query->where(function ($q) {
            $q->whereNull('device')->orWhere('device', '<>', 'Bot');
        });
    }

    public function eventTypeLabel(): string
    {
        return self::TYPE_LABELS[$this->event_type] ?? self::TYPE_LABELS[self::TYPE_VIEW];
    }

    public function eventTypeBadge(): string
    {
        return self::TYPE_BADGES[$this->event_type] ?? self::TYPE_BADGES[self::TYPE_VIEW];
    }
}
