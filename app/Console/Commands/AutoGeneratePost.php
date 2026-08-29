<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Category;
use App\Services\AI\PostGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoGeneratePost extends Command
{
    protected $signature = 'post:auto-generate {--force : Bỏ qua kiểm tra thời gian chờ}';

    protected $description = 'Tự động tạo bài viết blog cho store (xoay vòng store cũ -> mới, hết danh sách quay lại đầu)';

    private const LAST_RUN_CACHE_KEY = 'ai_auto_post:last_run';
    private const LAST_STORE_CACHE_KEY = 'ai_auto_post:last_store_id';

    public function handle(PostGeneratorService $service): int
    {
        $settings = Setting::getAllSetting();

        if (($settings['ai_auto_post_enabled'] ?? 0) != 1) {
            $this->info('Auto post is disabled. Skip.');
            return self::SUCCESS;
        }

        $interval = (int) ($settings['ai_auto_post_interval'] ?? 30);

        if (!$this->option('force') && $interval > 0) {
            $lastRun = Cache::get(self::LAST_RUN_CACHE_KEY);
            if ($lastRun) {
                $minutesAgo = Carbon::parse($lastRun)->diffInMinutes(now());
                if ($minutesAgo < $interval) {
                    $this->info("Last run was {$minutesAgo} minutes ago. Waiting for {$interval} minutes interval. Skip.");
                    return self::SUCCESS;
                }
            }
        }

        $store = $this->pickStore();

        if (!$store) {
            $this->info('No store available for auto post (no active store with offers).');
            return self::SUCCESS;
        }

        $this->info("Generating post for store: {$store->name} (ID: {$store->id}) [round-robin]...");

        try {
            $data = $service->generateFromStore($store->id);

            $postCategoryId = Category::getPostCategoryIdFromStoreCategory($store->cat_id ?? 0);

            $post = new Post();
            $post->fill($data);
            $post->slug = $this->makeUniqueSlug($data['slug'] ?? 'post-' . time());
            $post->status = 1;
            $post->is_hot = 0;
            $post->priority = 9999;
            $post->view_num = 0;
            $post->language = app()->getLocale();

            if ($postCategoryId) {
                $post->cat_id = $postCategoryId;
            }

            $post->save();

            if ($postCategoryId) {
                $post->categories()->sync([$postCategoryId]);
            }

            Cache::put(self::LAST_RUN_CACHE_KEY, now()->toDateTimeString(), now()->addDays(30));

            $this->info("Post created successfully! ID: {$post->id}");
            Log::info('Auto post created', [
                'post_id' => $post->id,
                'store_id' => $store->id,
                'store_name' => $store->name,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to generate post: ' . $e->getMessage());
            Log::error('Auto post failed', [
                'store_id' => $store->id,
                'store_name' => $store->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }

    private function pickStore(): ?Store
    {
        // Xoay vòng mọi store đủ điều kiện — không loại store đã có bài viết.
        $stores = Store::query()
            ->active()
            ->language()
            ->whereHas('offers', function ($q) {
                $q->active()->language();
            })
            ->orderBy('id')
            ->get(['id', 'name', 'cat_id']);

        if ($stores->isEmpty()) {
            return null;
        }

        $lastStoreId = (int) Cache::get(self::LAST_STORE_CACHE_KEY, 0);

        $nextStore = $stores->first(fn (Store $store) => $store->id > $lastStoreId)
            ?? $stores->first();

        Cache::put(self::LAST_STORE_CACHE_KEY, $nextStore->id, now()->addDays(365));

        Log::info('Auto post store rotation', [
            'last_store_id' => $lastStoreId,
            'picked_store_id' => $nextStore->id,
            'picked_store_name' => $nextStore->name,
            'total_eligible_stores' => $stores->count(),
        ]);

        return $nextStore;
    }

    private function makeUniqueSlug(string $slug): string
    {
        $original = $slug;
        $i = 1;

        while (Post::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }
}
