<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private MultiKeyManager $keyManager;
    private ModelManager $modelManager;

    public function __construct()
    {
        $this->keyManager = new MultiKeyManager('gemini_api_keys');
        $this->modelManager = new ModelManager('gemini_models');
    }

    public function generateContent(string $prompt): array
    {
        $this->guard();

        $lastError = null;

        // Try each model in priority order
        while ($this->modelManager->hasModels()) {
            $model = $this->modelManager->getModel();

            // Try each key for this model
            $lastError = $this->keyManager->call(function (string $apiKey) use ($prompt, $model) {
                return $this->callGemini($apiKey, $prompt, $model, fn($text) => $text);
            });

            if ($lastError === null) {
                return ['text' => $this->lastText, 'model' => $model];
            }

            // Model unavailable (deprecated/404) → skip all keys, move to next model
            if ($lastError instanceof ModelUnavailableException) {
                Log::warning('Gemini model unavailable, skipping: ' . $lastError->model);
                if (!$this->modelManager->rotate()) {
                    break;
                }
                $this->keyManager->resetIndex();
                continue;
            }

            // Other error on all keys → rotate to next model
            if (!$this->modelManager->rotate()) {
                break;
            }
            $this->keyManager->resetIndex();
        }

        throw new \Exception('All Gemini models and keys failed: ' . ($lastError?->getMessage() ?? 'Unknown error'));
    }

    public function generateJson(string $prompt): array
    {
        $this->guard();

        $lastError = null;

        while ($this->modelManager->hasModels()) {
            $model = $this->modelManager->getModel();

            $lastError = $this->keyManager->call(function (string $apiKey) use ($prompt, $model) {
                return $this->callGemini($apiKey, $prompt, $model, fn($text) => $this->parseJson($text));
            });

            if ($lastError === null) {
                return $this->lastJson;
            }

            if ($lastError instanceof ModelUnavailableException) {
                Log::warning('Gemini model unavailable, skipping: ' . $lastError->model);
                if (!$this->modelManager->rotate()) {
                    break;
                }
                $this->keyManager->resetIndex();
                continue;
            }

            if (!$this->modelManager->rotate()) {
                break;
            }
            $this->keyManager->resetIndex();
        }

        throw new \Exception('All Gemini models and keys failed: ' . ($lastError?->getMessage() ?? 'Unknown error'));
    }

    private string $lastText = '';
    private array $lastJson = [];

    private function callGemini(string $apiKey, string $prompt, string $model, callable $onSuccess): mixed
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
        $isJson = ($onSuccess === fn($t) => $this->parseJson($t));

        $response = Http::timeout(60)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => $isJson ? 0.3 : 0.7,
                    'maxOutputTokens' => 8192,
                    'topP' => $isJson ? 0.9 : 0.95,
                    'topK' => 40,
                ],
            ]);

        if ($response->failed()) {
            $body = $response->json();
            $msg = $body['error']['message'] ?? $response->body();

            // Model deprecated / no longer available → skip this model entirely
            $status = $response->status();
            if ($status === 404 && str_contains($msg, 'no longer available')) {
                throw new ModelUnavailableException("Model {$model} is no longer available.", $model);
            }

            throw new \Exception("Gemini error (HTTP {$status}) on {$model}: {$msg}");
        }

        $body = $response->json();
        $text = data_get($body, 'candidates.0.content.parts.0.text', '');

        if (empty($text)) {
            throw new \Exception("Gemini returned empty response on {$model}.");
        }

        // Store for return
        $this->lastText = $text;

        return $onSuccess($text);
    }

    private function parseJson(string $text): array
    {
        $text = trim($text);

        if (str_starts_with($text, '```json')) {
            $text = substr($text, 7);
        } elseif (str_starts_with($text, '```')) {
            $text = substr($text, 3);
        }

        if (str_ends_with($text, '```')) {
            $text = substr($text, 0, -3);
        }

        $text = trim($text);
        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Gemini did not return valid JSON: ' . json_last_error_msg() . ' | Raw: ' . substr($text, 0, 200));
        }

        $this->lastJson = $decoded;
        return $decoded;
    }

    private function guard(): void
    {
        if (!$this->keyManager->hasKeys()) {
            throw new \Exception('No Gemini API keys configured.');
        }
        if (!$this->modelManager->hasModels()) {
            throw new \Exception('No Gemini models configured.');
        }
    }

    public function getModelManager(): ModelManager
    {
        return $this->modelManager;
    }
}
