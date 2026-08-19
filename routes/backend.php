<?php

use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\MemberController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\StoreController;
use App\Http\Controllers\Backend\OfferController;
use App\Http\Controllers\Backend\FeedbackController;
use App\Http\Controllers\Backend\FileManagerController;
use App\Http\Controllers\Backend\LandingPageController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\WidgetController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\GroupController;
use Illuminate\Support\Facades\Route;

Route::localized(function () {
    Route::get('/backend', function () {
        return view('backend.dashboard.index');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::prefix('backend')->middleware(['auth', 'can:backend_access'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('backend_home');

        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('backend_category');
            Route::post('/', [CategoryController::class, 'saveDataIndex'])->name('backend_category_save_data_index');
            Route::get('/create', [CategoryController::class, 'edit'])->name('backend_category_create');
            Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('backend_category_edit');
            Route::post('/save/{category?}', [CategoryController::class, 'save'])->name('backend_category_save');
            Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('backend_category_delete');
            Route::post('/bulk_delete', [CategoryController::class, 'bulkDelete'])->name('backend_category_bulk_delete');
            Route::get('/export', [CategoryController::class, 'export'])->name('backend_category_export');
        });

        Route::prefix('menu')->group(function () {
            Route::get('/', [MenuController::class, 'index'])->name('backend_menu');
            Route::post('/', [MenuController::class, 'saveDataIndex'])->name('backend_menu_save_data_index');
            Route::get('/create', [MenuController::class, 'edit'])->name('backend_menu_create');
            Route::get('/edit/{menu}', [MenuController::class, 'edit'])->name('backend_menu_edit');
            Route::post('/save/{menu?}', [MenuController::class, 'save'])->name('backend_menu_save');
            Route::get('/delete/{id}', [MenuController::class, 'delete'])->name('backend_menu_delete');
            Route::post('/bulk_delete', [MenuController::class, 'bulkDelete'])->name('backend_menu_bulk_delete');
        });

        Route::prefix('page')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('backend_page');
            Route::post('/', [PageController::class, 'saveDataIndex'])->name('backend_page_save_data_index');
            Route::get('/create', [PageController::class, 'edit'])->name('backend_page_create');
            Route::get('/edit/{page}', [PageController::class, 'edit'])->name('backend_page_edit');
            Route::post('/save/{page?}', [PageController::class, 'save'])->name('backend_page_save');
            Route::get('/delete/{id}', [PageController::class, 'delete'])->name('backend_page_delete');
            Route::post('/bulk_delete', [PageController::class, 'bulkDelete'])->name('backend_page_bulk_delete');
        });

        Route::prefix('member')->group(function () {
            Route::get('/', [MemberController::class, 'index'])->name('backend_member');
            Route::post('/', [MemberController::class, 'saveDataIndex'])->name('backend_member_save_data_index');
            Route::get('/create', [MemberController::class, 'edit'])->name('backend_member_create');
            Route::get('/edit/{member}', [MemberController::class, 'edit'])->name('backend_member_edit');
            Route::post('/save/{member?}', [MemberController::class, 'save'])->name('backend_member_save');
            Route::get('/delete/{id}', [MemberController::class, 'delete'])->name('backend_member_delete');
            Route::post('/bulk_delete', [MemberController::class, 'bulkDelete'])->name('backend_member_bulk_delete');
        });

        Route::prefix('post')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('backend_post');
            Route::post('/', [PostController::class, 'saveDataIndex'])->name('backend_post_save_data_index');
            Route::get('create', [PostController::class, 'edit'])->name('backend_post_create');
            Route::get('edit/{post}', [PostController::class, 'edit'])->name('backend_post_edit');
            Route::post('save/{post?}', [PostController::class, 'save'])->name('backend_post_save');
            Route::get('delete/{id}', [PostController::class, 'delete'])->name('backend_post_delete');
            Route::post('bulk_delete', [PostController::class, 'bulkDelete'])->name('backend_post_bulk_delete');
            Route::get('clone/{post}', [PostController::class, 'clone'])->name('backend_post_clone');
            Route::get('restore/{id}', [PostController::class, 'restore'])->name('backend_post_restore');
            Route::get('force-delete/{id}', [PostController::class, 'forceDelete'])->name('backend_post_force_delete');
        });

        Route::prefix('store')->group(function () {
            Route::get('/', [StoreController::class, 'index'])->name('backend_store');
            Route::post('/', [StoreController::class, 'saveDataIndex'])->name('backend_store_save_data_index');
            Route::get('create', [StoreController::class, 'edit'])->name('backend_store_create');
            Route::get('edit/{store}', [StoreController::class, 'edit'])->name('backend_store_edit');
            Route::post('save/{store?}', [StoreController::class, 'save'])->name('backend_store_save');
            Route::post('import', [StoreController::class, 'import'])->name('backend_store_import');
            Route::get('delete/{id}', [StoreController::class, 'delete'])->name('backend_store_delete');
            Route::post('bulk_delete', [StoreController::class, 'bulkDelete'])->name('backend_store_bulk_delete');
            Route::get('clone/{store}', [StoreController::class, 'clone'])->name('backend_store_clone');
            Route::get('restore/{id}', [StoreController::class, 'restore'])->name('backend_store_restore');
            Route::get('force-delete/{id}', [StoreController::class, 'forceDelete'])->name('backend_store_force_delete');
            Route::post('/export', [StoreController::class, 'export'])->name('backend_store_export');

        });

        Route::prefix('offer')->group(function () {
            Route::get('/', [OfferController::class, 'index'])->name('backend_offer');
            Route::post('/', [OfferController::class, 'saveDataIndex'])->name('backend_offer_save_data_index');
            Route::get('create', [OfferController::class, 'edit'])->name('backend_offer_create');
            Route::get('edit/{offer}', [OfferController::class, 'edit'])->name('backend_offer_edit');
            Route::post('save/{offer?}', [OfferController::class, 'save'])->name('backend_offer_save');
            Route::get('delete/{id}', [OfferController::class, 'delete'])->name('backend_offer_delete');
            Route::post('bulk_delete', [OfferController::class, 'bulkDelete'])->name('backend_offer_bulk_delete');
            Route::get('clone/{offer}', [OfferController::class, 'clone'])->name('backend_offer_clone');
            Route::get('restore/{id}', [OfferController::class, 'restore'])->name('backend_offer_restore');
            Route::get('force-delete/{id}', [OfferController::class, 'forceDelete'])->name('backend_offer_force_delete');
        });

        Route::prefix('feedback')->group(function () {
            Route::get('/', [FeedbackController::class, 'index'])->name('backend_feedback');
            Route::post('/', [FeedbackController::class, 'saveDataIndex'])->name('backend_feedback_save_data_index');
            Route::get('/create', [FeedbackController::class, 'edit'])->name('backend_feedback_create');
            Route::get('/edit/{feedback}', [FeedbackController::class, 'edit'])->name('backend_feedback_edit');
            Route::post('/save/{feedback?}', [FeedbackController::class, 'save'])->name('backend_feedback_save');
            Route::get('/delete/{id}', [FeedbackController::class, 'delete'])->name('backend_feedback_delete');
            Route::post('/bulk_delete', [FeedbackController::class, 'bulkDelete'])->name('backend_feedback_bulk_delete');
        });

        Route::get('file-manager', [FileManagerController::class, 'index'])->name('backend_file_manager');

        Route::prefix('lading-page')->group(function () {
            Route::get('/home', [LandingPageController::class, 'home'])->name('backend_landing_page_home');
            Route::get('/job', [LandingPageController::class, 'job'])->name('backend_landing_page_job');
            Route::post('/save/{key}', [LandingPageController::class, 'save'])->name('backend_landing_page_save');
        });

        Route::prefix('setting')->group(function () {
            Route::get('/general', [SettingController::class, 'general'])->name('backend_setting_general');
            Route::get('/author', [SettingController::class, 'author'])->name('backend_setting_author');
            Route::get('/payment', [SettingController::class, 'payment'])->name('backend_setting_payment');
            Route::get('/social', [SettingController::class, 'social'])->name('backend_setting_social');
            Route::get('/seo', [SettingController::class, 'seo'])->name('backend_setting_seo');
            Route::get('/ads', [SettingController::class, 'ads'])->name('backend_setting_ads');
            Route::get('/content', [SettingController::class, 'content'])->name('backend_setting_content');
            Route::post('/save', [SettingController::class, 'save'])->name('backend_setting_save');
        });

        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('backend_user');
            Route::post('save/{user?}', [UserController::class, 'save'])->name('backend_user_save');
            Route::get('create', [UserController::class, 'edit'])->name('backend_user_create');
            Route::get('edit/{user}', [UserController::class, 'edit'])->name('backend_user_edit');
            Route::get('delete/{user}', [UserController::class, 'delete'])->name('backend_user_delete');
        });

        Route::prefix('group')->group(function () {
            Route::get('/', [GroupController::class, 'index'])->name('backend_group');
            Route::post('save/{group?}', [GroupController::class, 'save'])->name('backend_group_save');
            Route::get('create', [GroupController::class, 'edit'])->name('backend_group_create');
            Route::get('edit/{group}', [GroupController::class, 'edit'])->name('backend_group_edit');
            Route::get('delete/{group}', [GroupController::class, 'delete'])->name('backend_group_delete');
        });

        Route::prefix('widget')->group(function () {
            Route::get('/', [WidgetController::class, 'index'])->name('backend_widget');
            Route::post('/', [WidgetController::class, 'saveDataIndex'])->name('backend_widget_save_data_index');
            Route::get('create', [WidgetController::class, 'edit'])->name('backend_widget_create');
            Route::get('edit/{widget}', [WidgetController::class, 'edit'])->name('backend_widget_edit');
            Route::post('save/{widget?}', [WidgetController::class, 'save'])->name('backend_widget_save');
            Route::get('clone/{widget}', [WidgetController::class, 'clone'])->name('backend_widget_clone');
            Route::get('delete/{id}', [WidgetController::class, 'delete'])->name('backend_widget_delete');
            Route::get('clone/{widget}', [WidgetController::class, 'clone'])->name('backend_widget_clone');
            Route::post('bulk_delete', [WidgetController::class, 'bulkDelete'])->name('backend_widget_bulk_delete');
        });
    });
});
