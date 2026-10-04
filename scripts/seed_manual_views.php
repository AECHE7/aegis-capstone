<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\Scholarship;
use App\Models\AcademicTerm;
use App\Models\Application;
use App\Models\Document;
use App\Models\AIResult;
use App\Models\StatusLog;
use App\Models\Announcement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "[*] Seeding data for Manual & Screenshot generation...\n";

// 1. Ensure Academic Term exists & active
$term = AcademicTerm::firstOrCreate(
    ['semester' => '2nd Semester', 'academic_year' => '2025-2026'],
    ['is_active' => true]
);

// 2. Ensure Scholarships exist
$dost = Scholarship::firstOrCreate(
    ['name' => 'DOST-SEI Merit Scholarship'],
    ['min_gwa_required' => 1.50, 'status' => 'Active', 'max_renewals' => 4, 'description' => 'Merit-based science and engineering scholarship grant.']
);
$univ = Scholarship::firstOrCreate(
    ['name' => 'University Scholar (Institutional)'],
    ['min_gwa_required' => 1.45, 'status' => 'Active', 'max_renewals' => 4, 'description' => 'CLSU institutional top honors scholarship for students with GWA 1.00-1.45.']
);
$coll = Scholarship::firstOrCreate(
    ['name' => 'College Scholar (Institutional)'],
    ['min_gwa_required' => 1.75, 'status' => 'Active', 'max_renewals' => 4, 'description' => 'Deans List collegiate honor scholarship for GWA 1.46-1.75.']
);
$ched = Scholarship::firstOrCreate(
    ['name' => 'CHED Tulong Dunong Program'],
    ['min_gwa_required' => 2.50, 'status' => 'Active', 'max_renewals' => 4, 'description' => 'CHED financial assistance grant for deserving tertiary students.']
);

$allScholarships = [$dost->id, $univ->id, $coll->id, $ched->id];

// 3. Ensure Staff & Director have all scholarships assigned in pivot
$admin = User::firstOrCreate(
    ['email' => 'admin@clsu.edu.ph'],
    ['name' => 'OSA Admin', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()]
);
$admin->update(['email_verified_at' => now(), 'password' => Hash::make('password')]);

$director = User::firstOrCreate(
    ['email' => 'director@clsu.edu.ph'],
    ['name' => 'OSA Director', 'password' => Hash::make('password'), 'role' => 'superadmin', 'email_verified_at' => now()]
);
$director->update(['email_verified_at' => now(), 'password' => Hash::make('password')]);

foreach ([$admin->id, $director->id] as $staffId) {
    foreach ($allScholarships as $schId) {
        DB::table('scholarship_staff')->updateOrInsert(
            ['user_id' => $staffId, 'scholarship_id' => $schId],
            ['created_at' => now(), 'updated_at' => now()]
        );
    }
}
echo "   [✓] Staff & Director assigned to all scholarships in pivot.\n";

// 4. Student 1: Juan Dela Cruz (With Active Application -> for Figure 13 Student Dashboard)
$student1 = User::firstOrCreate(
    ['email' => 'student@clsu.edu.ph'],
    [
        'name' => 'Juan Dela Cruz',
        'password' => Hash::make('password'),
        'role' => 'student',
        'email_verified_at' => now(),
        'dpa_consent_at' => now(),
        'is_active' => true
    ]
);
$student1->update(['email_verified_at' => now(), 'password' => Hash::make('password')]);

StudentProfile::updateOrCreate(
    ['user_id' => $student1->id],
    [
        'clsu_id_number' => '22-1234',
        'college' => 'College of Science',
        'course' => 'BS Information Technology',
        'year_level' => '3rd Year',
        'contact_number' => '09171234567',
        'guardian_name' => 'Maria Dela Cruz',
        'emergency_contact_number' => '09181234567',
        'province' => 'Nueva Ecija',
        'city_municipality' => 'Science City of Muñoz',
        'barangay' => 'Bantug'
    ]
);

