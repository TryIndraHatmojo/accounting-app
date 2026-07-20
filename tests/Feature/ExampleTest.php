<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_the_admin_panel(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/admin');
    }

    public function test_admin_panel_uses_professional_theme_and_light_mode_by_default(): void
    {
        $panel = Filament::getPanel('admin');
        $colors = $panel->getColors();

        $this->assertSame(ThemeMode::Light, $panel->getDefaultThemeMode());
        $this->assertSame('resources/css/filament/admin/theme.css', $panel->getViteTheme());
        $this->assertSame(Width::Full, $panel->getMaxContentWidth());
        $this->assertTrue($panel->isSidebarCollapsibleOnDesktop());
        $this->assertSame('17rem', $panel->getSidebarWidth());
        $this->assertSame(Color::Indigo, $colors['primary']);
        $this->assertSame(Color::Sky, $colors['info']);
        $this->assertSame(Color::Slate, $colors['gray']);
        $this->assertSame(Color::Emerald, $colors['success']);
        $this->assertSame(Color::Amber, $colors['warning']);
        $this->assertSame(Color::Rose, $colors['danger']);
    }
}
