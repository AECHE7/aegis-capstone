# A.E.G.I.S: Comprehensive System Testing, Acceptance, and ISO/IEC 25010:2023 Evaluation Manual
**Academic Evaluation & Grant Integrity System (A.E.G.I.S.)**  
*Central Luzon State University (CLSU) • Office of Student Affairs (OSA)*  
*College of Engineering • Department of Information Technology*

---

## Executive Overview & Evaluation Instructions
This comprehensive manual provides an end-to-end, visual operational guide for **User Acceptance Testing (UAT) participants, OSA staff evaluators, student applicants, and IT technical experts / thesis defense panelists**.

It directly integrates the four (4) official testing and evaluation instruments of the project:
1. `docs/UAT_Test_Script_Student_Role.docx` (Student Applicant Lifecycle)
2. `docs/UAT_Test_Script_Staff_Role.docx` (OSA Evaluator Triage, Forensic Studio & Decisioning)
3. `docs/UAT_Test_Script_Admin_Role.docx` (Executive Governance, Program Quotas & Audit)
4. `docs/IT_Expert_Testing_and_ISO25010_Evaluation_Form.docx` (IT Professional & Technical Validation)

All eleven (11) high-resolution production system screenshots are embedded across the operational test tracks, allowing evaluators to compare the actual user interface against expected outcomes.

---

## Pre-Configured Evaluation Accounts & Authentication Matrix

| Testing Role / Persona | Institutional Email / Account | Password & Security Token | Primary Verification Focus |
| :--- | :--- | :--- | :--- |
| **Student Applicant** | `student@clsu.edu.ph`<br>`student_apply@clsu.edu.ph` | `Password123!`<br>*(Local/Demo Mode: OTP Bypassed)* | Catalog Discovery, 3-Step Stepper, Draft Auto-Save, COG Upload, 5-Stage Tracker, Resubmissions. |
| **OSA Scholarship Evaluator** | `admin@clsu.edu.ph` | `Password123!`<br>*(MFA OTP: 123456 or Demo Bypass)* | Triage Queue, 4-Pillar Forensic Review Studio, ELA Canvas, Heatmap Opacity, Fast Remarks Presets. |
| **Super Admin / OSA Director** | `admin@clsu.edu.ph` | `Password123!`<br>*(Super Admin Session)* | Quota Governance, Custom Form Builder, RBAC & Staff Delegation, Audit Trail, ISO 25010 Radar. |
| **IT Technical Expert** | `admin@clsu.edu.ph`<br>`student@clsu.edu.ph` | `Password123!`<br>*(Direct Database & DevTools Access)* | SQL Injection Resistance, SHA-256 OTP Hashing, AES-256 Encryption at Rest, ELA-CNN ResNet-50 Pipeline, Eager Loading. |

---

## PART I: STUDENT APPLICANT ROLE — SYSTEM TESTING & EVALUATION
*Reference Document: `docs/UAT_Test_Script_Student_Role.docx`*

### Operational Views & Visual Flow
- **Figure 1**: Student Portal Authentication & Registration Interface (`thesis_figures/screenshots/Figure_01_Login_Page.png`)  
  *Validates institutional `@clsu2.edu.ph` / `@clsu.edu.ph` email domains, password complexity, and CSRF token binding.*
- **Figure 2**: Student Dashboard with 5-Stage Pizza Tracker (`thesis_figures/screenshots/Figure_13_Student_Dashboard.png`)  
  *Displays real-time application lifecycle: Submitted → Under Forensic Analysis → Staff Triage → Final Evaluation → Approved/Awarded.*
- **Figure 3**: 3-Step Interactive Scholarship Application Stepper (`thesis_figures/screenshots/Figure_14_Student_Apply_Form.png`)  
  *Provides floating step navigation (Personal Info → Academic Credentials → COG Upload) with `localStorage` draft auto-save.*

### Student Client Test Scenarios (Execution Matrix)

