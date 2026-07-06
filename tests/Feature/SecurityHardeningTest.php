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

        // Core security headers
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Note: X-XSS-Protection intentionally removed — deprecated in modern browsers
        // and can introduce security risks. Replaced with a strong CSP.

        // New A+-grade headers
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Resource-Policy', 'same-site');

        // Permissions-Policy must be present
        $permPolicy = $response->headers->get('Permissions-Policy');
        $this->assertNotNull($permPolicy, 'Permissions-Policy header must be present.');
        $this->assertStringContainsString('camera=()', $permPolicy);
        $this->assertStringContainsString('microphone=()', $permPolicy);

        // CSP must be present and correctly configured
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

    /**
     * Test admin cannot access unassigned scholarship application review
     */
    public function test_admin_cannot_access_unassigned_scholarship_application_review(): void
    {
        // Create an admin
        $admin = \App\Models\User::create([
            'name' => 'OSA Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create two scholarships
        $scholarshipA = \App\Models\Scholarship::create([
            'name' => 'Scholarship A',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        $scholarshipB = \App\Models\Scholarship::create([
            'name' => 'Scholarship B',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        // Assign only Scholarship A to the admin
        $admin->scholarships()->attach($scholarshipA->id);

        // Create an application for Scholarship B
        $student = \App\Models\User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $application = \App\Models\Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarshipB->id,
            'program_name' => $scholarshipB->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        // Access unassigned application review page -> should get 403
        $response = $this->actingAs($admin)->get(route('admin.review', $application->id));
        $response->assertStatus(403);

        // Access assigned scholarship (Scholarship A) application -> should get 200 (redirects to Under Review status or renders)
        $applicationA = \App\Models\Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarshipA->id,
            'program_name' => $scholarshipA->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $responseA = $this->actingAs($admin)->get(route('admin.review', $applicationA->id));
        // It updates status to Under Review and returns status 200
        $responseA->assertStatus(200);
    }

    /**
     * Test admin cannot access unassigned document and heatmap endpoints
     */
    public function test_admin_cannot_access_unassigned_scholarship_document_endpoints(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'OSA Staff',
            'email' => 'staff@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $scholarshipA = \App\Models\Scholarship::create([
            'name' => 'Scholarship A',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        $scholarshipB = \App\Models\Scholarship::create([
            'name' => 'Scholarship B',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        $admin->scholarships()->attach($scholarshipA->id);

        $student = \App\Models\User::create([
            'name' => 'Student User',
            'email' => 'student@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $application = \App\Models\Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarshipB->id,
            'program_name' => $scholarshipB->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $document = \App\Models\Document::create([
            'application_id' => $application->id,
            'file_path' => 'uploads/test_file.png',
            'original_name' => 'test_file.png',
            'document_type' => 'COG',
        ]);

        $aiResult = \App\Models\AIResult::create([
            'document_id' => $document->id,
            'fraud_probability' => 10.0,
            'classification' => 'authentic',
            'heatmap_path' => 'uploads/heatmap_test.png',
        ]);

        // Attempt download document -> 403
        $responseDownload = $this->actingAs($admin)->get(route('admin.document.download', $document->id));
        $responseDownload->assertStatus(403);

        // Attempt document view image -> 403
        $responseImage = $this->actingAs($admin)->get(route('document.view', $document->id));
        $responseImage->assertStatus(403);

        // Attempt document view heatmap -> 403
        $responseHeatmap = $this->actingAs($admin)->get(route('document.heatmap', $document->id));
        $responseHeatmap->assertStatus(403);
    }
}
