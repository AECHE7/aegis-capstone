# A.E.G.I.S. Comprehensive System Analysis Report

**Project:** Academic Evaluation & Grant Integrity System (A.E.G.I.S.)  
**Institution:** Central Luzon State University (CLSU) — Office of Student Affairs (OSA)  
**Repository:** `https://github.com/AECHE7/aegis-capstone`  
**Current branch:** `staging`  
**Report date:** 2026-07-20  

---

## 1. Executive Summary

A.E.G.I.S. is a **production-oriented capstone scholarship portal** that digitizes CLSU’s student grant application lifecycle. It combines:

1. A **Laravel 12** multi-role web portal (student / OSA staff / director / master)
2. A **Python Flask deep-learning microservice** (ELA + ResNet-50 + Grad-CAM) for Certificate of Grades (COG) forgery detection
3. **Compliance-grade audit logging**, MFA, encrypted PII, and free-tier cloud infrastructure targeting Render + Supabase + Cloudflare R2 + Brevo

The system is **feature-complete for its intended UAT/staging scope**: applications, AI scans, auto-approval, load-balanced staff assignment, renewals, announcements, master privilege transfer, and extensive CSV/PDF audit exports. Recent commits focus on MFA bypass for dummy accounts, master role switching, accessibility (WCAG 2.2 AA), and security remediation.

---

## 2. Problem Statement & Purpose

| Pain point | A.E.G.I.S. response |
|---|---|
| Manual COG review is slow and error-prone | Async AI pipeline with fraud probability + heatmaps |
| Grade sheet forgery risk | ELA + CNN classification + GWA discrepancy hard-fail |
| Fragmented scholarship workflows | Normalized programs, custom fields, staff scoping |
| Weak audit trails | Six compliance log tables + unified `AuditLoggerService` |
| Staff onboarding friction | Token-based invitations with expiration |
| Sensitive student data | AES-256 column encryption + anonymized file names |

### Primary users

| Role | Who | Main capabilities |
|---|---|---|
| `student` | CLSU students (`@clsu.edu.ph` / `@clsu2.edu.ph`) | Apply, upload docs, renew, forfeit, profile |
| `admin` | OSA Staff | Review queue, run AI scans, approve/reject, bulk actions, announcements |
| `superadmin` | OSA Director | Scholarships, staff, analytics, settings, trash, audit exports, broadcast |
| **Master** | System owner (email in `settings.master_email`) | Role impersonation + secure privilege transfer |

---

## 3. Technology Stack

### 3.1 Application layer

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Blade, Tailwind CSS 3, Alpine.js, Bootstrap 5 (hybrid), Vite 7 |
| Auth | Custom `AuthController` + Breeze leftovers for registration/password |
| PDF | barryvdh/laravel-dompdf |
| Backup | spatie/laravel-backup |
| Errors | sentry/sentry-laravel (installed; DSN-gated) |

### 3.2 AI microservice (`aegis-ai/`)

| Component | Technology |
|---|---|
| API | Flask 3.0.3 + Gunicorn |
| Model | TensorFlow 2.16 / Keras 3.3 — ResNet-50 (`aegis_resnet50_v1.keras`) |
| Vision | OpenCV, Pillow |
| Optional storage | Cloudinary for persistent heatmaps |
| Fallback | Simulation mode if TensorFlow/model missing |

### 3.3 Data & infrastructure (6-layer free-tier target)

| Layer | Service | Purpose |
|---|---|---|
| 1 & 3 | Upstash Redis | Sessions, cache, queue |
| 2 | Supabase/Neon PostgreSQL | Primary DB (local: SQLite) |
| 4 | Cloudflare R2 | Private document storage |
| 5 | Observability | Sentry, Spatie Backup, `/health`, CI |
| 6 | Brevo SMTP | MFA OTP, status mail, invitations |

### Local vs production parity

| Concern | Local | Production goal |
|---|---|---|
| Database | SQLite + WAL | PostgreSQL + PgBouncer |
| Cache / session / queue | `database` | `redis` |
| Storage | `local` | `r2` (+ hourly sync job) |
| Mail | `log` | Brevo API |

