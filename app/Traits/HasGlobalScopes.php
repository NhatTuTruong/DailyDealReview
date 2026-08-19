<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

trait HasGlobalScopes
{
    /**
     * Scope: Chỉ lấy dữ liệu có status = 1 (active)
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: Lọc theo ngôn ngữ, mặc định lấy App::getLocale()
     */
    public function scopeLanguage(Builder $query, $language = null)
    {
        return $query->where('language', $language ?? App::getLocale());
    }
}
