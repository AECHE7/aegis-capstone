# A.E.G.I.S. Project Blueprint & Architectural Roadmap

## 1. Overview & Purpose
**A.E.G.I.S.** (Academic Evaluation & Grant Integrity System) is the official scholarship management and integrity auditing portal for the Central Luzon State University (CLSU) Office of Student Affairs (OSA).

The system streamlines scholarship applications, automated grade sheet (GWA) integrity checking via AI OCR scanning, student stipend tracking, and multi-tiered admin reviews (OSA Staff & Director).

---

## 2. Implemented Features & Technical Architecture

### Core Stack
- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Frontend Architecture**: Blade Templating Engine, Vite Asset Pipeline, Bootstrap 5, FontAwesome 6, Tailwind CSS v3, Vanilla JavaScript
- **Databases**: SQLite (`database/database.sqlite` for local dev), PostgreSQL / Supabase (Staging & Production)
- **Security & Compliance**: Custom MFA (6-digit OTP), 30-Day Trusted Device Tokens (bound to UA hash), Column-Level AES Encryption at rest for student financial data, CSP & Security Headers Middleware.

### System Capabilities & Modules
1. **User Authentication & Role Management**:
   - Roles: `student`, `admin` (OSA Staff), `superadmin` (OSA Director).
   - Secure activation token invitation system (`UserInvitation`) for administrative onboarding.
   - **Dedicated Mobile Auth Views**: Touch-optimized Mobile Login & Registration views hiding heavy desktop sidebars on smartphones, featuring 16px iOS-friendly inputs, numeric keypads (`inputmode`), and sticky single-thumb login buttons.
2. **App-Like Mobile Navigation & PWA Shell**:
   - Fixed Bottom Navigation bar (`mobile-nav.blade.php`) for one-thumb screen switching (`d-md-none`).
   - Mobile slide-up Bottom Sheet Drawer component (`mobile-sheet.blade.php`).
   - Web App Manifest (`public/manifest.json`) supporting "Add to Home Screen" standalone app mode.
3. **Scholarship & Application Engine**:
   - Configurable grant programs, academic term management, custom program-specific application fields.
   - Student application submission with direct mobile camera capture (`capture="environment"`) for photo-scanning transcripts.
4. **AI Fraud Detection & Document Verification**:
   - Document upload verification with SHA-256 integrity checksums.
   - AI OCR GWA extractor flagging resolution anomalies, font mismatches, low confidence scores, and grade manipulation trails.
5. **Audit & Compliance System**:
   - System logging (`AuthLog`, `AdminActionLog`, `ConfigChangeLog`, `ExportAccessLog`, `EmailLog`).
   - Reporting and CSV/PDF export capability.

---

## 3. Completed Changes: Dedicated Mobile UI/UX & Render 502 Cold Start Remediation

### Summary of Completed Fixes
1. **Render 502 Bad Gateway Cold Start Fix ([start.sh](file:///f:/aegis-capstone/start.sh))**:
   - Resolved HTTP 502 Bad Gateway during Render container startup after sleep/inactivity.
   - Binds HTTP server listening on port `10000` **immediately** (< 0.5s) upon container wake-up.
   - Offloaded blocking `migrate` and `queue:work` executions to asynchronous background processes, avoiding proxy timeouts.
2. **Dedicated Mobile Login & Register Views**:
   - Implemented full-bleed mobile auth card layout suppressing desktop hero panels on smartphone viewports (`< 768px`).
   - Touch-friendly floating label inputs (`height: 52px`, `font-size: 16px`), single-thumb submit buttons, and mobile brand header.
3. **App-Like Mobile Navigation**:
   - Added fixed bottom navigation bar (`d-md-none`) in [mobile-nav.blade.php](file:///f:/aegis-capstone/resources/views/layouts/mobile-nav.blade.php) for instant tab switching.
4. **PWA Enablement**:
   - Configured [manifest.json](file:///f:/aegis-capstone/public/manifest.json) for iOS & Android standalone installation.
