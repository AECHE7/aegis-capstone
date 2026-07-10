# A.E.G.I.S. Architecture Reference

## 1. The 6-Layer Infrastructure Stack

A.E.G.I.S. targets a **zero-cost, fully free-tier** production deployment. The stack is defined in `.env.example.production`:

```
                  ┌──────────────────────────────────────┐
                  │         Render (Web Service)          │
                  │   PHP CLI Server Workers (x10)        │
                  │   + Background Queue Worker           │
                  └──────────┬───────────────┬────────────┘
                             │               │
              ┌──────────────┘               └──────────────┐
              ▼                                              ▼
┌─────────────────────────┐            ┌──────────────────────────┐
│  Layer 1 & 3: Redis     │            │  Layer 4: Cloudflare R2  │
│  (Upstash Free)         │            │  (10GB Free Tier)        │
│  • Caching              │            │  • Private doc storage   │
│  • Session store        │            │  • CDN URLs             │
│  • Queue backend        │            │  • Local fallback       │
└─────────────────────────┘            └──────────────────────────┘
              ▲
              │
┌─────────────────────────┐            ┌──────────────────────────┐
│  Layer 2: PostgreSQL    │            │  Layer 6: Brevo SMTP     │
│  (Supabase/Neon Free)   │            │  (300 emails/day free)   │
│  • PgBouncer port 6543  │            │  • Approval notifications│
│  • DB_EMULATE_PREPARES  │            │  • MFA OTP delivery     │
│  • SSL required         │            │  • Staff invitations    │
└─────────────────────────┘            └──────────────────────────┘
```

### Layer Details

| Layer | Service | Config Key | Free Tier | Purpose |
|-------|---------|-----------|-----------|---------|
| **1** | Upstash Redis | `REDIS_*` | 10MB | Session storage (`SESSION_DRIVER=redis`) |
| **2** | Supabase/Neon PostgreSQL | `DB_*` (pgsql) | 500MB/0.5GB | Primary database |
| **3** | Upstash Redis | `REDIS_*` | Same as L1 | Cache store (`CACHE_STORE=redis`) + Queue backend (`QUEUE_CONNECTION=redis`) |
| **4** | Cloudflare R2 | `CLOUDFLARE_R2_*` | 10GB | Private document storage (`FILESYSTEM_DISK=r2`) |
| **5** | *(See gap analysis below)* | | | |
| **6** | Brevo SMTP | `MAIL_*` (brevo_api) | 300/day | Transactional email |

## 2. Current Local Dev Configuration

The `.env` file uses an entirely different stack for local development:

| Area | Local (.env) | Production Goal (.env.example.production) |
|------|-------------|-------------------------------------------|
| **Database** | SQLite + WAL mode | PostgreSQL + PgBouncer |
| **Cache** | `database` driver | `redis` driver |
| **Sessions** | `database` driver | `redis` driver |
| **Queue** | `database` driver (SQLite `jobs` table) | `redis` driver |
| **Storage** | `local` disk | `r2` (Cloudflare) |
| **Mail** | `log` driver | Brevo SMTP API |

### Key Config Files

- **`config/database.php`**: All 4 DB drivers configured. PgBouncer compatibility via `DB_EMULATE_PREPARES`. SQLite optimized with `busy_timeout=10000` and `WAL` journal mode.
- **`config/queue.php`**: Supports `database`, `redis`, `sync` drivers. Default is `database`.
- **`config/cache.php`**: Supports `database`, `redis`, `array` stores. Default is `database`.
- **`config/filesystems.php`**: `r2` disk configured as S3-compatible driver. `local` disk is default.
- **`config/sentry.php`**: Sentry Laravel package installed but requires `SENTRY_LARAVEL_DSN` env var to activate.
- **`config/logging.php`**: Stack driver defaults to `single` (daily file logs locally).

## 3. Supporting Infrastructure

### Containerization (Docker)
- **`Dockerfile`**: Laravel production image with PDO PostgreSQL extension, Node/NPM for Vite builds.
- **`docker-compose.yml`**: Orchestrates 4 services:
  - `laravel` — PHP-FPM app server
  - `queue_worker` — Background queue processor
  - `nginx` — Reverse proxy (docker-compose/nginx/aegis.conf)
  - `flask` — Python AI microservice

