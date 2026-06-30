# A.E.G.I.S. — Project Architecture Context
> **AI-Enhanced Grant Information System**
> Paste this at the start of every AI coding session to maintain consistency.

---

## Project Overview

A.E.G.I.S. is a web-based scholarship management system for the **Office of Student Affairs, Central Luzon State University (CLSU)**. It features AI-powered document fraud detection using a Convolutional Neural Network (ResNet-50) hosted on Hugging Face.

---

## Team

| Role | Name |
|---|---|
| Lead Developer | Noriel S. Gadiano (Yong) |
| Developer | Carillo, John Andrei |
| Developer | Razon, Joshua |
| Adviser | Ma'am Anjela (Louise Gwendolyn Hidalgo) |

---

## Tech Stack (Locked — Do Not Suggest Alternatives)

| Layer | Technology | Notes |
|---|---|---|
| Backend Framework | Laravel 10 | PHP 8.x |
| Database | Supabase (PostgreSQL) | Port 6543 (PgBouncer pooler) |
| File Storage | Supabase Storage | Private bucket only |
| AI Model | Hugging Face — ResNet-50 (CNN) | Document fraud detection |
| Hosting | Render | Dockerfile-based PHP deployment |
| Queue Driver | Database (Laravel) | Jobs stored in Supabase `jobs` table |
| Auth | Laravel Sanctum | Token-based API auth |
| IDE | Antigravity IDE | AI-assisted development |

---

## System Architecture

```
[User / Browser]
      |
      v
[Render — Laravel 10 App]
   |           |
   |           v
   |     [Render Background Worker]
   |       php artisan queue:work
   |           |
   v           v
[Supabase]   [Hugging Face Inference API]
  - PostgreSQL   - ResNet-50 CNN
  - Storage      - Document authenticity scan
  - RLS Enabled
```

### Document Scan Flow (Async)
```
1. User uploads document
2. Laravel validates file (MIME, size)
3. File stored → Supabase Storage (private bucket)
4. ScanDocumentJob dispatched → database queue
5. Response returned: { status: "pending" }

[Background Worker]
6. Job picked up by queue worker
7. File retrieved from Supabase Storage
8. Image preprocessed (224×224, normalized)
9. POST to Hugging Face Inference API (ResNet-50)
10. Confidence score received
11. Result written to `scan_logs` table in Supabase
12. Application record updated with scan status
```

---

## Database Schema (9 Tables)

| Table | Purpose |
|---|---|
| `users` | Applicant and admin accounts |
| `scholarships` | Available scholarship programs |
| `applications` | Student scholarship applications |
| `documents` | Uploaded document metadata + storage path |
| `scan_logs` | AI scan results — confidence score, status, timestamp |
| `requirements` | Per-scholarship document requirements |
| `notifications` | System notifications to users |
| `jobs` | Laravel queue jobs (database driver) |
| `failed_jobs` | Failed queue jobs for inspection |

---

## Naming Conventions

### PHP / Laravel
- **Controllers**: PascalCase, singular — `ApplicationController`, `DocumentController`
- **Models**: PascalCase, singular — `Application`, `Document`, `ScanLog`
- **Jobs**: PascalCase, verb-first — `ScanDocumentJob`, `NotifyApplicantJob`
- **Service classes**: PascalCase, noun+Service — `HuggingFaceService`, `DocumentStorageService`
- **Routes**: kebab-case — `/scholarship-applications`, `/document-scan`
- **Database tables**: snake_case, plural — `scan_logs`, `failed_jobs`
- **Column names**: snake_case — `confidence_score`, `is_authentic`, `scanned_at`

### JavaScript (if any frontend JS)
- camelCase for variables and functions
- PascalCase for components

---

## Key Design Decisions

1. **Queue over sync**: All HF API calls are async via Laravel Queue — never called synchronously in a request lifecycle.
2. **No direct port 5432**: Always use Supabase PgBouncer on port **6543** to avoid connection exhaustion.
3. **Private storage only**: No scholarship document is ever stored in a public Supabase bucket.
4. **Manual review threshold**: Confidence scores between 50–80% are flagged for manual admin review — never auto-rejected.
5. **Audit log mandatory**: Every AI scan result must be written to `scan_logs` with timestamp, document ID, score, and status.
6. **RLS always on**: Supabase Row Level Security is active — all policies must be explicitly defined.

---

## Environment Variables (Keys Only — No Values Here)

```env
# App
APP_NAME=AEGIS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

# Database (Supabase)
DB_CONNECTION=pgsql
DB_HOST=
DB_PORT=6543
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Supabase Storage
SUPABASE_URL=
SUPABASE_KEY=
SUPABASE_STORAGE_BUCKET=

# Hugging Face
HUGGINGFACE_API_KEY=
HUGGINGFACE_MODEL_URL=

# Queue
QUEUE_CONNECTION=database
```

---

## Laravel Artisan Commands Reference

```bash
# Run migrations
php artisan migrate

# Run queue worker (use this on Render Background Worker)
php artisan queue:work --tries=3 --timeout=60

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Retry failed jobs
php artisan queue:retry all

# List failed jobs
php artisan queue:failed
```

---

## What to Always Include When Asking the AI for Code

When prompting the AI in your IDE, always prefix with:

> "This is a Laravel 10 project using Supabase (PostgreSQL, port 6543), Hugging Face Inference API (ResNet-50), and Render for hosting. Auth is via Laravel Sanctum. Queue driver is database. Always use the `HuggingFaceService` class for API calls. Always write scan results to the `scan_logs` table."

---

## Rules for AI-Generated Code (Team Agreement)

- [ ] Never accept code with hardcoded credentials
- [ ] Never accept routes that skip `auth:sanctum` middleware
- [ ] Never accept file uploads without MIME type + size validation
- [ ] Never call the HF API directly in a controller — always via a Job
- [ ] Always check that new migrations don't conflict with existing schema
- [ ] Always verify that Eloquent models have `$fillable` defined (no mass assignment vulnerability)
- [ ] Always add a `failed()` method to every Job class
