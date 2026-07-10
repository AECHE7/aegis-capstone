# A.E.G.I.S. Directory Structure & Folder Context

This document provides a detailed mapping of the directories, sub-folders, and critical files inside the A.E.G.I.S. Capstone project codebase. It helps future AI assistants navigate the repository effectively.

---

## 📂 Project Directory Map

### 1. `app/` — Core PHP Application Code
Contains the business logic, controllers, database models, and service classes.
* **`Console/`**: Custom command definitions.
  * Contains scheduler commands like `CloseExpiredScholarships.php` (checks expired terms and automatically archives outdated applications).
* **`Http/`**: Core request handling layer.
  * **`Controllers/`**: Controller classes routing requests and preparing Blade responses.
    * `AuthController.php`: Logic for login, Multi-Factor Authentication (MFA), trusted device cookies, and rate limiting.
    * `AdminController.php`: Handles staff workflows, application queue management, GWA verification, and custom criteria review.
    * `SuperAdminController.php`: Handles OSA Director controls, UAT resets, settings toggles (like AI fraud thresholds), and audit database tables.
    * `ApplicationController.php`: Manages student-facing submission pipelines, multi-step forms, document attachments, and renewals.
    * `ReportController.php`: Generates descriptive evaluation summaries and triggers CSV/PDF exports.
    * `AnnouncementController.php`: Publishes dashboard bulletins with automatic scheduling.
    * **`Auth/`**: Subfolder with standard Laravel auth handlers for registration, password resets, and email verification.
  * **`Middleware/`**: Request filters.
    * `SecurityHeaders.php`: Injects HSTS, CSP, and clickjacking protection headers (relaxed in development/staging).
    * `CheckUserActive.php`: Automatically force-logs-out users immediately if an administrator deactivates their account.
* **`Mail/`**: PHP classes defining SMTP email templates sent via Brevo.
  * `MfaOtpMail.php`: OTP template containing verification tokens.
  * `StaffInvitationMail.php`: Activation link mail containing registration hashes.
* **`Models/`**: Eloquent database entities.
  * `User.php`: Main account schema containing authentication records and roles.
  * `StudentProfile.php`: Encrypted student identifiers, courses, and bank details.
  * `Application.php`: Tracks student application states, GWA metrics, and reviews.
  * `Document.php`: PDF file metadata, storage paths, upload events, and integrity checksums.
  * `AIResult.php`: Stores AI document scans, confidence levels, and fraud flags.
  * `UserMfaDevice.php`: Stored cookie tokens for trusted 30-day device authentications.
  * `Setting.php`: Key-value registry for global application variables.
  * `AuthLog.php`, `AdminActionLog.php`, `ConfigChangeLog.php`, `ExportAccessLog.php`: Audit trails capturing security compliance.
  * `EmailLog.php`: Diagnostics tracking email delivery failures.

### 2. `bootstrap/` — Framework Bootstrapper
* **`app.php`**: Registers service providers and configures global middleware stacks.
* **`providers.php`**: Lists active Laravel providers.

### 3. `config/` — Framework Configurations
Holds standard configuration arrays for Laravel dependencies:
* `app.php`: Global project settings, name configuration, and fallback locations.
* `auth.php`: Auth guards, user providers, password timeouts, and password resets.
* `database.php`: Connection configuration (SQLite for local development, PostgreSQL for Supabase).
* `session.php`: Cookie settings, secure connection requirements, and session lifetimes.
* `mail.php`: Mailer drivers (configured to use SMTP for Brevo/Sendinblue).
* `filesystems.php`: Local uploads and remote cloud bucket endpoints (R2/S3 storage).

### 4. `database/` — Migration & Seeding Engine
* **`migrations/`**: Chronological files defining table schemas and alterations.
* **`seeders/`**: Population engines.
  * `DashboardMockSeeder.php`: Main mock population script. Creates active academic terms, default settings, scholarships, and 40 simulated student application workflows.

### 5. `public/` — Public Assets
The web server's document root. Holds static logos, default icons (`logo.webp`, `logo.png`), CSS, and compiled JS.

### 6. `resources/` — Templates & Frontend Sources
* **`css/`** & **`js/`**: Source style components and bootstrap scripts.
* **`views/`**: Legacy Blade HTML rendering templates.
  * **`layouts/`**: Base HTML skeletons and wrappers.
    * `sidebar.blade.php`: Collapsible side navigation mapping actions dynamically per user role.
    * `header.blade.php`: Top navigation bar containing user profiles and terms.
  * **`auth/`**: Core security views for sign-in, MFA OTP checks, registration, and resets.
  * **`student/`**: Student portal dashboards, renewal tabs, application steps, and document upload forms.
  * **`admin/`**: Staff review screens, applicant queue tables, and grade sheet check panels.
  * **`superadmin/`**: Director's descriptive charts, settings control panel, and trash bins.
  * **`emails/`**: Rich HTML/CSS email formatting models.

### 7. `routes/` — URL Routing Registry
* **`web.php`**: Registers all web-accessible page routes, middleware bindings, and controller actions.
* **`auth.php`**: Standard Laravel login/logout/registration route definitions.

### 8. `tests/` — Test Suite
* **`Feature/`**: Automated functional tests validating security constraints, MFA actions, logs, audits, and settings changes. Includes:
  * `AuthSecurityEnhancementsTest.php`: Asserts secure cookie flags, deactivation redirects, and session resets.
  * `MfaAuthenticationTest.php`: Tests OTP token generation, verification, and trusted device cookies.
  * `SecurityHardeningTest.php`: Verifies custom security headers, rate-limiting, and unauthorized page blocks.
  * `SystemSettingsTest.php`: Asserts database hard-reset wipes and changes to AI confidence settings.
