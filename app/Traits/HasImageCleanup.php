<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait HasImageCleanup
{
    /**
     * Mỗi model sử dụng trait này phải khai báo danh sách field cần quét ảnh.
     * - key: tên field
     * - value: 'path' (1 ảnh dạng string) | 'html' (nội dung có <img src="...">)
     *
     * Ví dụ ở model:
     * protected array $imageCleanupConfig = [
     *     'image'   => 'path',
     *     'content' => 'html',
     * ];
     */
    //protected array $imageCleanupConfig = [];

    /**
     * Bật/tắt check tham chiếu chéo (nếu ảnh còn dùng ở model khác thì không xoá).
     * Gợi ý: bật để an toàn ở môi trường production.
     */
    protected bool $imageCleanupCheckReferences = true;

    /**
     * Giới hạn xoá ảnh chỉ khi URL match pattern này (tránh xoá nhầm ảnh bên ngoài)
     */
    protected string $imageCleanupAllowedPrefix = '/uploads/';

    /**
     * Disk ưu tiên dùng để xoá (nếu bạn đang dùng Storage disk).
     * Để null nếu file đang nằm trực tiếp trong public/ (ví dụ public/uploads/...)
     */
    protected ?string $imageCleanupDisk = null; // ví dụ: 'public' hoặc 's3'

    /**
     * Khai báo tập bảng/field khác để check tham chiếu chéo.
     * Mặc định: các bảng được nêu trong bài toán của bạn.
     */
    protected array $imageCleanupReferenceMap = [
        'posts' => ['image', 'content'],
        'pages' => ['image', 'content'],
        'categories' => ['image'],
        'widgets' => ['image'],
        'stores' => ['image'],
    ];

    /**
     * Boot trait: tự động hook vào deleting.
     */
    public static function bootHasImageCleanup()
    {
        static::deleting(function ($model) {
            if (!property_exists($model, 'imageCleanupConfig') || empty($model->imageCleanupConfig)) {
                return;
            }

            $urls = $model->collectImageUrlsFromModel();

            if (empty($urls)) {
                return;
            }

            // Chỉ giữ các URL hợp lệ theo prefix
            $urls = array_values(array_unique(array_filter($urls, function ($u) use ($model) {
                return is_string($u) && str_starts_with($u, $model->imageCleanupAllowedPrefix);
            })));

            if (empty($urls)) {
                return;
            }

            // Nếu bật check tham chiếu: chỉ xoá ảnh không còn được sử dụng
            if ($model->imageCleanupCheckReferences) {
                $urls = array_values(array_filter($urls, function ($u) use ($model) {
                    return !$model->isImageReferencedElsewhere($u);
                }));
            }

            if (empty($urls)) {
                return;
            }

            // Thực sự xoá
            foreach ($urls as $url) {
                $model->deleteImageByUrl($url);
            }
        });
    }

    /**
     * Gom toàn bộ URL ảnh từ các field được khai báo.
     */
    protected function collectImageUrlsFromModel(): array
    {
        $found = [];

        foreach ($this->imageCleanupConfig as $field => $type) {
            $value = $this->{$field} ?? null;
            if (!$value || !is_string($value)) continue;

            if ($type === 'path') {
                // 1 ảnh dạng path (ví dụ /uploads/images/...)
                $found[] = trim($value);
            } elseif ($type === 'html') {
                // HTML có nhiều ảnh
                $found = array_merge($found, $this->extractImageUrlsFromHtml($value));
            }
        }

        // unique, bỏ rỗng
        $found = array_values(array_unique(array_filter(array_map('trim', $found))));
        return $found;
    }

    /**
     * Regex lấy <img src="..."> hoặc src='...'
     */
    protected function extractImageUrlsFromHtml(string $html): array
    {
        $urls = [];
        $pattern = '~<img[^>]+src\s*=\s*([\'"])(.*?)\1~i';
        if (preg_match_all($pattern, $html, $m)) {
            foreach ($m[2] as $src) {
                $src = trim($src);
                // Bỏ query string nếu có (để map đúng file path)
                $src = strtok($src, '?');
                $urls[] = $src;
            }
        }
        return $urls;
    }

    /**
     * Kiểm tra ảnh có còn được tham chiếu ở record khác không.
     * - Nếu field là 'html', dùng LIKE '%url%'
     * - Nếu field là 'path', so sánh bằng '='
     * Bỏ qua record hiện tại (nếu còn tồn tại id).
     */
    protected function isImageReferencedElsewhere(string $url): bool
    {
        foreach ($this->imageCleanupReferenceMap as $table => $fields) {
            foreach ($fields as $field) {
                $query = DB::table($table);

                if ($field === 'content') {
                    $query->where($field, 'like', '%' . $url . '%');
                } else {
                    $query->where($field, '=', $url);
                }

                // Bỏ record hiện tại (nếu cùng bảng & có id)
                if (isset($this->table) && $this->table === $table && isset($this->attributes['id'])) {
                    $query->where('id', '!=', $this->attributes['id']);
                }

                if ($query->exists()) {
                    return true; // còn nơi khác dùng → không xoá
                }
            }
        }
        return false;
    }

    /**
     * Xoá file từ URL.
     * - Nếu cấu hình dùng Storage disk: convert URL → path tương đối (bỏ slash đầu) và Storage::delete
     * - Nếu file nằm trong public/: unlink qua public_path
     */
    protected function deleteImageByUrl(string $url): void
    {
        $relativePath = ltrim(parse_url($url, PHP_URL_PATH) ?? $url, '/'); // bỏ slash đầu

        // Nếu bạn dùng Storage disk (ví dụ ảnh đã được "symlink" vào public nhưng file thật ở storage/app/public)
//        if ($this->imageCleanupDisk) {
//            try {
//                Storage::disk($this->imageCleanupDisk)->delete($relativePath);
//            } catch (\Throwable $e) {
//                // fallback thử xoá trực tiếp trong public/
//                $this->unlinkPublicIfExists('/' . $relativePath);
//            }
//            return;
//        }

        // Mặc định: ảnh nằm trực tiếp ở public/
        $this->unlinkPublicIfExists('/' . $relativePath);
    }

    protected function unlinkPublicIfExists(string $urlPath): void
    {
        $full = public_path($urlPath);
        if (is_file($full)) {
            @unlink($full);
        }
    }
}
