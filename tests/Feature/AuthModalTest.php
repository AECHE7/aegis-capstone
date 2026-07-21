<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthModalTest extends TestCase
{
    /**
     * Test that login page loads auth modal component
     *
     * @return void
     */
    public function test_login_page_loads_auth_modal()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('authModal');
        $response->assertSee('modalLoginEmail');
        $response->assertSee('modalLoginPassword');
    }

    /**
     * Test that register page loads auth modal with register tab
     *
     * @return void
     */
    public function test_register_page_loads_auth_modal()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('authModal');
        $response->assertSee('modalRegPassword');
        $response->assertSee('passwordStrength'); // New password strength indicator
        $response->assertSee('passwordMatch'); // New password match validator
    }

    /**
     * Test that password strength elements are present
     *
     * @return void
     */
    public function test_password_strength_elements_exist()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('id="passwordStrength"', false);
        $response->assertSee('id="strengthBar"', false);
        $response->assertSee('id="strengthLabel"', false);
        $response->assertSee('Password Strength:', false);
    }

    /**
     * Test that password match validation elements exist
     *
     * @return void
     */
    public function test_password_match_elements_exist()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('id="passwordMatch"', false);
        $response->assertSee('id="passwordMismatch"', false);
        $response->assertSee('Passwords match', false);
        $response->assertSee('Passwords do not match', false);
    }

    /**
     * Test that password toggle buttons exist for all password fields
     *
     * @return void
     */
    public function test_password_toggle_buttons_exist()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        // Login password toggle
        $response->assertSee('data-target="modalLoginPassword"', false);
        // Register password toggle
        $response->assertSee('data-target="modalRegPassword"', false);
        // Confirm password toggle
        $response->assertSee('data-target="modalRegPasswordConfirm"', false);
        // Toggle button icons
        $response->assertSee('fa-eye', false);
    }

    /**
     * Test that password field has minimum length requirement
     *
     * @return void
     */
    public function test_password_field_has_min_length()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('minlength="8"', false);
    }

    /**
     * Test that auth modal JavaScript is included
     *
     * @return void
     */
    public function test_auth_modal_javascript_included()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('passwordInput.addEventListener', false);
        $response->assertSee('checkPasswordMatch', false);
        $response->assertSee('strengthBar.style.width', false);
    }
}
