<?php

return [
    'cleanup_enabled' => filter_var(env('STORE_VIEW_LOG_CLEANUP_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'cleanup_chunk' => (int) env('STORE_VIEW_LOG_CLEANUP_CHUNK', 1000),
    'skip_ips' => array_values(array_unique(array_filter(array_map(
        'trim',
        preg_split('/[,;\s]+/', (string) env('STORE_VIEW_LOG_SKIP_IPS', '')) ?: []
    )))),
];
