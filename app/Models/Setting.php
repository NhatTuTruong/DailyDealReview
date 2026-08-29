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
        }

        $language = App::getLocale();
        $settings = self::where('language', $language)->get();
        $results = [];
        foreach ($settings as $setting) {
            $results[$setting->skey] = $setting->svalue;
        }

        $cached['all_setting'] = array_merge(self::defaultSettings(), $results);
        $cached['all_setting']['site_name'] = self::siteName();

        return $cached['all_setting'];
    }

    public static function siteName(): string
    {
        return (string) config('app.site_name', 'DealHunter365');
    }

    public static function getSocialLinks(array $settings = []): array
    {
        if (empty($settings)) {
            $settings = self::getAllSetting();
        }

        $networks = [
            'instagram' => ['icon' => 'fab fa-instagram', 'label' => 'Instagram'],
            'youtube' => ['icon' => 'fab fa-youtube', 'label' => 'YouTube'],
            'facebook' => ['icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
        ];

        $links = [];
        foreach ($networks as $key => $network) {
            $url = trim((string) ($settings[$key] ?? ''));
            if ($url !== '' && ! in_array($url, ['#', '#!'], true)) {
                $links[] = array_merge($network, ['url' => $url]);
            }
        }

        return $links;
    }

    public static function defaultSettings(): array
    {
        $siteName = self::siteName();

        return [
            'site_name' => $siteName,
            'slogan' => '',
            'logo' => '',
            'favicon' => '',
            'noindex' => 0,
            'allow_search_store' => 1,
            'phone' => '',
            'fax' => '',
            'address' => '',
            'email' => '',
            'copyright' => '',
            'google_map' => '',
            'about_us' => '',
            'footer_info' => '',
            'meta_title' => $siteName . ' - Coupons & Deals',
            'meta_keywords' => '',
            'meta_description' => '',
            'meta_tag' => '',
            'tracking_code_head' => '',
            'tracking_code_body' => '',
            'tracking_code_bottom' => '',
            'og_image' => '',
            'facebook' => '#',
            'youtube' => '#',
            'twitter' => '#',
            'instagram' => '#',
            'pinterest' => '#',
            'facebook_app_id' => '',
            'author_name' => 'Admin',
            'author_avatar' => '',
            'author_info' => '',
            'author_website' => '',
            'author_facebook' => '',
            'author_instagram' => '',
            'author_twitter' => '',
            'author_youtube' => '',
            'coupon_description' => "Great savings at {{store_name}} - shop now and save big!\nExclusive deals and promo codes for {{store_name}}.\nSave money with verified {{store_name}} coupon codes.\nDon't miss out on the latest {{store_name}} discounts.",
            'how_to_apply' => self::defaultStoreHowToApplyTemplate(),
            'faqs' => self::defaultStoreFaqsTemplate(),
            'ads_keyword_coupon' => '',
            'ads_title_coupon' => '',
            'ads_description_coupon' => '',
            'ads_keyword_discount' => '',
            'ads_title_discount' => '',
            'ads_description_discount' => '',
            'gemini_api_keys' => '',
            'gemini_models' => "gemini-2.0-flash\n",
            'apify_api_keys' => '',
            'ai_auto_post_enabled' => 0,
            'ai_auto_post_interval' => 30,
            'body_class' => '',
            'business_hour' => '',
            'page_rule' => 0,
        ];
    }

    public static function getSettingByKey($key, $default = '')
    {
        if ($key === 'site_name') {
            return self::siteName();
        }

        $language = App::getLocale();
        $setting = Setting::where('language', $language)
            ->where('skey', $key)
            ->first();
        return data_get($setting, 'svalue', $default);
    }

    public static function getRandomShortDescription($store_name = '')
    {
        $settings = Setting::getAllSetting();
        $coupon_description = $settings['coupon_description'] ?? '';
        $site_name = $store_name ?: self::siteName();
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $coupon_description))));

        if (empty($lines)) {
            return "Great savings at {$site_name} - shop now and save big!";
        }

        $random_line = $lines[array_rand($lines)];

        return str_replace('{{store_name}}', $site_name, $random_line);
    }

    public static function getStoreFAQ($store_name)
    {
        $settings = Setting::getAllSetting();
        $faqs = trim($settings['faqs'] ?? '');
        if ($faqs === '') {
            $faqs = self::defaultStoreFaqsTemplate();
        }
        $site_name = self::siteName();

        return self::replaceStorePlaceholders($faqs, $store_name, $site_name);
    }

    public static function getStoreHowToApply($store_name)
    {
        $settings = Setting::getAllSetting();
        $how_to_apply = trim($settings['how_to_apply'] ?? '');
        if ($how_to_apply === '') {
            $how_to_apply = self::defaultStoreHowToApplyTemplate();
        }
        $site_name = self::siteName();

        return self::replaceStorePlaceholders($how_to_apply, $store_name, $site_name);
    }

    public static function defaultStoreHowToApplyTemplate(): string
    {
        return <<<'HTML'
<p><strong>How to apply {{store_name}} coupon codes?</strong></p>
<p><strong>Step 1:</strong> Find your {{store_name}} coupons, discount codes on this page and click "GET CODE" button to view the code, then click "Copy" and the coupons, discount codes will be copied to your phone's or computer's clipboard.</p>
<p><strong>Step 2:</strong> Go to {{store_name}} then select all items you want to buy and add to shopping cart. When finished shopping, go to the {{store_name}} checkout page.</p>
<p><strong>Step 3:</strong> During checkout, find the text "Promo Code" or "Discount Code" and paste your {{store_name}} coupons, discount codes in step 1 to this box. Click "Apply" and your savings for {{store_name}} will be applied.</p>
HTML;
    }

    public static function defaultStoreFaqsTemplate(): string
    {
        return <<<'HTML'
<p><strong>Q: Why should I visit {{site_name}} for {{store_name}} coupons?</strong><br>A: {{site_name}} collects the top discounts from {{store_name}}, even at the last minute while updating continually to ensure consumer savings. Coupons, promo codes, gift cards and many more can also be found on the website.</p>
<p><strong>Q: Where to find {{store_name}} promo codes?</strong><br>A: Right on the website of {{store_name}} or join {{site_name}} for more options of {{store_name}} promo codes.</p>
<p><strong>Q: Will all {{store_name}} discounts automatically be applied at checkout?</strong><br>A: No. It depends on each {{store_name}} deal. Some require you to apply a code at discount field while some are applied automatically.</p>
<p><strong>Q: Can you give me a guide for using {{store_name}} coupon codes?</strong><br>A: Follow the guide below to score {{store_name}} coupon: - Copy the coupon code that fits your order. - Navigate to {{store_name}} and add your favorite items into the cart. - Apply code at checkout and enjoy saving.</p>
<p><strong>Q: Are there any {{store_name}} Gift Cards available?</strong><br>A: If a {{store_name}} Gift Card is available, it will be aggregated above. Let's check!</p>
<p><strong>Q: How to use {{store_name}} coupon code?</strong><br>A: Visit {{store_name}} on {{site_name}} and pick the coupon making your order the biggest saving. Click GET CODE or GET DEAL for code displaying and enjoy the discount.</p>
<p><strong>Q: How often does {{store_name}} release a new coupon?</strong><br>A: For normal days, there is no specific frequency for {{store_name}} coupon releasing, but it tends to give out once per month. On the peak times of shopping, deals and discounts will be constantly launched and much bigger.</p>
<p><strong>Q: How to know if an item of {{store_name}} is eligible for a coupon?</strong><br>A: Each option of {{store_name}} coupons or deals comes with a detailed description for eligible items and the discount rate. Pick the right choice for your order.</p>
<p><strong>Q: How long are {{store_name}} deals valid?</strong><br>A: {{store_name}} will announce how long their promotional program will last. {{store_name}} deals will also be valid within that period of time.</p>
<p><strong>Q: How are errors about {{store_name}} coupons reported?</strong><br>A: Let us know at 'Contact Us'. Describe your problems and the errors about {{store_name}} coupons in detail, we will solve it as soon as possible.</p>
<p><strong>Q: When to use {{store_name}} coupons to save at best?</strong><br>A: Go to {{store_name}} and find the time that coupon is applicable. Huge discounts and deals tend to be available in a short period of time, requiring buyers to hurry. Be ready at that time to save at best.</p>
<p><strong>Q: How to get {{store_name}} free shipping offer?</strong><br>A: Check {{store_name}} coupons at {{site_name}} to find a deal of free shipping. Learn the condition for using the offer to make sure your order can meet it.</p>
<p><strong>Q: Who to contact if I am having trouble using a {{store_name}} coupon?</strong><br>A: That {{store_name}} coupon may expire at that moment. Shopping at {{store_name}} and then contact its owner to let them know your issue.</p>
<p><strong>Q: What is currently the best coupon of {{store_name}}?</strong><br>A: As of the latest update, the best coupon of {{store_name}} can give customers a discount corresponding to half of their purchase.</p>
<p><strong>Q: How do I find {{store_name}} coupons?</strong><br>A: Visiting the website of {{store_name}} regularly or checking {{store_name}} coupons on {{site_name}} is the best and easiest way to score a discount.</p>
<p><strong>Q: How to know if the {{store_name}} coupon discount was deducted from my purchase?</strong><br>A: When you apply the coupon or promo code into the discount field at {{store_name}}, the discount will be promptly deducted from your purchase. You will see both the amount of discount and the price after being discounted before clicking to finish the order.</p>
<p><strong>Q: Does {{store_name}} have a loyalty program?</strong><br>A: The loyalty program of {{store_name}} will be applicable for regular customers. Thereby, they can redeem {{store_name}} awards for promotions, coupons and special sales.</p>
<p><strong>Q: Does a sitewide coupon of {{store_name}} exist?</strong><br>A: If {{store_name}} makes available a sitewide coupon, it will be gathered above. Let's have a look!</p>
<p><strong>Q: How many items can I include with a {{store_name}} discount code?</strong><br>A: Usually, {{store_name}} doesn't limit the number of items customers have to buy to enjoy discounts. However, they will lay down conditions on the value of your order to be eligible for applying discount codes of {{store_name}}.</p>
HTML;
    }

    private static function replaceStorePlaceholders(string $content, string $storeName, string $siteName): string
    {
        return str_replace(
            ['{{store_name}}', '{{site_name}}'],
            [$storeName, $siteName],
            $content
        );
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

        $meta_coupon = array_values(array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $meta_coupon_config))));
        $meta_discount = array_values(array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $meta_discount_config))));

        if (empty($meta_coupon) && empty($meta_discount)) {
            return '';
        }

        $replaceFn = function ($template) use ($store_name, $max_offer) {
            return strtr($template, [
                '{{store_name}}' => $store_name,
                '{{max_offer}}' => $max_offer,
                '"' => '',
            ]);
        };

        $final = [];
        $attempts = 0;

        while (count($final) < 4 && $attempts < 20) {
            $attempts++;

            $selected = [];
            if (!empty($meta_coupon)) {
                $pickCount = min(2, count($meta_coupon));
                $couponIndexes = (array) array_rand($meta_coupon, $pickCount);
                foreach ((array) $couponIndexes as $i) {
                    $selected[] = $replaceFn($meta_coupon[$i]);
                }
            }
            if (!empty($meta_discount)) {
                $pickCount = min(2, count($meta_discount));
                $discountIndexes = (array) array_rand($meta_discount, $pickCount);
                foreach ((array) $discountIndexes as $i) {
                    $selected[] = $replaceFn($meta_discount[$i]);
                }
            }

            if (empty($selected)) {
                break;
            }

            shuffle($selected);
            $final = array_unique(array_merge($final, $selected));

            if (count($final) > 4) {
                $final = array_slice($final, 0, 4);
            }
        }
        $separator = $type == 'meta_keywords' ? ', ' : ' ';
        return implode($separator, array_values($final));
    }
}
