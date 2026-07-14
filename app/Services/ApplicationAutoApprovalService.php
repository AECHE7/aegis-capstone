<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Application;
use App\Models\StatusLog;
use App\Models\Setting;
use App\Models\User;
use App\Mail\ApplicationStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ApplicationAutoApprovalService
{
    /**
     * Evaluate and process auto-approval for a given application.
     *
     * @param Application $application
     * @return bool True if auto-approved, false otherwise
     */
    public static function evaluate(Application $application): bool
    {
        // 1. Check if global auto-approval is enabled
        $isEnabled = Setting::get('auto_approval_enabled', '0') === '1';
        if (!$isEnabled) {
            return false;
        }

        // 2. Load relationships
        $application->load(['scholarship.fields', 'documents.aiResult', 'user.profile']);

        // 3. Skip if the scholarship requires custom field uploads (manual verification required)
        if ($application->scholarship->fields->isNotEmpty()) {
            Log::info("Auto-approval skipped for Application ID {$application->id}: Scholarship requires custom fields.");
            return false;
        }

        // 4. Skip if the application has no documents uploaded
        if ($application->documents->isEmpty()) {
            return false;
        }

        // 5. Check if all documents have been scanned successfully and meet the criteria
        $minConfidence = (float) Setting::get('auto_approval_min_confidence', '95.0');
        $maxAnomalies = (int) Setting::get('auto_approval_max_anomalies', '0');
        $fraudThreshold = (float) Setting::get('ai_fraud_threshold', '50.0');

        foreach ($application->documents as $document) {
            $aiResult = $document->aiResult;

            // Skip if document has no scan result yet
            if (!$aiResult) {
                return false;
            }

            // Skip if the scan failed
            if ($aiResult->classification === 'failed') {
                return false;
            }

            // Skip if classification is not authentic
            if (strtolower($aiResult->classification) !== 'authentic') {
                return false;
            }

            // Skip if fraud probability exceeds system settings threshold
            if ($aiResult->fraud_probability >= $fraudThreshold) {
                return false;
            }

            // Safety Guardrail: Calculate confidence score (100 - fraud_probability)
            $confidence = 100.0 - $aiResult->fraud_probability;
            if ($confidence < $minConfidence) {
                return false;
            }

            // Anomaly indicator check: must have no more than max allowed anomalies
            $anomalies = $aiResult->anomaly_indicators ?? [];
            if (count($anomalies) > $maxAnomalies) {
                return false;
            }
        }

        // 6. All checks passed! Proceed with Auto-Approval
        $application->update([
            'status' => 'Approved',
            'remarks' => 'Auto-approved by A.E.G.I.S. Smart Verification Engine.',
            'evaluated_by' => null, // Approved by system
        ]);

        $systemUser = User::where('role', 'superadmin')->first();
        $systemUserId = $systemUser ? $systemUser->id : $application->user_id;

        // Status log
        StatusLog::create([
            'application_id' => $application->id,
            'status' => 'Approved',
            'remarks' => 'Application auto-approved by Smart Verification Engine (Zero anomalies detected).',
            'changed_by' => $systemUserId,
        ]);

        // Audit log
        AuditLoggerService::logAdminAction(
            $systemUserId,
            'system_auto_approved',
            'Application',
            $application->id,
            "Application APP-{$application->id} was automatically verified and approved by the system.",
            '127.0.0.1'
        );

        // Notify student via database notification
        try {
            if ($application->user) {
                $application->user->notify(new \App\Notifications\ApplicationStatusNotification($application));
            }
        } catch (\Exception $e) {
            Log::error('Auto-approval: Failed to send database notification: ' . $e->getMessage());
        }

        // Send approval email notification
        if ($application->user && $application->user->email) {
            $mailSubject = "[A.E.G.I.S.] Official Update: Application APPROVED";
            try {
                $application->load(['user.profile', 'document.aiResult', 'evaluator']);
                Mail::to($application->user->email)->send(new ApplicationStatusMail($application));

                // Log email in EmailLog
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Status updated to: Approved. Remarks: Auto-approved by A.E.G.I.S. Smart Verification Engine.",
                    'status' => 'sent',
                ]);
            } catch (\Exception $e) {
                Log::error('Auto-approval: Failed to deliver email: ' . $e->getMessage());

                // Log failed email
                \App\Models\EmailLog::create([
                    'application_id' => $application->id,
                    'recipient' => $application->user->email,
                    'subject' => $mailSubject,
                    'content' => "Status updated to: Approved. Remarks: Auto-approved by A.E.G.I.S. Smart Verification Engine.",
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
            }
        }

        return true;
    }
}
