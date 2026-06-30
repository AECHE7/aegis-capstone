# A.E.G.I.S. — Vibe Code Security & Quality Checklist
> Run through this before every deployment and after every major AI-generated feature.

---

## 🔐 SECTION 1: Authentication & Authorization

### Laravel Sanctum
- [ ] All API routes requiring login are wrapped in `auth:sanctum` middleware
- [ ] No route in `api.php` is accidentally left unauthenticated
- [ ] Tokens are stored securely (httpOnly cookie or secure local storage)
- [ ] Admin routes have a separate middleware check (e.g., `IsAdmin`)

### Authorization (IDOR Prevention)
- [ ] When fetching a document, check `where('user_id', auth()->id())` — never fetch by ID alone
- [ ] When fetching an application, verify it belongs to the logged-in user
- [ ] Admins cannot be created via the public registration endpoint
- [ ] Policy classes (Laravel Policies) are used for resource-level authorization

```php
// ❌ DANGEROUS — any logged-in user can fetch any document
Document::find($request->document_id);

// ✅ SAFE — only fetch documents belonging to the current user
Document::where('id', $request->document_id)
         ->where('user_id', auth()->id())
         ->firstOrFail();
```

---

## 📁 SECTION 2: File Upload Security

- [ ] File MIME type validated using Laravel's `mimes:pdf,jpeg,png,jpg` rule
- [ ] File size validated (e.g., `max:5120` for 5MB cap)
- [ ] File extension validation done alongside MIME (not instead of it)
- [ ] Files are stored using `Storage::putFile()`, never `move()` on raw upload
- [ ] File names are never taken directly from user input — use `$file->hashName()`
- [ ] Uploaded files go to **Supabase Storage private bucket**, not Render's local filesystem
- [ ] No PHP, .sh, or executable file can be uploaded (enforced by MIME + extension check)

```php
// ✅ CORRECT upload validation
$request->validate([
    'document' => 'required|file|mimes:pdf,jpeg,jpg,png|max:5120',
]);
$path = Storage::disk('supabase')->putFile('documents', $request->file('document'));
```

---

## 🛡️ SECTION 3: Laravel-Specific Security

### Mass Assignment
- [ ] Every Eloquent model has `$fillable` explicitly defined
- [ ] No model uses `$guarded = []` (disables all protection)

```php
// ✅ Safe model
class Application extends Model {
    protected $fillable = [
        'user_id', 'scholarship_id', 'status', 'submitted_at'
    ];
}
```

### CSRF Protection
- [ ] All web form routes use `@csrf` blade directive
- [ ] API routes (via Sanctum) are excluded from CSRF via `VerifyCsrfToken` middleware correctly

### SQL Injection
- [ ] No raw `DB::statement()` or `DB::select()` calls with unsanitized user input
- [ ] All queries use Eloquent ORM or parameterized query bindings

```php
// ❌ DANGEROUS
DB::select("SELECT * FROM applications WHERE user_id = $userId");

// ✅ SAFE
Application::where('user_id', $userId)->get();
```

### Sensitive Data in Responses
- [ ] API responses never return `password`, `remember_token`, or internal system fields
- [ ] Models have `$hidden` defined for sensitive columns

```php
protected $hidden = ['password', 'remember_token', 'api_token'];
```

---

## 🤖 SECTION 4: Hugging Face API Integration

- [ ] HF API key is stored only in `.env` / Render environment variables — never in code
- [ ] All HF API calls go through `HuggingFaceService` class — never called raw in a controller
- [ ] API call is inside a Laravel Queue Job (`ScanDocumentJob`) — never synchronous
- [ ] Job has a `failed()` method that logs the failure and updates the scan status
- [ ] Retry logic handles `503 Model is loading` response (wait + retry up to 3x)
- [ ] Timeout is set on the HTTP client call (at least 30 seconds)
- [ ] Confidence threshold is defined as a config value, not hardcoded
- [ ] Every scan result (pass, fail, flagged) is written to `scan_logs` table

```php
// ✅ Correct HF call structure inside a Job
public function handle(HuggingFaceService $hf)
{
    $result = retry(3, function () use ($hf) {
        return $hf->scanDocument($this->documentPath);
    }, 5000); // 5 second wait between retries

    ScanLog::create([
        'document_id'      => $this->document->id,
        'confidence_score' => $result['score'],
        'status'           => $result['score'] >= 0.80 ? 'authentic' : 'flagged',
        'scanned_at'       => now(),
    ]);
}

public function failed(\Throwable $exception)
{
    ScanLog::create([
        'document_id' => $this->document->id,
        'status'      => 'scan_failed',
        'error'       => $exception->getMessage(),
        'scanned_at'  => now(),
    ]);
}
```

