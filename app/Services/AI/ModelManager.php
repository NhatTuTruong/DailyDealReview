<?php

namespace App\Services\AI;

use App\Models\Setting;

class ModelManager
{
    /** @var string[] */
    private array $models = [];
    private int $currentIndex = 0;

    public const AVAILABLE = [
        'gemini-3.6-flash' => 'Gemini 3.6 Flash (Mới, nhanh nhất)',
        'gemini-3.5-flash' => 'Gemini 3.5 Flash',
        'gemini-3.1-flash-lite' => 'Gemini 3.1 Flash Lite (Tiết kiệm nhất)',
        'gemini-2.5-flash' => 'Gemini 2.5 Flash',
        'gemini-2.5-flash-lite' => 'Gemini 2.5 Flash Lite',
        'gemini-2.0-flash' => 'Gemini 2.0 Flash (Đã ngừng - bỏ qua)',
        'gemini-1.5-flash' => 'Gemini 1.5 Flash',
        'gemini-2.5-pro' => 'Gemini 2.5 Pro (Mạnh, context dài)',
        'gemini-1.5-pro' => 'Gemini 1.5 Pro (Mạnh hơn)',
    ];

    // Models that return 404 / are fully deprecated and should be skipped
    private const DEPRECATED = [
        'gemini-2.0-flash',
    ];

    public function __construct(string $settingKey = 'gemini_models')
    {
        $this->loadModels($settingKey);
    }

    private function loadModels(string $settingKey): void
    {
        $raw = Setting::getSettingByKey($settingKey, '');
        if (trim($raw) === '') {
            $this->models = ['gemini-3.6-flash'];
            return;
        }
        $lines = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $raw)));
        // Skip models not in AVAILABLE list AND skip fully deprecated ones
        $this->models = array_values(array_filter($lines, function ($m) {
            return isset(self::AVAILABLE[$m]) && !in_array($m, self::DEPRECATED, true);
        }));
        if (empty($this->models)) {
            $this->models = ['gemini-3.6-flash'];
        }
    }

    public function getModel(): ?string
    {
        if (empty($this->models)) {
            return null;
        }
        return $this->models[$this->currentIndex % count($this->models)];
    }

    public function rotate(): bool
    {
        $this->currentIndex++;
        return $this->currentIndex < count($this->models);
    }

    public function hasModels(): bool
    {
        return !empty($this->models);
    }

    public function count(): int
    {
        return count($this->models);
    }

    public function all(): array
    {
        return $this->models;
    }

    public function getCurrentIndex(): int
    {
        return $this->currentIndex;
    }

    public function reset(): void
    {
        $this->currentIndex = 0;
    }

    public static function isDeprecated(string $model): bool
    {
        return in_array($model, self::DEPRECATED, true);
    }
}
