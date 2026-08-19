<?php

namespace App\Libs;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Mail;

class Util
{
    public static function makeHTMLOptions($_arr, $sk = 0, $flag1 = 0, $flag2 = 0, $istext = 0)
    {
        $html = "";
        if (is_array($_arr)) {
            foreach ($_arr as $k => $v) {
                $value = $k;
                $option = $v;
                if ($flag2 == 1) {
                    $value = $option;
                }
                if ($flag1 == 0) {
                    $selected = ($value == $sk) ? "selected" : "";
                } else {
                    $selected = ($option == $sk) ? "selected" : "";
                }
                $html .= "<option value='$value' $selected >" . $option . "</option>";
                if ($selected == 'selected' && $istext == 1) {
                    return str_replace("|__", "", $option);
                }
            }
            if ($istext == 1) {
                return "";
            }
            return $html;
        } else {
            return "";
        }
    }

    public static function url_category($category): string
    {
        $slug = data_get($category, 'slug');
        if (!$slug) {
            return '#!';//route('product_all');
        } else {
            return route('category', $slug);
        }
    }

    public static function url_page($page): string
    {
        if (!$page) {
            return '';
        } else {
            return route('page_content', $page->slug);
        }
    }

    public static function url_post($post): string
    {
        if (!$post) {
            return '';
        } else {
            return route('post_detail', ['slug' => $post->slug, 'id' => $post->id]);
        }
    }

    public static function url_store($store): string
    {
        if (!$store) {
            return '';
        } else {
            return route('store_detail', ['slug' => $store->slug]);
        }
    }

    public static function url_product($product): string
    {
        if (!$product) {
            return '';
        } else {
            return route('product_detail', $product->slug);
        }
    }


    public static function sendEmail($template, $data, $subject, $to, $cc = []): void
    {
        if ($template && is_array($data) && $subject && $to) {
            Mail::send($template, $data, function ($message) use ($to, $cc, $subject) {
                $message->to($to)->cc($cc)->subject($subject);
            });
        }
    }

    public static function maskInfo($str)
    {
        $length = strlen($str);
        if ($length < 3) {
            return $str;
        }

        return substr($str, 0, $length - 3) . '***';
    }


    public static function limitContentWords($content = '', $limit = 20): string
    {
        if (!$content) {
            return '';
        }

        $plainText = strip_tags($content);

        $words = preg_split('/\s+/', $plainText, -1, PREG_SPLIT_NO_EMPTY);

        if (count($words) > $limit) {
            $limitedWords = array_slice($words, 0, $limit);
            $limitedContent = implode(' ', $limitedWords);
            $limitedContent .= '...';
            return $limitedContent;
        } else {
            return $content;
        }
    }

    public static function getFileType($file_path): string
    {
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        $image_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $image_extensions)) {
            return 'image';
        } elseif ($extension === 'pdf') {
            return 'pdf';
        }

        return 'unknown';
    }

    public static function getCountryFromIp(string $ip): ?array
    {
        try {
            $response = Http::timeout(3)->get("https://api.ipinfo.io/lite/{$ip}", [
                'token' => env('IPINFO_TOKEN', '20de8dd6e10594'),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'country_code' => $data['country_code'] ?? null,
                    'country' => $data['country'] ?? null,
                ];
            }

        } catch (\Throwable $e) {
            // Log::warning('IPInfo request failed: ' . $e->getMessage());
        }

        return null; // fallback nếu timeout hoặc lỗi
    }

    public static function convertToDatetime($date): ?Carbon
    {
        return Carbon::createFromFormat('d/m/Y', $date)->startOfDay();
    }
}