---

## 🗄️ SECTION 5: Supabase Configuration

- [ ] Connection is using **port 6543** (PgBouncer pooler), not 5432
- [ ] Row Level Security (RLS) is **enabled** on all tables
- [ ] RLS policies are explicitly written — not left as implicit "deny all"
- [ ] Storage bucket for documents is set to **Private** (not Public)
- [ ] Storage access is done server-side via Laravel, never exposing direct Supabase URLs to users
- [ ] Supabase service role key is only used server-side — never sent to the browser
- [ ] Database backups are configured and tested

---

## 🚀 SECTION 6: Render Deployment

- [ ] `APP_DEBUG=false` in Render environment variables
- [ ] `APP_ENV=production` in Render environment variables
- [ ] `APP_KEY` is set in Render (copy from local `.env`)
- [ ] All credentials are in Render dashboard env vars — not in `Dockerfile` or source code
- [ ] `.env` file is in `.gitignore` (verify with `git check-ignore -v .env`)
- [ ] Production build command includes:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
  - `php artisan migrate --force`
- [ ] **Separate Background Worker** service is running `php artisan queue:work`
- [ ] Render service is on a **paid plan** (not free tier) before going live with real applicants

---

## 🌐 SECTION 7: CORS & API Exposure

- [ ] `config/cors.php` has `allowed_origins` set to your specific frontend domain only
- [ ] `'allowed_origins' => ['*']` is **never** used in production
- [ ] No sensitive endpoints are accessible without authentication
- [ ] Rate limiting is applied to login and document upload endpoints

```php
// config/cors.php
'allowed_origins' => [env('FRONTEND_URL', 'https://your-aegis-app.onrender.com')],
```

---

## 📝 SECTION 8: Vibe Coding Specific Checks

Run these after every significant AI-generated feature:

### Code Understanding Check
- [ ] Every team member can explain what each new controller method does
- [ ] No "mystery code" — if no one can explain it, rewrite it
- [ ] AI-generated comments match what the code actually does

### Dependency Check
- [ ] Every new `composer require` package exists on Packagist
- [ ] Package was last updated within the last 2 years
- [ ] Package has no critical CVEs (check https://packagist.org)

### Consistency Check
- [ ] New code follows the naming conventions in `AEGIS_ARCHITECTURE.md`
- [ ] No duplicate functions doing the same thing in different files
- [ ] New migrations don't conflict with existing schema

### Environment Check
- [ ] All new `env()` calls have a matching variable in `.env` and Render dashboard
- [ ] No new hardcoded URLs, credentials, or magic numbers in code

---

## 🔥 SECTION 9: Pre-Defense Checklist

Before your panel defense:

- [ ] Every team member has reviewed the AI scan logic (threshold, flow, logging)
- [ ] You can explain why ResNet-50 was chosen for document fraud detection
- [ ] You can explain the confidence threshold decision (why X%, not Y%)
- [ ] You can trace a document through the entire system: upload → queue → HF → result
- [ ] The ERD matches the actual database schema exactly
- [ ] The system architecture diagram matches the actual deployed services
- [ ] All `scan_logs` entries have timestamps and human-readable statuses
- [ ] Manual review process for "flagged" documents is documented and working

---

## 📊 Quick Risk Reference

| Risk | Severity | Where It Happens |
|---|---|---|
| IDOR (unauthorized document access) | 🔴 Critical | DocumentController, ApplicationController |
| Exposed HF API key | 🔴 Critical | .env, Render env vars |
| Unvalidated file upload | 🔴 Critical | DocumentController@store |
| Missing RLS policy on Supabase | 🔴 Critical | Supabase dashboard |
| Sync HF API call (no queue) | 🟠 High | Any controller calling HuggingFaceService directly |
| Mass assignment vulnerability | 🟠 High | All Eloquent models |
| APP_DEBUG=true in production | 🟠 High | Render env vars |
| Queue worker not running | 🟠 High | Render Background Worker service |
| No failed() in Job class | 🟡 Medium | ScanDocumentJob |
| Missing scan_log entry | 🟡 Medium | ScanDocumentJob@handle |
| Port 5432 instead of 6543 | 🟡 Medium | config/database.php |
| AI-generated dead code | 🟢 Low | Throughout codebase |

---

*Last updated: June 2026 | A.E.G.I.S. Capstone — CLSU BSIT*
