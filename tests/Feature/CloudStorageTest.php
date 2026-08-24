<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Document;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\CloudStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CloudStorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear env values during test
        putenv('CLOUDFLARE_R2_ACCESS_KEY_ID=');
        putenv('CLOUDFLARE_R2_SECRET_ACCESS_KEY=');
        putenv('CLOUDFLARE_R2_BUCKET=');
    }

    #[Test]
    public function it_falls_back_to_local_storage_when_r2_keys_are_missing()
    {
        Storage::fake('local');
        Storage::fake('r2');

        $file = UploadedFile::fake()->create('cog.pdf', 100);
        $path = CloudStorageService::upload($file);

        // Verify it was stored on the local disk
        $this->assertStringStartsWith('uploads/', $path);
        Storage::disk('local')->assertExists($path);
        Storage::disk('r2')->assertMissing($path);
    }

    #[Test]
    public function it_uploads_to_r2_when_credentials_are_provided()
    {
        // Mock config temporarily
        config([
            'filesystems.disks.r2.key' => 'test-key',
            'filesystems.disks.r2.secret' => 'test-secret',
            'filesystems.disks.r2.bucket' => 'test-bucket',
            'filesystems.disks.r2.endpoint' => 'https://test-endpoint.com',
            'filesystems.disks.r2.url' => null,
        ]);

        Storage::fake('local');
        Storage::fake('r2');

        $file = UploadedFile::fake()->create('cog.pdf', 100);
        $url = CloudStorageService::upload($file);

        $this->assertStringContainsString('https://test-endpoint.com/test-bucket/uploads/', $url);

        // Clean up config
        config([
            'filesystems.disks.r2.key' => null,
            'filesystems.disks.r2.secret' => null,
            'filesystems.disks.r2.bucket' => null,
            'filesystems.disks.r2.endpoint' => null,
        ]);
    }

    #[Test]
    public function it_redirects_to_cdn_url_for_remote_documents()
    {
        \Illuminate\Support\Facades\Http::fake([
            'cdn.test.com/*' => \Illuminate\Support\Facades\Http::response('dummy pdf content', 200, ['Content-Type' => 'application/pdf']),
        ]);

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student);

        $scholarship = Scholarship::create([
            'name' => 'DOST Grant',
            'min_gwa_required' => 2.00,
            'status' => 'Active',
        ]);

        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'gwa' => 1.75,
            'status' => 'Pending',
        ]);

        $document = Document::create([
            'application_id' => $application->id,
            'file_path' => 'https://cdn.test.com/uploads/some_hash.pdf',
            'original_name' => 'cog.pdf',
            'document_type' => 'COG'
        ]);

        $response = $this->get(route('document.view', ['id' => $document->id]));

        // Should receive the proxied CDN content
        $response->assertStatus(200);
        $this->assertEquals('dummy pdf content', $response->getContent());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
