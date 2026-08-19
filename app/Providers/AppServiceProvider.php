<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Request $request): void
    {
        Paginator::useBootstrapFour();

        //Set route local
        $firstSegment = $request->segment(1);
        $availableLocales = config('app.available_locales');
        if (in_array($firstSegment, $availableLocales)) {
            $locale = $firstSegment;
        } else {
            $locale = config('app.fallback_locale');
        }

        App::setLocale($locale);

        $locale = $locale == config('app.fallback_locale') ? '' : $locale;
        // Tự động thêm prefix locale cho tất cả route
        Route::macro('localized', function ($callback) use ($availableLocales, $locale) {
            Route::group(['prefix' => $locale], function () use ($callback) {
                $callback();
            })->where(['locale' => implode('|', $availableLocales)]);
        });
    }
}
