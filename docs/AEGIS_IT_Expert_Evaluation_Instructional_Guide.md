# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. IT EXPERT TECHNICAL EVALUATION & INSTRUCTIONAL GUIDE
### System Architecture, Security Hardening, and ISO/IEC 25010:2023 Product Quality Assessment

- **Project Title:** A.E.G.I.S: AI-Enhanced Grant Information System with Document Forensics and Automated Notification for the Office of Student Affairs
- **Document Code:** `CLSU-CEn-DIT-AEGIS-GUIDE-IT-2026`
- **Applicable Evaluation Instrument:** `docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx` & `.md`
- **Classification:** Technical Evaluator Instructional Reference

---

## 1. Executive Technical Overview

**A.E.G.I.S.** is an enterprise-grade scholarship management platform built to modernize student financial aid intake, prevent academic document fraud, and maintain statutory auditability for the Central Luzon State University (CLSU) Office of Student Affairs (OSA).

### Core Architectural Stack
- **Web & Application Tier:** Laravel 12 (PHP 8.2+), MVC / Service Layer Architecture, Blade Templating, Vanilla CSS / Tailwind CSS, Bootstrap 5.
- **Forensic AI Tier:** Python 3.11 Flask microservice integrating:
  - Error Level Analysis (ELA) with 95% JPEG quality differential mapping.
  - ResNet-50 Deep Convolutional Neural Network for structural compression artifact classification.
  - SIFT (Scale-Invariant Feature Transform) feature matching for clone-stamp detection.
  - Grad-CAM (Gradient-weighted Class Activation Mapping) for localized explainability heatmaps.
- **Data & Storage Tier:** SQLite 3 (Dev/Staging) / PostgreSQL & MySQL 8.0 (Production), Cloudflare R2 / S3-compatible persistent object storage, AES-256 database column encryption.
- **Statutory & Security Controls:** R.A. 10173 (Data Privacy Act of 2012) compliant, SHA-256 zero-knowledge OTP hashing at rest, immutable admin action logging, role-based authorization gates (`student`, `admin`, `superadmin`).

---

## 2. Technical Evaluation Test Accounts

All evaluation environments include pre-configured test fixtures with pre-seeded datasets:

| Role Tested | Email Address | Password | MFA / OTP Security Behavior | Primary Evaluation Focus |
| :--- | :--- | :--- | :--- | :--- |
| **SuperAdmin / Director** | `director@clsu.edu.ph` *(Alt: `superadmin@clsu.edu.ph`)* | `password` | **Auto-Bypassed** in demo mode | System-wide settings, academic term governance, audit trail verification, user provisioning |
| **Admin / Staff Evaluator** | `admin@clsu.edu.ph` *(Alt: `staff@clsu.edu.ph`)* | `password` | **Auto-Bypassed** in demo mode | Intake queue, AI fraud score & Grad-CAM inspection, manual override decision logic, export logging |
| **Student (Active Records)** | `student@clsu.edu.ph` *(Juan Dela Cruz)* | `password` | **Universal Demo OTP:** `123456` or `000000` | Application tracker, student profile encryption, public catalog discovery |
| **Student (Clean Intake)** | `student_apply@clsu.edu.ph` *(Maria Clara Santos)* | `password` | **Universal Demo OTP:** `123456` or `000000` | 3-step application form, DPA statutory consent checkbox, COG upload validation |

---

## 3. Step-by-Step Technical Verification Protocols (Part 1 UAT Matrix)

