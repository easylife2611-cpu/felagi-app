<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\BreachIncident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class BreachIncidentTest extends TestCase
{
    use RefreshDatabase;

    public function test_severity_constants_match(): void
    {
        $this->assertSame('P1', BreachIncident::SEV_P1);
        $this->assertSame('P2', BreachIncident::SEV_P2);
        $this->assertSame('P3', BreachIncident::SEV_P3);
        $this->assertSame('P4', BreachIncident::SEV_P4);
    }

    public function test_status_constants_match(): void
    {
        $this->assertSame('detected', BreachIncident::STATUS_DETECTED);
        $this->assertSame('contained', BreachIncident::STATUS_CONTAINED);
        $this->assertSame('notified', BreachIncident::STATUS_NOTIFIED);
        $this->assertSame('resolved', BreachIncident::STATUS_RESOLVED);
    }

    public function test_deadline_constant_is_72_hours(): void
    {
        $this->assertSame(72, BreachIncident::HOURS_DEADLINE);
    }

    public function test_data_categories_casts_to_array(): void
    {
        $b = BreachIncident::factory()->create(['data_categories' => ['email', 'phone']]);
        $fresh = $b->fresh();
        $this->assertIsArray($fresh->data_categories);
        $this->assertSame(['email', 'phone'], $fresh->data_categories);
    }

    public function test_detected_at_casts_to_carbon(): void
    {
        $b = BreachIncident::factory()->create();
        $this->assertInstanceOf(Carbon::class, $b->fresh()->detected_at);
    }

    public function test_default_status_is_detected(): void
    {
        $b = BreachIncident::factory()->create();
        $this->assertSame(BreachIncident::STATUS_DETECTED, $b->fresh()->status);
    }

    public function test_is_deadline_exceeded_false_when_recent(): void
    {
        $b = BreachIncident::factory()->detectedHoursAgo(10)->create();
        $this->assertFalse($b->fresh()->isDeadlineExceeded());
    }

    public function test_is_deadline_exceeded_true_after_72h(): void
    {
        $b = BreachIncident::factory()->detectedHoursAgo(73)->create();
        $this->assertTrue($b->fresh()->isDeadlineExceeded());
    }

    public function test_is_deadline_exceeded_false_when_eca_notified(): void
    {
        $b = BreachIncident::factory()
            ->detectedHoursAgo(100)
            ->notifiedEca()
            ->create();
        $this->assertFalse($b->fresh()->isDeadlineExceeded());
    }

    public function test_hours_until_deadline_returns_remaining(): void
    {
        $b = BreachIncident::factory()->detectedHoursAgo(10)->create();
        $remaining = $b->fresh()->hoursUntilDeadline();
        // 72 - 10 = 62 (allow +/- 1 hour tolerance for execution time)
        $this->assertGreaterThanOrEqual(61, $remaining);
        $this->assertLessThanOrEqual(62, $remaining);
    }

    public function test_hours_until_deadline_clamps_at_zero_when_overdue(): void
    {
        $b = BreachIncident::factory()->detectedHoursAgo(100)->create();
        $this->assertSame(0, $b->fresh()->hoursUntilDeadline());
    }

    public function test_requires_eca_notification_true_for_p1(): void
    {
        $b = BreachIncident::factory()->p1()->create();
        $this->assertTrue($b->requiresEcaNotification());
    }

    public function test_requires_eca_notification_true_for_p2(): void
    {
        $b = BreachIncident::factory()->p2()->create();
        $this->assertTrue($b->requiresEcaNotification());
    }

    public function test_requires_eca_notification_false_for_p3(): void
    {
        $b = BreachIncident::factory()->p3()->create();
        $this->assertFalse($b->requiresEcaNotification());
    }

    public function test_requires_eca_notification_false_for_p4(): void
    {
        $b = BreachIncident::factory()->p4()->create();
        $this->assertFalse($b->requiresEcaNotification());
    }

    public function test_requires_user_notification_only_for_p1(): void
    {
        $this->assertTrue(BreachIncident::factory()->p1()->create()->requiresUserNotification());
        $this->assertFalse(BreachIncident::factory()->p2()->create()->requiresUserNotification());
        $this->assertFalse(BreachIncident::factory()->p3()->create()->requiresUserNotification());
        $this->assertFalse(BreachIncident::factory()->p4()->create()->requiresUserNotification());
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $b = new BreachIncident();
        foreach ([
            'title', 'description', 'severity', 'status',
            'data_categories', 'affected_count',
            'detected_at', 'contained_at',
            'eca_notified_at', 'users_notified_at', 'resolved_at',
            'reported_by', 'remediation',
        ] as $field) {
            $this->assertContains($field, $b->getFillable());
        }
    }
}
