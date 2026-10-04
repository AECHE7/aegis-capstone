<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "A_I_RESULTS columns: " . json_encode(\Illuminate\Support\Facades\Schema::getColumnListing('a_i_results')) . "\n";
foreach (App\Models\User::all() as $u) {
    echo "ID: {$u->id} | Email: {$u->email} | Role: {$u->role} | Name: {$u->name} | Verified: " . ($u->email_verified_at ? 'YES' : 'NO') . "\n";
}

echo "\n=== SCHOLARSHIPS ===\n";
foreach (App\Models\Scholarship::all() as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Status: {$s->status} | Deadline: {$s->deadline}\n";
}

echo "\n=== SCHOLARSHIP STAFF PIVOT ===\n";
$pivot = \Illuminate\Support\Facades\DB::table('scholarship_staff')->get();
foreach ($pivot as $p) {
    echo "User ID: {$p->user_id} | Scholarship ID: {$p->scholarship_id}\n";
}

echo "\n=== APPLICATIONS ===\n";
$apps = App\Models\Application::withTrashed()->with(['documents.aiResult', 'user'])->get();
echo "Total Applications: " . $apps->count() . "\n";
foreach ($apps as $a) {
    echo "ID: {$a->id} | User: " . ($a->user->name ?? 'None') . " (ID: {$a->user_id}) | Program: {$a->program_name} (Sch ID: {$a->scholarship_id}) | Status: {$a->status} | Trashed: " . ($a->trashed() ? 'YES' : 'NO') . " | Docs: " . $a->documents->count() . "\n";
    foreach ($a->documents as $doc) {
        $ai = $doc->aiResult;
        echo "   -> Doc ID: {$doc->id} | Type: {$doc->document_type} | File: {$doc->file_path} | AI Status: " . ($ai ? "Score={$ai->fraud_probability}, Class={$ai->classification}" : "NO AI RESULT") . "\n";
    }
}