---

## 4. System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│  Browser (Student / Admin / Director / Master)              │
└───────────────────────────┬─────────────────────────────────┘
                            │ HTTPS
┌───────────────────────────▼─────────────────────────────────┐
│  Nginx / PHP CLI workers (Render) — Laravel 12              │
│  Controllers → Services → Eloquent → Jobs                   │
│  Middleware: SecurityHeaders, CheckRole, CheckUserActive    │
└──────────┬──────────────────────────────┬───────────────────┘
           │                              │
           ▼                              ▼
   ┌───────────────┐            ┌─────────────────────────────┐
   │ PostgreSQL /  │            │ Queue Worker                │
   │ SQLite        │            │ ScanDocumentJob             │
   │ Redis (prod)  │            │ BroadcastAnnouncementEmail  │
   └───────────────┘            │ SendBulkStatusEmail         │
                                └────────────┬────────────────┘
                                             │ HTTP POST /analyze-document
                                ┌────────────▼────────────────┐
                                │ Flask AI (port 5000)        │
                                │ ELA → ResNet-50 → Grad-CAM  │
                                └─────────────────────────────┘
```

### Docker Compose (local orchestration)

- `laravel` — PHP-FPM app
- `queue_worker` — `php artisan queue:work`
- `ai_service` — Python microservice
- `webserver` — Nginx reverse proxy on port 8000

### Deploy entrypoint (`start.sh`)

1. `migrate --force`
2. Idempotent `db:seed`
3. Config/route/view cache
4. Background `queue:work`
5. `php artisan serve` with **10 CLI workers** and `--no-reload`

---

## 5. Domain Model & Database

**21 Eloquent models · 42 migrations**

### Core entities

```
User ──1:1── StudentProfile (encrypted clsu_id, contact)
  │
  ├──1:N── Application ──N:1── Scholarship ──N:M── User (staff pivot)
  │              │                    │
  │              │                    └── ScholarshipField (custom form defs)
  │              ├── Document ──1:1── AIResult
  │              ├── ApplicationField (responses)
  │              ├── StatusLog
  │              └── EmailLog
  │
  ├── UserInvitation, UserMfaDevice
  └── AuthLog / AdminActionLog / ...

