# A.E.G.I.S. (Academic Evaluation & Grant Integrity System)

**A.E.G.I.S.** is an enterprise-grade scholarship management and academic integrity auditing platform developed for the **Central Luzon State University (CLSU) Office of Student Affairs (OSA)**. It integrates automated multi-tier administrative workflows with an AI-powered document forensics microservice to identify tampering, calculate Grade Point Averages (GWA), and prevent scholarship fraud.

---

## 🏛️ System Architecture

```mermaid
graph TB
    subgraph Client Layer
        Browser[Modern Web Browser / Mobile PWA]
    end

    subgraph Reverse Proxy & Web Server
        Nginx[Nginx Reverse Proxy & Static Caching]
        FPM[PHP 8.4-FPM Engine with OPcache]
    end

    subgraph Laravel Core Application
        Auth[Authentication & 6-Digit MFA Engine]
        StudentPortal[Student Portal & Multi-Step Application]
        AdminPortal[OSA Staff Review & Triage Studio]
        DirectorPortal[SuperAdmin Analytics & Program Control]
        Queue[Supervised Queue Worker]
    end

    subgraph Data & Cloud Storage
        DB[(PostgreSQL / Supabase Database)]
        R2[(Cloudflare R2 Encrypted Document Vault)]
        Sentry[Sentry Error & Performance Monitoring]
        Brevo[Brevo SMTP / Transactional Mail API]
    end

    subgraph AI Forensics Engine (Port 5000 / HuggingFace)
        SyntaxGate[Pillar 1: Document Syntax Gate]
        OCR[Pillar 2: Tesseract OCR Grade Extraction]
        ELA[Pillar 3: ELA & CNN Tampering Detection]
        EXIF[Pillar 4: Metadata Provenance Inspector]
    end

    Browser -->|HTTPS / Port 443| Nginx
    Nginx -->|FastCGI / Port 9000| FPM
    FPM --> Auth
    Auth --> StudentPortal
    Auth --> AdminPortal
    Auth --> DirectorPortal
    FPM --> DB
    FPM --> R2
    FPM --> Sentry
    FPM --> Brevo
    Queue -->|Asynchronous Scan Request| SyntaxGate
    SyntaxGate --> OCR
    OCR --> ELA
    ELA --> EXIF
    EXIF -->|Fraud Probability & Heatmap| DB
```

---

## 🔒 Security & Compliance Architecture

- **Zero-Backdoor Production Authentication**: Enforces strict two-factor authentication (6-digit OTP via email) with SHA-256 hashed storage, 10-minute expiry windows, and 30-day device fingerprinting bound to User-Agent hashes. Universal demo OTP bypasses (`000000`, `123456`) are strictly disabled in production.
- **Data Privacy Act of 2012 (R.A. 10173) Compliance**: Student bank accounts, contact numbers, and permanent home address fields are encrypted at rest using Laravel's AES-256-CBC cipher with randomized initialization vectors (IVs).
- **Cryptographic Blind Indexing**: Student CLSU ID numbers (`XX-XXXX`) utilize deterministic SHA-256 hashing (`clsu_id_hash`) for fast, collision-resistant database uniqueness checks without exposing plaintext identifiers.
- **OWASP Top 10 Hardened**: CSP, HSTS (`max-age=31536000`), X-Frame-Options (`SAMEORIGIN`), X-Content-Type-Options (`nosniff`), Permissions Policy, and dynamic per-IP/email rate limiting on all endpoints.

---

## ⚙️ Production Deployment Guide

### Option A: Deploy via Docker (Recommended)

