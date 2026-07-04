# A.E.G.I.S. Capstone Blueprint

This document serves as the single source of truth for the A.E.G.I.S. Capstone project, detailing its purpose, system architecture, implemented design system, current capabilities, and development roadmap.

---

## 1. Project Purpose & Core Capabilities

A.E.G.I.S. (Academic Evaluation & Grade Integrity System) is a full-stack Laravel web application integrated with a deep learning python microservice. It is designed to modernize and secure the scholarship application process for Central Luzon State University (CLSU).

### Core Features
- **Normalized Scholarship Management:** Super Admins can configure scholarships, establish minimum General Weighted Average (GWA) rules, and track real-time analytics.
- **Secure Student Applications:** Students can upload digital Certificates of Grades (COGs) and submit application forms.
- **Deep Learning Forgery Detection:** Integrated python Flask service processes uploaded documents through:
  - **Error Level Analysis (ELA):** Detects digital tampering by identifying compression mismatch anomalies.
  - **ResNet-50 Classifier:** Evaluates ELA outputs to estimate fraud probabilities.
  - **Grad-CAM Heatmaps:** Generates visual heatmaps highlighting exactly where the academic records were edited.
- **Audit Trails & Security:** Ensures all admin evaluations (Approve/Reject) log the specific evaluator, status timestamps, and remarks.

---

## 2. Technical System Architecture

```mermaid
graph TB
    subgraph Laravel Application (Port 8000)
        web[web.php Router] --> auth[Auth Middleware]
        auth --> student[Student Portal: ApplicationController]
        auth --> admin[Admin Portal: AdminController]
        auth --> super[SuperAdmin Portal: SuperAdminController]
        db[(SQLite Database)] <--> LaravelModels[Eloquent Models]
    end

    subgraph Deep Learning Microservice (Port 5000)
        flask[app.py Flask API] --> ela[ELA Preprocessing]
        ela --> cnn[ResNet-50 Model]
        cnn --> gradcam[Grad-CAM Visual Generator]
    end

    admin -->|Synchronous HTTP Post| flask
    flask -->|JSON Results + Heatmap Path| admin
```

---

## 3. Design System & Styling Rules

The front-end is built using **Blade Templating**, **Tailwind CSS**, and **Alpine.js**.

- **Typography:** Uses Google Fonts (Inter / Outfit) for expressiveness and readability.
- **Color Palette:** Curated HSL colors tailored to professional dark modes and dynamic status grids.
- **Shadows & Depth:** Multi-layered drop shadows are applied to layout cards to give them a premium, "lifted" appearance.
- **Visual Micro-animations:** Subtle hover-states on primary/secondary buttons and dashboard top-cards.

---

## 4. Current Implementation Status

All core MVC components are fully operational:
- **Models:** Normalized schemas for `User`, `StudentProfile`, `Scholarship`, `Application`, `Document`, `AIResult`, and `StatusLog`.
- **Database:** Seeded with 30 applications spanning various status flows (Pending, Approved, Rejected) and fraud scores.
- **Controllers & Routing:** Complete authorization structure for Student, Admin, and SuperAdmin roles.
- **Vite compilation:** Frontend build configuration compiled to production assets under `/public/build`.

---

## 5. Optimal Roadmap & Execution Plan

To transition the project from its current MVP setup to a robust, production-ready system, we outline the following sprints:

### Phase 1: Microservice Model Initialization (Immediate) - [COMPLETED]
- **Goal:** Enable the AI analysis backend.
- **Steps:**
  1. Initialize the Python environment in the `aegis-ai` directory.
  2. Execute the `train_model.py` script to generate a synthetic dataset of authentic and tampered Certificates of Grades (COGs), train the ResNet-50 network, and save the binary model as `aegis_resnet50_v1.keras`.
  3. Start the Flask service (`python app.py`) to handle incoming HTTP scan requests.

