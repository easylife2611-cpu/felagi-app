<?php

namespace Tests\Feature\Privacy;

use App\Mail\BreachNoticeMail;
use App\Models\BreachIncident;
use App\Models\User;
use App\Services\Privacy\BreachNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BreachNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_creates_incident(): void
    {
        $service = app(BreachNotificationService::class);
        $incident = $service->report([
            'title'           => 'Test breach',
            'description'     => 'Description',
            'severity'        => BreachIncident::SEV_P2,
            'data_categories' => ['email', 'name'],
            'affected_count'  => 10,
        ]);

        $this->assertDatabaseHas('breach_incidents', [
            'title'    => 'Test breach',
            'severity' => 'P2',
            'status'   => 'detected',
        ]);
        $this->assertEquals(10, $incident->affected_count);
    }

    public function test_72h_deadline_not_exceeded_when_fresh(): void
    {
        $incident = BreachIncident::create([
            'title'           => 'Fresh',
            'description'     => 'x',
            'severity'        => 'P2',
            'data_categories' => [],
            'detected_at'     => now(),
        ]);

        $this->assertFalse($incident->isDeadlineExceeded());
        $this->assertGreaterThan(70, $incident->hoursUntilDeadline());
    }

    public function test_72h_deadline_exceeded_after_72h(): void
    {
        $incident = BreachIncident::create([
            'title'           => 'Old',
            'description'     => 'x',
            'severity'        => 'P2',
            'data_categories' => [],
            'detected_at'     => now()->subHours(80),
        ]);

        $this->assertTrue($incident->isDeadlineExceeded());
        $this->assertEquals(0, $incident->hoursUntilDeadline());
    }

    public function test_notify_eca_sets_timestamp(): void
    {
        $incident = BreachIncident::create([
            'title'           => 'x',
            'description'     => 'x',
            'severity'        => 'P1',
            'data_categories' => [],
            'detected_at'     => now(),
        ]);

        app(BreachNotificationService::class)->notifyEca($incident);

        $this->assertNotNull($incident->fresh()->eca_notified_at);
        $this->assertEquals('notified', $incident->fresh()->status);
    }

    public function test_notify_users_sends_mail(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'u@example.com']);
        $incident = BreachIncident::create([
            'title'           => 'x',
            'description'     => 'x',
            'severity'        => 'P1',
            'data_categories' => ['email'],
            'detected_at'     => now(),
        ]);

        $count = app(BreachNotificationService::class)
            ->notifyAffectedUsers($incident, [$user->id]);

        $this->assertEquals(1, $count);
        Mail::assertSent(BreachNoticeMail::class);
    }

    public function test_overdue_returns_only_past_deadline(): void
    {
        BreachIncident::create([
            'title'           => 'Recent',
            'description'     => 'x',
            'severity'        => 'P2',
            'data_categories' => [],
            'detected_at'     => now(),
        ]);
        BreachIncident::create([
            'title'           => 'Old',
            'description'     => 'x',
            'severity'        => 'P2',
            'data_categories' => [],
            'detected_at'     => now()->subHours(80),
        ]);

        $overdue = app(BreachNotificationService::class)->overdueIncidents();
        $this->assertEquals(1, $overdue->count());
        $this->assertEquals('Old', $overdue->first()->title);
    }

    public function test_requires_eca_for_p1_p2(): void
    {
        $p1 = new BreachIncident(['severity' => 'P1']);
        $p3 = new BreachIncident(['severity' => 'P3']);
        $this->assertTrue($p1->requiresEcaNotification());
        $this->assertFalse($p3->requiresEcaNotification());
    }

    public function test_amharic_breach_mail_renders(): void
    {
        $incident = BreachIncident::create([
            'title'           => 'ጥሰት ሙከራ',
            'description'     => 'x',
            'severity'        => 'P1',
            'data_categories' => ['email'],
            'detected_at'     => now(),
        ]);

        $mail = new BreachNoticeMail($incident, 'Test', 'am');
        $this->assertStringContainsString('ማሳወቂያ', $mail->envelope()->subject);
    }

    public function test_english_breach_mail_renders(): void
    {
        $incident = BreachIncident::create([
            'title'           => 'Breach',
            'description'     => 'x',
            'severity'        => 'P1',
            'data_categories' => ['email'],
            'detected_at'     => now(),
        ]);

        $mail = new BreachNoticeMail($incident, 'Test', 'en');
        $this->assertStringContainsString('Privacy Notice', $mail->envelope()->subject);
    }

    public function test_artisan_command_runs(): void
    {
        $this->artisan('breach:check-deadlines')->assertExitCode(0);

        BreachIncident::create([
            'title'           => 'Old',
            'description'     => 'x',
            'severity'        => 'P2',
            'data_categories' => [],
            'detected_at'     => now()->subHours(80),
        ]);

        $this->artisan('breach:check-deadlines')->assertExitCode(1);
    }
}
