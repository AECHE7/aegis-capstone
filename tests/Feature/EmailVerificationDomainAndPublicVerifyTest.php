<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\CustomVerifyEmailNotification;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;

class EmailVerificationDomainAndPublicVerifyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_email_verification_url_resolves_to_production_or_current_domain_not_mock(): void
    {
        config(['app.url' => 'https://clsu.osa.scholarship']);

        $user = User::create([
            'name' => 'Calvin Klein Juatchon',
            'email' => 'calvin@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'dpa_consent_at' => now(),
        ]);

        $notification = new class extends CustomVerifyEmailNotification {
            public function getPublicVerificationUrl($notifiable): string
            {
                return $this->verificationUrl($notifiable);
            }
        };

        $url = $notification->getPublicVerificationUrl($user);

        // Assert it does NOT contain the unreachable mock domain
        $this->assertStringNotContainsString('clsu.osa.scholarship', $url);
        // Assert it resolves to the real production domain or current host
        $this->assertStringContainsString('aegis-capstone.onrender.com', $url);
        $this->assertStringContainsString('/email/verify/' . $user->id, $url);
    }

    #[Test]
    public function test_unauthenticated_user_can_verify_email_via_signed_link(): void
    {
        $user = User::create([
            'name' => 'Student Tester',
            'email' => 'student.tester@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student',
            'dpa_consent_at' => now(),
        ]);

        $this->assertFalse($user->hasVerifiedEmail());

        // Generate signed URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        // Click while guest (logged out)
        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
    }
}
