<?php
namespace Tests\Feature\Screens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class S001WelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_welcome_has_widget_container(): void
    {
        $this->get('/')->assertSee('id="widget-container"', false);
    }

    public function test_welcome_has_telegram_integration(): void
    {
        $this->get('/')->assertSee('telegram', false);
    }

    public function test_welcome_loads_telegram_widget_script(): void
    {
        $this->get('/')->assertSee('telegram-widget.js', false);
    }
}
