<?php

namespace App\Libs;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImageWebpConverter
{
    public static function isSupported(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatefromstring');
    }

    /**
     * Convert raw image bytes to WebP bytes.
     */
    public static function convertContent(string $content, int $quality = 85): ?string
    {
        if (!self::isSupported() || $content === '') {
            return null;
        }

        $image = @imagecreatefromstring($content);
        if ($image === false) {
            return null;
        }

        if (function_exists('imagepalettetotruecolor')) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, true);
        imagesavealpha($image, true);

        ob_start();
        $saved = imagewebp($image, null, $quality);
        $webp = ob_get_clean();
        imagedestroy($image);

        return ($saved && $webp !== false && $webp !== '') ? $webp : null;
    }

    /**
     * Save image bytes to a public upload directory as WebP.
     * Falls back to the original format when WebP conversion is unavailable.
     */
    public static function saveAsWebp(string $content, string $uploadRelativeDir, string $filenamePrefix): string
    {
        $uploadRelativeDir = trim($uploadRelativeDir, '/');
        $uploadPath = public_path($uploadRelativeDir);

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $webpContent = self::convertContent($content);
        if ($webpContent !== null) {
            $filename = $filenamePrefix . '-' . Str::random(16) . '.webp';
            File::put($uploadPath . '/' . $filename, $webpContent);

            return '/' . $uploadRelativeDir . '/' . $filename;
        }

        $extension = self::guessExtension($content);
        $filename = $filenamePrefix . '-' . Str::random(16) . '.' . $extension;
        File::put($uploadPath . '/' . $filename, $content);

        return '/' . $uploadRelativeDir . '/' . $filename;
    }

    /**
     * Copy a local public image into upload dir as WebP for post usage.
     */
    public static function copyLocalToWebp(string $publicPath, string $uploadRelativeDir, string $filenamePrefix): ?string
    {
        $publicPath = trim($publicPath);
        if ($publicPath === '' || preg_match('#^https?://#i', $publicPath)) {
            return null;
        }

        if (str_ends_with(strtolower($publicPath), '.webp')) {
            return str_starts_with($publicPath, '/') ? $publicPath : '/' . ltrim($publicPath, '/');
        }

        $fullPath = public_path(ltrim($publicPath, '/'));
        if (!File::exists($fullPath)) {
            return null;
        }

        return self::saveAsWebp(File::get($fullPath), $uploadRelativeDir, $filenamePrefix);
    }

    /**
     * Ensure a post image path points to a WebP file when possible.
     */
    public static function ensureWebpForPost(?string $publicPath, string $filenamePrefix = 'featured'): ?string
    {
        if ($publicPath === null || $publicPath === '') {
            return $publicPath;
        }

        $normalized = str_starts_with($publicPath, '/') ? $publicPath : '/' . ltrim($publicPath, '/');
        if (str_ends_with(strtolower($normalized), '.webp')) {
            return $normalized;
        }

        return self::copyLocalToWebp($normalized, 'uploads/images/blog', $filenamePrefix) ?? $normalized;
    }

    private static function guessExtension(string $content): string
    {
        $info = @getimagesizefromstring($content);
        if ($info && isset($info['mime'])) {
            return match ($info['mime']) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg',
            };
        }

        return 'jpg';
    }
}
