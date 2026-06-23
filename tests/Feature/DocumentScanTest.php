<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Scholarship;
use App\Models\Application;
use App\Models\Document;
use App\Models\AIResult;
use App\Jobs\ScanDocumentJob;
use Illuminate\Support\Facades\Queue;

class DocumentScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_trigger_document_scan_which_dispatches_background_job(): void
    {
        Queue::fake();

        // 1. Create Admin
        $admin = User::create([
            'name' => 'OSA Admin Test',
            'email' => 'admintest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // 2. Create Student User & Scholarship
        $student = User::create([
            'name' => 'Student Test',
            'email' => 'studenttest@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Test Scholarship',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        // 3. Create Application & Document
        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        $document = Document::create([
            'application_id' => $application->id,
            'file_path' => 'test/path.jpg',
            'original_name' => 'test_file.jpg'
        ]);

        // Act: Authenticate Admin and trigger scan POST route
        $response = $this->actingAs($admin)
            ->post(route('admin.scan', $application->id));

        // Assert redirect back
        $response->assertStatus(302);

        // Assert job was pushed
        Queue::assertPushed(ScanDocumentJob::class, function ($job) use ($application) {
            return $job->applicationId === $application->id;
        });

        // Assert placeholder AIResult is scanning
        $this->assertDatabaseHas('a_i_results', [
            'document_id' => $document->id,
            'classification' => 'scanning'
        ]);
    }

    public function test_reviewing_unscanned_application_auto_triggers_scan(): void
    {
        Queue::fake();

        // 1. Create Admin
        $admin = User::create([
            'name' => 'OSA Admin Test',
            'email' => 'admintest2@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        // 2. Create Student & Scholarship
        $student = User::create([
            'name' => 'Student Test 2',
            'email' => 'studenttest2@clsu.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'student'
        ]);

        $scholarship = Scholarship::create([
            'name' => 'Test Scholarship 2',
            'min_gwa_required' => 2.00,
            'status' => 'Active'
        ]);

        // 3. Create Application & Document (WITHOUT AIResult)
        $application = Application::create([
            'user_id' => $student->id,
            'scholarship_id' => $scholarship->id,
            'program_name' => $scholarship->name,
            'gwa' => '1.75',
            'status' => 'Pending'
        ]);

        $document = Document::create([
            'application_id' => $application->id,
            'file_path' => 'test/path2.jpg',
            'original_name' => 'test_file2.jpg'
        ]);

        // Act: Authenticate Admin and open the review screen
        $response = $this->actingAs($admin)
            ->get(route('admin.review', $application->id));

        $response->assertStatus(200);

        // Assert job was pushed automatically
        Queue::assertPushed(ScanDocumentJob::class, function ($job) use ($application) {
            return $job->applicationId === $application->id;
        });

        // Assert placeholder AIResult is scanning
        $this->assertDatabaseHas('a_i_results', [
            'document_id' => $document->id,
            'classification' => 'scanning'
        ]);
    }
}
