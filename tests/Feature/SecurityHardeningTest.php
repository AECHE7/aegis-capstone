<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that security headers are injected in standard web responses.
     */
    public function test_http_responses_contain_security_headers(): void
    {
        $response = $this->get(route('login'));

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotNull($csp);
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("https://cdn.jsdelivr.net", $csp);
    }

    /**
     * Test that login POST endpoint rate limits clients after 5 failed attempts.
     */
    public function test_login_route_enforces_rate_limiting(): void
    {
        // Make 5 requests to the login submission endpoint
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('login.submit'), [
                'email' => 'hacker@clsu.edu.ph',
                'password' => 'wrongpass'
            ]);
            $response->assertStatus(302); // Redirect back on fail
        }

        // The 6th request must trigger a 429 Too Many Requests block
        $responseBlock = $this->post(route('login.submit'), [
            'email' => 'hacker@clsu.edu.ph',
            'password' => 'wrongpass'
        ]);

        $responseBlock->assertStatus(429);
    }
}
