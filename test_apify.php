<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$apiKeyRaw = \App\Models\Setting::getSettingByKey('apify_api_keys', '');
$tokens = array_filter(array_map('trim', explode("\n", $apiKeyRaw)));
$apiKey = $tokens[0] ?? '';

$actorId = 'bvAQMqCbp6wE53JzK';
$query = 'golden retriever puppy';
$actorUrl = "https://api.apify.com/v2/acts/{$actorId}/run-sync-get-dataset-items";

echo "=== Test actor: {$actorId} ===\n";
echo "Query: {$query}\n\n";

// Thử không có token trước
$payload = [
    'queries' => [$query],
    'maxResultsPerQuery' => 1,
    'gl' => 'us',
    'hl' => 'en',
];
echo "Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

$r = Http::timeout(180)
    ->withHeaders(['Content-Type' => 'application/json'])
    ->post($actorUrl, $payload);
echo "Status: " . $r->status() . "\n";
echo "Body: " . substr($r->body(), 0, 3000) . "\n\n";

if ($r->status() === 200) {
    $data = $r->json();
    echo "Count: " . count($data) . "\n";
    if (!empty($data)) {
        echo "First item keys: " . implode(', ', array_keys($data[0])) . "\n";
        print_r($data[0]);
    }
} else {
    // Thử với token
    echo "=== With token ===\n";
    $payload2 = $payload;
    $payload2['token'] = $apiKey;
    $r2 = Http::timeout(180)
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post($actorUrl, $payload2);
    echo "Status: " . $r2->status() . "\n";
    echo "Body: " . substr($r2->body(), 0, 3000) . "\n";

    if ($r2->status() === 200) {
        $data = $r2->json();
        echo "Count: " . count($data) . "\n";
        if (!empty($data)) {
            echo "First item keys: " . implode(', ', array_keys($data[0])) . "\n";
            print_r($data[0]);
        }
    }
}

// Check actor metadata
echo "\n=== Actor Metadata ===\n";
$r3 = Http::timeout(15)->get("https://api.apify.com/v2/acts/{$actorId}");
echo "Status: " . $r3->status() . "\n";
if ($r3->status() === 200) {
    $meta = $r3->json();
    echo "Name: " . ($meta['data']['name'] ?? 'N/A') . "\n";
    echo "Username: " . ($meta['data']['username'] ?? 'N/A') . "\n";
    echo "Description: " . substr($meta['data']['description'] ?? '', 0, 200) . "\n";
}