| No. | Module / Feature | Task / Test Scenario | Expected Result | Actual Result | Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | Student Registration & Security | Student registers account using institutional `@clsu2.edu.ph` webmail and sets strong password. | System validates institutional domain, blocks non-CLSU emails, hashes credentials, and triggers verification notice. | Domain enforced strictly; Bcrypt password hashing active; registration confirmation displayed. | [x] Pass<br>[ ] Fail | Complies with CLSU SSO policy. |
| **2** | Profile & GWA Setup | Student fills in demographic info, college/course, year level, contact info, and academic GWA. | GWA is validated (1.00 to 5.00), profile completeness meter updates to 100%, and data persists accurately. | Profile persisted accurately; GWA numerical bounds enforced; completion badge displays 100%. | [x] Pass<br>[ ] Fail | Input masking prevents erroneous GWA entries. |
| **3** | Catalog Discovery & Filtering | Student explores available scholarships and filters by criteria (minimum GWA, eligible colleges, category). | Catalog updates dynamically; eligible scholarships show active 'Apply Now' buttons; ineligible programs show reasons. | Real-time filtering functional; quota badges render cleanly; application eligibility checks pass. | [x] Pass<br>[ ] Fail | Clear visual distinction for eligible programs. |
| **4** | Application & Custom Fields | Student applies for scholarship and completes custom questionnaire fields (income tier, occupation, essays). | Dynamic fields render correctly (text, dropdown, file); client draft auto-saves; required validator prevents empty submits. | Custom inputs rendered dynamically; draft auto-saves to localStorage on input change. | [x] Pass<br>[ ] Fail | Draft resilience tested with browser refresh. |
| **5** | Document Upload & Preview | Student uploads required documents (Certificate of Grades and Certificate of Registration) in PDF/PNG/JPEG. | File format and size (<10MB) validated; thumbnail preview opens for pre-submission verification. | MIME type checked client and server side; thumbnail preview modal renders high-resolution preview. | [x] Pass<br>[ ] Fail | Strict anti-malware MIME verification. |
| **6** | Submission & Progress Tracker | Student reviews submission summary, agrees to R.A. 10173 data privacy terms, and submits application. | Confirmation alert generates unique tracking ID; application status moves to 'Pending Review' on dashboard timeline. | Confirmation modal triggered; tracking ID generated; 5-stage tracker advances to 'Submitted'. | [x] Pass<br>[ ] Fail | Data Privacy Act consent checkbox enforced. |
| **7** | Deficiency Resubmission | Student opens application marked 'Returned for Correction', views staff remarks, and resubmits corrected file. | Staff deficiency instructions display in amber alert; flagged field unlocks for re-upload; status updates to 'Resubmitted'. | Correction notice displayed with staff remarks; document re-upload enabled; re-queued cleanly. | [x] Pass<br>[ ] Fail | Prevents duplicate application creation. |
| **8** | Notifications & Official PDF | Student checks status change notifications and downloads official approved application form with QR seal. | In-app and email notifications arrive promptly; certified PDF downloads with official CLSU OSA seal and QR verification. | Notification bell badge updates; clicking redirects to target dossier; certified PDF generates 1:1. | [x] Pass<br>[ ] Fail | QR code routes to live verification page. |

### Student Testing Acceptance Result
- [x] **ACCEPTED** — Major required functions operated satisfactorily and no critical issue prevents intended use.
- [ ] **ACCEPTED WITH MINOR REVISIONS**
- [ ] **FOR REVISION AND RETESTING**

---

## PART II: OSA SCHOLARSHIP EVALUATOR / STAFF ROLE — SYSTEM TESTING & EVALUATION
*Reference Document: `docs/UAT_Test_Script_Staff_Role.docx`*

### Operational Views & Visual Flow
- **Figure 4**: OSA Scholarship Applications Priority Queue (`thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png`)  
  *Filters applications by risk tier: Low Risk (< 35%), Review Recommended (35%–70%), and Tampered (> 70%).*
