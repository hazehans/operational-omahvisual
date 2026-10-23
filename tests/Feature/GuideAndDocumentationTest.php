<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GuideAndDocumentationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::where('email', 'owner@omahvisual.com')->firstOrFail();
    }

    public function test_guests_are_redirected_to_login_when_accessing_guide(): void
    {
        $response = $this->get(route('guide.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_guide_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('guide.index'));

        $response->assertStatus(200);
        $response->assertViewIs('guide.index');
        $response->assertSee('Buku Panduan &amp; Standar Operasional Sistem', false);
        $response->assertSee('Alur Bisnis Event (10 Langkah)');
        $response->assertSee('Panduan Tiap Peran (Roles)');
        $response->assertSee('Fitur Kunci &amp; Keunggulan', false);
        $response->assertSee('Akun Simulasi Bos (Testing)');
    }

    public function test_guide_page_displays_demo_accounts_for_testing(): void
    {
        $response = $this->actingAs($this->user)->get(route('guide.index'));

        $response->assertStatus(200);
        $response->assertSee('super@omahvisual.com');
        $response->assertSee('owner@omahvisual.com');
        $response->assertSee('manager.ops@omahvisual.com');
        $response->assertSee('admin@omahvisual.com');
        $response->assertSee('warehouse@omahvisual.com');
        $response->assertSee('kepala.servis@omahvisual.com');
        $response->assertSee('teknisi@omahvisual.com');
        $response->assertSee('password123');
    }

    public function test_dashboard_displays_guide_banner_and_action_link(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('guide.index'));
        $response->assertSee('Buku Panduan &amp; Standar Operasional (SOP)', false);
        $response->assertSee('Mode Uji Coba Bos');
    }

    public function test_dashboard_displays_team_radar_and_live_activity_stream(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Kondisi &amp; Radar Tim Operasional', false);
        $response->assertSee('Manager Operasional');
        $response->assertSee('Admin LED &amp; Finance', false);
        $response->assertSee('Tim Gudang (Warehouse)');
        $response->assertSee('Ruang Servis &amp; Workshop', false);
        $response->assertSee('Aktivitas Terkini Sistem');
    }
}
