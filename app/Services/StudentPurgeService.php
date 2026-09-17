<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Application;
use App\Models\Document;
use App\Models\UserMfaDevice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentPurgeService
{
    /**
     * Completely remove all student user records and their associated
     * applications, documents, custom fields, and notifications.
     * Leaves staff, admin, and director accounts completely intact.
     *
     * @return int Number of student users purged
     */
    public static function purgeAllStudents(): int
    {
        return DB::transaction(function () {
            $studentIds = User::where('role', 'student')->pluck('id');
            $count = $studentIds->count();

            if ($count === 0) {
                return 0;
            }

            // 1. Gather all application IDs submitted by these students
            $appIds = Application::whereIn('user_id', $studentIds)->pluck('id');

            if ($appIds->isNotEmpty()) {
                // Delete physical files and records for documents
                $docs = Document::whereIn('application_id', $appIds)->get();
                foreach ($docs as $doc) {
                    if (!empty($doc->file_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
                        \Illuminate\Support\Facades\Storage::delete($doc->file_path);
                    }
                }

                $docIds = $docs->pluck('id');
                if ($docIds->isNotEmpty()) {
                    if (Schema::hasTable('a_i_results')) {
                        DB::table('a_i_results')->whereIn('document_id', $docIds)->delete();
                    }
                    if (Schema::hasTable('document_ai_results')) {
                        DB::table('document_ai_results')->whereIn('document_id', $docIds)->delete();
                    }
                }

                // Delete document records
                Document::whereIn('application_id', $appIds)->delete();

                // Delete custom application fields
                if (Schema::hasTable('application_fields')) {
                    DB::table('application_fields')->whereIn('application_id', $appIds)->delete();
                }

                // Delete status logs
                if (Schema::hasTable('status_logs')) {
                    DB::table('status_logs')->whereIn('application_id', $appIds)->delete();
                }

                // Delete application status histories
                if (Schema::hasTable('application_histories')) {
                    DB::table('application_histories')->whereIn('application_id', $appIds)->delete();
                }

                // Delete email logs linked to these applications
                if (Schema::hasTable('email_logs')) {
                    DB::table('email_logs')->whereIn('application_id', $appIds)->delete();
                }

                // Force delete applications
                Application::whereIn('id', $appIds)->forceDelete();
            }

            // 2. Delete student profiles
            if (Schema::hasTable('student_profiles')) {
                DB::table('student_profiles')->whereIn('user_id', $studentIds)->delete();
            }

            // 3. Delete MFA devices
            UserMfaDevice::whereIn('user_id', $studentIds)->delete();

            // 4. Delete notifications for students
            if (Schema::hasTable('notifications')) {
                DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->whereIn('notifiable_id', $studentIds)
                    ->delete();
            }

            // 5. Delete trusted devices
            if (Schema::hasTable('user_trusted_devices')) {
                DB::table('user_trusted_devices')->whereIn('user_id', $studentIds)->delete();
            }

            // 6. Delete password reset tokens and student email logs
            $studentEmails = User::whereIn('id', $studentIds)->pluck('email');
            if (Schema::hasTable('password_reset_tokens')) {
                DB::table('password_reset_tokens')->whereIn('email', $studentEmails)->delete();
            }

            if (Schema::hasTable('email_logs')) {
                DB::table('email_logs')->whereIn('recipient', $studentEmails)->delete();
            }

            // 7. Delete auth logs for students
            if (Schema::hasTable('auth_logs')) {
                DB::table('auth_logs')
                    ->whereIn('user_id', $studentIds)
                    ->orWhereIn('email_attempted', $studentEmails)
                    ->delete();
            }

            // 8. Delete student UAT feedback
            if (Schema::hasTable('uat_feedbacks')) {
                DB::table('uat_feedbacks')
                    ->whereIn('user_id', $studentIds)
                    ->orWhere('role', 'student')
                    ->delete();
            }

            // 9. Force delete student users
            User::whereIn('id', $studentIds)->forceDelete();

            return $count;
        });
    }
}