### Phase 2: Asynchronous Scan Pipeline (Optimal Architecture Choice) - [COMPLETED]
- **Goal:** Prevent the Laravel request loop from blocking while waiting for deep learning inference.
- **Steps:**
  1. Refactor `AdminController@runScan` to dispatch a Laravel queued job (`ScanDocumentJob`).
  2. Configure a local queue worker (e.g. SQLite database driver or Redis).
  3. Implement Alpine.js polling or Laravel Echo (websockets) in the Admin Review interface to dynamically update the UI once the scan results are persisted in `a_i_results`.

### Phase 3: Testing & Code Cleanup - [COMPLETED]
- **Goal:** Ensure code stability and remove old boilerplate code.
- **Steps:**
  1. Remove deprecated default Laravel Breeze tests (`tests/Feature/Auth/*` and `tests/Feature/ProfileTest.php`) since auth is handled by the custom `AuthController`.
  2. Write feature tests covering roles-based dashboard access, scholarship GWA boundary checks, CSV export formats, and the status update audit trail.

### Phase 4: Notifications & Filtered Reporting - [COMPLETED]
- **Goal:** Connect the email notification loop and implement full filtering for CSV and PDF compliance exports.
- **Steps:**
  1. Modify `AdminController@updateStatus` to dispatch the `ApplicationStatusMail` template.
  2. Create full filtering inputs (Search, Scholarship, Status, Year) on the Admin Dashboard queue.
  3. Wire the CSV and PDF export routes to `ReportController` and apply query parameters to support compliance-ready, filtered outputs.
  4. Design a summary statistics section in the PDF output with CLSU letterheads and signature blocks.

### Phase 5: Client Enhancements (Privacy & UAT Integration) - [COMPLETED]
- **Goal:** Secure student data and build integrated UAT evaluation panels for ISO/IEC 25010 compliance.
- **Steps:**
  1. Apply AES-256 Eloquent database casts to `StudentProfile` attributes.
  2. Create UAT feedback table migrations, models, controllers, and routes.
  3. Inject the floating evaluation modal into the master application layout.
  4. Build the UAT ratings summary module on the Super Admin Analytics dashboard.

#### Phase 6: Thesis Alignment & Security Hardening - [COMPLETED]
- **Goal:** Resolve remaining gaps between current codebase and the capstone thesis specifications.
- **Steps:**
  1. **Schema Normalization:** Add `academic_terms` and `email_logs` tables and models. Add audit columns to `status_logs` and `document_type` to `documents`.
  2. **Audit & Log Generation:** Track all status changes in `status_logs` and notifications in `email_logs` in real time.
  3. **SHA-256 UUID Upload Renaming:** Enforce anonymous file renaming on upload to protect student identity at rest.
  4. **Forensics Classification Thresholds:** Align classification score boundaries (`p = 0.50` or 50% threshold) and risk-tier badge calculations with Chapter III diagrams.
  5. **Vertical Timeline UI:** Render dynamic status log transitions on the student dashboard.

### Phase 7: Comprehensive Gap Analysis & UAT Mock Asset Verification - [COMPLETED]
- **Goal:** Analyze the capstone thesis manuscript vs. the codebase, create technical handover documentation, and fix broken image links in seeded UAT data.
- **Steps:**
  1. **Auditing:** Conduct a detailed review comparing Chapter I-III claims to codebase functionality (completed).
  2. **Technical Handover:** Produce a System Administration Manual (`system_admin_manual.md`) outlining hosting, process supervision (Supervisor), queue execution, and model retraining (completed).
  3. **Mock Asset Seeding:** Patch `UatSeeder.php` to copy mock image files automatically, eliminating 404 broken images on the admin evaluation review dashboard (completed).
  4. **Verification:** Validate stability with a complete PHPUnit test run (completed).

### Phase 8: Form Submission Stalling Fix - [COMPLETED]
- **Goal:** Resolve front-end submit button lock preventing student application submission.
- **Steps:**
  1. **Identify Bug:** Located synchronous disabling of the submit button (`btn.disabled = true`), which cancels form submission (completed).
  2. **Remediation:** Introduce a micro-delay (`setTimeout`) in `showLoading()` to allow the native form submission event to fire before the button is disabled (completed).
  3. **Verification:** Verify with feature test suites (completed).

