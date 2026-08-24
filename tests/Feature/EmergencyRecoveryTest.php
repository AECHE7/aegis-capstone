<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Document;
use App\Models\ApplicationField;
use App\Models\Scholarship;
use App\Models\User;
use App\Services\CloudStorageService;
use App\Jobs\ScanDocumentJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EmergencyRecoveryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function database_outage_gracefully_falls_back_to_login_screen_in_read_only_mode()
    {
        $handler = app(\Illuminate\Contracts\Debug\ExceptionHandler::class);
        $request = \Illuminate\Http\Request::create('/login', 'GET');
        
        $exception = new \Illuminate\Database\QueryException(
            'sqlite',
            'select * from "users" where "id" = ? limit 1',
            [1],
            new \PDOException("SQLSTATE[HY000]: General error: 5 database is locked")
        );

        $response = $handler->render($request, $exception);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Emergency Read-Only Mode', $response->getContent());
        $this->assertStringContainsString('The application database is temporarily offline', $response->getContent());
    }

    #[Test]
    public function scan_document_job_supports_retry_and_exponential_backoff()
    {
        $job = new ScanDocumentJob(1);

        $this->assertEquals(5, $job->tries);
        $this->assertEquals([15, 45, 90, 180, 360], $job->backoff());
    }

    #[Test]
    public function cloud_storage_service_falls_back_to_local_storage_and_returns_unsynced_state_on_r2_exception()
    {
        config([
            'filesystems.disks.r2.key' => 'test-key',
            'filesystems.disks.r2.secret' => 'test-secret',
            'filesystems.disks.r2.bucket' => 'test-bucket',
            'filesystems.disks.r2.endpoint' => 'https://test-endpoint.com',
        ]);

        Storage::fake('local');
        
        $mockDisk = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $mockDisk->shouldReceive('putFileAs')
            ->once()
            ->andThrow(new \Exception("R2 Connection failed"));

        Storage::set('r2', $mockDisk);

        $file = UploadedFile::fake()->create('cog.pdf', 100);
        $isSynced = true;
        $path = CloudStorageService::upload($file, 'uploads', $isSynced);

        $this->assertFalse($isSynced);
        $this->assertStringStartsWith('uploads/', $path);
        Storage::disk('local')->assertExists($path);
    }

    #[Test]
    public function sync_r2_artisan_command_processes_unsynced_files_correctly()
    {
        Storage::fake('local');
        Storage::fake('r2');

        config([
            'filesystems.disks.r2.key' => 'test-key',
            'filesystems.disks.r2.secret' => 'test-secret',
            'filesystems.disks.r2.bucket' => 'test-bucket',
            'filesystems.disks.r2.url' => 'https://r2.test.com',
        ]);

        $localPath = 'uploads/test_unsynced.pdf';
        Storage::disk('local')->put($localPath, 'dummy file content');

        $student = User::factory()->create(['role' => 'student']);
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
            'file_path' => $localPath,
            'original_name' => 'cog.pdf',
            'document_type' => 'COG',
            'is_synced' => false
        ]);

        $this->artisan('storage:sync-r2')
            ->expectsOutput('Starting Cloudflare R2 backup synchronization...')
            ->expectsOutput('Found 1 unsynced documents.')
            ->expectsOutput("Successfully synced Document ID {$document->id} to R2.")
            ->assertExitCode(0);

        $document->refresh();
        $this->assertTrue((bool)$document->is_synced);
        $this->assertStringStartsWith('https://r2.test.com/', $document->file_path);

        Storage::disk('r2')->assertExists($localPath);
        Storage::disk('local')->assertMissing($localPath);
    }
}
