<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Message;
use App\Models\Need;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * L347-F — Message model unit tests (B18 item 6).
 */
class MessageTest extends TestCase
{
    use RefreshDatabase;

    private function makeMessage(array $overrides = []): Message
    {
        $owner    = User::factory()->create();
        $provider = User::factory()->create();
        $cat      = Category::create([
            'slug'       => 'msg-cat-' . Str::lower(Str::random(6)),
            'name_am'    => 'ምድብ',
            'name_en'    => 'Cat',
            'active'     => true,
            'sort_order' => 1,
        ]);
        $need = Need::create([
            'requester_id' => $owner->id,
            'category_id'  => $cat->id,
            'title'        => 'Need ' . Str::random(4),
            'description'  => 'Desc',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
        $offer = Offer::create([
            'need_id'          => $need->id,
            'provider_id'      => $provider->id,
            'offered_price'    => '100.00',
            'currency'         => 'ETB',
            'proposal_message' => 'I can help',
            'status'           => Offer::STATUS_PENDING,
            'version'          => 1,
        ]);

        return Message::create(array_merge([
            'offer_id'   => $offer->id,
            'sender_id'  => $provider->id,
            'content'    => 'Hello there',
            'created_at' => now(),
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $m = $this->makeMessage();
        $this->assertNotNull($m->id);
        $this->assertTrue(Str::isUuid($m->id));
    }

    public function test_no_timestamps(): void
    {
        $m = new Message();
        $this->assertFalse($m->timestamps);
    }

    public function test_soft_deletes(): void
    {
        $m = $this->makeMessage();
        $m->delete();
        $this->assertSoftDeleted('messages', ['id' => $m->id]);
    }

    public function test_persists_core_attributes(): void
    {
        $m = $this->makeMessage(['content' => 'My message content']);
        $this->assertDatabaseHas('messages', [
            'id'      => $m->id,
            'content' => 'My message content',
        ]);
    }

    public function test_read_at_null_by_default(): void
    {
        $m = $this->makeMessage();
        $this->assertNull($m->fresh()->read_at);
    }

    public function test_read_at_casts_datetime(): void
    {
        $m = $this->makeMessage(['read_at' => '2026-12-31 12:00:00']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $m->fresh()->read_at);
    }

    public function test_created_at_casts_datetime(): void
    {
        $m = $this->makeMessage();
        $this->assertInstanceOf(\Carbon\Carbon::class, $m->fresh()->created_at);
    }

    public function test_belongs_to_offer(): void
    {
        $m = $this->makeMessage();
        $this->assertInstanceOf(Offer::class, $m->offer);
    }

    public function test_belongs_to_sender(): void
    {
        $sender = User::factory()->create();
        $m = $this->makeMessage(['sender_id' => $sender->id]);
        $this->assertInstanceOf(User::class, $m->sender);
        $this->assertSame($sender->id, $m->sender->id);
    }

    public function test_has_many_attachments(): void
    {
        $m = $this->makeMessage();
        $this->assertCount(0, $m->attachments);
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class,
            $m->attachments
        );
    }

    public function test_is_read_returns_false_initially(): void
    {
        $m = $this->makeMessage();
        $this->assertFalse($m->isRead());
    }

    public function test_is_read_returns_true_when_read_at_set(): void
    {
        $m = $this->makeMessage(['read_at' => now()]);
        $this->assertTrue($m->isRead());
    }

    public function test_mark_as_read_sets_timestamp(): void
    {
        $m = $this->makeMessage();
        $this->assertNull($m->read_at);

        $m->markAsRead();
        $m->refresh();

        $this->assertNotNull($m->read_at);
        $this->assertTrue($m->isRead());
    }

    public function test_mark_as_read_is_idempotent(): void
    {
        $m = $this->makeMessage();
        $m->markAsRead();
        $first = $m->fresh()->read_at;

        sleep(0); // ensure time diff
        $m->markAsRead();
        $second = $m->fresh()->read_at;

        $this->assertSame(
            $first->toDateTimeString(),
            $second->toDateTimeString(),
            'markAsRead must not update read_at on second call'
        );
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $fillable = (new Message())->getFillable();
        foreach (['offer_id', 'sender_id', 'content', 'read_at'] as $field) {
            $this->assertContains($field, $fillable, "Missing fillable: {$field}");
        }
    }
}