### Phase 9: AI Pipeline Script Alignment - [COMPLETED]
- **Goal:** Align the Python retraining script `train_model.py` with the 70/15/15 split and data augmentation specifications detailed in Chapter III.
- **Steps:**
  1. **Split Refactoring:** Partition synthetic COG data into `train`, `val`, and `test` directories matching a 70/15/15 ratio.
  2. **Data Augmentation:** Configure `rotation_range=15` and `horizontal_flip=True` in `ImageDataGenerator` for the training subset.
  3. **Performance Metrics:** Add evaluation code to output final test accuracy, precision, and recall metrics matching Chapter III targets.

### Phase 10: Python 3.11 Virtual Environment Setup - [COMPLETED]
- **Goal:** Set up a dedicated Python 3.11 virtual environment for the AI microservice to support TensorFlow and Keras.
- **Steps:**
  1. **Initialization:** Create a virtual environment inside `aegis-ai/venv` using the preinstalled Python 3.11 binary at `C:\Python311\python.exe`.
  2. **Install Dependencies:** Upgrade pip and install packages listed in `requirements.txt`.
  3. **Verification:** Confirm that TensorFlow 2.16.1 and Keras 3.3.3 import correctly.

### Phase 11: AI Model Training Execution - [COMPLETED]
- **Goal:** Execute the training script to generate the synthetic COG dataset, train the ResNet-50 network, and save it in the Keras 3-compliant `.keras` format.
- **Steps:**
  1. **Update Code paths:** Modify `train_model.py` and `app.py` to target `aegis_resnet50_v1.keras` instead of `aegis_resnet50_v1.h5` (completed).
  2. **Execution:** Launch `train_model.py` using the Python 3.11 virtual environment under `$env:PYTHONIOENCODING="utf-8"` (completed).
  3. **Monitoring:** Verify training progresses across 10 epochs and achieves the target metric scores (Accuracy >= 90%, Precision/Recall >= 85%) (completed).
  4. **Verification:** Confirm that the output model `aegis_resnet50_v1.keras` is successfully generated, loadable, and evaluate it on the test partition (completed).

### Phase 12: Automatic AI Scan Triggering on Admin Review - [COMPLETED]
- **Goal:** Improve user experience by automatically running the AI forensics scan when an admin opens a student application for evaluation.
- **Steps:**
  1. **Update Controller:** Modify `AdminController@review` to detect if the student application has an uploaded document and has not yet been processed by the AI classifier.
  2. **Trigger Scan:** Auto-create the placeholder `AIResult` with `scanning` classification and dispatch the `ScanDocumentJob` in the background immediately.
  3. **Robust Environment Resolution:** Updated `ScanDocumentJob` and `AIVerificationService` to support both `AEGIS_AI_URL` and `AI_SERVICE_URL` environment variables, with an increased timeout threshold (120 seconds) to handle Hugging Face cold starts.
  4. **Background Queue Execution**: Configured Docker container CMD to launch a background queue worker (`php artisan queue:work &`) alongside the web server, enabling asynchronous execution on single-process free tier servers.
  5. **Verification:** Wrote integration test `test_reviewing_unscanned_application_auto_triggers_scan` in `DocumentScanTest.php` and verified that the queue worker correctly dispatches the scan automatically on page load.

### Phase 13: Student Registration & Staff Management - [COMPLETED]
- **Goal:** Implement secure student self-registration with institutional email verification, and a Superadmin staff invitation/account activation flow.
- **Steps:**
  1. **Configure Model:** Enable `MustVerifyEmail` in the `User` model, configure mass assignability for `email_verified_at`, and set up relationships for invitations.
  2. **Student Signup:** Create registration page (`/register`), verification notice prompt view, and routing. Enforce `@clsu.edu.ph` / `@clsu2.edu.ph` email domain validation in registration request.
  3. **Superadmin Staff Inviting:** Create a migration for `user_invitations`, build the staff list/invite interface (`/superadmin/staff`) on the Superadmin dashboard, and add token-based activation routes (`/activate-account`).
  4. **Email Delivery & Audit Logs:** Dispatch verification links and invitation links using Laravel notifications (`CustomVerifyEmailNotification` and `StaffInvitationNotification`), and record all outgoing emails inside the `email_logs` table (making the `application_id` column nullable to support standalone accounts verification/invitations logging).
  5. **Secure Middleware:** Secure student portal routes using the `verified` middleware. Also, explicitly check and redirect unverified student login attempts directly to the verification notice from the `AuthController@login` controller.
  6. **Testing:** Write feature test suites `tests/Feature/UserRegistrationTest.php` and `tests/Feature/StaffInvitationTest.php`, verifying unverified student redirect locks and successful verified student access.