AcademicTerm (exactly one is_active)
Setting (key-value, forever-cached)
Announcement, UatFeedback, MasterTransfer
```

### Application lifecycle

Statuses (`ApplicationStatus` enum):

`Pending` → `Under Review` → `Approved` | `Rejected` | `Cancelled`

Additional flags: soft deletes, archive, renewal (`previous_application_id`), forfeit reason, `assigned_to` staff.

### Key configurable settings

| Key | Role |
|---|---|
| `mfa_enforcement` | `all` / `students` / `none` |
| `ai_fraud_threshold` | Fraud % flag threshold (default 50) |
| `gwa_discrepancy_tolerance` | Declared vs extracted GWA (default 0.01) |
| `auto_approval_enabled` | Smart auto-approval switch |
| `auto_approval_min_confidence` | Default 95.0 |
| `auto_approval_max_anomalies` | Default 0 |
| `master_email` | Master account identity |
| `app_logo` | Branding |

---

## 6. Major Subsystems

### 6.1 Authentication & security

- Email/password login with **rate limits** (5/min login, 3/min MFA resend, etc.)
- **MFA:** 6-digit OTP via email, 10-minute expiry
- **Trusted devices:** 30-day cookie bound to User-Agent hash
- **CLSU domain lock** on student registration
- **Email verification** required for student portal
- **Master account:** session-based role switching (`student` / `admin` / `superadmin`) + secure transfer tokens
- Dummy test accounts can bypass MFA (recent feature)
- Encrypted PII on `StudentProfile`
- Upload filenames anonymized (SHA-256 / UUID patterns)
- Document proxy routes prevent IDOR
- `SecurityHeaders`: HSTS, CSP, X-Frame-Options (relaxed in non-prod for Cloud IDE previews)
- Soft-delete authorization guards; emergency DB error pages under lock contention

### 6.2 Student portal

- Multi-step apply form with dynamic scholarship fields
- COG / custom document upload
- Single active application constraints
- Cancel / withdraw / restore / forfeit
- Scholarship renewals (`max_renewals`)
- Profile management + onboarding tour
- In-app notifications

### 6.3 Admin (OSA Staff) review console

- Filtered application queue
- Per-application review with AI scan trigger
- Status updates with remarks + email notification
- Admin notes, archive/unarchive, soft restore
- Bulk actions
- CSV/PDF exports (filtered)
- Announcement board management

Staff are optionally scoped via `scholarship_staff`. New applications are **load-balanced** by pending workload (`ApplicationAssignmentService`). Deactivating staff **re-distributes** open work.

### 6.4 Super Admin (Director)

- Scholarship CRUD + custom fields + min GWA + toggle active
- Staff invite / revoke / reactivate / assign programs
- Analytics dashboard (+ mock seed for demos)
- System trash (applications, scholarships, staff)
- Settings panel (MFA, AI thresholds, auto-approval, logo, security device wipe)
- Email broadcast center
- Extensive audit export suite (auth, admin actions, config, AI scans, evaluations, timelines, uploads, export access, email logs)
- UAT hard-reset (**POST + confirmation token**, blocked in production)

### 6.5 AI forensics pipeline

```
Upload → ScanDocumentJob (5 tries, exponential backoff)
       → Flask /analyze-document
            1. Error Level Analysis (JPEG recompress mismatch)
            2. ResNet-50 on ELA image → fraud_probability
            3. Grad-CAM heatmap of tamper regions
       → Persist AIResult
       → GWA discrepancy check (mismatch → 99% fraud)
       → ApplicationAutoApprovalService::evaluate()
