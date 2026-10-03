<?php

declare(strict_types=1);

namespace App\Services\AI;

/**
 * L343-C2 — Deterministic in-process Gemini client.
 *
 * No network. No credentials. Same return shape as GeminiClient::post().
 * Used when GEMINI_API_KEY is not configured, so the AI pipeline can
 * run end-to-end in dev/CI without live credentials.
 */
class NullGeminiClient extends GeminiClient
{
    public function post(array $payload): array
    {
        $stub = [
            'stub'       => true,
            'driver'     => 'null',
            'schemaHash' => substr(sha1(json_encode($payload['generationConfig']['response_schema'] ?? [])), 0, 12),
            'echo'       => $payload['contents'][0]['parts'][0]['text'] ?? null,
        ];

        $raw = json_encode($stub, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return [
            'json'  => $stub,
            'raw'   => $raw,
            'usage' => [
                'promptTokenCount'     => 0,
                'candidatesTokenCount' => 0,
                'totalTokenCount'      => 0,
            ],
            'error' => null,
        ];
    }
}
