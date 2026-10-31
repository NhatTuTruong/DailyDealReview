<?php

return [
    // Người thường / NAT dễ vượt 10 hit. Cần điểm cao hơn để giảm false positive.
    'score_threshold' => 50,
    // Khách Google Ads (gclid) chỉ gắn cờ khi có tín hiệu cứng.
    'ads_score_threshold' => 55,

    'burst_hits' => 15,
    'burst_minutes' => 5,
    'burst_unique_stores' => 5,

    'affiliate_hits' => 10,
    'affiliate_minutes' => 5,
    'affiliate_unique_urls' => 8,
    'rapid_click_hits' => 5,
    'rapid_click_seconds' => 2,
    'rapid_view_hits' => 5,
    'rapid_view_seconds' => 2,
    'f5_hits' => 5,
    'f5_seconds' => 60,

    'no_interaction_views' => 15,
    'no_interaction_minutes' => 10,
    'no_interaction_unique_stores' => 5,

    'ua_rotation_count' => 4,

    'ads_repeat_hits' => 3,
    'ads_repeat_minutes' => 10,

    'short_bounce_seconds' => 5,
    'short_bounce_hits' => 3,
    'short_bounce_minutes' => 10,

    'repeat_same_url_hits' => 4,
    'repeat_same_url_minutes' => 10,

    'periodic_min_views' => 6,
    'periodic_minutes' => 10,

    'many_brands_hits' => 4,
    'many_brands_minutes' => 5,

    'skip_search_engines' => true,
    'skip_google_ads_bots' => true,
];
