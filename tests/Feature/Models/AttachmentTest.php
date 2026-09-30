<?php

namespace Tests\Feature\Models;

use App\Models\Attachment;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Models\Concerns\CreatesTestCategory;
use Tests\TestCase;

/**
 * WP-B27 / R-TEST-05 — Attachment model unit tests.
 * Schema: 2026_09_29_000017.
 * NOTE: SoftDeletes, $timestamps=false, but has created_at auto.
 */
class AttachmentTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestCategory;

    private function makeAttachment(array $overrides = []): Attachment
    {
        $user = User::factory()->create();

        return Attachment::create(array_merge([
            'uploaded_by'    => $user->id,
            'purpose'        => Attachment::PURPOSE_PROFILE,
            'storage_disk'   => 'local',
            'storage_key'    => 'attachments/' . Str::uuid() . '.pdf',
            'original_name'  => 'document.pdf',
            'detected_mime'  => 'application/pdf',
            'byte_size'      => 12345,
            'sha256'         => hash('sha256', 'test-content'),
            'visibility'     => Attachment::VISIBILITY_PRIVATE,
            'scan_status'    => Attachment::SCAN_PENDING,
        ], $overrides));
    }

    public function test_uuid_auto_generated(): void
    {
        $a = $this->makeAttachment();
        $this->assertNotNull($a->id);
        $this->assertTrue(Str::isUuid($a->id));
    }

    public function test_no_timestamps(): void
    {
        $this->assertFalse((new Attachment())->usesTimestamps());
    }

    public function test_uses_soft_deletes(): void
    {
        $a = $this->makeAttachment();
        $a->delete();

        $this->assertSoftDeleted('attachments', ['id' => $a->id]);
        $this->assertNotNull(Attachment::withTrashed()->find($a->id));
    }

    public function test_persists_core_attributes(): void
    {
        $a = $this->makeAttachment([
            'original_name' => 'myfile.pdf',
            'byte_size'     => 9999,
        ]);

        $this->assertDatabaseHas('attachments', [
            'id'            => $a->id,
            'original_name' => 'myfile.pdf',
            'byte_size'     => 9999,
            'purpose'       => 'PROFILE',
            'scan_status'   => 'PENDING',
        ]);
    }

    public function test_belongs_to_uploader(): void
    {
        $a = $this->makeAttachment();
        $this->assertInstanceOf(User::class, $a->uploader);
        $this->assertSame($a->uploaded_by, $a->uploader->id);
    }

    public function test_need_id_nullable(): void
    {
        $a = $this->makeAttachment(['need_id' => null]);
        $this->assertNull($a->fresh()->need_id);
        $this->assertNull($a->fresh()->need);
    }

    public function test_offer_id_nullable(): void
    {
        $a = $this->makeAttachment(['offer_id' => null]);
        $this->assertNull($a->fresh()->offer_id);
    }

    public function test_message_id_nullable(): void
    {
        $a = $this->makeAttachment(['message_id' => null]);
        $this->assertNull($a->fresh()->message_id);
    }

    public function test_belongs_to_need_when_set(): void
    {
        $user = User::factory()->create();
        $need = Need::create([
            'requester_id' => $user->id,
            'category_id'  => $this->makeCategory(),
            'title'        => 'Test Need',
            'description'  => 'Desc',
            'status'       => 'OPEN',
        ]);

        $a = $this->makeAttachment([
            'need_id' => $need->id,
            'purpose' => Attachment::PURPOSE_NEED,
        ]);

        $this->assertInstanceOf(Need::class, $a->fresh()->need);
        $this->assertSame($need->id, $a->fresh()->need->id);
    }

    public function test_byte_size_casts_integer(): void
    {
        $a = $this->makeAttachment(['byte_size' => 2048]);
        $this->assertSame(2048, $a->fresh()->byte_size);
    }

    public function test_created_at_casts_to_datetime(): void
    {
        $a = $this->makeAttachment();
        $this->assertInstanceOf(\Carbon\Carbon::class, $a->fresh()->created_at);
    }

    public function test_purpose_constants(): void
    {
        $this->assertSame('PROFILE', Attachment::PURPOSE_PROFILE);
        $this->assertSame('NEED', Attachment::PURPOSE_NEED);
        $this->assertSame('OFFER', Attachment::PURPOSE_OFFER);
        $this->assertSame('MESSAGE', Attachment::PURPOSE_MESSAGE);
    }

    public function test_visibility_constants(): void
    {
        $this->assertSame('PUBLIC', Attachment::VISIBILITY_PUBLIC);
        $this->assertSame('PRIVATE', Attachment::VISIBILITY_PRIVATE);
    }

    public function test_scan_status_constants(): void
    {
        $this->assertSame('PENDING', Attachment::SCAN_PENDING);
        $this->assertSame('CLEAN', Attachment::SCAN_CLEAN);
        $this->assertSame('REJECTED', Attachment::SCAN_REJECTED);
    }

    public function test_default_visibility_is_private(): void
    {
        $user = User::factory()->create();
        $a = Attachment::create([
            'uploaded_by'   => $user->id,
            'purpose'       => 'PROFILE',
            'storage_disk'  => 'local',
            'storage_key'   => 'x',
            'original_name' => 'f.pdf',
            'detected_mime' => 'application/pdf',
            'byte_size'     => 100,
            'sha256'        => hash('sha256', 'x'),
        ]);
        $this->assertSame('PRIVATE', $a->fresh()->visibility);
    }

    public function test_default_scan_status_is_pending(): void
    {
        $user = User::factory()->create();
        $a = Attachment::create([
            'uploaded_by'   => $user->id,
            'purpose'       => 'PROFILE',
            'storage_disk'  => 'local',
            'storage_key'   => 'x',
            'original_name'  => 'f.pdf',
            'detected_mime' => 'application/pdf',
            'byte_size'     => 100,
            'sha256'        => hash('sha256', 'x'),
        ]);
        $this->assertSame('PENDING', $a->fresh()->scan_status);
    }

    public function test_scope_clean_filters(): void
    {
        $this->makeAttachment(['scan_status' => 'CLEAN']);
        $this->makeAttachment(['scan_status' => 'CLEAN']);
        $this->makeAttachment(['scan_status' => 'PENDING']);
        $this->makeAttachment(['scan_status' => 'REJECTED']);

        $this->assertSame(2, Attachment::clean()->count());
    }




    public function test_is_scan_clean_returns_true_for_clean(): void
    {
        $a = $this->makeAttachment(['scan_status' => 'CLEAN']);
        $this->assertTrue($a->isScanClean());
    }

    public function test_is_scan_clean_returns_false_for_pending(): void
    {
        $a = $this->makeAttachment(['scan_status' => 'PENDING']);
        $this->assertFalse($a->isScanClean());
    }

    public function test_sha256_is_64_chars(): void
    {
        $a = $this->makeAttachment();
        $this->assertSame(64, strlen($a->fresh()->sha256));
    }

    public function test_soft_delete_keeps_row(): void
    {
        $a = $this->makeAttachment();
        $a->delete();

        $this->assertNull(Attachment::find($a->id));
        $this->assertNotNull(Attachment::withTrashed()->find($a->id));
    }
}
