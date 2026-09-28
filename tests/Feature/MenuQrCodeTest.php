<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuQrCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_code_endpoint_returns_a_generated_svg_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('menu.qrcode'));

        $response->assertOk();
        $response->assertHeader('content-type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
        $this->assertStringContainsString('viewBox=', $response->getContent());
    }

    public function test_qr_code_endpoint_returns_same_origin_image_url_for_refresh_requests(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('menu.qrcode'));

        $response->assertOk();
        $response->assertJsonPath('qr_url', route('menu.qrcode', ['v' => substr(md5('initial'), 0, 8)]));
        $response->assertJsonPath('menu_url', url('/menu?v=' . substr(md5('initial'), 0, 8)));
    }
}