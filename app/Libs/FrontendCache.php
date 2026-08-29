<?php

namespace App\Libs;

use Illuminate\Support\Facades\Cache;

class FrontendCache
{
    public static function flush(): void
    {
        foreach (config('app.available_locales', ['en']) as $locale) {
            Cache::forget('frontend_share_v1_' . $locale);
            Cache::forget('home_page_data_v1_' . $locale);
        }
    }
}
