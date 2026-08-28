<?php

namespace App\Services\AI;

use App\Models\Setting;

class MultiKeyManager
{
    private array $keys = [];
    private int $currentIndex = 0;

    public function __construct(private readonly string $settingKey)
    {
        $this->loadKeys();
    }

    private function loadKeys(): void
    {
        $raw = Setting::getSettingByKey($this->settingKey, '');
        $lines = array_filter(array_map('trim', preg_split('/\r\n|\n|\r/', $raw)));
        $this->keys = array_values(array_filter($lines, fn($k) => !empty($k)));
        $this->currentIndex = 0;
    }

    public function getKey(): ?string
    {
        if (empty($this->keys)) {
            return null;
        }
        $key = $this->keys[$this->currentIndex % count($this->keys)];
        $this->currentIndex++;
        return $key;
    }

    public function rotate(): bool
    {
        $this->currentIndex++;
        return $this->currentIndex < count($this->keys);
    }

    public function hasKeys(): bool
    {
        return !empty($this->keys);
    }

    public function count(): int
    {
        return count($this->keys);
    }

    public function resetIndex(): void
    {
        $this->currentIndex = 0;
    }

    /**
     * Try each key with the callback.
     * Returns null on success (callback return value is ignored here — caller uses side effects).
     * Returns \Exception on failure.
     *
     * @param callable $callback fn(string $apiKey): mixed
     * @return \Exception|null
     */
    public function call(callable $callback): ?\Exception
    {
        $lastError = null;

        while ($this->hasKeys()) {
            $key = $this->getKey();
            if (!$key) {
                break;
            }

            try {
                $callback($key);
                return null; // success
            } catch (\Exception $e) {
                $lastError = $e;
                if (!$this->rotate()) {
                    break;
                }
            }
        }

        return $lastError ?? new \Exception('All API keys failed: Unknown error');
    }
}