// Also create or ensure Application 1 for Juan Dela Cruz
$app1 = Application::withTrashed()->find(1) ?? new Application();
$app1->id = 1;
$app1->user_id = $student1->id;
$app1->scholarship_id = $dost->id;
$app1->academic_term_id = $term->id;
$app1->program_name = 'DOST-SEI Merit Scholarship';
$app1->gwa = 1.45;
$app1->status = 'Under Review';
$app1->remarks = 'Academic records verified by OSA Evaluator. All supporting subject grades meet qualification criteria.';
$app1->admin_notes = 'OCR extracted GWA exactly matches transcript. Ready for final committee approval.';
$app1->evaluated_by = $admin->id;
$app1->assigned_to = $admin->id;
$app1->is_archived = false;
$app1->is_renewal = false;
$app1->dpa_consent_at = now()->subDays(2);
$app1->submitted_after_hours = false;
$app1->deleted_at = null;
$app1->save();

// Ensure sample COG document exists
$doc1 = Document::updateOrCreate(
    ['application_id' => $app1->id],
    [
        'document_type' => 'Certificate of Grades',
        'file_path' => 'documents/sample_cog.jpg',
        'original_name' => 'Official_Certificate_of_Grades_2025.jpg',
    ]
);

// Create sample image file if not on disk so image viewer renders
$sampleImagePath = public_path('documents/sample_cog.jpg');
if (!file_exists(dirname($sampleImagePath))) {
    mkdir(dirname($sampleImagePath), 0755, true);
}
if (!file_exists($sampleImagePath)) {
    // Copy CLSU seal or create placeholder image
    $seal = public_path('images/clsu-seal.png');
    if (file_exists($seal)) {
        copy($seal, $sampleImagePath);
    }
}

// Deep analysis report structure for the 4-pillar forensic framework
$deepReport = [
    'verdict' => 'Authentic Academic Record',
    'overall_fraud_score' => 12.5,
    'pillars' => [
        'syntax_gate' => [
            'status' => 'passed',
            'label' => 'Valid Institutional Transcript / Grade Slip',
            'mime' => 'image/jpeg',
            'resolution' => '2400x3200 (300 DPI High-Res)'
        ],
        'ocr_consistency' => [
            'status' => 'passed',
            'declared_gwa' => 1.45,
            'extracted_gwa' => 1.45,
            'discrepancy' => 0.00,
            'tier' => 'Tier 1 - Verified Exact Match',
            'ocr_engine' => 'Tesseract v5.3.3 + OpenCV Binarization'
        ],
        'compression_forensics' => [
            'status' => 'passed',
            'ela_score' => 0.042,
            'cnn_confidence' => 0.985,
            'model' => 'ResNet-50 Dual-Branch CNN',
            'verdict' => 'Uniform quantization matrix across grade cells'
        ],
        'metadata_provenance' => [
            'status' => 'passed',
            'software' => 'None (Untampered Hardware Scanner Signature)',
            'camera_make' => 'Epson Expression 12000XL Flatbed Scanner',
            'exif_tampered' => false
        ]
    ],
    'visualizations' => [
        'layer_heatmaps' => [
            'ela_detailed' => true,
            'noise_consistency' => true,
            'edge_consistency' => true,
        ]
    ]
];

AIResult::updateOrCreate(
    ['document_id' => $doc1->id],
    [
        'fraud_probability' => 12.50,
        'classification' => 'authentic',
        'detected_software' => 'None (Hardware Scanner Firmware)',
        'deep_analysis_report' => $deepReport,
    ]
);

// Status logs for Pizza Tracker
if (!StatusLog::where('application_id', $app1->id)->where('status', 'Submitted')->exists()) {
    $l1 = new StatusLog();
    $l1->application_id = $app1->id;
    $l1->status = 'Submitted';
    $l1->remarks = 'Online application submitted via student portal.';
    $l1->changed_by = $student1->id;
    $l1->created_at = now()->subDays(2);
    $l1->save();
}
if (!StatusLog::where('application_id', $app1->id)->where('status', 'Under Review')->exists()) {
    $l2 = new StatusLog();
    $l2->application_id = $app1->id;
    $l2->status = 'Under Review';
    $l2->remarks = 'Application assigned to OSA Evaluator for forensic audit.';
    $l2->changed_by = $admin->id;
    $l2->created_at = now()->subHours(4);
    $l2->save();
}

echo "   [✓] Student 1 & Application 1 seeded (Status: Under Review, GWA: 1.45).\n";

