<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Str;

final class NeedUnderstandingService
{
    public const PROMPT_VERSION = '1.0';

    public function __construct(private readonly GeminiClient $client) {}

    public function understand(array $input): array
    {
        $result = $this->client->generateStructured(
            $this->schema(),
            [[ 'role' => 'user', 'parts' => [[ 'text' => $this->prompt($input) ]] ]],
            'You help a requester express a marketplace Need. Never invent facts. Preserve the original text. Mark unknown values as unknown. Do not post anything.'
        );

        if ($result['error'] !== null) {
            throw new \RuntimeException('AI need understanding failed: ' . $result['error']);
        }

        $data = is_array($result['json']) ? $result['json'] : [];
        $data['original_text'] = $input['text'];
        $data['draft_id'] = (string) Str::uuid();
        $data['prompt_version'] = self::PROMPT_VERSION;
        $data['user_confirmation_required'] = true;

        return $data;
    }

    private function prompt(array $input): string
    {
        return json_encode([
            'original_text' => $input['text'],
            'locale' => $input['locale'] ?? app()->getLocale(),
            'known_category_id' => $input['category_id'] ?? null,
            'known_location' => $input['location_text'] ?? null,
            'attachments' => $input['attachment_ids'] ?? [],
            'instruction' => 'Return only structured interpretation. Separate required, preferred, optional, and unknown facts.',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'title' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'category_id' => ['type' => 'string'],
                'location_text' => ['type' => 'string'],
                'budget_min' => ['type' => 'number'],
                'budget_max' => ['type' => 'number'],
                'currency' => ['type' => 'string'],
                'quantity' => ['type' => 'number'],
                'required' => ['type' => 'array', 'items' => ['type' => 'string']],
                'preferred' => ['type' => 'array', 'items' => ['type' => 'string']],
                'optional' => ['type' => 'array', 'items' => ['type' => 'string']],
                'unknown' => ['type' => 'array', 'items' => ['type' => 'string']],
                'clarifying_questions' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
            'required' => ['title', 'description', 'required', 'preferred', 'optional', 'unknown', 'clarifying_questions'],
        ];
    }
}
