<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('02:00');
Schedule::command('scholarships:close-expired')->daily();
Schedule::command('storage:sync-r2')->hourly();

Artisan::command('storage:sync-r2', function () {
    $this->info('Starting Cloudflare R2 backup synchronization...');

    // 1. Process unsynced documents
    $unsyncedDocs = \App\Models\Document::where('is_synced', false)->get();
    $this->info("Found {$unsyncedDocs->count()} unsynced documents.");

    foreach ($unsyncedDocs as $doc) {
        $localPath = $doc->file_path;
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($localPath)) {
            $this->error("Local file missing for Document ID {$doc->id} at {$localPath}");
            continue;
        }

        try {
            $fileContents = \Illuminate\Support\Facades\Storage::disk('local')->get($localPath);
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->put($localPath, $fileContents);
            if ($path) {
                $baseUrl = config('filesystems.disks.r2.url');
                if (empty($baseUrl)) {
                    $endpoint = config('filesystems.disks.r2.endpoint');
                    $baseUrl = rtrim($endpoint, '/') . '/' . config('filesystems.disks.r2.bucket');
                }
                $r2Url = rtrim($baseUrl, '/') . '/' . $localPath;

                $doc->update([
                    'file_path' => $r2Url,
                    'is_synced' => true
                ]);

                \Illuminate\Support\Facades\Storage::disk('local')->delete($localPath);
                $this->info("Successfully synced Document ID {$doc->id} to R2.");
            }
        } catch (\Exception $e) {
            $this->error("Failed to sync Document ID {$doc->id}: " . $e->getMessage());
        }
    }

    // 2. Process unsynced custom application fields
    $unsyncedFields = \App\Models\ApplicationField::where('is_synced', false)->get();
    $this->info("Found {$unsyncedFields->count()} unsynced application fields.");

    foreach ($unsyncedFields as $field) {
        $localPath = $field->field_value;
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($localPath)) {
            $this->error("Local file missing for ApplicationField ID {$field->id} at {$localPath}");
            continue;
        }

        try {
            $fileContents = \Illuminate\Support\Facades\Storage::disk('local')->get($localPath);
            $path = \Illuminate\Support\Facades\Storage::disk('r2')->put($localPath, $fileContents);
            if ($path) {
                $baseUrl = config('filesystems.disks.r2.url');
                if (empty($baseUrl)) {
                    $endpoint = config('filesystems.disks.r2.endpoint');
                    $baseUrl = rtrim($endpoint, '/') . '/' . config('filesystems.disks.r2.bucket');
                }
                $r2Url = rtrim($baseUrl, '/') . '/' . $localPath;

                $field->update([
                    'field_value' => $r2Url,
                    'is_synced' => true
                ]);

                \Illuminate\Support\Facades\Storage::disk('local')->delete($localPath);
                $this->info("Successfully synced ApplicationField ID {$field->id} to R2.");
            }
        } catch (\Exception $e) {
            $this->error("Failed to sync ApplicationField ID {$field->id}: " . $e->getMessage());
        }
    }

    $this->info('R2 synchronization complete.');
})->purpose('Synchronize failed local fallback file uploads to Cloudflare R2 cloud storage');

Artisan::command('mail:test-broadcast', function () {
    $this->info('Starting mailer connection test broadcast...');

    $emails = \App\Models\User::pluck('email')->toArray();
    $masterEmail = \App\Models\Setting::get('master_email', 'gadianoriel07@gmail.com');
    if ($masterEmail) {
        $emails[] = $masterEmail;
    }

    $uniqueEmails = array_unique(array_map('strtolower', $emails));
    $this->info('Target emails: ' . implode(', ', $uniqueEmails));

    foreach ($uniqueEmails as $email) {
        $this->info("Sending test email to: {$email}...");
        try {
            \Illuminate\Support\Facades\Mail::html(
                '<h1>A.E.G.I.S. Mailer Connection Test</h1><p>This is a test email sent from the A.E.G.I.S. Capstone system to verify mailer connectivity.</p>',
                function ($message) use ($email) {
                    $message->to($email)
                            ->subject('[A.E.G.I.S.] Mailer Connection Test');
                }
            );
            $this->info("Successfully sent to: {$email}");
        } catch (\Exception $e) {
            $this->error("Failed to send to {$email}: " . $e->getMessage());
        }
    }

    $this->info('Mail test broadcast complete.');
})->purpose('Send a test mail to all registered users and the master email to verify mailer connectivity');

