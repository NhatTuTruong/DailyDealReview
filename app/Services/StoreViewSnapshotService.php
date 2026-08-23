<?php

namespace App\Services;

use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class StoreViewSnapshotService
{
    private string $storagePath;

    public function __construct()
    {
        $this->storagePath = storage_path('app/store-view-snapshots');
    }

    public function capture(?Carbon $date = null): int
    {
        $date = ($date ?? Carbon::today())->copy()->startOfDay();
        File::ensureDirectoryExists($this->storagePath);

        $stores = $this->fetchStoreViews();
        $payload = [
            'date' => $date->toDateString(),
            'captured_at' => now()->toIso8601String(),
            'stores' => $stores,
        ];

        File::put($this->pathFor($date), json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return count($stores);
    }

    public function getSnapshot(Carbon $date): ?array
    {
        $path = $this->pathFor($date->copy()->startOfDay());
        if (!File::exists($path)) {
            return null;
        }

        $data = json_decode(File::get($path), true);
        if (!is_array($data) || !isset($data['stores']) || !is_array($data['stores'])) {
            return null;
        }

        return $this->normalizeStores($data['stores']);
    }

    public function getCurrentViews(): array
    {
        return $this->fetchStoreViews();
    }

    public function compare(Carbon $date, bool $weekly = false): array
    {
        $date = $date->copy()->startOfDay();
        $compareDate = $weekly ? $date->copy()->subWeek() : $date->copy()->subDay();

        if ($date->isToday()) {
            $current = $this->getCurrentViews();
            $this->capture($date);
        } else {
            $current = $this->getSnapshot($date);
            if ($current === null) {
                return [
                    'ok' => false,
                    'message' => 'Không có dữ liệu snapshot cho ngày ' . $date->format('d-m-Y') . '.',
                    'date' => $date->format('d-m-Y'),
                    'compare_date' => $compareDate->format('d-m-Y'),
                    'compare_mode' => $weekly ? 'week' : 'day',
                    'stores' => [],
                ];
            }
        }

        $previous = $this->getSnapshot($compareDate);
        if ($previous === null) {
            return [
                'ok' => false,
                'message' => 'Không có dữ liệu snapshot cho ngày so sánh ' . $compareDate->format('d-m-Y') . '.',
                'date' => $date->format('d-m-Y'),
                'compare_date' => $compareDate->format('d-m-Y'),
                'compare_mode' => $weekly ? 'week' : 'day',
                'stores' => [],
            ];
        }

        $stores = [];
        $storeIds = array_unique(array_merge(array_keys($current), array_keys($previous)));

        foreach ($storeIds as $storeId) {
            $currentViews = (int) ($current[$storeId]['view_num'] ?? 0);
            $previousViews = (int) ($previous[$storeId]['view_num'] ?? 0);
            $viewChange = $currentViews - $previousViews;

            if ($viewChange === 0) {
                continue;
            }

            $store = $current[$storeId] ?? $previous[$storeId];
            $stores[] = [
                'id' => (int) $storeId,
                'name' => $store['name'] ?? '',
                'slug' => $store['slug'] ?? '',
                'view_num' => $currentViews,
                'previous_view_num' => $previousViews,
                'view_change' => $viewChange,
            ];
        }

        usort($stores, fn ($a, $b) => $b['view_change'] <=> $a['view_change']);

        return [
            'ok' => true,
            'date' => $date->format('d-m-Y'),
            'compare_date' => $compareDate->format('d-m-Y'),
            'compare_mode' => $weekly ? 'week' : 'day',
            'total' => count($stores),
            'stores' => $stores,
        ];
    }

    private function fetchStoreViews(): array
    {
        return Store::query()
            ->select(['id', 'name', 'slug', 'view_num'])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (Store $store) => [
                (string) $store->id => [
                    'id' => (int) $store->id,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'view_num' => (int) $store->view_num,
                ],
            ])
            ->all();
    }

    private function normalizeStores(array $stores): array
    {
        $normalized = [];

        foreach ($stores as $key => $store) {
            if (!is_array($store)) {
                continue;
            }

            $id = (string) ($store['id'] ?? $key);
            $normalized[$id] = [
                'id' => (int) ($store['id'] ?? $key),
                'name' => $store['name'] ?? '',
                'slug' => $store['slug'] ?? '',
                'view_num' => (int) ($store['view_num'] ?? 0),
            ];
        }

        return $normalized;
    }

    private function pathFor(Carbon $date): string
    {
        return $this->storagePath . DIRECTORY_SEPARATOR . $date->format('Y-m-d') . '.json';
    }
}
