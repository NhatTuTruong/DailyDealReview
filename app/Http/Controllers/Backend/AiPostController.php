<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Store;
use App\Services\AI\PostGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class AiPostController extends Controller
{
    public function __construct()
    {
        $this->selectedMainMenu = 'post';
        parent::__construct();

        if (!Gate::allows('post')) {
            abort(403, self::MESSAGE_UNAUTHORIZED);
        }
    }

    /**
     * AJAX: Lấy preview bài viết được sinh bởi AI (chưa lưu)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'store_id' => 'required|integer|exists:stores,id',
        ]);

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $store = Store::query()
            ->whereKey($request->input('store_id'))
            ->whereHas('offers', function ($q) {
                $q->active()->language();
            })
            ->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store không tồn tại, đã bị xóa, hoặc chưa có offer active.',
            ], 422);
        }

        try {
            $service = new PostGeneratorService();
            $data = $service->generateFromStore($store->id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI Post generation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * AJAX: Lưu bài viết từ data đã được AI sinh
     */
    public function saveGenerated(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'content' => 'required|string',
            'store_id' => 'required|integer|exists:stores,id',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'name',
                'slug',
                'description',
                'content',
                'image',
                'meta_title',
                'meta_keywords',
                'meta_description',
                'store_id',
                'cat_id',
            ]);

            $data['slug'] = $this->makeUniqueSlug($data['slug']);
            $data['status'] = 1;
            $data['is_hot'] = 0;
            $data['priority'] = 9999;
            $data['view_num'] = 0;
            $data['user_id'] = auth()->id();
            $data['language'] = app()->getLocale();

            $post = new Post();
            $post->fill($data);

            $postCategoryId = !empty($data['cat_id'])
                ? (int) $data['cat_id']
                : Category::getPostCategoryIdFromStoreCategory(
                    Store::query()->whereKey($data['store_id'])->value('cat_id')
                );

            if ($postCategoryId) {
                $post->cat_id = $postCategoryId;
            }

            $post->save();

            if ($postCategoryId) {
                $post->categories()->sync([$postCategoryId]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'post_id' => $post->id,
                'redirect' => route('backend_post_edit', $post->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AI Post save failed: ' . $e->getMessage(), [
                'store_id' => $request->input('store_id'),
                'slug' => $request->input('slug'),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function makeUniqueSlug(string $slug): string
    {
        $base = $slug ?: 'post-' . time();
        $newSlug = $base;
        $i = 1;

        while (Post::where('slug', $newSlug)->exists()) {
            $newSlug = $base . '-' . $i;
            $i++;
        }

        return $newSlug;
    }
}