### Phase 14: Brevo Integration - [COMPLETED]
- **Goal:** Configure the Laravel application to send real emails to CLSU domains using Brevo's REST API to bypass outbound port blocks (587) on Render.
- **Steps:**
  1. Create a custom mail transport `BrevoTransport` targeting `https://api.brevo.com/v3/smtp/email`.
  2. Register the `brevo_api` driver extension in `AppServiceProvider`.
  3. Configure environment variables (`MAIL_MAILER=brevo_api` and `BREVO_API_KEY` containing the Brevo API Key starting with `xkeysib-`).
  4. Perform diagnostic end-to-end tests via `/test-mail` to verify successful HTTP REST delivery.

### Phase 15: Verification Auto-Redirection - [COMPLETED]
- **Goal:** Enable the email verification prompt page to automatically redirect the student once they click the verification link in a new tab.
- **Steps:**
  1. Define a JSON endpoint `/email/verification-status` in `routes/web.php` that returns the logged-in user's verification state.
  2. Implement Javascript polling in `resources/views/auth/verify-email.blade.php` to fetch this endpoint periodically (every 2 seconds) and trigger a redirect to `student.dashboard` once verified.

### Phase 16: PDF Application Form Attachment on Approval - [COMPLETED]
- **Goal:** Dynamically generate a PDF scholarship application form upon approval, and automatically attach it to the student's status update email.
- **Steps:**
  1. Create the PDF Blade layout (`resources/views/emails/application_form_pdf.blade.php`) representing the student's scholarship application form with personal, academic, and forensics verification metadata.
  2. Optimize database preloading in `AdminController@updateStatus` to eager-load `user.profile`, `document.aiResult`, and `evaluator` relations.
  3. Modify the mailable class `ApplicationStatusMail` to render, output, and attach the PDF memory buffer dynamically when the status is updated to "Approved".
  4. Write integration test assertions to verify that the PDF is correctly compiled and attached.

### Phase 17: Student Profile Management - [COMPLETED]
- **Goal:** Allow student applicants to edit their basic personal and academic details, and propagate updates to scholarship forms and approved PDF attachments.
- **Steps:**
  1. Define profile edit routes (`GET /student/profile` and `POST /student/profile`) in `routes/web.php`.
  2. Implement `editProfile()` and `updateProfile()` methods in `ApplicationController.php` with institutional validations (formatted CLSU ID and Philippine contact number).
  3. Create a styled, responsive `profile.blade.php` student dashboard view matching CLSU green/gold HSL aesthetic guidelines.
  4. Null-safe student profile properties in PDF templates and dashboard view tables to prevent property access errors on unpopulated user profile entities.
  5. Add test assertions inside `UserProfileTest.php` validating rendering, validations, database persistence, and profile AES-256 database encryption casts.

### Phase 18: Manuscript Integration - [COMPLETED]
- **Goal:** Programmatically write all implemented features and test results (Chapters IV & V) into the final Word manuscript (`edited-AEGIS.docx`).
- **Steps:**
  1. Expand the drafted chapters in `chapters_4_and_5.md` to detail Student Profile Management, Brevo SMTP configuration, email verification check, and dynamic PDF attachments on approval.
  2. Implement `write_chapters.py` in the `aegis-ai` directory to parse markdown headers, paragraphs, lists, and tables, and insert them before the REFERENCES section.
  3. Format tables and paragraphs programmatically (using double-line spacing and border XML elements).
  4. Execute the integration script, backing up the document first to prevent corruption.
  5. Verify the updated document structure programmatically.

