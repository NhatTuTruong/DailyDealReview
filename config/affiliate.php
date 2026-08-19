<?php

return [
    // Shared API key. Set AFFILIATE_API_KEY in .env (change in production).
    'api_key' => env('AFFILIATE_API_KEY', 'ducnh241'),

    // Where store/offer/post images uploaded via the API are written (under public/).
    'logo_dir' => 'uploads/affiliate',

    // status value treated as published/visible (STATUS_ARRAY: 1 = approved).
    'published_state' => 1,

    // Offers list is sorted by `priority DESC` on the store page, so a HIGHER priority
    // shows first. We invert the incoming menu_order (0 = top) as: priority = base - menu_order.
    'offer_priority_base' => 1000,
];
