# A.E.G.I.S. Project Blueprint & Architectural Roadmap

## 1. Overview & Purpose
**A.E.G.I.S.** (Academic Evaluation & Grant Integrity System) is the official scholarship management and integrity auditing portal for the Central Luzon State University (CLSU) Office of Student Affairs (OSA).

The system streamlines scholarship applications, automated grade sheet (GWA) integrity checking via AI OCR scanning, student stipend tracking, and multi-tiered admin reviews (OSA Staff & Director).

---

## 2. Implemented Features & Technical Architecture

### Core Stack & Performance Pipeline
- **Backend Framework**: Laravel 12 (PHP 8.4+ with Bytecode OPcache)
- **Response Compression**: `GzipResponse` Middleware (80-85% payload size reduction, 1-Year Immutable Browser Asset Caching)
- **Frontend Architecture**: Blade Templating Engine, Vite Asset Pipeline, Bootstrap 5, FontAwesome 6, Tailwind CSS v3, Vanilla JavaScript
- **Databases**: SQLite (`database/database.sqlite` for local dev), PostgreSQL / Supabase (Staging & Production)
- **Security & Compliance**: Custom MFA (6-digit OTP), 30-Day Trusted Device Tokens (bound to UA hash), Column-Level AES Encryption at rest for student financial data, CSP & Security Headers Middleware.

---

## 3. Completed Fixes & Branding Enhancements

### Summary of Recent Enhancements
1. **Dynamic MFA Timer Persistence ([AuthController.php](file:///f:/aegis-capstone/app/Http/Controllers/AuthController.php), [mfa_verify.blade.php](file:///f:/aegis-capstone/resources/views/auth/mfa_verify.blade.php))**:
   - Fixed timer resetting back to 10:00 / 60s upon browser refresh by dynamically calculating remaining seconds from database timestamp (`otp_expires_at`) and session creation timestamp (`mfa_sent_at`).
2. **Resolved MFA Syntax Error ([mfa_verify.blade.php](file:///f:/aegis-capstone/resources/views/auth/mfa_verify.blade.php))**:
   - Fixed `ParseError: syntax error, unexpected token "\"` on line 231 by correcting quote syntax inside Blade `{{ route("login") }}` tags.
3. **Gmail Message Clipping Fix ([layout.blade.php](file:///f:/aegis-capstone/resources/views/emails/layout.blade.php), [logo-email.png](file:///f:/aegis-capstone/public/logo-email.png))**:
   - Served lightweight 15.4KB HTTPS PNG asset instead of 450KB inline Base64 text, keeping email size under 5KB so Gmail renders full email cards without clipping.
4. **1Mbps High-Speed Optimization & Core Web Vitals LCP Fix ([Dockerfile](file:///f:/aegis-capstone/Dockerfile), [GzipResponse.php](file:///f:/aegis-capstone/app/Http/Middleware/GzipResponse.php))**:
   - Enabled Gzip compression, OPcache bytecode caching, and native font fallbacks, fixing the 8.14s LCP bottleneck for slow network connections.
