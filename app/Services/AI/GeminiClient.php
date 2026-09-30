<?php

namespace App\Services\AI;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin HTTP wrapper for the Google Gemini Developer API.
 *
 * Endpoint: POST {base_url}/models/{model}:generateContent?key={api_key}
 * Docs: https://ai.google.dev/api/generate-content
 *
 * No external SDK — Laravel HTTP client only.
 */
class GeminiClient
{
    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly ?string $baseUrl = null,
        private readonly ?string $model = null,
    ) {}

    /**
     * Structured JSON generation with schema enforcement.
     *
     * @return array{json: array|null, raw: string, usage: array, error: string|null}
     */
    public function generateStructured(
        array $responseSchema,
        array $contents,
        ?string $systemInstruction = null,
        array $safetySettings = [],
    ): array {
        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'response_mime_type' => 'application/json',
                'response_schema' => $responseSchema,
                'temperature' => 0.2,
                'topP' => 0.95,
                'maxOutputTokens' => 4096,
            ],
            'safetySettings' => $safetySettings ?: $this->defaultSafetySettings(),
        ];

        if ($systemInstruction !== null) {
            $payload['system_instruction'] = [
                'parts' => [['text' => $systemInstruction]],
            ];
        }

        return $this->post($payload);
    }

    public function post(array $payload): array
    {
        $url = $this->url() . '?key=' . $this->key();
        $maxRetries = (int) config('ai.gemini.max_retries', 3);
        $baseDelay = (int) config('ai.gemini.retry_delay_ms', 1000);

        $attempt = 0;
        $lastError = null;

        while ($attempt <= $maxRetries) {
            $attempt++;
            $delayMs = $baseDelay * (2 ** ($attempt - 1));

            $response = $this->http()->post($url, $payload);

            if ($response->successful()) {
                return $this->normalize($response->json() ?? []);
            }

            $status = $response->status();
            $lastError = "HTTP {$status}: " . substr($response->body(), 0, 500);

            $shouldRetry = in_array($status, [429, 500, 502, 503, 504], true);

            Log::warning('Gemini API call failed', [
                'status' => $status,
                'attempt' => $attempt,
                'max_retries' => $maxRetries,
                'retry' => $shouldRetry && $attempt <= $maxRetries,
            ]);

            if (!$shouldRetry || $attempt > $maxRetries) {
                break;
            }

            usleep($delayMs * 1000);
        }

        return ['json' => null, 'raw' => '', 'usage' => [], 'error' => $lastError];
    }

    private function normalize(array $body): array
    {
        $candidate = $body['candidates'][0] ?? null;

        if (!$candidate) {
            $blockReason = $body['promptFeedback']['blockReason'] ?? null;
            return [
                'json' => null,
                'raw' => '',
                'usage' => $body['usageMetadata'] ?? [],
                'error' => $blockReason ? "blocked: {$blockReason}" : 'no_candidates',
            ];
        }

        $parts = $candidate['content']['parts'] ?? [];
        $text = '';
        foreach ($parts as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }
        }

        $decoded = null;
        if ($text !== '') {
            // Strip markdown code fences that Gemini sometimes adds
            $cleanText = trim($text);
            if (preg_match('/^```(?:json)?\s*(.+?)\s*```$/s', $cleanText, $m)) {
                $cleanText = trim($m[1]);
            }
            try {
                $decoded = json_decode($cleanText, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                return [
                    'json' => null,
                    'raw' => $text,
                    'usage' => $body['usageMetadata'] ?? [],
                    'error' => 'json_decode_failed: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'json' => $decoded,
            'raw' => $text,
            'usage' => $body['usageMetadata'] ?? [],
            'error' => null,
        ];
    }

    private function http(): PendingRequest
    {
        return Http::timeout((int) config('ai.gemini.timeout', 60))
            ->acceptJson()
            ->asJson();
    }

    private function url(): string
    {
        $base = rtrim($this->baseUrl ?? config('ai.gemini.base_url'), '/');
        $model = $this->model ?? config('ai.gemini.model');
        return "{$base}/models/{$model}:generateContent";
    }

    private function key(): string
    {
        $key = $this->apiKey ?? config('ai.gemini.api_key');
        if (empty($key)) {
            throw new \RuntimeException('GEMINI_API_KEY is not configured.');
        }
        return $key;
    }

    private function defaultSafetySettings(): array
    {
        $categories = [
            'HARM_CATEGORY_HARASSMENT',
            'HARM_CATEGORY_HATE_SPEECH',
            'HARM_CATEGORY_SEXUALLY_EXPLICIT',
            'HARM_CATEGORY_DANGEROUS_CONTENT',
        ];

        return array_map(
            fn ($category) => [
                'category' => $category,
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE',
            ],
            $categories
        );
    }
}
