<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Offer;

use App\Exceptions\OfferSubmission\IdempotencyConflictException;
use App\Exceptions\OfferSubmission\PolicyUnknownException;
use App\Models\Category;
use App\Models\Need;
use App\Models\Offer;
use App\Models\OfferSubmission;
use App\Models\User;
use App\Services\Offer\OfferSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\TestCase;

final class OfferSubmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private OfferSubmissionService $service;
    private User $provider;
    private User $owner;
    private Need $need;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service  = app(OfferSubmissionService::class);
        $this->provider = User::factory()->create();
        $this->owner    = User::factory()->create();

        $cat = Category::create([
            'slug' => 'svc-' . Str::lower(Str::random(6)),
            'name_am' => 'ምድብ', 'name_en' => 'Cat',
            'active' => true, 'sort_order' => 1,
        ]);

        $this->need = Need::create([
            'requester_id' => $this->owner->id,
            'category_id'  => $cat->id,
            'title'        => 'Test Need',
            'description'  => 'Desc',
            'status'       => Need::STATUS_OPEN,
            'version'      => 1,
        ]);
    }

    private function setFree(): void
    {
        config(['payments.unlock' => [
            'feature_enabled' => false,
            'amount_minor'    => 0,
            'policy_version'  => '1.3',
        ]]);
    }

    private function setPaid(): void
    {
        config(['payments.unlock' => [
            'feature_enabled' => true,
            'amount_minor'    => 5000,
            'policy_version'  => '1.3',
        ]]);
    }

    private function payload(): array
    {
        return [
            'offered_price'      => '500.00',
            'currency'           => 'ETB',
            'proposal_message'   => 'I can deliver this well.',
            'delivery_time_text' => '3 days',
            'availability_text'  => 'Now',
        ];
    }

    private function draft(): array
    {
        return [
            'draft_id'        => (string) Str::uuid(),
            'draft_version'   => 1,
            'draft_hash'      => hash('sha256', 'draft-' . Str::random(8)),
            'idempotency_key' => 'idem-' . Str::random(20),
        ];
    }

    private function submit(array $d): OfferSubmission
    {
        return $this->service->submit(
            $this->provider, $this->need, $this->payload(),
            $d['draft_id'], $d['draft_version'], $d['draft_hash'], $d['idempotency_key']
        );
    }

    public function test_policy_free_default(): void
    {
        $this->setFree();
        $p = $this->service->resolvePolicy();
        $this->assertSame(OfferSubmissionService::POLICY_FREE, $p['kind']);
        $this->assertSame(0, $p['amount_minor']);
    }

    public function test_policy_free_when_enabled_zero(): void
    {
        config(['payments.unlock' => ['feature_enabled' => true, 'amount_minor' => 0, 'policy_version' => '1.3']]);
        $p = $this->service->resolvePolicy();
        $this->assertSame(OfferSubmissionService::POLICY_FREE, $p['kind']);
    }

    public function test_policy_paid_when_enabled_positive(): void
    {
        $this->setPaid();
        $p = $this->service->resolvePolicy();
        $this->assertSame(OfferSubmissionService::POLICY_PAID, $p['kind']);
        $this->assertSame(5000, $p['amount_minor']);
    }

    public function test_policy_unknown_when_null(): void
    {
        config(['payments.unlock' => null]);
        $this->expectException(PolicyUnknownException::class);
        $this->service->resolvePolicy();
    }

    public function test_policy_unknown_when_incomplete(): void
    {
        config(['payments.unlock' => ['feature_enabled' => true]]);
        $this->expectException(PolicyUnknownException::class);
        $this->service->resolvePolicy();
    }

    public function test_free_path_creates_one_offer(): void
    {
        $this->setFree();
        $s = $this->submit($this->draft());
        $this->assertSame(OfferSubmission::STATE_SUBMITTED, $s->state);
        $this->assertNotNull($s->offer_id);
        $this->assertSame(1, Offer::where('need_id', $this->need->id)->count());
        $this->assertSame(0, $s->amount_minor);
    }

    public function test_free_path_offer_relation(): void
    {
        $this->setFree();
        $s = $this->submit($this->draft());
        $this->assertInstanceOf(Offer::class, $s->offer);
    }

    public function test_free_path_offer_price(): void
    {
        $this->setFree();
        $s = $this->submit($this->draft());
        $this->assertSame('500.00', $s->offer->fresh()->offered_price);
    }

    public function test_paid_path_creates_payment_required(): void
    {
        $this->setPaid();
        $s = $this->submit($this->draft());
        $this->assertSame(OfferSubmission::STATE_PAYMENT_REQUIRED, $s->state);
        $this->assertNull($s->offer_id);
        $this->assertSame(5000, $s->amount_minor);
    }

    public function test_paid_path_does_not_create_offer(): void
    {
        $this->setPaid();
        $this->submit($this->draft());
        $this->assertSame(0, Offer::where('need_id', $this->need->id)->count());
    }

    public function test_idempotent_same_payload(): void
    {
        $this->setFree();
        $d = $this->draft();
        $s1 = $this->submit($d);
        $s2 = $this->submit($d);
        $this->assertSame($s1->id, $s2->id);
        $this->assertSame(1, OfferSubmission::count());
    }

    public function test_idempotent_different_hash_throws(): void
    {
        $this->setFree();
        $d = $this->draft();
        $this->submit($d);

        $this->expectException(IdempotencyConflictException::class);
        $this->service->submit(
            $this->provider, $this->need, $this->payload(),
            $d['draft_id'], $d['draft_version'], hash('sha256', 'different'), $d['idempotency_key']
        );
    }

    public function test_owner_cannot_unlock_own_need(): void
    {
        $this->setFree();
        $d = $this->draft();
        $this->expectException(InvalidArgumentException::class);
        $this->service->submit(
            $this->owner, $this->need, $this->payload(),
            $d['draft_id'], $d['draft_version'], $d['draft_hash'], $d['idempotency_key']
        );
    }

    public function test_non_open_need_rejected(): void
    {
        $this->setFree();
        $this->need->update(['status' => Need::STATUS_CANCELLED]);
        $d = $this->draft();
        $this->expectException(InvalidArgumentException::class);
        $this->service->submit(
            $this->provider, $this->need->fresh(), $this->payload(),
            $d['draft_id'], $d['draft_version'], $d['draft_hash'], $d['idempotency_key']
        );
    }

    public function test_deadline_passed_rejected(): void
    {
        $this->setFree();
        $this->need->update(['offer_deadline_at' => now()->subHour()]);
        $d = $this->draft();
        $this->expectException(InvalidArgumentException::class);
        $this->service->submit(
            $this->provider, $this->need->fresh(), $this->payload(),
            $d['draft_id'], $d['draft_version'], $d['draft_hash'], $d['idempotency_key']
        );
    }

    public function test_duplicate_offer_rejected(): void
    {
        $this->setFree();
        $this->submit($this->draft());
        $d = $this->draft();
        $this->expectException(InvalidArgumentException::class);
        $this->submit($d);
    }

    public function test_get_by_idempotency_key(): void
    {
        $this->setFree();
        $d = $this->draft();
        $this->submit($d);
        $found = $this->service->getByIdempotencyKey($this->provider, $d['idempotency_key']);
        $this->assertNotNull($found);
        $this->assertSame($d['idempotency_key'], $found->idempotency_key);
    }

    public function test_get_by_idempotency_key_null_when_missing(): void
    {
        $this->assertNull($this->service->getByIdempotencyKey($this->provider, 'nonexistent'));
    }

    public function test_draft_fields_stored(): void
    {
        $this->setFree();
        $d = $this->draft();
        $s = $this->submit($d);
        $this->assertSame($d['draft_id'], $s->draft_id);
        $this->assertSame($d['draft_version'], $s->draft_version);
        $this->assertSame($d['draft_hash'], $s->draft_hash);
    }

    public function test_policy_version_stored(): void
    {
        $this->setFree();
        $s = $this->submit($this->draft());
        $this->assertSame('1.3', $s->policy_version);
    }
}