### Deployment (Render)
- **`start.sh`**: Entrypoint that runs migrations, seeds, config caching, starts queue worker background process, then boots PHP CLI server with 10 workers.
- **`PHP_CLI_SERVER_WORKERS=10`**: Enables concurrent request handling (critical: SSE connections were locking logouts before `--no-reload` flag was added in Phase 34).

### Monitoring & Resilience
- **`/health` endpoint** (`HealthController`): Validates database connectivity and storage status. Used by Render load balancer for health checks.
- **Spatie Backup** (`routes/console.php`): Scheduled daily backup at 2:00 AM + cleanup at 1:00 AM.
- **Sentry**: Error tracing installed (`sentry-laravel` v4.26), captures queue jobs, SQL queries, cache ops as performance spans. Inactive until DSN is configured.
- **Close Expired Scholarships**: Daily cron via `scholarships:close-expired` command.

### CI/CD
- **`.github/workflows/ci.yml`**: GitHub Actions pipeline running on push/PR to `main`:
  - PHP 8.2 + Redis service container
  - Node 20 for Vite asset compilation
  - SQLite test database with `CACHE_STORE=array` and `QUEUE_CONNECTION=sync`
  - Runs `php artisan test --parallel`

## 4. Layer 5 Gap Analysis

The documented layers skip from 4 (R2) to 6 (Brevo). Layer 5 was never formally defined, but evidence from the codebase suggests it maps to **Observability & Monitoring**:

| Candidate | Status | Evidence |
|-----------|--------|----------|
| **Sentry Error Tracking** | Installed, dormant | `composer.json` has `sentry/sentry-laravel`, `config/sentry.php` fully configured, needs DSN |
| **Spatie Backup** | Active | Scheduled in `routes/console.php`, `composer.json` has `spatie/laravel-backup` |
| **Health Check Endpoint** | Active | `GET /health` → `HealthController` |
| **CI Pipeline** | Active | `.github/workflows/ci.yml` runs tests on push |
| **GitHub Actions** | Active | Test runner on `main` branch |

**Recommendation**: Officially designate **Layer 5 as "Observability & Resilience"** covering Sentry (error tracking), Spatie Backup (disaster recovery), the `/health` endpoint (load balancer health checks), and the CI pipeline (quality gate).

## 5. Security Architecture Layers

### Authentication
- **Custom AuthController** (not Laravel Breeze/Jetstream): Login, registration, MFA verification
- **MFA**: 6-digit OTP via email, 10-minute expiry
- **Trusted Device Bypass**: 30-day cookie bound to `user_agent` hash
- **MFA Enforcement Levels**: `all`, `students`, `none` (configurable via settings panel)

### Authorization
- **Roles**: `student`, `admin` (OSA Staff), `superadmin` (OSA Director)
- **Middleware**: `CheckRole` (route-level gates), `CheckUserActive` (deactivated account lockout)
- **Scholarship Staff Assignment**: Admins scoped to specific scholarship programs via `scholarship_staff` pivot

### Data Protection
- **Encryption at Rest**: AES-256 for `student_profiles` (bank details, contact numbers) via Eloquent casts
- **File Uploads**: SHA-256 UUID renaming on upload (anonymized filenames)
- **IDOR Prevention**: Secure document proxy routes (`document.view`, `application-field.file`) verify ownership before streaming

### Request Security
- **SecurityHeaders Middleware**: HSTS, CSP, X-Frame-Options, nosniff headers. Auto-relaxes for Cloud IDE frames in non-production.
- **Rate Limiting**: Throttle on login, registration, password reset (configurable in `web.php`)
- **`.htaccess`**: Script execution prevention in public uploads directory
- **CSRF**: Laravel's built-in CSRF protection on all POST routes

## 6. Audit & Compliance

### Log Tables (Phase 55)
| Table | Captures |
|-------|----------|
| `auth_logs` | Login success/failure, MFA events, device trust actions |
| `admin_action_logs` | Scholarship CRUD, status reviews, setting changes |
| `config_change_logs` | All Setting model value changes with old/new diff |
| `export_access_logs` | Every CSV/PDF download by admin/superadmin |
| `email_logs` | Delivery status, error messages for Brevo sends |
| `status_logs` | Application status transitions with evaluator attribution |

