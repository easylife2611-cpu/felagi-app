<?php

namespace App\Services\Consent;

use App\Models\ConsentLog;
use App\Models\User;
use Illuminate\Http\Request;

class ConsentService
{
    public function grant(User $user, string $type, string $source, ?Request $request = null): ConsentLog
    {
        return ConsentLog::create([
            'user_id'      => $user->id,
            'consent_type' => $type,
            'version'      => '1.0',
            'granted'      => true,
            'granted_at'   => now(),
            'ip_address'   => $request?->ip(),
            'user_agent'   => $request?->userAgent(),
            'source'       => $source,
        ]);
    }

    public function revoke(User $user, string $type, string $source, ?Request $request = null): ?ConsentLog
    {
        $active = ConsentLog::where('user_id', $user->id)
            ->where('consent_type', $type)
            ->active()
            ->first();

        if (! $active) {
            return null;
        }

        $active->update([
            'granted'    => false,
            'revoked_at' => now(),
            'source'     => $source,
        ]);

        return $active->fresh();
    }

    public function has(User $user, string $type): bool
    {
        return ConsentLog::where('user_id', $user->id)
            ->where('consent_type', $type)
            ->active()
            ->exists();
    }

    public function all(User $user): array
    {
        $types = [
            ConsentLog::TYPE_MARKETING,
            ConsentLog::TYPE_ADS,
            ConsentLog::TYPE_AI_COMPARE,
            ConsentLog::TYPE_TELEGRAM,
            ConsentLog::TYPE_CROSS_BORDER,
        ];

        $result = [];
        foreach ($types as $type) {
            $result[$type] = $this->has($user, $type);
        }
        return $result;
    }
}
