<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\AdCampaign;
use App\Models\AdCreative;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class AdCreativeTest extends TestCase
{
    use RefreshDatabase;

    public function test_uses_uuid_primary_key(): void
    {
        $c = AdCreative::factory()->create();
        $this->assertTrue(Str::isUuid($c->id));
    }

    public function test_formats_lists_three(): void
    {
        $this->assertCount(3, AdCreative::FORMATS);
        $this->assertContains('CARD', AdCreative::FORMATS);
        $this->assertContains('BANNER', AdCreative::FORMATS);
        $this->assertContains('COMPACT', AdCreative::FORMATS);
    }

    public function test_format_constants_match(): void
    {
        $this->assertSame('CARD', AdCreative::FORMAT_CARD);
        $this->assertSame('BANNER', AdCreative::FORMAT_BANNER);
        $this->assertSame('COMPACT', AdCreative::FORMAT_COMPACT);
    }

    public function test_default_format_is_banner(): void
    {
        $c = AdCreative::factory()->create();
        $this->assertSame(AdCreative::FORMAT_BANNER, $c->format);
    }

    public function test_card_factory_state(): void
    {
        $c = AdCreative::factory()->card()->create();
        $this->assertSame(AdCreative::FORMAT_CARD, $c->format);
    }

    public function test_compact_factory_state(): void
    {
        $c = AdCreative::factory()->compact()->create();
        $this->assertSame(AdCreative::FORMAT_COMPACT, $c->format);
    }

    public function test_validation_receipt_casts_to_array(): void
    {
        $c = AdCreative::factory()->create([
            'validation_receipt' => ['status' => 'PASS'],
        ]);
        $fresh = $c->fresh();
        $this->assertIsArray($fresh->validation_receipt);
        $this->assertSame('PASS', $fresh->validation_receipt['status']);
    }

    public function test_validation_receipt_nullable(): void
    {
        $c = AdCreative::factory()->create(['validation_receipt' => null]);
        $this->assertNull($c->fresh()->validation_receipt);
    }

    public function test_version_casts_to_integer(): void
    {
        $c = AdCreative::factory()->create(['version' => 3]);
        $this->assertSame(3, $c->fresh()->version);
    }

    public function test_has_many_campaigns(): void
    {
        $c = AdCreative::factory()->create();
        AdCampaign::factory()->count(2)->create(['creative_id' => $c->id]);
        $this->assertCount(2, $c->fresh()->campaigns);
    }

    public function test_am_copy_fields_required(): void
    {
        $c = AdCreative::factory()->create();
        $fresh = $c->fresh();
        $this->assertNotEmpty($fresh->copy_am_title);
        $this->assertNotEmpty($fresh->copy_am_body);
        $this->assertNotEmpty($fresh->copy_am_cta);
    }

    public function test_en_copy_fields_required(): void
    {
        $c = AdCreative::factory()->create();
        $fresh = $c->fresh();
        $this->assertNotEmpty($fresh->copy_en_title);
        $this->assertNotEmpty($fresh->copy_en_body);
        $this->assertNotEmpty($fresh->copy_en_cta);
    }
}