```

**Auto-approval rules (all must pass):**

1. Global setting enabled
2. Scholarship has **no** custom fields
3. All docs scanned, classification authentic
4. Fraud % below threshold
5. Confidence ≥ min confidence
6. Anomaly count ≤ max allowed

### 6.6 Notifications & mail

| Channel | Use |
|---|---|
| Database notifications | In-app status updates |
| `ApplicationStatusMail` | Approve/reject emails |
| `MfaOtpMail` | Login OTP |
| Staff invitation mail | Activation links |
| `AnnouncementMail` | Broadcasts (queued) |
| `MasterTransferMail` | Privilege transfer |
| `EmailLog` | sent/failed + error text |

### 6.7 Storage resilience

- Prefer Cloudflare R2 when configured
- Local fallback with `is_synced` flags
- Hourly `storage:sync-r2` artisan command
- Cloudinary optional for AI heatmaps

### 6.8 Scheduling & ops

| Schedule | Command |
|---|---|
| Daily 01:00 | `backup:clean` |
| Daily 02:00 | `backup:run` |
| Daily | `scholarships:close-expired` |
| Hourly | `storage:sync-r2` |
| On-demand | `/scheduler/run?key=…` (HMAC-safe secret compare) |
| Health | `GET /health` + Laravel `/up` |

---

## 7. Frontend & UX

- **68 Blade views** across auth, student, admin, superadmin, master, emails, errors
- Hybrid styling: Tailwind + Bootstrap badges/components
- Accessibility work (recent): WCAG 2.2 AA — skip link, landmarks, focus-visible, reduced-motion, 44px targets, `aria-live`, table `scope`, fluid typography
- Responsive sidebar/dashboard refinements
- Alpine.js for interactive review/polling UX

---

## 8. Testing & Quality

| Metric | Value |
|---|---|
| Feature test files | **36** |
| Documented suite size | ~162 tests / ~632 assertions (architecture ref) |
| Unit tests | Minimal (`ExampleTest` only) |
| CI | GitHub Actions (PHP 8.2, Node 20, SQLite, `QUEUE_CONNECTION=sync`) |

### Coverage themes (by test file names)

MFA, auth security, staff invites, document scan, grade discrepancy fraud, auto-approval, assignment, bulk actions, compliance logs, exports, cloud storage, emergency recovery, master account, renewals, announcements, settings, query caching, UI health.

### Gaps

- Jobs mostly run **sync** in CI → real queue race conditions under-tested
- Redis path not exercised (`CACHE_STORE=array`)
- Almost no pure unit tests for services
- AI microservice has no dedicated automated test suite in-repo

---

## 9. Security Posture (Strengths & Residual Risks)

### Strengths

- Role middleware + active-user enforcement
- MFA with device binding
- Encrypted student identifiers/contacts
- Rate limiting on auth endpoints
- Audit trail for auth, admin, config, exports, email
- Production block on destructive UAT reset
- Soft-delete authorization hardening
- Scheduler key timing-safe comparison

### Residual risks / technical debt

| Risk | Notes |
|---|---|
| SQLite queue in local/staging | Writer lock under concurrency despite WAL |
| Ephemeral disk on Render | Mitigated only if R2 env is set and sync runs |
| Sentry inactive without DSN | Production errors may go unseen |
| README outdated | Still mentions MySQL; architecture uses PostgreSQL |
| Hybrid frontend stack | Bootstrap + Tailwind increases CSS surface area |
| Master role override via session | Powerful; relies on audit logs + transfer controls |
| AI simulation mode | Filename-based heuristics if model missing — unsafe if used as real verdicts |
| Free-tier limits | Brevo 300/day, Redis 10MB, DB 500MB — capacity planning needed |

---

## 10. Git & Delivery State

**Active branches:** `staging` (current), `production`, `06222026`

### Recent trajectory (newest first)

1. MFA optional/bypass alignment in tests
2. Dummy-account MFA bypass; director invite domain flexibility
3. Idempotent admin seeding
4. Master user + settings in UAT seeder
5. Master role switching + privilege transfer
6. Accessibility phases 1–5
7. Security remediation + cache optimization
8. Emergency recovery / resilience layers
9. Soft-delete auth bypass fix
10. Smart auto-approval + assignment engine (162 tests era)

**Implication:** Project is past MVP; focus has shifted to **governance, accessibility, resilience, and UAT readiness**.

---

## 11. Directory Map (Condensed)

```
aegis-capstone/
├── app/                 Controllers, Models, Services, Jobs, Mail, Middleware
├── aegis-ai/            Flask + ResNet model + datasets + train_model.py
├── database/            42 migrations, seeders, SQLite file
├── resources/views/     Role-based Blade UI
├── routes/web.php       Full route surface
├── tests/Feature/       36 feature suites
├── config/              DB, mail, queue, filesystems, sentry, backup
├── docker-compose.yml   Laravel + queue + AI + nginx
├── start.sh             Render production boot
├── blueprint.md         Product roadmap (many phases marked complete)
└── skills/ + .agents/   Architecture skill for AI-assisted development
```

### Service layer (business logic concentration)

| Service | Responsibility |
|---|---|
| `AIVerificationService` | Sync HTTP scan helper |
| `ApplicationAutoApprovalService` | Post-scan auto-approve engine |
| `ApplicationAssignmentService` | Load-balanced staff assignment |
| `AuditLoggerService` | Unified compliance logging |
| `CloudStorageService` | R2/local storage abstraction |

---

## 12. End-to-End Workflows

### Happy path — student application

1. Register with CLSU email → verify email
2. Complete profile (encrypted fields)
3. Select scholarship + term → upload COG (+ custom fields)
4. Application created as `Pending`; staff auto-assigned
5. Queue runs `ScanDocumentJob`
6. AI returns fraud score/heatmap; GWA cross-check
7. If eligible → auto-approve; else staff reviews
8. Status change emails student; logs written

### Staff review path

1. Admin opens queue (scoped by assignment/scholarship)
2. Opens review → optional re-scan
3. Views heatmap + anomaly indicators
4. Approves/rejects with remarks
5. Email + StatusLog + AdminActionLog

### Director governance path

1. Manage programs and staff invitations
2. Tune AI/MFA/auto-approval settings (ConfigChangeLog)
3. Export audit packs for compliance
4. Broadcast announcements
5. Soft-delete / restore / force-delete via trash

---

## 13. Maturity Assessment

| Dimension | Score (1–5) | Rationale |
|---|---|---|
| Functional completeness | **4.5** | Full scholarship lifecycle + AI + master controls |
| Architecture clarity | **4** | Clear layers; some dual paths (sync AI service vs job) |
| Security | **4** | Strong baseline; free-tier/ops gaps remain |
| Test coverage | **4** | Broad feature tests; weak unit/AI/CI realism |
| DevOps / prod readiness | **3.5** | Scripts exist; parity risks (SQLite vs PG, storage) |
| UX / a11y | **4** | Recent WCAG push; hybrid CSS still complex |
| Documentation | **3.5** | Strong internal skills/refs; README partially stale |
| Observability | **3** | Health + backup ready; Sentry dormant |

**Overall maturity:** **Late-stage capstone / UAT-ready**, approaching production if free-tier infra env vars and monitoring are fully activated.

---

## 14. Recommendations (Priority Order)

1. **Activate production Layer 5** — set `SENTRY_LARAVEL_DSN`; confirm `/health` on Render
2. **Force R2 in staging/prod** — `FILESYSTEM_DISK=r2`; verify hourly sync and heatmaps
3. **Move queue/cache/session to Redis** — eliminate SQLite lock risk under multi-worker load
4. **Expand CI to `staging`/`production` branches** — currently oriented around `main`
5. **Refresh README** — PostgreSQL, roles, master account, auto-approval, Docker, deploy
6. **Harden AI fallback** — never treat simulation classifications as authoritative in non-dev
7. **Add service-level unit tests** for auto-approval, assignment, GWA discrepancy logic
8. **Capacity plan free tiers** — email volume, DB size, Redis memory for real enrollment
9. **Unify frontend stack** long-term (prefer Tailwind-only or Bootstrap-only)
10. **Document operational runbooks** — UAT reset, staff invite, disaster recovery restore from Spatie backup

---

## 15. Conclusion

A.E.G.I.S. is a **coherent, multi-layer scholarship integrity platform** tailored to CLSU OSA operations. Its differentiator is not only workflow automation but **forensic-grade document verification** (ELA + ResNet-50 + Grad-CAM) wired into a real admin review and optional auto-approval loop, backed by compliance logging suitable for institutional audit.

The codebase shows disciplined evolution: from core MVC → async AI → security/MFA → assignment/auto-approval → master governance → accessibility and resilience. Remaining work is primarily **operational hardening** (Redis, R2, Sentry, CI branch coverage, doc accuracy) rather than greenfield feature building.

---

## Appendix A — Key Source Paths

| Area | Path |
|---|---|
| Routes | `routes/web.php` |
| Models | `app/Models/` |
| Services | `app/Services/` |
| Jobs | `app/Jobs/ScanDocumentJob.php` |
| AI microservice | `aegis-ai/app.py` |
| Middleware | `app/Http/Middleware/` |
| Migrations | `database/migrations/` |
| Feature tests | `tests/Feature/` |
| Deploy script | `start.sh` |
| Docker stack | `docker-compose.yml` |
| Architecture skill | `.agents/skills/aegis-architecture/` |
| Product blueprint | `blueprint.md` |

## Appendix B — Compliance Log Tables

| Table | Captures |
|---|---|
| `auth_logs` | Login success/failure, MFA events, device trust actions |
| `admin_action_logs` | Scholarship CRUD, status reviews, setting changes |
| `config_change_logs` | Setting value changes with old/new diff |
| `export_access_logs` | CSV/PDF download operations |
| `email_logs` | Delivery status and error messages |
| `status_logs` | Application status transitions with evaluator attribution |

---

*Generated from full codebase and architecture analysis of the A.E.G.I.S. Capstone project.*
