<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreViewLog extends Model
{
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
}