### AuditLoggerService
Unified service (`app/Services/AuditLoggerService.php`) wrapping all log creation:
- `logAuth()` → `AuthLog`
- `logAdminAction()` → `AdminActionLog`
- `logConfigChange()` → `ConfigChangeLog`
- `logExportAccess()` → `ExportAccessLog`

## 7. AI Microservice Integration

### Architecture
```
Laravel (ScanDocumentJob) ──HTTP POST──> Flask (aegis-ai/app.py)
                                              │
                                    ┌─────────┴─────────┐
                                    ▼                   ▼
                              ELA Analysis        ResNet-50
                              (compression        (fraud
                               artifacts)         classifier)
                                    │                   │
                                    └─────────┬─────────┘
                                              ▼
                                        Grad-CAM
                                        (heatmap)
                                              │
                                              ▼
                                    JSON Response:
                                    fraud_probability
                                    classification
                                    extracted_gwa
                                    anomaly_indicators
                                    paths.heatmap_path
```

### Key Components
- **`ScanDocumentJob`**: Dispatched on student submission + admin review. Calls AI service async. Stores `AIResult`. Now also triggers `ApplicationAutoApprovalService::evaluate()` on completion.
- **`AIVerificationService`**: Handles HTTP communication with Flask/Hugging Face. Supports `AEGIS_AI_URL` and `AI_SERVICE_URL` env vars.
- **GWA Discrepancy Check**: If extracted GWA differs from declared GWA beyond tolerance, fraud probability is forced to 99.00% (Phase 41).
- **Anomaly Indicators**: JSON field storing AI-detected flags (`font_inconsistency`, `low_res_blur`, `high_edit_probability`, etc.)

## 8. Dev/Prod Parity Risks

| Risk | Impact | Mitigation |
|------|--------|------------|
| **SQLite vs PostgreSQL type casting** | Migrations/tests pass locally but fail in prod | Cast numeric columns explicitly; use DB-agnostic queries |
| **Database-backed queue on SQLite** | Concurrent workers cause `database is locked` | WAL mode + busy_timeout=10000 (Phase 49) |
| **Local file storage** | Uploads lost on Render restart | `CloudStorageService` supports R2 fallback; swap `FILESYSTEM_DISK` in prod |
| **Email via log driver** | MFA/testing emails not deliverable locally | Use Mailtrap or Brevo test API key for local dev |
| **CI uses `QUEUE_CONNECTION=sync`** | Queue job bugs not caught in CI | Manual staging deployment + Render worker test |
| **No Redis in CI** | Cache-dependent features untested with real Redis | Redis service in CI but `CACHE_STORE=array` bypasses it |

## 9. Optimization Opportunities

### Queue: Database → Redis
The SQLite-backed queue is the #1 bottleneck. At any concurrency level above 1 worker:
- SQLite single-writer locks the `jobs` table
- `busy_timeout=10000` (10s) is a band-aid, not a fix
- Upstark Redis free tier (10MB) is sufficient for queue + cache + sessions

### Storage: Local → R2
Render uses ephemeral storage. Uploaded documents are lost on every deploy/restart. R2 disk config is already wired; only `FILESYSTEM_DISK=r2` env var is missing.

### CI Pipeline
Currently only runs on `main` branch. The project uses `staging` as the deployment branch. CI runs on `push` but `staging` pushes bypass it. Consider expanding CI to run on all push branches.

### Sentry Activation
Error tracing is fully configured but gated behind `SENTRY_LARAVEL_DSN`. Activating it would catch production-only PostgreSQL/SQLite divergence bugs early.

## 10. Test Suite Profile

| Metric | Value |
|--------|-------|
| **Total tests** | 162 |
| **Assertions** | 632 |
| **Test groups** | Feature (all), Unit (none currently) |
| **CI driver** | `QUEUE_CONNECTION=sync` (jobs run inline) |
| **CI database** | SQLite |
| **CI cache** | `CACHE_STORE=array` |
| **Key test files** | `ApplicationAssignmentTest`, `ApplicationAutoApprovalTest`, `ComplianceLogTest`, `SystemSettingsTest`, `MfaAuthenticationTest`, `AdminReportTest`, `DocumentScanTest` |
