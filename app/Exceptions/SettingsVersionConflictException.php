<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a publish attempt uses a stale expected_version.
 * Maps to HTTP 409 with error code SETTINGS_VERSION_CONFLICT.
 *
 * Per DFM-FDS-1.4.md §8.3:
 *   "Concurrent drafts use version preconditions;
 *    stale publish returns 409 SETTINGS_VERSION_CONFLICT."
 */
class SettingsVersionConflictException extends Exception
{
    public function __construct(
        public readonly string $settingKey,
        public readonly int $expectedVersion,
        public readonly int $actualVersion,
    ) {
        parent::__construct(
            "Settings version conflict for '{$settingKey}': "
            . "expected version {$expectedVersion}, "
            . "but current version is {$actualVersion}."
        );
    }

    /**
     * Convert to array for API response details.
     */
    public function toArray(): array
    {
        return [
            'setting_key'      => $this->settingKey,
            'expected_version' => $this->expectedVersion,
            'actual_version'   => $this->actualVersion,
        ];
    }
}
