<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SlugController;
use App\Http\Controllers\AjaxController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';
require __DIR__ . '/backend.php';
require __DIR__ . '/member.php';

Route::localized(function () {
    Route::group(['prefix' => 'ajax'], function () {
        Route::post('/get_district', [AjaxController::class, 'getDistrict'])->name('ajax_get_district');
        Route::post('/get_ward', [AjaxController::class, 'getWard'])->name('ajax_get_ward');
    });

//Route::get('test-send-mail', 'HomeController@testSendMail');

    Route::get('/', [HomeController::class, 'index'])->name('home_page');
    Route::get('/home', [HomeController::class, 'index']);
    // Route::get('/sitemap.xml', [HomeController::class, 'siteMap'])->name('site_map');
    Route::match(['get', 'post'], '/contact.html', [HomeController::class, 'contact'])->name('contact');

    Route::get('/search', [PostController::class, 'search'])->name('search');
    Route::match(['get', 'post'], '/search/suggest', [HomeController::class, 'search'])->name('search_suggest');
    Route::get('/page/{slug}.html', [HomeController::class, 'page'])->where(['slug' => '[a-z0-9\-]+'])->name('page_content');
    Route::post('subscribe', [HomeController::class, 'subscribe'])->name('subscribe');

    Route::get('categories', [CouponController::class, 'categories'])->name('category_coupons');
    Route::get('store', [StoreController::class, 'all'])->name('store_all');
    Route::get('store/{slug}', [StoreController::class, 'index'])->where(['slug' => '[a-z0-9\-]+'])->name('store_detail');

    Route::post('/api/record', [TrackingController::class, 'trackingFootprint']);

    Route::get('{slug}-n{id}.html', [PostController::class, 'detail'])->where(['slug' => '[a-z0-9\-]+', 'id' => '[0-9]+'])->name('post_detail');
    Route::get('tag/{tag}', [PostController::class, 'tag'])->where(['tag' => '[a-z0-9\-]+'])->name('post_tag');
    Route::get('blogs', [PostController::class, 'all'])->name('post_all');

    Route::get('{slug}', [SlugController::class, 'index'])->where(['slug' => '[a-zA-Z0-9\-]+'])->name('category');
});

Route::fallback(function () {
//    return redirect('/', 301);
});