### Phase 19: Accessibility & Auto-Fill Enhancements - [COMPLETED]
- **Goal:** Fix DevTools console warnings related to accessibility (label-association) and autofill compatibility (`autocomplete` attributes).
- **Steps:**
  1. Add autocomplete attributes (`username`, `current-password`, `new-password`, `name`, `email`) to login, register, forgot-password, and staff invitation forms.
  2. Associate inputs with their labels (by applying matching `id` and `for` attributes) in the scholarship application form, UAT feedback modal, and administrative modals (staff/scholarships).
  3. Run the feature test suite to verify no regressions were introduced.

### Phase 20: Mobile Responsiveness & Design Enhancements - [COMPLETED]
- **Goal:** Improve the mobile responsiveness and design of the AEGIS portal views.
- **Steps:**
  1. Add CSS media queries to slide the admin sidebar drawer off-screen and remove margins for the main wrapper on mobile viewports.
  2. Add a mobile hamburger toggler and backdrop overlay to the layout with JS controllers.
  3. Refactor the student navbar to use Bootstrap 5's responsive collapse components.
  4. Adjust headers, flex boxes, and spacing in student dashboard and admin table views to stack vertically.

### Phase 21: Advanced Portal Workflows & Builder Sprint - [COMPLETED]
- **Goal:** Implement application archiving, staff revocation, staff scholarship assignment, database-driven notification box, and dynamic form builders.
- **Steps:**
  1. Database Migrations: Add `is_active` to `users`, `is_archived` to `applications`, generate the standard database `notifications` table, create pivot `scholarship_staff`, and dynamic field tables (`scholarship_fields`, `application_fields`) (completed).
  2. Archiving & Revocation: Add archive/unarchive controller endpoints and deactivation-checks in auth handlers. Add status toggles on Admin and Super Admin dashboards (completed).
  3. Notifications System: Create custom Notification classes for student updates and admin queue updates. Embed notification bell overlays with unread badges into app master layouts (completed).
  4. Staff Assignments: Update staff creation and editing flows to sync active scholarship program pivot rows. Filter admin dashboard queues to only match assigned categories (completed).
  5. Dynamic Forms Builder: Build interactive field creators inside scholarships management view, dynamically validate submissions in `ApplicationController`, and encrypt dynamically stored records using AES-256 casts (completed).

### Phase 22: GAD Student Assistant Application Form PDF Refactoring - [COMPLETED]
- **Goal:** Redesign the dynamic scholarship application form PDF layout to mirror Form ACA.OSA.CDE.F.007.
- **Steps:**
  1. Refactor `resources/views/emails/application_form_pdf.blade.php` to replicate the official layout structure (header, picture box, checkboxes, personal info grids, family, education, availment history, and signature lines).
  2. Implement helper methods inside the template to dynamically map student profile columns and custom form fields while rendering underlines for blank fields.
  3. Keep the A.E.G.I.S. digital forensics audit report section on the form.
  4. Implement `CheckRole` middleware and route restrictions preventing unauthorized students from loading admin/superadmin interfaces.
  5. Add ownership validation to document image and Grad-CAM heatmap endpoints to prevent horizontal data disclosure (IDOR).
  6. Verify compilation and security gates with a complete PHPUnit test run.

### Phase 23: Systematic Security Hardening - [COMPLETED]
- **Goal:** Defend against brute-force rate-limiting, upload folder script executions, and HTTP header injections.
- **Steps:**
  1. Set up throttle limits on login, registration, and forgot-password POST submissions inside `web.php`.
  2. Create a global `SecurityHeaders` middleware to enforce `X-Frame-Options`, nosniff, and a whitelist CSP.
  3. Create an `.htaccess` script execution prevention file in the public uploads directory.
  4. Write `SecurityHardeningTest.php` feature tests to verify headers presence and 429 rate limit triggers.

