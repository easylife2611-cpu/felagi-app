<?php

namespace Tests\Feature\Screens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S021ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_renders(): void
    {
        $res = $this->get('/support/report');
        $res->assertStatus(200);
        $res->assertSee('id="report-form"', false);
    }

    public function test_page_has_all_form_fields(): void
    {
        $res = $this->get('/support/report');
        foreach (['id="type"','id="entity_type"','id="entity_id"','id="reason"','id="contact"','id="submit-btn"'] as $m) {
            $res->assertSee($m, false);
        }
    }

    public function test_page_has_all_report_types(): void
    {
        $res = $this->get('/support/report');
        foreach (['spam','harassment','fraud','inappropriate','other'] as $t) {
            $res->assertSee('value="'.$t.'"', false);
        }
    }

    public function test_page_has_all_target_types(): void
    {
        $res = $this->get('/support/report');
        foreach (['user','need','offer','message'] as $t) {
            $res->assertSee('value="'.$t.'"', false);
        }
    }

    public function test_page_has_offline_banner(): void
    {
        $res = $this->get('/support/report');
        $res->assertSee('id="offline-banner"', false);
        $res->assertSee('id="status"', false);
    }
}