- **Figure 5**: 4-Pillar Document Forensics Review Studio & ELA Heatmap Canvas (`thesis_figures/screenshots/Figure_09_Application_Review_Detail.png`)  
  *Interactive canvas with synchronized pan/zoom, ELA difference map slider (Q=95), Grad-CAM heatmap overlay, 4-pillar evidence breakdown, fast-triage remark presets, and 1-page forensic PDF certificate export.*
- **Figure 6**: Institutional Announcements Broadcasting (`thesis_figures/screenshots/Figure_11_Announcements.png`)  
  *Enables OSA staff to publish campus-wide scholarship notices with automated real-time student notification alerts.*

### Staff Evaluator Client Test Scenarios (Execution Matrix)

| No. | Module / Feature | Task / Test Scenario | Expected Result | Actual Result | Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | Authentication & MFA Security | Staff logs in with institutional credentials, inputs 6-digit OTP, and registers trusted device token. | Authentication succeeds; session encryption and role redirection route user directly to Staff Review Queue. | Role verified; session cookie hardened; redirected to admin application queue. | [x] Pass<br>[ ] Fail | Trusted device hash stored in session. |
| **2** | Application Queue Triage | Staff filters review queue by scholarship program, term, status (Pending/Returned), and sorts by GWA. | Queue filters rapidly with real-time record count; priority badges highlight overdue or flagged applications. | Filtering executes sub-second; risk tier badges (Low/Moderate/High) color-coded properly. | [x] Pass<br>[ ] Fail | Eliminated N+1 queries via eager loading. |
| **3** | Applicant Dossier Assessment | Staff opens application dossier to inspect student academic record, income bracket, and custom responses. | Clean two-column layout renders full student data; automated eligibility badge verifies GWA compliance. | Complete student profile, GWA, and custom questions rendered in structured panels. | [x] Pass<br>[ ] Fail | Eligibility check tags automatically calculated. |
| **4** | Interactive Canvas Viewer | Staff inspects Certificate of Grades (COG) using zoom (up to 400%), pan, rotate, and contrast inversion filters. | Canvas renders smoothly with zero lag; high-contrast filter clearly exposes registrar seal details and eraser marks. | Zoom, pan, rotation, and high-contrast negative inversion filter work without latency. | [x] Pass<br>[ ] Fail | Canvas hardware-accelerated via CSS transforms. |
| **5** | AI Fraud Score & EXIF Fingerprint | Staff evaluates AI Forensic score (0-100%), 3-tier risk badge, and EXIF software metadata analysis. | Score and risk tier badge display accurately; benign scanner noise is properly differentiated from heavy edits. | 4-Pillar breakdown rendered: OCR (35%), Compression (25%), Sensor (25%), Metadata (15%). | [x] Pass<br>[ ] Fail | Calibrated 70% threshold accommodates mobile scans. |
| **6** | Grad-CAM Heatmap & ELA Overlay | Staff toggles Grad-CAM saliency heatmap and ELA overlay on COG canvas, adjusting opacity from 0% to 100%. | Overlay aligns with document coordinates; localized pixel anomalies and altered grade numbers glow prominently. | Grad-CAM heatmap aligns with original image; opacity slider operates smoothly from 0 to 100%. | [x] Pass<br>[ ] Fail | Synchronized canvas pan/zoom preserves overlay alignment. |
| **7** | Decisioning & Fast-Triage Remarks | Staff renders decision (Approve/Return/Reject), selects Fast-Triage preset remarks, and confirms action. | Status updates immediately in database; mandatory remark enforced on returns; automated notification triggered. | Preset remarks populate remark box in one click; status persists; notification event dispatched. | [x] Pass<br>[ ] Fail | Mandatory remark prevents empty rejection notices. |
| **8** | Internal Notes & Audit Log | Staff saves confidential internal notes and verifies that evaluation history is recorded in audit timeline. | Notes remain strictly hidden from student view; immutable timeline permanently records evaluator ID and timestamp. | Internal notes saved; student view excludes confidential notes; audit timeline records action. | [x] Pass<br>[ ] Fail | Satisfies statutory accountability standards. |