### Phase 24: Database Query Caching - [COMPLETED]
- **Goal:** Optimize read times for static config data using database query caching and automatic model observers.
- **Steps:**
  1. Refactor `ApplicationController.php` and `SuperAdminController.php` to cache active term and scholarship listings.
  2. Implement `booted()` cache-busting hooks inside `AcademicTerm.php` and `Scholarship.php`.
  3. Write `QueryCachingTest.php` feature tests verifying query caching and automatic cache clearing.
  4. Run PHPUnit test suites.

### Phase 25: DevOps & Production Integration - [COMPLETED]
- **Goal:** Set up continuous integration, disaster recovery backups, and monitoring mechanisms.
- **Steps:**
  1. Upgrade public uploads directory execution block inside `.htaccess`.
  2. Build GitHub Actions CI testing pipeline inside `.github/workflows/ci.yml`.
  3. Install and publish Spatie Laravel Backup and Sentry Laravel packages.
  4. Schedule cleaner and runner backup cron tasks inside `routes/console.php`.

### Phase 26: Docker Compose Containerization - [COMPLETED]
- **Goal:** Bundle Laravel, Queue, Flask, and Nginx reverse proxy services into unified containers.
- **Steps:**
  1. Create Laravel production `Dockerfile`.
  2. Create Python AI microservice `aegis-ai/Dockerfile`.
  3. Setup Nginx reverse proxy routing inside `docker-compose/nginx/aegis.conf`.
  4. Orchestrate all 4 services inside a root `docker-compose.yml` configuration schema.

### Phase 27: Cloud Deployment on Render & Supabase - [COMPLETED]
- **Goal:** Deploy the Laravel web app to Render connected to a Supabase Postgres instance, with the AI microservice hosted on Hugging Face Spaces.
- **Steps:**
  1. Modify root `Dockerfile` to compile the `pdo_pgsql` PHP extension.
  2. Map database schema structure to support pgsql-compliant fields.
  3. Configure environment variables in the Render web service console (pointing to Supabase DB and Hugging Face AI endpoint).
  4. Run migrations and seed data on the live remote database.

### Phase 28: Authentication & Session Security Hardening - [COMPLETED]
- **Goal:** Enforce strict password validation and secure sessions for all users.
- **Steps:**
  1. Implement `CheckUserActive` middleware to force logout users whose accounts have been deactivated.
  2. Configure production-ready password complexity validation rules.
  3. Enable HTTPS-only session cookies dynamically based on the active environment.
  4. Validate and assert all scenarios with a custom feature test suite.

### Phase 29: UI/UX & Load Balancing Enhancements - [COMPLETED]
- **Goal:** Elevate system design with a light/dark mode theme system and support load balancer health monitoring.
- **Steps:**
  1. Add design token variables and theme toggle buttons to admin and student master layouts.
  2. Implement an anti-flash theme script with localStorage persistence.
  3. Add skeleton loaders and glassmorphic card classes.
  4. Create a public `/health` endpoint validating database and storage status.
  5. Assert functional correctness with integration tests.

### Phase 30: Storage Path Alignment, Security Hardening, & UAT Live Preview Upgrades - [COMPLETED]
- **Goal:** Resolve critical storage/security mismatches, implement UAT ISO/IEC 25010 radar visualization, and build a live dynamic form preview.
- **Steps:**
  1. Fix background worker storage path resolution mismatch in `ScanDocumentJob.php` (completed).
  2. Refactor document image source in admin review view (`review.blade.php`) to use the secure document route rather than public asset URLs (completed).
  3. Upgrade the UAT Ratings Card with tooltip definitions and a Chart.js Radar Chart in `analytics.blade.php` (completed).
  4. Build a responsive, real-time Live Form Preview Panel inside `newProgramModal` in `scholarships.blade.php` (completed).
  5. Verify correctness and run the automated PHPUnit test suites (completed).

