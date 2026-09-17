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
                // Delete AI results linked to documents
                if (Schema::hasTable('document_ai_results')) {
                    $docIds = Document::whereIn('application_id', $appIds)->pluck('id');
                    DB::table('document_ai_results')->whereIn('document_id', $docIds)->delete();
                }

                // Delete documents
                Document::whereIn('application_id', $appIds)->delete();

                // Delete custom application fields
                if (Schema::hasTable('application_fields')) {
                    DB::table('application_fields')->whereIn('application_id', $appIds)->delete();
                }

                // Delete application status histories
                if (Schema::hasTable('application_histories')) {
                    DB::table('application_histories')->whereIn('application_id', $appIds)->delete();
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

            // 6. Delete password reset tokens if any
            if (Schema::hasTable('password_reset_tokens')) {
                $studentEmails = User::whereIn('id', $studentIds)->pluck('email');
                DB::table('password_reset_tokens')->whereIn('email', $studentEmails)->delete();
            }

            // 7. Force delete student users
            User::whereIn('id', $studentIds)->forceDelete();

            return $count;
        });
    }
}