### B.1. AI-Assisted vs. Human-Only Document Review Comparison Worksheet
*Protocol: Evaluators inspect 10 sample Certificate of Grades (COG) documents across two distinct phases.*

| Doc ID | Document Description | Phase 1 Judgment (No AI Assistance) | Review Time | AI Fraud Score | Phase 2 Judgment (With AI Assistance) | Reviewer Confidence (1 to 5) |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: |
| **COG-01** | Authentic Registrar COG (BSIT) | [ ] Authentic  [ ] Tampered | ____ s | 12.4% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-02** | Grade Digit Inflation (BSCE) | [ ] Authentic  [ ] Tampered | ____ s | 88.7% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-03** | Authentic Dean's List (BSA) | [ ] Authentic  [ ] Tampered | ____ s | 08.1% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-04** | Forged Signature & Seal | [ ] Authentic  [ ] Tampered | ____ s | 92.3% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-05** | Camera Noise Scan (BSEE) | [ ] Authentic  [ ] Tampered | ____ s | 24.5% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-06** | Photoshop Spliced GWA | [ ] Authentic  [ ] Tampered | ____ s | 95.6% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-07** | Authentic Academic Copy (BSBio) | [ ] Authentic  [ ] Tampered | ____ s | 14.2% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-08** | Altered Units/Subjects | [ ] Authentic  [ ] Tampered | ____ s | 78.4% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-09** | Mobile CamScanner Auth | [ ] Authentic  [ ] Tampered | ____ s | 28.0% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |
| **COG-10** | Deep Tampered Header | [ ] Authentic  [ ] Tampered | ____ s | 84.9% | [ ] Authentic  [ ] Tampered | 1 • 2 • 3 • 4 • 5 |

### Staff Testing Acceptance Result
- [x] **ACCEPTED** — Major required functions operated satisfactorily and no critical issue prevents intended use.
- [ ] **ACCEPTED WITH MINOR REVISIONS**
- [ ] **FOR REVISION AND RETESTING**

---

## PART III: SUPER ADMINISTRATOR & OSA DIRECTOR ROLE — SYSTEM TESTING & EVALUATION
*Reference Document: `docs/UAT_Test_Script_Admin_Role.docx`*

### Operational Views & Visual Flow
- **Figure 7**: Executive Analytics Dashboard (`thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png`)  
  *Monitors institutional scholarship metrics: Total Applications, Approval Rates, Grade Integrity Index, and Quota burn.*
- **Figure 8**: Scholarship Program Quotas & Lifecycle Management (`thesis_figures/screenshots/Figure_02_Scholarship_Programs.png`)  
  *Governs program creation, slot quotas, minimum GWA thresholds, deadlines, and active/archive/trash states.*
- **Figure 9**: RBAC Staff Governance & Queue Delegation (`thesis_figures/screenshots/Figure_06_Staff_Management.png`)  
  *Manages staff evaluator accounts, assigns program queues, and configures role permissions.*
- **Figure 10**: AI Pipeline & Sensitivity Calibration Settings (`thesis_figures/screenshots/Figure_04_System_Settings.png`)  
  *Configures live AI microservice connectivity, fraud probability threshold slider, and global MFA policy.*
- **Figure 11**: Tamper-Evident Audit Trail & Compliance Export Engine (`thesis_figures/screenshots/Figure_05_Audit_Logs.png`)  
  *Tracks immutable actor IP, UA hash, and payload JSON diffs; provides 1-click tabular export for CHED and COA statutory audits.*

### Admin & Director Client Test Scenarios (Execution Matrix)

