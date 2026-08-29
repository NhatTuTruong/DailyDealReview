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

    protected $description = 'Tự động tạo bài viết blog cho store (theo thứ tự cũ -> mới, chưa có bài viết ưu tiên trước)';

    public function handle(PostGeneratorService $service): int
    {
        $settings = Setting::getAllSetting();

        if (($settings['ai_auto_post_enabled'] ?? 0) != 1) {
            $this->info('Auto post is disabled. Skip.');
            return self::SUCCESS;
        }

        $interval = (int) ($settings['ai_auto_post_interval'] ?? 30);
        $cacheKey = 'ai_auto_post:last_run';

        if (!$this->option('force') && $interval > 0) {
            $lastRun = Cache::get($cacheKey);
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
            $this->info('No store available for auto post. All stores have recent posts or no stores exist.');
            return self::SUCCESS;
        }

        $this->info("Generating post for store: {$store->name} (ID: {$store->id})...");

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

            Cache::put($cacheKey, now()->toDateTimeString(), now()->addDays(30));

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
        // Ưu tiên store chưa có bài viết nào
        $storesWithoutPost = Store::whereDoesntHave('posts', function ($q) {
            $q->where('language', app()->getLocale());
        })
            ->orderBy('id')
            ->get();

        if ($storesWithoutPost->isNotEmpty()) {
            return $storesWithoutPost->first();
        }

        // Nếu tất cả đã có bài, lấy store có bài viết lâu nhất
        return Store::whereHas('posts', function ($q) {
                $q->where('language', app()->getLocale());
            })
            ->with(['posts' => function ($q) {
                $q->select('store_id', 'created_at')
                  ->where('language', app()->getLocale())
                  ->orderBy('created_at', 'asc');
            }])
            ->get()
            ->sortBy(function ($store) {
                return $store->posts->first()->created_at ?? now();
            })
            ->first();
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