// 5. Student 2: Maria Clara Santos (Without Active Application -> for Figure 14 Student Apply Form)
$student2 = User::firstOrCreate(
    ['email' => 'student_apply@clsu.edu.ph'],
    [
        'name' => 'Maria Clara Santos',
        'password' => Hash::make('password'),
        'role' => 'student',
        'email_verified_at' => now(),
        'dpa_consent_at' => now(),
        'is_active' => true
    ]
);
$student2->update(['email_verified_at' => now(), 'password' => Hash::make('password')]);

StudentProfile::updateOrCreate(
    ['user_id' => $student2->id],
    [
        'clsu_id_number' => '23-5678',
        'college' => 'College of Agriculture',
        'course' => 'BS Agriculture',
        'year_level' => '2nd Year',
        'contact_number' => '09179876543',
        'guardian_name' => 'Pedro Santos',
        'emergency_contact_number' => '09189876543',
        'province' => 'Nueva Ecija',
        'city_municipality' => 'Science City of Muñoz',
        'barangay' => 'Villa Santos'
    ]
);
// Delete any applications for student 2 so /apply is completely open
Application::where('user_id', $student2->id)->forceDelete();
echo "   [✓] Student 2 (student_apply@clsu.edu.ph) seeded with NO active applications (ready for /apply).\n";

// 6. Seed Institutional Announcements (for Figure 11 Announcements)
Announcement::firstOrCreate(
    ['title' => 'AY 2025-2026 2nd Semester Scholarship Intake Schedule'],
    [
        'content' => 'The CLSU Office of Student Affairs (OSA) announces that the online scholarship application portal is now accepting submissions for the 2nd Semester. Please ensure your digital Certificate of Grades is clearly scanned before submitting.',
        'author_id' => $admin->id,
    ]
);
Announcement::firstOrCreate(
    ['title' => 'Stipend Payroll Claim Guidelines at OSA Cashier'],
    [
        'content' => 'Approved scholars must download and print their Official Approved Application Form (PDF) bearing the CLSU institutional seal and present their validated University ID card during check release.',
        'author_id' => $admin->id,
    ]
);
Announcement::firstOrCreate(
    ['title' => 'Notice on Image Quality and Tampering Precautions'],
    [
        'content' => 'Students are reminded to upload original camera scans without digital filters or editing software modifications. All uploaded grade slips are automatically processed through the A.E.G.I.S. ResNet-50 Error Level Analysis (ELA) engine.',
        'author_id' => $director->id,
    ]
);
echo "   [✓] Institutional Announcements seeded.\n";

// 7. Seed Additional Applications for Evaluator Queue & Analytics Table
$student3 = User::firstOrCreate(
    ['email' => 'esteban.garcia@clsu.edu.ph'],
    ['name' => 'Esteban Garcia', 'password' => Hash::make('password'), 'role' => 'student', 'email_verified_at' => now()]
);
StudentProfile::firstOrCreate(['user_id' => $student3->id], [
    'clsu_id_number' => '22-4412', 'college' => 'College of Engineering', 'course' => 'BS Civil Engineering',
    'year_level' => '4th Year', 'contact_number' => '09191112233', 'emergency_contact_number' => '09192223344',
    'province' => 'Nueva Ecija', 'city_municipality' => 'Science City of Muñoz', 'barangay' => 'Poblacion'
]);

$app3 = Application::withTrashed()->find(2) ?? new Application();
$app3->id = 2;
$app3->user_id = $student3->id;
$app3->scholarship_id = $univ->id;
$app3->academic_term_id = $term->id;
$app3->program_name = 'University Scholar (Institutional)';
$app3->gwa = 1.25;
$app3->status = 'Approved';
$app3->remarks = 'Dean List First Honors. Approved for full semester stipend.';
$app3->evaluated_by = $admin->id;
$app3->assigned_to = $admin->id;
$app3->is_archived = false;
$app3->deleted_at = null;
$app3->save();

if (!StatusLog::where('application_id', $app3->id)->where('status', 'Approved')->exists()) {
    $l3 = new StatusLog();
    $l3->application_id = $app3->id;
    $l3->status = 'Approved';
    $l3->remarks = 'Official grant approved by OSA Director.';
    $l3->changed_by = $director->id;
    $l3->created_at = now()->subDays(1);
    $l3->save();
}

echo "   [✓] Evaluation records seeded for Analytics table.\n";
echo "[OK] Manual views seeding completed successfully!\n";
