# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. IT EXPERT TECHNICAL EVALUATION & INSTRUCTIONAL MANUAL
### Comprehensive Guide for Technical Evaluators, Software Architects, and Security Auditors
#### Evaluation Standard: ISO/IEC 25010:2023 Software Product Quality

- **Project Title:** A.E.G.I.S: AI-Enhanced Grant Information System with Document Forensics and Automated Notification for the Office of Student Affairs
- **Document Code:** `CLSU-CEn-DIT-AEGIS-GUIDE-IT-2026`
- **Companion Evaluation Form:** [docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx](file:///f:/aegis-capstone/docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx)
- **Target Audience:** IT Faculty Members, Software Engineers, Cybersecurity Auditors, System Architects

---

## 1. Executive Technical Architecture

**A.E.G.I.S.** is a mission-critical web platform engineered for Central Luzon State University (CLSU) Office of Student Affairs (OSA). It transitions traditional, manual paper scholarship processing into an automated, tamper-evident digital ecosystem.

### Architectural Stack
- **Web & Backend Framework:** Laravel 12.x running on PHP 8.2+.
- **Database Layer:** SQLite 3 (Dev/Staging) / PostgreSQL & MySQL 8.0 (Production) with AES-256-CBC column-level encryption.
- **Forensic AI Microservice:** Python 3.11 Flask API running dual-pipeline analysis:
  - *Pipeline V1:* Error Level Analysis (ELA) with 95% JPEG quality differential mapping.
  - *Pipeline V2:* ResNet-50 Deep CNN classification + SIFT keypoint clone-stamp detection.
  - *Explainability:* Grad-CAM (Gradient-weighted Class Activation Mapping) localized bounding boxes.
- **Storage Tier:** Cloudflare R2 / S3-compatible persistent object storage with dual-storage Base64 database fallback.
- **Statutory & Security Controls:** Republic Act No. 10173 (Data Privacy Act of 2012) compliant, SHA-256 zero-knowledge OTP hashing at rest, immutable admin action logging, role-based authorization gates (`student`, `admin`, `superadmin`).

---

## 2. System Access & Endpoints Directory

Evaluators may conduct tests on either the live cloud staging deployment or a local developer instance:

| Environment | Purpose / Access Link | Status & Health |
| :--- | :--- | :--- |
| **Live Cloud Staging (Render)** | `https://aegis-capstone.onrender.com` | **Active / Primary Evaluation URL** |
| **Local Development Instance** | `http://localhost:8000` *(or `http://127.0.0.1:8000`)* | Fallback / Local evaluation |
| **System Health Check API** | `https://aegis-capstone.onrender.com/api/health-check` | Returns JSON status, DB latency & queue state |
| **Public Scholarship Catalog** | `https://aegis-capstone.onrender.com/scholarships` | Publicly discoverable without authentication |
| **Direct Application Verification** | `https://aegis-capstone.onrender.com/verify/application/{code}` | Validates tamper-evident cryptographic QR codes |

---

## 3. Complete Test Accounts & Credentials Directory

All designated institutional evaluation accounts are pre-seeded and pre-configured. Use this exact directory for all test scenarios:

| Stakeholder Role | Email Address | Password | MFA / OTP Security Behavior | Key Privileges & Scope |
| :--- | :--- | :--- | :--- | :--- |
| **SuperAdmin / OSA Director** | `director@clsu.edu.ph`<br>*(Alt: `superadmin@clsu.edu.ph`)* | `password` | **Auto-Bypassed** *(Direct login)* | Full system governance, academic term switcher, global settings, audit logs, user management |
| **Master Administrator** | `gadianoriel07@gmail.com` | `password` | **Auto-Bypassed** *(Direct login)* | Master redirection gateway, security policy overrides |
| **OSA Staff / Administrator** | `admin@clsu.edu.ph`<br>*(Alt: `staff@clsu.edu.ph`)* | `password` | **Auto-Bypassed** *(Direct login)* | Intake review queue, document verification, AI fraud inspection, approvals/rejections, CSV/PDF export |
| **Student 1 (Active Records)** | `student@clsu.edu.ph`<br>*(Juan Dela Cruz · ID: 22-1234)* | `password` | **Demo OTP:** `123456` or `000000` | Application status tracker, profile editing, announcements feed, existing COG record |
| **Student 2 (Clean /apply)** | `student_apply@clsu.edu.ph`<br>*(Maria Clara Santos · ID: 23-5678)* | `password` | **Demo OTP:** `123456` or `000000` | Fresh intake testing: 3-step application form, DPA consent checkbox, live COG upload |

> [!NOTE]
> **Universal Demo OTP Bypass**: When logging in as a student (`student@clsu.edu.ph` or `student_apply@clsu.edu.ph`), the system prompts for a 6-digit MFA verification code. In demonstration mode, entering either **`123456`** or **`000000`** immediately validates authentication.

---

## 4. Test Artifacts & Forensic Documents Repository

To evaluate the AI Document Forensics Module (TC-IT-05), download and use these prepared test Certificate of Grades (COG) files:

| Artifact Name | Direct Web Access URL | Local Repository Path | Characteristics |
| :--- | :--- | :--- | :--- |
| **Authentic CLSU COG** | `https://aegis-capstone.onrender.com/documents/sample_cog_authentic.jpg` | `public/documents/sample_cog_authentic.jpg` | High-fidelity genuine academic record. Consistent JPEG error level compression. |
| **Tampered CLSU COG** | `https://aegis-capstone.onrender.com/documents/sample_cog_tampered.jpg` | `public/documents/sample_cog_tampered.jpg` | Spliced grade modification (`1.00`) and altered compression boundaries triggering ELA anomaly. |

---

## 5. Technical Verification Procedures (Part 1 UAT Matrix)

The following procedures guide the execution of all 10 technical test scenarios in [docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx](file:///f:/aegis-capstone/docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx):

### TC-IT-01: Authentication Hardening, SQL Injection & Brute-Force Throttling
- **Target Route:** `POST /login`
- **Credentials / Payloads:**
  - SQLi Email 1: `' OR 1=1--` (Password: `any`)
  - SQLi Email 2: `admin@clsu.edu.ph' UNION SELECT 1,2,3,4,5--`
  - Brute Force: Enter incorrect password `wrongpass` 6 times consecutively within 60 seconds.
- **Verification Protocol:**
  1. Submit the SQLi payloads; verify that Eloquent PDO prepared statements neutralize the injection without SQL syntax error leakage.
  2. On the 6th failed login attempt, verify that the rate-limiter triggers HTTP `429 Too Many Requests` or displays *"Too many login attempts. Please try again in X seconds."*
  3. Verify password storage: Passwords are hashed using bcrypt with work factor `cost=12`.

### TC-IT-02: Multi-Factor Authentication & Zero-Knowledge OTP Storage
- **Target Route:** `POST /login` → `POST /mfa/verify`
- **Account:** `student@clsu.edu.ph` / `password`
- **Verification Protocol:**
  1. Authenticate with student credentials; observe redirection to `/login/mfa`.
  2. Inspect database table `users` for `otp_code` column:
     ```sql
     SELECT id, email, otp_code, otp_expires_at FROM users WHERE email = 'student@clsu.edu.ph';
     ```
  3. Verify the OTP is stored strictly as a 64-character hexadecimal SHA-256 hash (`hash('sha256', $code)`). Plaintext OTP is NEVER stored at rest.
  4. Verify the 10-minute dynamic countdown timer (`10:00`).
  5. Input demo OTP `123456` or `000000`; verify successful authentication and redirection to `/student/dashboard`.

### TC-IT-03: AES-256 Column-Level Encryption at Rest
- **Target Model:** `App\Models\StudentProfile`
- **Verification Protocol:**
  1. Inspect the raw database records in `student_profiles`:
     ```sql
     SELECT user_id, clsu_id_number, contact_number, emergency_contact_number FROM student_profiles LIMIT 1;
     ```
  2. Verify that values are stored as encrypted ciphertexts starting with base64-encoded JSON payloads (e.g. `eyJpdiI6...`) using `AES-256-CBC` encryption via Laravel's `$casts = ['clsu_id_number' => 'encrypted']`.
  3. Verify that data is decrypted into human-readable text only in-memory during authenticated sessions.

### TC-IT-04: Role-Based Access Control (RBAC) & Boundary Isolation
- **Target Account:** Student (`student@clsu.edu.ph` / `password`)
- **Verification Protocol:**
  1. Log in as a Student.
  2. Attempt direct URL navigation to privileged administrative routes:
     - `GET /admin/dashboard`
     - `GET /superadmin/scholarships`
     - `GET /superadmin/users`
     - `GET /superadmin/audit-logs`
     - `GET /superadmin/settings`
     - `GET /superadmin/analytics`
  3. Verify that `CheckRole` middleware intercepts every request and returns HTTP `403 Forbidden` or redirects safely to unauthorized warnings without leaking administrative DOM elements or backend state.

### TC-IT-05: AI Forensic ELA-CNN Pipeline & Grad-CAM Explainability
- **Target Route:** `GET /admin/review/{id}`
- **Account:** `admin@clsu.edu.ph` / `password`
- **Verification Protocol:**
  1. Log in as Administrator. Open an application review screen with an attached COG.
  2. Inspect the **AI Forensic Decision Support Card**:
     - **Fraud Probability Score (FPS):** Quantified percentage (0% to 100%).
     - **Risk Tier Badge:** Categorized as Low Risk, Moderate Risk, or High Risk.
     - **Error Level Analysis (ELA):** Visual differential map computed at 95% JPEG quality.
     - **Grad-CAM Heatmap Overlay:** Heatmap localizing anomalous pixels or bounding boxes.
  3. Verify human-in-the-loop decision autonomy: The officer can override the AI score with mandatory justification logging.

### TC-IT-06: Tamper-Evident Audit Logging & Statutory Non-Repudiation
- **Target Route:** `GET /superadmin/audit-logs`
- **Account:** `director@clsu.edu.ph` / `password`
- **Verification Protocol:**
  1. Perform an administrative action (e.g., update application status, modify active semester, or export records).
  2. Navigate to **Audit Logs** (`/superadmin/audit-logs`).
  3. Verify immutable log records containing:
     - Actor ID, User Email, and Role.
     - Action Name, Route Endpoint, and Target Resource.
     - Client IP Address and User-Agent cryptographic hash.
     - Timestamp and structured JSON `before` and `after` state diffs.

### TC-IT-07: Transport Security & Session Hardening
- **Verification Protocol:**
  1. Open Browser DevTools (F12) > **Network** tab. Refresh any authenticated page.
  2. Inspect HTTP Response Headers:
     - `Strict-Transport-Security: max-age=31536000; includeSubDomains` (HTTPS enforced)
     - `X-Frame-Options: SAMEORIGIN` (Clickjacking mitigation)
     - `X-Content-Type-Options: nosniff` (MIME sniffing prevention)
     - `Content-Security-Policy` directives
  3. Inspect **Application > Cookies**:
     - Verify session cookies are flagged `HttpOnly`, `Secure`, and `SameSite=Lax`.

### TC-IT-08: High-Fidelity 1-Page PDF & Isolated Iframe Print Engine
- **Target Route:** In `/admin/review/{id}`, click **"Generate Evaluation Sheet"** or **"Print Preview"**.
- **Verification Protocol:**
  1. Verify the generated PDF fits exactly on a 1-page letter layout without blank overflow pages.
  2. Verify institutional branding: CLSU seal, student profile summary, verification checklist.
  3. Inspect the cryptographic QR code: Scan or navigate to the QR target URL (`/verify/application/{code}`); verify that it returns official verification status.
  4. Test browser printing: Verify the isolated hidden iframe print engine executes cleanly without freezing the parent DOM.

### TC-IT-09: Asynchronous Queuing & Fault Recovery
- **Verification Protocol:**
  1. Submit an application with a large document as `student_apply@clsu.edu.ph`.
  2. Verify that intensive OCR extraction and forensic analysis are offloaded to background database queue workers (`php artisan queue:work`).
  3. Verify exponential retry backoffs (`[15s, 45s, 90s, 180s, 360s]`) to absorb microservice cold-start delays without failing the HTTP request cycle.

### TC-IT-10: Database Portability & Asset Optimization
- **Verification Protocol:**
  1. Inspect database schema portability: Migrations run seamlessly on both SQLite and PostgreSQL.
  2. Inspect eager loading: Application queues execute with `with(['student', 'scholarship', 'documents', 'aiResult'])`, eliminating N+1 database queries.
  3. Inspect static assets: Pre-bundled Vite CSS/JS assets in `/public/build/assets/` achieve >75% size reduction under Gzip compression.

---

## 6. Database Inspection SQL Reference

For evaluators inspecting the backend database:

```sql
-- 1. Inspect AES-256 Column Encryption (Raw Ciphertext)
SELECT id, user_id, clsu_id_number, contact_number, emergency_contact_number 
FROM student_profiles LIMIT 2;

-- 2. Inspect SHA-256 Zero-Knowledge OTP Storage (64-char Hash)
SELECT id, name, email, otp_code, otp_expires_at 
FROM users WHERE role = 'student';

-- 3. Inspect Audit Logs (Actor, IP, User-Agent, JSON Diff)
SELECT id, user_id, action, ip_address, user_agent, created_at 
FROM admin_action_logs ORDER BY id DESC LIMIT 5;

-- 4. Inspect Academic Term State (Active Term Governance)
SELECT id, semester, academic_year, is_active 
FROM academic_terms ORDER BY is_active DESC;
```

---

## 7. ISO/IEC 25010:2023 Product Quality Rating Guide

Please complete **Part 2** of the [IT Expert Evaluation Form](file:///f:/aegis-capstone/docs/AEGIS_IT_Expert_Testing_and_Evaluation_Form.docx) by scoring the 24 technical statements across the 8 ISO 25010 software quality dimensions:

| Dimension | Scope of Technical Inspection |
| :--- | :--- |
| **1. Functional Suitability** | Completeness of scholarship intake, GWA calculations, and automated status notifications. |
| **2. Performance Efficiency** | Page response latency (<1.5s), eager loading query efficiency, asset compression. |
| **3. Compatibility** | Multi-container Docker orchestration, Brevo SMTP integration, Cloudflare R2 storage. |
| **4. Usability** | Interface consistency, validation feedback, accessibility, guided tour. |
| **5. Reliability** | Fault-tolerant queue retry schedules, database connection recovery, test stability. |
| **6. Security** | AES-256 encryption at rest, SHA-256 OTP hashing, RBAC isolation, CSRF/XSS protection. |
| **7. Maintainability** | MVC architecture, service layer abstraction, automated test coverage (36 feature assertions). |
| **8. Portability** | Cross-browser compatibility (Chrome, Edge, Firefox, Safari) and containerized deployment. |

### 5-Point Likert Rating Scale:
- **5 = Strongly Agree (SA)**: Technical implementation exceeds expectations; robust resilience.
- **4 = Agree (A)**: Satisfies all technical criteria smoothly.
- **3 = Neither Agree nor Disagree (N)**: Meets minimum standard; minor optimization potential.
- **2 = Disagree (D)**: Deficiencies identified affecting performance or security.
- **1 = Strongly Disagree (SD)**: Critical flaw or architectural defect detected.
- **N/A = Not Applicable**: Outside testing scope.

---

## 8. Acceptance Endorsement & Sign-Off

Upon completing the test matrix and questionnaire:
1. Indicate your overall acceptance verdict in **Part 3** of the form:
   - `[ ]` **ACCEPTED** — Ready for institutional deployment.
   - `[ ]` **ACCEPTED WITH MINOR REVISIONS** — Technically sound with minor optimizations.
   - `[ ]` **FOR REVISION AND RETESTING** — Architectural or security flaws require remediation.
2. Provide technical commendations, observations, and recommendations.
3. Affix your physical or digital signature, designation, and date.
