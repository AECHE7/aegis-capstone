<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class NpcSealTest extends TestCase
{
    #[Test]
    public function cor_seal_image_asset_exists_and_is_valid()
    {
        $sealPath = public_path('images/CORSeal.jpg');
        $this->assertFileExists($sealPath);
        $this->assertGreaterThan(100000, filesize($sealPath));
    }

    #[Test]
    public function login_landing_page_displays_cor_seal_modal_and_trigger_badge()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('corSealModal', false);
        $response->assertSee('SEAL OF REGISTRATION', false);
        $response->assertSee('CLSU Official Data', false);
        $response->assertSee('images/CORSeal.jpg', false);
        $response->assertSee('This seal authenticates Certificate of Registration from CLSU.', false);
        $response->assertSee('I Understand', false);
        $response->assertSee('showCorSealModal()', false);
    }

    #[Test]
    public function welcome_portal_displays_cor_seal_modal_and_floating_badge()
    {
        $response = $this->get('/welcome');

        $response->assertStatus(200);
        $response->assertSee('corSealModal', false);
        $response->assertSee('SEAL OF REGISTRATION', false);
        $response->assertSee('NPC Seal of Registration', false);
        $response->assertSee('images/CORSeal.jpg', false);
        $response->assertSee('showCorSealModal()', false);
    }

    #[Test]
    public function register_page_contains_npc_seal_modal_and_dpa_trigger()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('corSealModal', false);
        $response->assertSee('View NPC Seal', false);
        $response->assertSee('showCorSealModal()', false);
    }
}