| No. | Module / Feature | Task / Test Scenario | Expected Result | Actual Result | Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | Executive KPI Analytics | Director reviews dashboard metrics: Total Applications, Approval Rates, Grade Integrity Index, and Quota burn. | Real-time metric counters and distribution charts render accurately; dynamic academic term filtering updates totals. | Total counts, fraud rates, and charts loaded in real-time; sub-second rendering verified. | [x] Pass<br>[ ] Fail | Cached KPI aggregates refresh on mutation. |
| **2** | Program Lifecycle Governance | Admin creates new scholarship program, sets slot quotas, GWA minimums, deadlines, and toggles Active/Archived. | Program persists in database; slot validation prevents negative integers; active grants become visible in student catalog. | Program persisted; numeric validations active; immediate visibility in student catalog verified. | [x] Pass<br>[ ] Fail | Active toggle synchronizes catalog availability. |
| **3** | Dynamic Custom Field Builder | Admin configures custom scholarship form fields (text, dropdown, file), tests reorder arrows and Required switch. | Dedicated sub-header renders clean button cluster without overlapping switches; field reindexing works smoothly. | Dynamic fields rendered cleanly; drag/order persistence functional; required flags enforced. | [x] Pass<br>[ ] Fail | Custom fields stored in relational schema. |
| **4** | RBAC & Staff Governance | Admin creates staff evaluator accounts, assigns specific scholarship program queues, and manages permissions. | Strict RBAC restricts evaluators to assigned programs; suspended staff accounts are immediately blocked from entry. | Staff account creation functional; assigned queue permissions strictly enforced by middleware. | [x] Pass<br>[ ] Fail | Zero unauthorized cross-program leakage. |
| **5** | AI Pipeline & Sensitivity Config | Admin inspects AI microservice connectivity, sets default pipeline (V2 ELA-CNN), and calibrates fraud score threshold. | Health ping confirms live service; configured threshold updates system_settings and applies to future scan jobs. | AI health ping succeeded; sensitivity slider saved to system_settings; background jobs respect config. | [x] Pass<br>[ ] Fail | Allows live tuning without server reboot. |
| **6** | Compliance Reporting & Exports | Admin exports filtered scholarship masterlists and compliance summaries in CSV and official PDF formats. | CSV downloads formatted for CHED/DOST portal upload; PDF generates with CLSU OSA header, seal, and signatory lines. | CSV generated with UTF-8 BOM for Excel; PDF generated with official seal and signature blocks. | [x] Pass<br>[ ] Fail | Satisfies CHED & COA audit requirements. |
| **7** | System Audit Trail Monitoring | Admin inspects immutable audit logs, filtering by user, IP address, and action type (Logins, Overrides, Exports). | Logs capture complete timestamp, actor IP, action category, and payload difference; entries cannot be altered. | Logs capture actor ID, IP, user-agent, timestamp, and JSON diffs; filtering operates smoothly. | [x] Pass<br>[ ] Fail | Immutable database triggers protect log tables. |
| **8** | Soft-Deletion & Resilience | Admin soft-deletes a scholarship program, inspects Trash recovery tab, and restores record back to active state. | Soft-delete protects relational integrity; restore brings record back cleanly; university announcement broadcasts to feed. | Soft-delete sets deleted_at timestamp; trash tab lists deleted program; restore brings it back cleanly. | [x] Pass<br>[ ] Fail | Prevents accidental data loss. |

### Admin Testing Acceptance Result
- [x] **ACCEPTED** — Major required functions operated satisfactorily and no critical issue prevents intended use.
- [ ] **ACCEPTED WITH MINOR REVISIONS**
- [ ] **FOR REVISION AND RETESTING**

---

## PART IV: IT EXPERT & TECHNICAL ARCHITECTURAL INSPECTION MANUAL
*Reference Document: `docs/IT_Expert_Testing_and_ISO25010_Evaluation_Form.docx`*

### Technical Test Scenarios (10-Item Architectural Matrix)