The following procedures correspond directly to **Test Scenarios 1 through 10** in the [AEGIS IT Expert Evaluation Form](file:///f:/aegis-capstone/docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx):

### Scenario 1: Authentication Hardening & SQLi / Brute-Force Throttling
1. Navigate to the login route (`/login`).
2. **SQL Injection Test:** In the email input field, input standard SQL injection payloads:
   - `' OR 1=1--`
   - `admin@clsu.edu.ph' UNION SELECT null, null, null--`
   - *Expected Result:* Login safely rejected by Eloquent ORM PDO prepared statements without SQL syntax leakage.
3. **Rate Limiting Test:** Attempt 6 consecutive incorrect password submissions within 60 seconds.
   - *Expected Result:* The application returns an HTTP `429 Too Many Requests` or displays a clear rate-limit restriction ("Too many login attempts. Please try again in X seconds.").
4. **Password Storage:** Inspect database user tables; verify passwords are stored using bcrypt with work factor `cost=12`.

### Scenario 2: Multi-Factor Authentication & Zero-Knowledge OTP Storage
1. Navigate to `/login` and submit student credentials (`student@clsu.edu.ph` / `password`).
2. Observe redirection to `/mfa/verify`.
3. **Database Inspection:** Inspect the `otp_code` column for the user in `users`.
   - *Expected Result:* The code is stored as a 64-character hexadecimal SHA-256 hash (`hash('sha256', $code)`), guaranteeing zero plaintext leakage in database dumps.
4. **Timer & Expiry:** Verify the 10-minute dynamic countdown timer.
5. **OTP Bypass Verification:** Input demo code `123456` or `000000`. Verify successful authentication into `/student/dashboard`.

### Scenario 3: AES-256 Database Column Encryption at Rest
1. Authenticate as SuperAdmin (`director@clsu.edu.ph`).
2. Inspect student records via the database or backend models (`App\Models\StudentProfile`).
3. Verify that sensitive personally identifiable information (PII) including `clsu_id_number`, `contact_number`, and `emergency_contact_number` are encrypted at rest using `AES-256-CBC` via Laravel's `encrypted` model attribute casting.
4. Verify that decrypted values are loaded in-memory only during authorized session lifecycles.

### Scenario 4: Role-Based Access Control (RBAC) & Boundary Isolation
1. Authenticate as Student (`student@clsu.edu.ph`).
2. Attempt direct URL navigation to privileged administrative endpoints:
   - `GET /admin/dashboard`
   - `GET /superadmin/scholarships`
   - `GET /superadmin/users`
   - `GET /superadmin/audit-logs`
   - `GET /superadmin/settings`
3. *Expected Result:* Blocked by `CheckRole` middleware; system responds with HTTP `403 Forbidden` or redirects to unauthorized warning without exposing administrative DOM elements or API payloads.

### Scenario 5: Dual-Pipeline AI Forensic Document Analysis & Explainability
1. Authenticate as Admin (`admin@clsu.edu.ph`) and open an application with an attached Certificate of Grades (COG).
2. Inspect the **AI Forensic Decision Support Card**:
   - Verify the **Fraud Probability Score (0% – 100%)** and color-coded risk badge (Low, Moderate, High Risk).
   - Verify the **Error Level Analysis (ELA)** preview generated at 95% compression threshold.
   - Verify the **Grad-CAM Heatmap Overlay** highlighting anomalous grade fields or modified stamp boundaries.
3. Verify that human administrative authority remains supreme: the officer can approve or reject the application regardless of the AI recommendation with mandatory rationale logging.

### Scenario 6: Tamper-Evident Audit Logging & Statutory Non-Repudiation
1. Perform an administrative state change (e.g., approve an application, modify a scholarship quota, or export records).
2. Navigate to **SuperAdmin > Audit Logs** (`/superadmin/audit-logs`).
3. Verify structured event capture:
   - Timestamp, User Identity, Role, Action Type, IP Address, and User-Agent Hash.
   - Detailed JSON `before` and `after` attribute state diffs.
   - Verify immutable logging for CSV/PDF student data exports.

### Scenario 7: Transport & Session Security Headers
1. Open Browser DevTools (F12) > **Network** tab.
2. Inspect the HTTP Response Headers on any authenticated page:
   - `Strict-Transport-Security: max-age=31536000; includeSubDomains` (HTTPS)
   - `X-Frame-Options: SAMEORIGIN` (Clickjacking mitigation)
   - `X-Content-Type-Options: nosniff` (MIME-sniffing prevention)
   - `Content-Security-Policy` directives
3. Inspect **Application > Cookies**:
   - Session cookie flagged with `HttpOnly`, `Secure`, and `SameSite=Lax`.

### Scenario 8: High-Fidelity 1-Page PDF & Isolated Iframe Print Engine
1. In the admin review screen, trigger **Generate Evaluation Sheet** or **Print Preview**.
2. Inspect the rendered evaluation summary:
   - Exactly formatted to 1-page letter dimensions without overflow or clipped headers.
   - Includes verification checklist, institutional seal, and cryptographic validation QR code.
3. Test print dialog execution via isolated hidden iframe; verify parent DOM remains responsive.

### Scenario 9: Asynchronous Queuing & Fault Recovery
1. Upload a high-resolution COG document during student submission.
2. Verify that heavy OCR and neural network inference are delegated to background job queues (`database` queue driver).
3. Verify exponential retry backoffs (`[15s, 45s, 90s, 180s, 360s]`) to recover gracefully from cold-start AI container timeouts.

### Scenario 10: Portability, Asset Bundling & Eager Loading
1. Verify static asset bundling: Inspect `/public/build/assets/` containing production-compiled CSS and JS bundles.
2. Inspect database query execution: Verify eager loading relationships (`with(['student', 'scholarship', 'documents', 'aiResult'])`) to eliminate N+1 query overhead.

---

## 4. Completing Part 2: ISO/IEC 25010:2023 Product Quality Evaluation

Evaluators are requested to rate the software quality across the eight (8) standard ISO/IEC 25010 characteristics using the standardized 5-point Likert scale:

| Scale Value | Qualitative Rating | Statistical Interpretation Range | Description |
| :---: | :--- | :---: | :--- |
| **5** | **Strongly Agree (SA)** | 4.50 – 5.00 | Technical implementation exceeds standard requirements with robust reliability. |
| **4** | **Agree (A)** | 3.50 – 4.49 | Technical implementation satisfies all technical criteria smoothly. |
| **3** | **Neither Agree nor Disagree (N)** | 2.50 – 3.49 | Meets minimum standards but displays minor latency or design limitations. |
| **2** | **Disagree (D)** | 1.50 – 2.49 | Deficiencies detected that impact efficiency or maintainability. |
| **1** | **Strongly Disagree (SD)** | 1.00 – 1.49 | Critical architectural or security flaws observed. |
| **N/A** | **Not Applicable** | — | Characteristic not observable in the current testing scope. |

### Evaluation Dimensions Evaluated by IT Experts:
1. **Functional Suitability** (Functional completeness, calculation correctness, technical appropriateness).
2. **Performance Efficiency** (Time behavior, resource utilization, concurrent capacity).
3. **Compatibility** (Co-existence in containerized cloud environments, API interoperability).
4. **Usability / Interaction Capability** (Recognizability, technical learnability, input validation, UI consistency).
5. **Reliability** (Fault tolerance, recoverability, regression stability).
6. **Security** (Confidentiality via AES-256, integrity, non-repudiation via audit logs, zero-knowledge MFA).
7. **Maintainability** (Modularity, reusability, testability, clean architecture).
8. **Portability** (Adaptability across browsers and containerized deployment infrastructure).

---

## 5. Submission & Acceptance Endorsement

Upon completing the 10 test scenarios and scoring the ISO 25010 questionnaire:
1. Fill out **Part 3: Acceptance Result & Sign-Off Endorsement** on page 5 of [AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx](file:///f:/aegis-capstone/docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx).
2. Mark the overall decision:
   - `[ ]` **ACCEPTED** — Major required functions and security controls operated satisfactorily.
   - `[ ]` **ACCEPTED WITH MINOR REVISIONS** — Technically sound, subject to minor optimizations.
   - `[ ]` **FOR REVISION AND RETESTING** — Architectural or security defects identified.
3. Affix physical/digital signature, designation, and date.
