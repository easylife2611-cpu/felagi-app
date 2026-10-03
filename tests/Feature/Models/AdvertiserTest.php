<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\AdCampaign;
use App\Models\Advertiser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AdvertiserTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $a = Advertiser::factory()->create();
        $this->assertTrue(Str::isUuid($a->id));
    }

    public function test_status_constants_match_canonical(): void
    {
        $this->assertSame('ACTIVE', Advertiser::STATUS_ACTIVE);
        $this->assertSame('BLOCKED', Advertiser::STATUS_BLOCKED);
    }

    public function test_default_status_is_active(): void
    {
        $a = Advertiser::factory()->create();
        $this->assertSame(Advertiser::STATUS_ACTIVE, $a->status);
    }

    public function test_blocked_state_produces_blocked_advertiser(): void
    {
        $a = Advertiser::factory()->blocked()->create();
        $this->assertSame(Advertiser::STATUS_BLOCKED, $a->status);
    }

    public function test_has_many_campaigns(): void
    {
        $a = Advertiser::factory()->create();
        AdCampaign::factory()->count(3)->create(['advertiser_id' => $a->id]);
        $this->assertCount(3, $a->fresh()->campaigns);
    }

    public function test_campaigns_relationship_empty_initially(): void
    {
        $a = Advertiser::factory()->create();
        $this->assertCount(0, $a->fresh()->campaigns);
    }

    public function test_fillable_contains_expected_fields(): void
    {
        $a = new Advertiser();
        foreach (['name', 'display_name', 'contact_reference', 'status', 'notes'] as $field) {
            $this->assertContains($field, $a->getFillable());
        }
    }

    public function test_notes_nullable(): void
    {
        $a = Advertiser::factory()->create(['notes' => null]);
        $this->assertNull($a->notes);
    }

    public function test_contact_reference_nullable(): void
    {
        $a = Advertiser::factory()->create(['contact_reference' => null]);
        $this->assertNull($a->contact_reference);
    }
}