### Phase 31: Student Application Details Portal & Secure Custom File Downloads - [COMPLETED]
- **Goal:** Build secure application detail sidebars for student dashboards and implement IDOR-protected downloads for custom form files.
- **Steps:**
  1. Eager load custom parameters in `ApplicationController@dashboard` (completed).
  2. Implement an IDOR-protected `/application-field/{id}/file` route and use it for file links (completed).
  3. Restructure `student/dashboard.blade.php` to include the side-by-side details card (completed).
  4. Add feature test checks for route access validation (completed).
  5. Verify execution with the PHPUnit test suite (completed).

### Phase 32: Real-time Event Notifications - [COMPLETED]
- **Goal:** Replace standard 20s interval client polling with Server-Sent Events (SSE) to push status updates and staff notifications in real time.
- **Steps:**
  1. Register `/notifications/stream` SSE stream route in `routes/web.php` (completed).
  2. Implement `streamNotifications` in `AuthController.php` utilizing streamed database checks (completed).
  3. Integrate `EventSource` on the client side in `layouts/app.blade.php` with fallback polling support (completed).
  4. Write `RealtimeNotificationsTest.php` feature verification suite (completed).
  5. Validate implementation using PHPUnit (completed).

### Phase 33: Audit History Log Exports - [COMPLETED]
- **Goal:** Allow superadmin users to download compliance-grade CSV and PDF exports of application status transition logs and email dispatch histories.
- **Core Skills Applied:** Caching (Skill 11), API Gateway/Auth (Skill 7), Message Queue/Streaming (Skill 9).
- **Steps:**
  1. Add `exportAuditCsv`, `exportAuditPdf`, `exportEmailLogCsv`, `exportEmailLogPdf` methods to `ReportController.php`.
  2. Register four audit export routes in `routes/web.php` scoped to `superadmin` middleware.
  3. Add "Audit Log Exports" card with trigger buttons and date range filter inputs to `analytics.blade.php`.
  4. Write `AuditLogExportTest.php` feature verification suite.
  5. Validate implementation using PHPUnit and commit to Git.

### Phase 34: Root View Alignment & Deploy Timeout Fix - [COMPLETED]
- **Goal:** Resolve Render deployment timeouts by adding multi-worker PHP support and establish `/` as the permanent login gateway with active session notices.
- **Core Skills Applied:** Load Balancing & Statelessness (Skill 12), Security (Skill 7).
- **Steps:**
  1. Create a `start.sh` script to boot database migrations, seeding, background queue workers, and run `php artisan serve` with `PHP_CLI_SERVER_WORKERS=10` (completed).
  2. Update `Dockerfile` to configure `start.sh` as the container CMD (completed).
  3. Remove the `Auth::check()` redirect in `AuthController.php` for the `showLogin` method to allow accessing the login gateway directly (completed).
  4. Implement an `@auth` active session banner in `resources/views/auth/login.blade.php` (completed).
  5. Validate via test suite, commit, and push to remote (completed).
  6. Add `--no-reload` flag to `php artisan serve` in `start.sh` to resolve the CLI worker limit warning and enable proper concurrent request handling, preventing SSE connections from locking logout requests (completed).

### Phase 35: AJAX-Enabling the Frontend - [COMPLETED]
- **Goal:** Modernize all forms, status toggles, dashboard filters, and review flows across student, admin, and superadmin views using asynchronous Fetch API (AJAX) to eliminate page reloads.
- **Core Skills Applied:** API Gateway/Auth (Skill 7), Statelessness (Skill 12).
- **Steps:**
  1. Refactor controllers (Admin, SuperAdmin, Application) to return JSON responses for AJAX/JSON requests (completed).
  2. Implement a Blade partial for the admin dashboard application table to allow dynamic asynchronous reloading (completed).
  3. Update dashboard search/filters in `admin/dashboard.blade.php` to fetch table partials and update counts asynchronously (completed).
  4. AJAX-enable status decisions, archiving, and scan triggering in `admin/review.blade.php` (completed).
  5. Convert scholarship program configurations, toggle states, and staff management to Fetch API in superadmin views (completed).
  6. Refactor student application submissions and profile updates to use AJAX Fetch with progress indicators (completed).
  7. Run complete PHPUnit suite to verify backward-compatible HTTP redirects and JSON API responses (completed).





