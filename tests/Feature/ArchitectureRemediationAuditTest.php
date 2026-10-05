<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Scholarship;
use App\Models\Setting;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\UserMfaDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ArchitectureRemediationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();
        Setting::set('mfa_enforcement', 'all');
    }

    #[Test]
    public function test_remember_device_token_enforces_user_agent_binding(): void
    {
        $user = User::factory()->create([
            'email' => 'legit.student@clsu.edu.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $rawToken = Str::random(60);
        $hashedToken = hash('sha256', $rawToken);
        $validUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0';
        $rogueUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15';

        UserMfaDevice::create([
            'user_id' => $user->id,
            'device_token' => $hashedToken,
            'ip_address' => '127.0.0.1',
            'user_agent' => $validUserAgent,
            'user_agent_hash' => hash('sha256', $validUserAgent),
            'expires_at' => now()->addDays(30),
        ]);

        // 1. Attempt login with rogue User-Agent using stolen cookie -> Must NOT bypass MFA
        $responseRogue = $this->withHeaders(['User-Agent' => $rogueUserAgent])
            ->withCookies(['mfa_device_token' => $rawToken])
            ->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'Password123!',
            ]);

        $responseRogue->assertRedirect(route('login.mfa'));
        $this->assertFalse(auth()->check(), 'User should not be logged in immediately when User-Agent does not match.');

        // 2. Attempt login with legitimate User-Agent -> Should bypass MFA
        $responseLegit = $this->withHeaders(['User-Agent' => $validUserAgent])
            ->withCookies(['mfa_device_token' => $rawToken])
            ->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'Password123!',
            ]);

        $this->assertTrue(auth()->check(), 'User should be logged in immediately with matching token and User-Agent hash.');
        $responseLegit->assertRedirect(route('student.dashboard'));
    }

    #[Test]
    public function test_admin_can_search_student_by_encrypted_clsu_id(): void
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $term = AcademicTerm::create([
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'is_active' => true,
        ]);

        $scholarship = Scholarship::create([
            'name' => 'University Merit Grant',
            'min_gwa_required' => 1.75,
            'status' => 'Active',
        ]);

        $student = User::factory()->create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@clsu.edu.ph',
            'role' => 'student',
            'is_active' => true,
        ]);

        // The student profile encrypts clsu_id_number at rest and generates clsu_id_hash
        $profile = StudentProfile::create([
            'user_id' => $student->id,
            'clsu_id_number' => '24-1357',
            'college' => 'College of Science',
            'course' => 'BS Information Technology',
            'year_level' => '3rd Year',
            'contact_number' => '09171112233',
        ]);

        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'academic_term_id' => $term->id,
            'program_name' => $scholarship->name,
            'gwa' => 1.50,
            'status' => 'Pending',
        ]);

        // Query with search=24-1357 as Admin
        $response = $this->actingAs($admin)->get(route('admin.dashboard', ['search' => '24-1357']));

        $response->assertStatus(200);
        $response->assertSee('Maria Santos');
        $response->assertSee('APP-' . $application->id);
    }

    #[Test]
    public function test_health_check_returns_healthy_json_status(): void
    {
        $response = $this->get(route('health'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'timestamp',
            'environment',
            'checks' => [
                'database' => ['status'],
                'storage' => ['status'],
            ],
        ]);
        $response->assertJsonPath('status', 'ok');
    }

    #[Test]
    public function test_ai_wake_endpoint_sanitizes_topology_and_strips_internal_url(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*/health' => \Illuminate\Support\Facades\Http::response(['status' => 'healthy'], 200),
        ]);

        $response = $this->get(route('ai.wake'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 200,
        ]);
        // Zero internal URL or raw debug leakage
        $this->assertArrayNotHasKey('url', $response->json());
    }

    #[Test]
    public function test_universal_demo_otp_bypass_fails_for_real_student_account(): void
    {
        $realStudent = User::factory()->create([
            'email' => 'real.student.juandelacruz@clsu.edu.ph',
            'password' => Hash::make('Password123!'),
            'role' => 'student',
            'is_active' => true,
            'otp_code' => hash('sha256', '777888'),
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Attempt bypass using universal demo OTP '123456' on a real student account
        $response = $this->withSession(['mfa_user_id' => $realStudent->id])
            ->post(route('login.mfa.verify'), [
                'code' => '123456',
            ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse(auth()->check(), 'Real student account should never bypass MFA with demo OTP.');
    }
}
