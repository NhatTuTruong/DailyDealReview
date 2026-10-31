<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BlockedIp extends Model
{
    private const CACHE_KEY = 'blocked_ips_list';

    protected $fillable = [
        'ip',
        'note',
        'created_by',
    ];

    public static function cachedList(): array
    {
        return Cache::remember(self::CACHE_KEY, 300, function () {
            return self::query()->pluck('ip')->all();
        });
    }

    public static function isBlocked(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }

        return in_array($ip, self::cachedList(), true);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::forgetCache());
        static::deleted(fn () => self::forgetCache());
    }
}
