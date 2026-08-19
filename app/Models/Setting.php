<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Setting extends Model
{
    protected $table = 'settings';

    public static function check_exists_skey($skey = '')
    {
        $language = App::getLocale();
        if ($skey == '') {
            return false;
        }
        $setting = self::where('language', $language)->where('skey', $skey)->first();
        if (isset($setting)) {
            return true;//Đã tồn tại
        }
        return false;
    }

    public static function getAllSetting()
    {
        static $cached = [];
        if (isset($cached['all_setting'])) {
            return $cached['all_setting'];
        } else {
            $language = App::getLocale();
            $settings = self::where('language', $language)->get();
            $results = [];
            foreach ($settings as $setting) {
                $results[$setting->skey] = $setting->svalue;
            }
            $cached['all_setting'] = $results;
            return $results;
        }

    }

    public static function getSettingByKey($key, $default = '')
    {
        $language = App::getLocale();
        $setting = Setting::where('language', $language)
            ->where('skey', $key)
            ->first();
        return data_get($setting, 'svalue', $default);
    }

    public static function getRandomShortDescription($store_name = '')
    {
        $settings = Setting::getAllSetting();
        $coupon_description = $settings['coupon_description'];
        $site_name = $store_name ?: ($settings['site_name'] ?? 'DealHunter365');
        $lines = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $coupon_description)));

        // Chọn random 1 dòng
        $random_line = $lines[array_rand($lines)];

        // Thay {{store_name}} bằng giá trị thực tế
        return str_replace('{{store_name}}', $site_name, $random_line);
    }

    public static function getStoreFAQ($store_name)
    {
        $settings = Setting::getAllSetting();
        $faqs = $settings['faqs'];
        $site_name = $settings['site_name'] ?? 'DealHunter365';

        $faqs = str_replace('{{store_name}}', $store_name, $faqs);
        return str_replace('{{site_name}}', $site_name, $faqs);
    }

    public static function getStoreHowToApply($store_name)
    {
        $settings = Setting::getAllSetting();
        $how_to_apply = $settings['how_to_apply'];
        $site_name = $settings['site_name'] ?? 'DealHunter365';

        $how_to_apply = str_replace('{{store_name}}', $store_name, $how_to_apply);
        return str_replace('{{site_name}}', $site_name, $how_to_apply);
    }


    public static function generateMetaSEO(string $store_name, $max_offer, $type = 'title'): string
    {
        if (empty($store_name) || empty($max_offer)) {
            return '';
        }

        $settings = Setting::getAllSetting();
        if ($type == 'title') {
            $meta_coupon_config = $settings['ads_title_coupon'];
            $meta_discount_config = $settings['ads_title_discount'];
        } elseif ($type == 'meta_description') {
            $meta_coupon_config = $settings['ads_description_coupon'];
            $meta_discount_config = $settings['ads_description_discount'];
        } else {
            $meta_coupon_config = $settings['ads_keyword_coupon'];
            $meta_discount_config = $settings['ads_keyword_discount'];
        }

        $meta_coupon = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $meta_coupon_config)));
        $meta_discount = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $meta_discount_config)));

        $replaceFn = function ($template) use ($store_name, $max_offer) {
            return strtr($template, [
                '{{store_name}}' => $store_name,
                '{{max_offer}}' => $max_offer,
                '"' => '',
            ]);
        };

        $final = [];
        $attempts = 0;

        // Lặp đến khi có đủ 4 item unique hoặc thử quá 20 lần
        while (count($final) < 4 && $attempts < 20) {
            $attempts++;

            // Random 2 từ mỗi config
            $couponIndexes = (array)array_rand($meta_coupon, 2);
            $discountIndexes = (array)array_rand($meta_discount, 2);

            $selected = [];
            foreach ($couponIndexes as $i) {
                $selected[] = $replaceFn($meta_coupon[$i]);
            }
            foreach ($discountIndexes as $i) {
                $selected[] = $replaceFn($meta_discount[$i]);
            }

            shuffle($selected);

            // Merge vào $final và giữ unique
            $final = array_unique(array_merge($final, $selected));

            // Nếu nhiều hơn 4 thì cắt còn 4
            if (count($final) > 4) {
                $final = array_slice($final, 0, 4);
            }
        }
        $separator = $type == 'meta_keywords' ? ', ' : ' ';
        return implode($separator, array_values($final));
    }
}