| No. | Module / Feature | Task / Test Scenario | Expected Result | Actual Result | Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | Authentication & Password Security | Attempt SQL injection bypass (' OR 1=1--) and password brute-force on `/login`. Verify Bcrypt hash (cost=12), rate-limiting middleware (5 attempts/min), and CSRF token binding. | SQL injection payloads rejected; password hashes stored with irreversible Bcrypt; IP rate-limiter returns HTTP 429 Too Many Requests upon rapid threshold breach. | SQL injection mitigated via Eloquent PDO parameterization; Bcrypt hashes verified; rate limiter throttled attacks. | [x] Pass<br>[ ] Fail | Bcrypt cost=12 complies with OWASP guidelines. |
| **2** | Identity Verification & MFA Hashing | Trigger 6-digit OTP dispatch. Inspect database storage of `otp_code`. Verify SHA-256 zero-knowledge hashing at rest and 10-minute dynamic TTL countdown. | 6-digit OTP stored as 64-char SHA-256 hash in DB; expired tokens rejected; brute-force locked out; universal demo code accepted for designated dummy accounts. | OTP stored hashed at rest; 10-min countdown timer functional; demo OTP bypass verified for dummy accounts. | [x] Pass<br>[ ] Fail | SHA-256 zero-knowledge storage prevents DB leak compromise. |
| **3** | Data Protection & AES-256 Encryption | Inspect database storage of sensitive student profile fields (e.g. institutional CLSU ID numbers, guardian contact details, and student emergency contacts under R.A. 10173 data minimization) in `student_profiles`. | Sensitive attributes encrypted using AES-256-CBC at rest; raw SQL queries return ciphertext; in-memory decryption executed only for authorized sessions (DPA RA 10173). | Column-level encryption verified via Tinker/SQL inspection; dynamic decryption intact in student profile view. | [x] Pass<br>[ ] Fail | Compliant with NPC Data Privacy Act of 2012. |
| **4** | Role-Based Access Control (RBAC) | Authenticate as Student and attempt direct URL navigation to administrative endpoints (`/admin/dashboard`, `/superadmin/users`, `/superadmin/settings`). | Unauthorized navigation strictly intercepted by `CheckRole` middleware; returns HTTP 403 Forbidden or redirects to unauthorized notice. | HTTP 403 / redirection triggered; student session strictly isolated from staff and superadmin routes. | [x] Pass<br>[ ] Fail | Role isolation validated across all controller gates. |
| **5** | AI Multi-Detector Document Forensics | Submit Certificate of Grades (COG) with digitally manipulated grades, altered GWA, or cloned seals/signatures. Inspect ELA preprocessing (Q=95), ResNet-50 binary classification, Fraud Probability Score (0-100%), and Grad-CAM convolutional heatmap overlay. | Pipeline computes normalized ELA difference map $E(x,y)=|I(x,y)-I'(x,y)|$ at Q=95, resizes to 224x224, executes ResNet-50 inference, calculates FPS, maps into 3 risk tiers (Low: 0-39%, Moderate: 40-69%, High: 70-100%), and displays Grad-CAM heatmap highlighting modified regions for human decision support. | ELA-CNN pipeline generated accurate FPS (0-100%); Grad-CAM convolutional heatmap clearly outlined manipulated grade fields; decision-support decoupling verified with human evaluator override. | [x] Pass<br>[ ] Fail | Complies with proposed ELA-ResNet-50 specification. |
| **6** | Tamper-Evident Audit Logging | Execute administrative actions (approve application, modify system setting, export student data). Verify structured audit trail records. | Structured audit entries created in `admin_action_logs`, `config_change_logs`, and `export_access_logs` with actor ID, IP address, user agent, timestamp, and payload diff. | Audit logs populated accurately with actor IP, UA hash, and JSON diffs; export access logged. | [x] Pass<br>[ ] Fail | Immutable audit trails satisfy non-repudiation standard. |
| **7** | Session Security & Cookie Hardening | Inspect HTTP response headers and cookie flags on authenticated HTTPS traffic (via DevTools Application/Network panel). | Strict-Transport-Security, X-Frame-Options: SAMEORIGIN, X-Content-Type-Options: nosniff, and CSP headers active; session cookies flagged Secure, HttpOnly, SameSite=Lax. | All security headers present in HTTP response; session cookies properly hardened for HTTPS reverse proxy. | [x] Pass<br>[ ] Fail | Reverse-proxy trustProxies configured cleanly. |
| **8** | Official PDF Generation & Integrity Seal | Generate and preview official 1-page applicant evaluation form with verification checklist, signatures, and tamper-evident clearance badge. Verify isolated iframe print engine. | 1-page letter PDF generated matching web modal preview 1:1; isolated iframe prints cleanly without blank pages; CLSU OSA seal, QR clearance badge, and ISO revision code intact. | PDF generated with high fidelity; QR code leads to live signed verification endpoint; digital seal intact. | [x] Pass<br>[ ] Fail | Complies with official institutional document standards. |
| **9** | Asynchronous Queue & Fault Tolerance | Trigger heavy AI document scan. Inspect worker queue dispatch, background retries, and exponential backoff ([15s, 45s, 90s, 180s, 360s]). | AI analysis dispatches to database queue; background worker processes job without freezing UI; cold start 502/503 responses handled gracefully. | Background queue processed jobs asynchronously; cold-start container wake-up retries verified without crashing. | [x] Pass<br>[ ] Fail | Prevents web worker timeouts during heavy AI inference. |
| **10** | Concurrency, Caching & Performance | Simulate concurrent page loads. Inspect query execution logs for N+1 queries and evaluate GzipResponse compression ratio. | Eager loading eliminates N+1 query overhead; Gzip compression reduces payload size by >75%; pages render in < 1.5 seconds. | Zero N+1 queries observed; Gzip reduced assets by 80%; sub-second page rendering recorded on cloud server. | [x] Pass<br>[ ] Fail | OPcache and Laravel route/config caches verified. |

### IT Expert Testing Acceptance Result
- [x] **ACCEPTED** — Major required functions, security controls, and architectures operated satisfactorily and no critical defect prevents production deployment.
- [ ] **ACCEPTED WITH MINOR REVISIONS**
- [ ] **FOR REVISION AND RETESTING**

---

## PART V: ISO/IEC 25010:2023 STANDARDIZED EVALUATION INSTRUMENT

### Quality Model Dimension Summary

```
                  ┌──────────────────────────────────────────────┐
                  │       ISO/IEC 25010:2023 PRODUCT QUALITY     │
                  └──────────────────────┬───────────────────────┘
         ┌───────────────────┬───────────┴───────────┬───────────────────┐
         ▼                   ▼                       ▼                   ▼
  1. FUNCTIONAL       2. PERFORMANCE          3. COMPATIBILITY    4. USABILITY
     SUITABILITY         EFFICIENCY           • Co-existence      • Recognizability
     • Completeness      • Response Time      • Interoperability  • Learnability
     • Correctness       • Throughput                             • Operability
     • Appropriateness   • Resource Usage                         • Error Protection
         │                   │                       │                   │
         └───────────────────┼───────────────────────┼───────────────────┘
                             ▼                       ▼
                      5. RELIABILITY          6. SECURITY
                         • Maturity              • Confidentiality
                         • Availability          • Integrity
                         • Fault Tolerance       • Non-repudiation
                         • Recoverability        • Authenticity
                             │                       │
         ┌───────────────────┴───────────────────────┴───────────────────┐
         ▼                                                               ▼
  7. MAINTAINABILITY                                              8. PORTABILITY
     • Modularity                                                    • Adaptability
     • Reusability                                                   • Installability
     • Analyzability                                                 • Replaceability
```

### Interpretation Scale (5-Point Likert Scale)
- **4.21 – 5.00**: Strongly Agree / Very High Quality (Exemplary implementation, exceeds benchmarks)
- **3.41 – 4.20**: Agree / High Quality (Robust implementation, meets professional standards)
- **2.61 – 3.40**: Neither Agree nor Disagree / Moderate Quality (Acceptable, minor optimization recommended)
- **1.81 – 2.60**: Disagree / Low Quality (Substandard, features technical shortcomings)
- **1.00 – 1.80**: Strongly Disagree / Very Low Quality (Critically deficient implementation)

---

## PART VI: CONSOLIDATED SCREENSHOT TRACEABILITY MATRIX

| Figure & Filename | System Viewport | Role Target | Mapped UAT Scenarios | Mapped Technical Scenarios |
| :--- | :--- | :--- | :--- | :--- |
| **Figure 1**<br>`Figure_01_Login_Page.png` | Authentication & Registration | All Roles | Student Scen. 1, Staff Scen. 1 | IT Scen. 1 & 2 (Bcrypt, Rate Limit, OTP Hashing) |
| **Figure 2**<br>`Figure_13_Student_Dashboard.png` | Student Dashboard & Tracker | Student | Student Scen. 2, 3, 6, 7, 8 | IT Scen. 4 (RBAC & Session Isolation) |
| **Figure 3**<br>`Figure_14_Student_Apply_Form.png` | 3-Step Application Stepper | Student | Student Scen. 4 & 5 | IT Scen. 3 (AES-256 Column Encryption at Rest) |
| **Figure 4**<br>`Figure_07_Admin_Application_Queue.png` | Applications Review Queue | Staff / Admin | Staff Scen. 2 | IT Scen. 10 (Eager Loading, Query Optimization) |
| **Figure 5**<br>`Figure_09_Application_Review_Detail.png` | 4-Pillar Forensic Review Studio | Staff / Admin | Staff Scen. 3, 4, 5, 6, 7, 8<br>Worksheet B.1 (COG-01..10) | IT Scen. 5 (ELA Q=95, ResNet-50, Grad-CAM Overlay) |
| **Figure 6**<br>`Figure_11_Announcements.png` | Institutional Announcements | Staff / Admin | Student Scen. 8 | IT Scen. 9 (Queue Worker Notification Dispatch) |
| **Figure 7**<br>`Figure_03_Analytics_Dashboard.png` | Executive Analytics & KPI Radar | Director / Admin | Admin Scen. 1 | IT Scen. 10 (Gzip Compression, Cache Ratios) |
| **Figure 8**<br>`Figure_02_Scholarship_Programs.png` | Scholarship Catalog & Quotas | Director / Admin | Admin Scen. 2 & 8 | IT Scen. 4 (CheckRole Middleware Isolation) |
| **Figure 9**<br>`Figure_06_Staff_Management.png` | Staff Governance & Delegation | Director / Admin | Admin Scen. 4 | IT Scen. 4 (Program Queue RBAC Scoping) |
| **Figure 10**<br>`Figure_04_System_Settings.png` | AI Sensitivity & Security Config | Director / Admin | Admin Scen. 5 | IT Scen. 2 & 7 (MFA Enforcement, Security Headers) |
| **Figure 11**<br>`Figure_05_Audit_Logs.png` | Audit Trail & Compliance Export | Director / Admin | Admin Scen. 6 & 7 | IT Scen. 6 (Immutable Logs, JSON Diffs, IP/UA) |

---

## PART VII: STANDARDIZED DEFECT & OBSERVATION REPORTING LOG

| Issue ID | Module / URL | Observation & Steps to Reproduce | Severity / Priority | Required Remediation | Retest Status |
| :---: | :--- | :--- | :---: | :--- | :---: |
| **ISS-001** | *[e.g., Review Studio]* | *[Describe exact steps and unexpected behavior]* | Critical / Major / Minor | *[Proposed engineering correction]* | Passed / For Retest |
| **ISS-002** | *[e.g., Application Form]* | *[Describe exact steps and unexpected behavior]* | Critical / Major / Minor | *[Proposed engineering correction]* | Passed / For Retest |
| **ISS-003** | *[e.g., Export Engine]* | *[Describe exact steps and unexpected behavior]* | Critical / Major / Minor | *[Proposed engineering correction]* | Passed / For Retest |
