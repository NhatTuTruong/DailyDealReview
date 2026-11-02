<?php

return [
    'cleanup_enabled' => filter_var(env('STORE_VIEW_LOG_CLEANUP_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'cleanup_chunk' => (int) env('STORE_VIEW_LOG_CLEANUP_CHUNK', 1000),
];