The repository provides an enterprise multi-stage [Dockerfile](file:///f:/aegis-capstone/Dockerfile):
- **Stage 1 (Node 20)**: Minifies styles, scripts, and compiles Vite bundles into `public/build/`.
- **Stage 2 (Composer 2)**: Optimizes autoloader and installs production-only PHP dependencies.
- **Stage 3 (PHP 8.4-FPM Alpine)**: Configures Nginx, OPcache, and Supervisord to run the web server and background queue worker simultaneously.

```bash
# 1. Build production image
docker build -t aegis-app:latest .

# 2. Run container with production environment file
docker run -d \
  --name aegis-production \
  -p 10000:10000 \
  --env-file .env.production \
  --restart unless-stopped \
  aegis-app:latest
```

### Option B: Deploy on Render / Supabase

1. Connect your GitHub repository to Render as a **Web Service**.
2. Set Environment to **Docker** (Render uses the root `Dockerfile`).
3. Set Plan to **Starter** or higher (minimum 512MB RAM).
4. Configure all environment variables from [.env.production.example](file:///f:/aegis-capstone/.env.production.example) in the Render Dashboard.
5. In Supabase, copy your PostgreSQL Transaction Pooler connection string (`port 5432` or `port 6543`) into `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.

---

## 📋 Required Production Environment Variables

Ensure all required keys are defined before initiating deployment:

| Variable | Description | Example / Default |
| :--- | :--- | :--- |
| `APP_ENV` | Application environment | `production` |
| `APP_KEY` | 32-character AES application encryption key | `base64:...` (Generate with `php artisan key:generate`) |
| `APP_DEBUG` | Debug mode (MUST be false in production) | `false` |
| `APP_URL` | Canonical HTTPS URL of the application | `https://your-domain.clsu.edu.ph` |
| `ALLOW_DEMO_ACCOUNTS` | Universal OTP bypass lock (MUST be false) | `false` |
| `DB_CONNECTION` | Database engine (`pgsql` for Supabase/Render) | `pgsql` |
| `DB_HOST` | Database host / pooler address | `aws-0-ap-southeast-1.pooler.supabase.com` |
| `DB_PORT` | Database port | `5432` |
| `DB_DATABASE` | Database name | `postgres` |
| `DB_USERNAME` | Database username | `postgres.your_project_ref` |
| `DB_PASSWORD` | Database password | `StrongSecretPassword` |
| `SENTRY_LARAVEL_DSN` | Sentry error tracking DSN | `https://...@sentry.io/...` |
| `MAIL_MAILER` | Mail driver (`brevo_api` or `smtp`) | `brevo_api` |
| `BREVO_API_KEY` | Brevo API key for transactional emails | `xkeysib-...` |
| `MAIL_FROM_ADDRESS`| Verified organizational sender email | `noreply@clsu-aegis.ph` |
| `AEGIS_AI_URL` | AI Microservice endpoint URL | `https://xyoul-aegis-ai.hf.space` |
| `SCHEDULER_KEY` | Secret token protecting cron ping routes | Cryptographic random string |

---

## 🚀 Pre-Deployment Verification Checklist

Before opening the portal to students and evaluators:

1. **Generate App Key**: Run `php artisan key:generate` to produce a fresh encryption key.
2. **Execute Migrations**: Run `php artisan migrate --force` to apply all schema definitions including composite indexes.
3. **Seed Initial Director Account**: Run `php artisan db:seed --class=DatabaseSeeder` to provision initial academic terms and institutional scholarship programs.
4. **Storage Symlink**: Verify public storage access via `php artisan storage:link`.
5. **Warm Production Caches**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
6. **Verify AI Microservice Connectivity**: Navigate to `https://your-domain/settings/ai-status` or execute `curl -I https://your-ai-url/health` to confirm the Python container is awake and responding.

---

## 🧪 Automated Test Suite

A.E.G.I.S. includes comprehensive feature, unit, and policy tests covering authentication, MFA, application lifecycles, and forensic image analysis:

```bash
# Run all feature and unit tests
php artisan test
```

---

## 📄 License & Attribution
Developed for the Central Luzon State University (CLSU) Office of Student Affairs (OSA). All rights reserved.
