<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Need;
use App\Models\TelegramDestination;
use App\Models\TelegramPublication;
use App\Models\TelegramPublicationEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * L347-F — TelegramPublication model unit tests (B18 item 6).
 */
class TelegramPublicationTest extends TestCase
{
    use RefreshDatabase;

    private function makeNeed(): Need
    {
        $owner = User::factory()->create();
        $cat   = Category::create([
            'slug'       => 'tg-cat-' . Str::lower(Str::random(6)),
            'name_am'    => 'ምድብ',
            'name_en'    => 'Cat',
            'active'     => true,
            'sort_order' => 1,
        ]);

        return Need::create([
            'requester_id' => $owner->id,
            'category_id'  => $cat->id,
            'title'        => 'Need ' . Str::random(4),
            'description'  => 'Desc',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
    }

    private function makePub(array $overrides = []): TelegramPublication
    {
        $need = $this->makeNeed();

        return TelegramPublication::create(array_merge([
            'need_id'                   => $need->id,
            'destination_id'            => TelegramDestination::factory()->create()->id,
            'need_publication_version'  => 1,
            'content_hash'              => hash('sha256', 'test'),
            'payload_snapshot'          => ['title' => 'Test'],
            'state'                     => TelegramPublication::STATE_QUEUED,
            'attempts'                  => 0,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $p = $this->makePub();
        $this->assertNotNull($p->id);
        $this->assertTrue(Str::isUuid($p->id));
    }

    public function test_persists_core_attributes(): void
    {
        $p = $this->makePub([
            'content_hash'             => hash('sha256', 'abc'),
            'need_publication_version' => 3,
        ]);

        $this->assertDatabaseHas('telegram_publications', [
            'id'                       => $p->id,
            'need_publication_version' => 3,
            'state'                    => TelegramPublication::STATE_QUEUED,
        ]);
    }

    public function test_payload_snapshot_casts_to_array(): void
    {
        $p = $this->makePub(['payload_snapshot' => ['a' => 1, 'b' => 2]]);
        $this->assertSame(['a' => 1, 'b' => 2], $p->fresh()->payload_snapshot);
    }

    public function test_attempts_casts_integer(): void
    {
        $p = $this->makePub(['attempts' => 5]);
        $this->assertSame(5, $p->fresh()->attempts);
    }

    public function test_next_attempt_at_casts_datetime(): void
    {
        $p = $this->makePub(['next_attempt_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $p->fresh()->next_attempt_at);
    }

    public function test_posted_at_casts_datetime(): void
    {
        $p = $this->makePub(['posted_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $p->fresh()->posted_at);
    }

    public function test_removed_at_casts_datetime(): void
    {
        $p = $this->makePub(['removed_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $p->fresh()->removed_at);
    }

    public function test_telegram_message_id_casts_integer(): void
    {
        $p = $this->makePub(['telegram_message_id' => 12345]);
        $this->assertSame(12345, $p->fresh()->telegram_message_id);
    }

    public function test_state_constants_are_declared(): void
    {
        $this->assertSame('QUEUED', TelegramPublication::STATE_QUEUED);
        $this->assertSame('SENDING', TelegramPublication::STATE_SENDING);
        $this->assertSame('POSTED', TelegramPublication::STATE_POSTED);
        $this->assertSame('RETRY', TelegramPublication::STATE_RETRY);
        $this->assertSame('UNCERTAIN', TelegramPublication::STATE_UNCERTAIN);
        $this->assertSame('FAILED', TelegramPublication::STATE_FAILED);
        $this->assertSame('SKIPPED', TelegramPublication::STATE_SKIPPED);
        $this->assertSame('REMOVAL_PENDING', TelegramPublication::STATE_REMOVAL_PENDING);
        $this->assertSame('REMOVED', TelegramPublication::STATE_REMOVED);
    }

    public function test_no_stopped_state_exists(): void
    {
        // L347-A: stop -> REMOVAL_PENDING (not STOPPED)
        $ref = new \ReflectionClass(TelegramPublication::class);
        $this->assertFalse(
            $ref->hasConstant('STATE_STOPPED'),
            'STATE_STOPPED must not exist; stop is REMOVAL_PENDING'
        );
    }

    public function test_belongs_to_need(): void
    {
        $p = $this->makePub();
        $this->assertInstanceOf(Need::class, $p->need);
    }

    public function test_belongs_to_destination(): void
    {
        $p = $this->makePub();
        $this->assertInstanceOf(TelegramDestination::class, $p->destination);
    }

    public function test_has_many_events(): void
    {
        $p = $this->makePub();
        $this->assertCount(0, $p->events);
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $p->events
        );
    }

    public function test_scope_pending_includes_queued_and_retry(): void
    {
        $this->makePub(['state' => TelegramPublication::STATE_QUEUED]);
        $this->makePub(['state' => TelegramPublication::STATE_RETRY]);
        $this->makePub(['state' => TelegramPublication::STATE_SENDING]);
        $this->makePub(['state' => TelegramPublication::STATE_POSTED]);

        $this->assertSame(2, TelegramPublication::pending()->count());
    }

    public function test_scope_pending_excludes_uncertain(): void
    {
        $this->makePub(['state' => TelegramPublication::STATE_QUEUED]);
        $this->makePub(['state' => TelegramPublication::STATE_UNCERTAIN]);

        $this->assertSame(1, TelegramPublication::pending()->count());
    }

    public function test_scope_uncertain_filters(): void
    {
        $this->makePub(['state' => TelegramPublication::STATE_UNCERTAIN]);
        $this->makePub(['state' => TelegramPublication::STATE_UNCERTAIN]);
        $this->makePub(['state' => TelegramPublication::STATE_QUEUED]);

        $this->assertSame(2, TelegramPublication::uncertain()->count());
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new TelegramPublication())->getFillable();
        foreach (['state', 'need_id', 'attempts', 'payload_snapshot'] as $field) {
            $this->assertContains($field, $fillable, "Missing fillable: {$field}");
        }
    }

    public function test_fillable_does_not_contain_status_or_stopped_at(): void
    {
        // L347-A: status + stopped_at were the bug columns
        $fillable = (new TelegramPublication())->getFillable();
        $this->assertNotContains('status', $fillable);
        $this->assertNotContains('stopped_at', $fillable);
    }
}
