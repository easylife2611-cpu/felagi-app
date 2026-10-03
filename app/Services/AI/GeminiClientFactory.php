<?php

declare(strict_types=1);

namespace App\Services\AI;

use InvalidArgumentException;

/**
 * L343-C2 — Selects a Gemini client based on driver config.
 *
 *   AI_DRIVER=auto   (default) → null if API key missing, else real
 *   AI_DRIVER=null             → always NullGeminiClient
 *   AI_DRIVER=gemini           → always GeminiClient (requires key)
 */
final class GeminiClientFactory
{
    public function make(?string $driver = null): GeminiClient
    {
        $driver ??= (string) env('AI_DRIVER', 'auto');

        if ($driver === 'auto') {
            $driver = config('ai.gemini.api_key') ? 'gemini' : 'null';
        }

        return match ($driver) {
            'null'   => new NullGeminiClient(),
            'gemini' => new GeminiClient(),
            default  => throw new InvalidArgumentException(
                "Unsupported AI driver [{$driver}]. Supported: auto, null, gemini."
            ),
        };
    }
}
