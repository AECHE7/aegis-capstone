# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. CAPSTONE COMPLIANCE: COMPREHENSIVE SYSTEM TESTING AND ISO/IEC 25010:2023 EVALUATION SUITE
### Standardized Quality Assessment Instruments for IT Experts, OSA Director, Staff Evaluators, and Student Applicants

**Project Title:** A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS  
**Target Stakeholder Groups:**  
1. **IT Experts & Technical Specialists** (Software Architects, Cybersecurity Auditors, AI/ML Engineers, IT Faculty)  
2. **OSA Director / Super Administrator** (Executive Governance, Program Administration, Compliance)  
3. **OSA Staff / Scholarship Evaluators** (Operational Intake, Document Verification, AI Decisioning)  
4. **Student Applicants** (Undergraduate Grant Applicants, @clsu2.edu.ph institutional users)  

**Project Researchers:** John Andrei Carillo, Noriel S. Gadiano, Joshua A. Razon  
**Project Advisers:** Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT  
**Institutional Partner:** Central Luzon State University — Office of Student Affairs (CLSU OSA)  
**Standard References:** ISO/IEC 25010:2023 SQuaRE, Republic Act No. 10173 (Data Privacy Act of 2012), Republic Act No. 11032 (Ease of Doing Business and Efficient Government Service Delivery Act of 2018)  
**Document Code:** `CLSU-CEn-DIT-AEGIS-EVAL-2026-V1`  

---

## TABLE OF CONTENTS

- [General Guidelines & Regulatory Compliance Notice](#general-guidelines--regulatory-compliance-notice)
- [SECTION 1: IT EXPERT TESTING & ISO/IEC 25010 EVALUATION INSTRUMENT](#section-1-it-expert-testing--isoiec-25010-evaluation-instrument)
  - [Part 1.A: Technical Testing & Architectural Verification Matrix](#part-1a-technical-testing--architectural-verification-matrix)
  - [Part 1.B: ISO/IEC 25010:2023 Product Quality Evaluation (8 Characteristics)](#part-1b-isoiec-250102023-product-quality-evaluation-8-characteristics)
  - [Part 1.C: Technical Acceptance Sign-Off & Recommendations](#part-1c-technical-acceptance-sign-off--recommendations)
- [SECTION 2: OSA DIRECTOR (SUPER ADMINISTRATOR) TESTING & EVALUATION INSTRUMENT](#section-2-osa-director-super-administrator-testing--evaluation-instrument)
  - [Part 2.A: Executive Governance & Compliance Testing Matrix](#part-2a-executive-governance--compliance-testing-matrix)
  - [Part 2.B: ISO/IEC 25010:2023 Executive Quality Evaluation](#part-2b-isoiec-250102023-executive-quality-evaluation)
  - [Part 2.C: Executive Client Acceptance Sign-Off](#part-2c-executive-client-acceptance-sign-off)
- [SECTION 3: OSA STAFF (SCHOLARSHIP EVALUATOR) TESTING & EVALUATION INSTRUMENT](#section-3-osa-staff-scholarship-evaluator-testing--evaluation-instrument)
  - [Part 3.A: Operational Intake & AI Decision-Support Testing Matrix](#part-3a-operational-intake--ai-decision-support-testing-matrix)
  - [Part 3.B: ISO/IEC 25010:2023 Operational Usability & AI Utility Evaluation](#part-3b-isoiec-250102023-operational-usability--ai-utility-evaluation)
  - [Part 3.C: Operational Evaluator Acceptance Sign-Off](#part-3c-operational-evaluator-acceptance-sign-off)
- [SECTION 4: STUDENT APPLICANT (END-USER) TESTING & EVALUATION INSTRUMENT](#section-4-student-applicant-end-user-testing--evaluation-instrument)
  - [Part 4.A: Student End-User Workflow Testing Matrix](#part-4a-student-end-user-workflow-testing-matrix)
  - [Part 4.B: ISO/IEC 25010:2023 End-User Usability & Quality Evaluation](#part-4b-isoiec-250102023-end-user-usability--quality-evaluation)
  - [Part 4.C: Student Applicant Feedback & Acceptance Sign-Off](#part-4c-student-applicant-feedback--acceptance-sign-off)
- [SECTION 5: STATISTICAL SCORING GUIDE & INTERPRETATION BENCHMARKS](#section-5-statistical-scoring-guide--interpretation-benchmarks)

---

## GENERAL GUIDELINES & REGULATORY COMPLIANCE NOTICE

### 1. Purpose of the Evaluation Suite
This document provides the official testing protocols and standardized software quality evaluation instruments for the capstone software project **A.E.G.I.S.** (Academic Evaluation & Grant Integrity System). The evaluation instruments are formulated to satisfy the academic standards of the Department of Information Technology, College of Engineering, Central Luzon State University, and adhere strictly to international software engineering methodologies.

### 2. ISO/IEC 25010:2023 Quality Standard Alignment
Evaluations follow the **ISO/IEC 25010:2023** Systems and software Quality Requirements and Evaluation (SQuaRE) — Product Quality Model, analyzing software characteristics across:
- **Functional Suitability** (Functional Completeness, Correctness, Appropriateness)
- **Performance Efficiency** (Time Behavior, Resource Utilization, Capacity)
- **Compatibility** (Co-existence, Interoperability)
- **Interaction Capability / Usability** (Recognizability, Learnability, Operability, User Error Protection, User Interface Aesthetics, Accessibility)
- **Reliability** (Maturity, Availability, Fault Tolerance, Recoverability)
- **Security** (Confidentiality, Integrity, Non-repudiation, Authenticity, Accountability)
- **Maintainability** (Modularity, Reusability, Analyzability, Modifiability, Testability)
- **Portability / Flexibility** (Adaptability, Installability, Safety)

### 3. Privacy, Ethical Clearance, & Voluntary Participation Notice
- **Data Privacy Act Compliance (R.A. 10173):** All responses and user evaluations gathered through these instruments are strictly confidential. Personal identifiable information (PII) is protected under the data minimization principle and will be utilized exclusively for academic research, system validation, and statistical analysis.
- **Voluntary Participation:** Evaluators may choose to withhold responses to optional demographic items. Hand-signed evaluation forms serve as informed consent for institutional capstone documentation.
- **Dummy & Synthetic Data Safety:** All live system demonstrations, document tampering assessments, and OCR evaluations utilize synthetic CLSU Certificate of Grades (COG) documents to protect real student records.

---

## SECTION 1: IT EXPERT TESTING & ISO/IEC 25010 EVALUATION INSTRUMENT

### Part 1.A: Technical Testing & Architectural Verification Matrix
*Target Evaluators: Software Architects, Cybersecurity Auditors, AI/ML Engineers, IT Faculty*

| Field | Evaluator Response | Field | Evaluator Response |
| :--- | :--- | :--- | :--- |
| **Evaluator Name:** | __________________________________________________ | **Designation / Position:** | ________________________ |
| **Organization / Institution:** | __________________________________________________ | **Specialization:** | `[ ]` Architecture `[ ]` CyberSec `[ ]` AI/ML `[ ]` Faculty |
| **Date of Testing:** | October 2026 | **Testing Environment:** | Staging / Cloud Production Portal (`v1.0.0`) |

#### Technical Scenarios Execution Table

| No. | Architectural Domain | Test Scenario & Verification Protocol | Expected Technical Outcome | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Authentication & Password Hardening** | Inject SQL syntax payloads (`' OR 1=1--`) and brute-force password attempts on `/login`. Verify Bcrypt password hashing (`cost=12`), rate-limiting middleware (`5 attempts/min`), and CSRF token binding. | Injection rejected via Eloquent PDO binding; passwords irreversibly hashed; IP rate-limiter returns HTTP `429 Too Many Requests` on 6th failed attempt. | SQL injection blocked; Bcrypt cost verified; rate limiter throttled attacks. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | OWASP Top 10 A03/A07 compliant. |
| **2** | **MFA & Zero-Knowledge Hashing** | Trigger 6-digit OTP delivery. Inspect database column `otp_code` in `users`. Verify SHA-256 cryptographic hashing at rest, 10-minute dynamic TTL, and bypass guards. | OTP stored strictly as 64-character SHA-256 hash; expired OTP rejected; demo OTP accepted only for configured test fixtures. | SHA-256 hash verified at rest; 10-min countdown timer functional; demo OTP bypass verified. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Prevents plaintext OTP exposure in DB dumps. |
| **3** | **AES-256 Column Encryption** | Inspect raw database contents for sensitive student attributes (`clsu_id_number`, contact numbers, guardian information) in `student_profiles`. | Data stored as AES-256-CBC ciphertext; raw database queries return unintelligible ciphertext; decrypted in-memory only for authenticated sessions. | Column encryption verified via database inspection; runtime Eloquent accessors decrypt smoothly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Complies with R.A. 10173 at-rest security. |
| **4** | **RBAC & Authorization Gates** | Authenticate as Student and attempt direct URL navigation to privileged endpoints (`/admin/dashboard`, `/superadmin/users`, `/superadmin/settings`, `/superadmin/analytics`). | HTTP `403 Forbidden` or redirection to unauthorized error page triggered by `CheckRole` middleware; zero administrative data leaked. | Access strictly blocked by middleware; role segregation maintained. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Strong principle of least privilege. |
| **5** | **AI Forensic ELA-CNN Pipeline** | Submit a tampered Certificate of Grades with edited numerical grades and cloned university seal. Trigger forensic analysis. Inspect ELA computation (Q=95), ResNet-50 inference, and Grad-CAM explainability heatmap. | System outputs Error Level Analysis difference map, returns Fraud Probability Score (0–100%), assigns risk tier (Low, Moderate, High), and overlays Grad-CAM heatmap highlighting edited grade bounding box. | ELA-CNN generated accurate FPS; Grad-CAM heatmap clearly localized manipulated regions; human decision override intact. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | High forensic explainability for human decision support. |
| **6** | **Tamper-Evident Audit Logging** | Perform critical administrative actions (approve application, modify global setting, export student data). Inspect log tables (`admin_action_logs`, `config_change_logs`, `export_access_logs`). | Immutable log entries created with actor ID, IP address, user-agent hash, timestamp, and JSON before/after payload diffs. | Structured logs accurately captured actor details and state diffs; exports logged. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Satisfies statutory non-repudiation requirements. |
| **7** | **Session & Transport Hardening** | Inspect HTTP response headers and session cookie attributes via browser DevTools on authenticated HTTPS connection. | Response includes `Strict-Transport-Security`, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `CSP`; cookies flagged `Secure`, `HttpOnly`, `SameSite=Lax`. | Security headers verified; cookie hardening confirmed in production configuration. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Mitigates clickjacking, XSS, and MIME-sniffing. |
| **8** | **Official PDF & Isolated Print Engine** | Generate 1-page official applicant evaluation form with verification checklist, QR clearance code, and university seal. Trigger print engine. | 1-page letter PDF generated matching web preview 1:1; isolated iframe print engine executes without blank pages or layout clipping; QR code leads to signed verification endpoint. | High-fidelity PDF generated; isolated iframe print clean; QR validation route functional. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Meets institutional documentation standards. |
| **9** | **Asynchronous Queue & Fault Recovery** | Dispatch heavy batch AI forensic requests. Inspect database job queue, worker execution, and exponential backoff retry schedules (`[15s, 45s, 90s, 180s, 360s]`). | Asynchronous queue worker picks up tasks without blocking web UI thread; cold-start timeouts and microservice reconnections handled gracefully. | Job processed asynchronously; retry backoff handled cold-start connection delays gracefully. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Decouples heavy AI compute from HTTP cycle. |
| **10** | **Database Portability & Asset Optimization** | Verify database migrations execute seamlessly across SQLite (dev) and PostgreSQL (prod). Evaluate eager loading query efficiency and Gzip compression on static assets. | Schema portable without database-specific syntax; zero N+1 queries during application list rendering; Gzip compression achieves >75% asset reduction. | Schema executed without error; eager loading verified in Laravel Debugbar; sub-second page rendering recorded. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | High maintainability and dev/prod parity. |

---

### Part 1.B: ISO/IEC 25010:2023 Product Quality Evaluation (8 Characteristics)
*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | ISO/IEC 25010:2023 Quality Dimension & Technical Evaluation Statement | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 1.1 | The system completely fulfills all technical functional requirements for scholarship management, AI forensic analysis, and automated notifications. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.2 | The system executes algorithmic calculations (GWA computation, quota counters, AI fraud risk scores) with precision and verifiable accuracy. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.3 | The system provides appropriate technical utilities (batch dispatch, audit trails, active term switching) without redundant operations. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 2.1 | The system responds within acceptable latency thresholds (< 1.5 seconds) during typical database queries and page transitions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.2 | Server resources (CPU, RAM, database I/O) are utilized efficiently via eager loading, query caching, and asset compression. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.3 | The system sustains concurrent applicant uploads and background worker queuing without deadlocks or performance degradation. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. COMPATIBILITY** | | |
| 3.1 | The application co-exists smoothly in multi-container cloud environments (Docker, PHP-FPM, Alpine Linux, Nginx) without service contention. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.2 | The system integrates seamlessly with external web services and APIs (Brevo SMTP email, Cloudflare R2 object storage, Python AI microservices). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. USABILITY (INTERACTION CAPABILITY)** | | |
| 4.1 | The system architecture is recognizable and intuitive, with standardized navigation, breadcrumbs, and consistent SweetAlert2 dialogs. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.2 | Administrative and student interfaces enable rapid user learnability with minimal instruction through structured multi-step wizards and guided tours. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.3 | The application enforces robust client-side and server-side validation rules with accessible error handling to prevent user mistakes. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.4 | The user interface provides modern typography, high contrast, responsive viewports, and light/dark theme modes adhering to accessibility standards. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. RELIABILITY** | | |
| 5.1 | The software demonstrates architectural maturity, passing automated test suites with high code coverage and zero critical regressions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 5.2 | The system provides high operational availability with fault-tolerant fallbacks (database connection retries, email failover logging). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 5.3 | The system gracefully recovers from service interruptions (e.g. AI container cold starts) through automated job retry backoffs. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. SECURITY** | | |
| 6.1 | Unauthorized access to sensitive student records is strictly prevented through role-based middleware, secure sessions, and AES-256 database encryption. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.2 | Data integrity is robustly safeguarded through CSRF token verification, cryptographic URL signatures, and immutable audit logs. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.3 | System activities (approvals, rejections, setting updates, exports) are immutably tied to user identity for complete non-repudiation. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.4 | User identity is verified through cryptographically secure Multi-Factor Authentication (MFA OTP) with SHA-256 hash storage at rest. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. MAINTAINABILITY** | | |
| 7.1 | The codebase exhibits high modularity following MVC and Clean Architecture standards (Skinny Controllers, Fat Models, Service Layer). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 7.2 | Software components (forensic analyzers, notification dispatchers, PDF generators, alert engines) are cleanly abstracted for code reusability. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 7.3 | The codebase provides clear architectural documentation, structured logging, and high testability with automated test coverage. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **8. PORTABILITY** | | |
| 8.1 | The web application adapts seamlessly across modern web browsers (Chrome, Edge, Safari, Firefox) and multi-device form factors. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 8.2 | The application deployment pipeline is standardized via containerization (Dockerfile, `render.yaml`) and automated database migrations. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

### Part 1.C: Technical Acceptance Sign-Off & Recommendations

#### Overall Technical Quality Impression
- `[  ]` **ACCEPTED** — Major required functions, security controls, and architectures operated satisfactorily; ready for production deployment.
- `[  ]` **ACCEPTED WITH MINOR REVISIONS** — The system is technically sound and usable, subject to minor engineering optimizations.
- `[  ]` **FOR REVISION AND RETESTING** — One or more architectural, security, or functional defects must be remediated before acceptance.

**Technical Commendations:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

**Observed Bottlenecks or Recommended Revisions:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

<br>

| __________________________________________________ | __________________________________________________ |
| :---: | :---: |
| **IT Expert Evaluator Signature over Printed Name**<br>Date: ________________________ | **Project Leaders / Student Researchers**<br>John Andrei Carillo / Noriel S. Gadiano / Joshua A. Razon<br>Date: ________________________ |

---

## SECTION 2: OSA DIRECTOR (SUPER ADMINISTRATOR) TESTING & EVALUATION INSTRUMENT

### Part 2.A: Executive Governance & Compliance Testing Matrix
*Target Evaluator: Director / Head, Office of Student Affairs & System Super Administrator*

| Field | Evaluator Response | Field | Evaluator Response |
| :--- | :--- | :--- | :--- |
| **Evaluator Name:** | __________________________________________________ | **Designation / Position:** | Director / Head, Office of Student Affairs |
| **Client Agency:** | Central Luzon State University — OSA | **Date of Evaluation:** | October 2026 |
| **Testing Portal:** | Executive Director Dashboard (`/superadmin/dashboard`) | **Software Build:** | Production Web Portal (`v1.0.0`) |

#### Executive Governance Scenarios Table

| No. | Governance Domain | Test Scenario & Verification Protocol | Expected Governance Outcome | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Executive KPI Analytics & Dynamic Overhaul** | Navigate to `/superadmin/analytics`. Test 1-click active term filtering, slot quota capacity progress bars, GWA Grade Integrity Index, and forensic anomaly distribution. | Real-time counters compute accurately; slot quotas calculate against real caps; anomaly keys display readable titles; chart states adapt to active semester. | Metrics render dynamically; slot capacity accurate; active term pill filters cleanly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Provides actionable institutional oversight. |
| **2** | **Academic Term & Semester Management** | Navigate to System Settings `/superadmin/settings`. Review active academic terms. Create new semester, switch active term, and inspect topbar badge synchronization. | System strictly enforces single active term rule; active term badge in topbar updates in real time; past terms archived without deleting student records. | Term creation and activation succeeded; topbar badge updated instantly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Ensures semester-based scholarship tracking. |
| **3** | **Program Lifecycle Governance** | Create a new institutional scholarship program, configure eligibility thresholds (minimum GWA, eligible colleges, maximum slots), and design custom application fields. | Program published instantly in scholarship catalog; quota enforced; custom form questions display correctly in student application form. | Program configured and visible to applicants; slot limits enforced. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Flexible program administration. |
| **4** | **Staff Account & Queue Management** | Issue secure email invitation token for a new OSA scholarship evaluator. Assign specific grant programs to evaluator. | Token-based activation link dispatched; invited evaluator registers securely; assigned queues route matching applications directly to evaluator. | Staff invitation received; role and program queues successfully assigned. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Prevents unauthorized staff account creation. |
| **5** | **Unified Communications & Broadcast Center** | Open Communications Center (`/admin/announcements`). Publish an urgent portal bulletin, compose an email broadcast with audience filters, and inspect delivery logs. | Portal announcement renders on student notice board; targeted email broadcast dispatches to filtered student cohorts; logs record delivery count. | Dual-channel communications executed; email logs confirmed dispatch. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Unified communication eliminates disparate tools. |
| **6** | **Statutory Compliance Export Hub** | Navigate to Compliance Export Hub. Select date range presets (`This Year`, `Last 30d`) and generate official CHED StuFAPs and DOST-SEI compliance reports in CSV and PDF. | Formatted masterlist exports generated with applicant demographics, approved grant amounts, GWA ratings, and verification clearance timestamps. | CSV and PDF exports generated instantaneously matching CHED format. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Fulfills statutory regulatory audits. |
| **7** | **System Settings & Forensics Calibration** | Access `/superadmin/settings`. Adjust AI Fraud Detection Threshold (e.g. 70%), GWA discrepancy tolerance (0.01), and MFA enforcement level. | System dynamically updates global settings table; newly processed applications evaluate against new calibration values immediately. | Settings updated cleanly; audit log recorded configuration change diff. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Flexible institutional parameter tuning. |
| **8** | **System Trash & Soft-Deletion Recovery** | Soft-delete a test scholarship program. Navigate to System Trash `/superadmin/trash`. Verify program recovery and permanent purge controls. | Soft-deleted entity hidden from public views; restored cleanly from Trash tab without data loss; audit log captures recovery event. | Trash recovery verified; integrity preserved across associated applications. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Prevents catastrophic accidental data loss. |

---

### Part 2.B: ISO/IEC 25010:2023 Executive Quality Evaluation
*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | Evaluation Statement (Executive Governance Perspective) | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 2.1 | The system provides complete executive tools for managing university scholarship programs, evaluator staffing, and semester terms. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.2 | Real-time analytics, quota utilization tracking, and fraud indices produce accurate and dependable management information. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.3 | The system effectively automates administrative workloads that previously required tedious manual physical record collation. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 2.4 | Executive dashboards and analytics visualizations load swiftly without perceptible delay during operational hours. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.5 | Generation of extensive statutory compliance reports (CSV and PDF masterlists) executes rapidly. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. USABILITY (INTERACTION CAPABILITY)** | | |
| 2.6 | Executive navigation menus, metric summary cards, and quick filter pills are intuitive and straightforward to navigate. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.7 | The guided tour and onboarding cues effectively orient new administrators to portal capabilities. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.8 | Information presentation is visually polished, well-organized, and legible across desktop and laptop screens. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. RELIABILITY** | | |
| 2.9 | The system performs reliably without unexpected crashes, server errors, or interrupted operations. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.10 | Automated email broadcasts and applicant notifications deliver consistently without dropped messages. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. SECURITY & NON-REPUDIATION** | | |
| 2.11 | Access to confidential student financial and academic records is strictly safeguarded against unauthorized staff or external access. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.12 | Comprehensive 7-tier audit logs maintain complete accountability by recording every approval, rejection, and configuration update. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.13 | Multi-Factor Authentication (MFA) and trusted device controls offer dependable protection for administrative accounts. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. FLEXIBILITY & INSTITUTIONAL COMPLIANCE** | | |
| 2.14 | The system readily accommodates changing institutional scholarship rules, new grant programs, and unique application forms. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.15 | The system supports multi-semester management, allowing smooth transition between academic terms without data loss. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.16 | Exported reports conform to CHED StuFAPs and DOST-SEI institutional auditing and reporting standards. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. SAFETY & RISK REDUCTION** | | |
| 2.17 | The system provides clear confirmation dialogs before high-impact administrative actions (e.g. revoking grants, deleting terms). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.18 | The System Trash repository safeguards against accidental permanent deletion of valuable scholarship programs and records. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

### Part 2.C: Executive Client Acceptance Sign-Off

#### Client Acceptance Decision
- `[  ]` **ACCEPTED FOR LIVE INSTITUTIONAL USE** — The A.E.G.I.S. portal meets all executive governance requirements and operational objectives of the Office of Student Affairs.
- `[  ]` **ACCEPTED WITH MINOR REVISIONS** — Satisfactory overall, subject to the minor operational enhancements noted below.
- `[  ]` **FOR REVISION AND RETESTING** — Substantial governance or functional adjustments are required prior to institutional rollout.

**Director's Executive Comments & Directives:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

<br>

| __________________________________________________ | __________________________________________________ |
| :---: | :---: |
| **Office of Student Affairs (OSA) Director Signature**<br>Date: ________________________ | **Project Leaders / Student Researchers**<br>John Andrei Carillo / Noriel S. Gadiano / Joshua A. Razon<br>Date: ________________________ |

---

## SECTION 3: OSA STAFF (SCHOLARSHIP EVALUATOR) TESTING & EVALUATION INSTRUMENT

### Part 3.A: Operational Intake & AI Decision-Support Testing Matrix
*Target Evaluators: Scholarship Processing Staff, Document Evaluators, Administrative Officers*

| Field | Evaluator Response | Field | Evaluator Response |
| :--- | :--- | :--- | :--- |
| **Evaluator Name:** | __________________________________________________ | **Designation / Position:** | OSA Scholarship Evaluator / Staff Officer |
| **Unit / Section:** | Student Welfare & Scholarship Division | **Date of Evaluation:** | October 2026 |
| **Testing Module:** | Evaluator Application Queue (`/admin/applications`) | **Software Build:** | Production Web Portal (`v1.0.0`) |

#### Operational Evaluator Scenarios Table

| No. | Operational Domain | Test Scenario & Verification Protocol | Expected Operational Outcome | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Application Triage & Assigned Queue** | Access `/admin/applications`. Filter queue by program, status (`Under Review`, `Returned`), college, and search by student ID number. | Queue instantaneously filters matching submissions; priority badges display clearly; after-hours submission flags visible. | Instant filter results; assigned programs filtered accurately. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Streamlines daily intake organization. |
| **2** | **Deep-Dive Review Canvas & Dual Viewer** | Open applicant review canvas (`/admin/review/{id}`). Inspect side-by-side document inspection pane with zoom, pan, and contrast inversion controls. | High-resolution preview of Certificate of Grades (COG) and Certificate of Registration (COR) renders smoothly with fluid zoom and pan. | Document viewer operated cleanly; zoom and invert controls functional. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Eliminates need for external PDF viewers. |
| **3** | **Automated OCR Grade Parsing & GWA Check** | Inspect OCR extracted grade sheet table. Verify parsed course codes, credit units, numerical grades, and computed GWA against student declared GWA. | Parsed grade table highlights any mathematical discrepancies exceeding tolerance (0.01) with distinct warning badges. | Extracted grades displayed accurately; discrepancy flagged when mismatch induced. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Reduces manual calculator tallying time. |
| **4** | **AI Forensic ELA-CNN & Heatmap Inspection** | Inspect AI Analysis Dossier on the review canvas. Review Fraud Probability Score (FPS), Risk Tier badge (Low, Moderate, High), and toggle Grad-CAM heatmap overlay. | Color-coded risk tier displays prominently; Grad-CAM heatmap highlights suspicious altered pixel clusters (e.g. modified grade numbers); human evaluator retains final override authority. | FPS and risk tier clearly displayed; Grad-CAM heatmap overlay toggled smoothly; human decision independence intact. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Transparent AI decision-support without automation bias. |
| **5** | **Application Decisioning (Approve / Reject / Return)** | Process application decisions: (a) Approve grant, (b) Reject with standard justification, or (c) Return for Correction with specific deficiency remarks. | Decision modal requires mandatory justification for rejections/returns; updates status immediately in database; deducts slot quota upon approval. | Decisions submitted smoothly; validation prevented empty remarks on returns. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Structured decision lifecycle. |
| **6** | **Automated Notification Dispatch** | Render an application decision. Verify applicant receives automated status email and in-app bell notification drawer update. | Immediate automated email dispatched via Brevo SMTP containing official status, remarks, and next steps; bell drawer updates badge count. | Automated notification dispatched successfully; delivery recorded in email log. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Eliminates manual individualized emailing. |
| **7** | **Official 1-Page Evaluation PDF Printing** | Click "Generate Official Evaluation Form". Inspect modal preview and trigger print engine via isolated iframe. | Form renders in standard 1-page letter format with CLSU OSA seal, applicant credentials, checklist, QR verification badge, and signature lines without blank pages. | Form printed cleanly in 1 page; QR badge legible; iframe prevented main page disruption. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Publication-ready paper records when required. |
| **8** | **Deficiency Resubmission Re-Evaluation** | Open an application resubmitted by a student following a "Returned for Correction" status. Verify updated files and audit history. | History drawer displays revision timeline; newly uploaded documents replace deficient files; evaluator can review corrections and approve. | Revision timeline clearly tracked previous remarks and newly uploaded files. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Facilitates equitable student remedy. |

---

### Part 3.B: ISO/IEC 25010:2023 Operational Usability & AI Utility Evaluation
*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | Evaluation Statement (Operational Evaluator Perspective) | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 3.1 | The review canvas provides all tools necessary to evaluate student credentials, inspect documents, and render decisions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.2 | OCR grade extraction and GWA discrepancy checks accurately detect mismatches between student input and official documents. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.3 | The triage filtering tools enable efficient organization of applications by scholarship program and processing status. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 3.4 | Uploaded document images and PDF previews open swiftly without delaying the evaluation workflow. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.5 | Recording an evaluation decision and updating the application queue occurs instantly upon submission. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. USABILITY (INTERACTION CAPABILITY)** | | |
| 3.6 | The layout of the evaluation screen is logical, well-structured, and comfortable for extended daily use. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.7 | Document zoom, pan, and rotation controls are easy and intuitive to operate. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.8 | The interface effectively prevents accidental decisions by requiring explicit confirmation and remarks. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. AI FORENSIC UTILITY & EXPLAINABILITY** | | |
| 3.9 | The Fraud Probability Score (0–100%) and color-coded risk tier badges provide clear, actionable guidance during review. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.10 | The Grad-CAM explainability heatmap overlay helps locate specific suspicious areas on modified documents. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.11 | The system appropriately positions AI as a decision-support aid, ensuring that the human evaluator retains full authority. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. RELIABILITY & ERROR RECOVERY** | | |
| 3.12 | The application queue operates consistently without losing draft notes or entered evaluator comments. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.13 | Automated notification emails trigger reliably whenever an application status changes. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. SECURITY & DATA PRIVACY** | | |
| 3.14 | Student personal contact numbers, guardian records, and academic files are shielded from unauthorized exposure. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.15 | Every evaluator action is logged with an immutable timestamp, ensuring fairness and accountability. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. SAFETY** | | |
| 3.16 | The system requires mandatory correction remarks before returning an application, preventing student confusion. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.17 | The system issues clear warnings before permanent rejection of an applicant's scholarship grant. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

### Part 3.C: Operational Evaluator Acceptance Sign-Off

#### Operational Recommendation
- `[  ]` **ACCEPTED** — The evaluation canvas and AI forensic tools substantially improve review accuracy and speed; fully ready for operational deployment.
- `[  ]` **ACCEPTED WITH MINOR SUGGESTIONS** — Effective for daily workflow, with minor suggestions noted below.
- `[  ]` **REQUIRES REVISION** — Usability or workflow obstacles require revision before daily adoption.

**Staff Evaluator Comments & Recommendations:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

<br>

| __________________________________________________ | __________________________________________________ |
| :---: | :---: |
| **OSA Scholarship Evaluator Signature over Printed Name**<br>Date: ________________________ | **Project Leaders / Student Researchers**<br>John Andrei Carillo / Noriel S. Gadiano / Joshua A. Razon<br>Date: ________________________ |

---

## SECTION 4: STUDENT APPLICANT (END-USER) TESTING & EVALUATION INSTRUMENT

### Part 4.A: Student End-User Workflow Testing Matrix
*Target Evaluators: CLSU Undergraduate Students, Active Grant Applicants (@clsu2.edu.ph)*

| Field | Student Response | Field | Student Response |
| :--- | :--- | :--- | :--- |
| **Student Name (Optional):** | __________________________________________________ | **College / Program:** | ________________________ |
| **Year Level:** | `[ ]` 1st `[ ]` 2nd `[ ]` 3rd `[ ]` 4th / Higher | **Device Used for Testing:** | `[ ]` Laptop/PC `[ ]` Smartphone `[ ]` Tablet |
| **Date of Testing:** | October 2026 | **Testing Portal:** | Student Scholarship Portal (`/student`) |

#### Student Applicant Scenarios Table

| No. | Student Workflow | Test Scenario & Verification Protocol | Expected Student Experience | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Registration & Profile Setup** | Register account using institutional `@clsu2.edu.ph` email, set a secure password, and complete academic profile (CLSU ID, College, Course, Year Level). | System enforces institutional email format, securely validates profile details, and grants access to student scholarship dashboard. | Account created; profile saved; verified student badge displayed. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Straightforward registration. |
| **2** | **Catalog Discovery & Filtering** | Browse available scholarships on `/scholarships`. Filter by College, Year Level, and minimum GWA requirements. | Catalog dynamically shows eligible grants, application deadlines, stipend amounts, and required documents. | Filtered instantly; grant details displayed clearly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Clear visibility of grant opportunities. |
| **3** | **Multi-Step Grant Application** | Select an open scholarship, complete program-specific questions, and review requirements checklist. | Step-by-step application form guides user smoothly; indicates progress and prevents skipping required questions. | Wizard guided through inputs clearly; form validation worked. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Well-structured form experience. |
| **4** | **Document Upload & Client Validation** | Upload Certificate of Grades (COG) and Certificate of Registration (COR). Test file format validation (PDF/JPG/PNG) and size limits. | Clear upload drag-and-drop zone; client-side validator rejects oversized or invalid file extensions with helpful instructions. | File upload smooth; document previews generated cleanly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Prevents submission of corrupt files. |
| **5** | **Data Privacy Consent & Final Submission** | Review submission summary, accept Data Privacy Act (R.A. 10173) agreement, and submit application. | Submission confirmed with instant success modal; status updates to 'Pending'; email confirmation received immediately. | Submission receipt confirmed; email confirmation delivered to inbox. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Transparent privacy declaration. |
| **6** | **Live Status Tracking & Announcements** | Track application status on Student Dashboard `/student/dashboard`. Check in-app notification drawer and portal announcements bulletin. | Live progress tracker visually illustrates current stage (`Pending` → `Under Review` → `Approved`); announcements feed informs about university grant updates. | Progress tracker clear; announcements board accessible. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Keeps students informed in real time. |
| **7** | **Correction of Deficient Application** | Open an application marked "Returned for Correction". Read evaluator deficiency remarks, re-upload corrected COG, and resubmit. | Deficiency instructions clearly explained; allows replacement of flagged documents without restarting entire application. | Correction remarks easy to follow; updated document submitted cleanly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Transparent and fair remedy process. |
| **8** | **Approved Award Clearance Download** | View notification for an approved scholarship. Preview and download official 1-page Scholarship Award & Clearance Certificate. | High-quality 1-page certificate downloads with official university seal and QR code verification badge. | Official clearance PDF downloaded cleanly; QR code verifiable. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Instant proof of scholarship grant. |

---

### Part 4.B: ISO/IEC 25010:2023 End-User Usability & Quality Evaluation
*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | Evaluation Statement (Student Applicant Perspective) | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 4.1 | The portal provides all functions needed to find, apply for, and monitor university scholarships. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.2 | The system accurately calculates my submitted GWA and reflects my academic eligibility. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.3 | The online process eliminates the need to submit physical paper folders at the university office. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 4.4 | Application pages, catalogs, and status trackers load quickly on my device. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.5 | Uploading documents (grade sheets, registration cards) completes without long waiting times. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. COMPATIBILITY & MOBILE RESPONSIVENESS** | | |
| 4.6 | The system functions properly on my smartphone, tablet, or laptop browser. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.7 | The text, buttons, and upload forms remain readable and easy to tap on smaller mobile screens. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. INTERACTION CAPABILITY (USABILITY)** | | |
| 4.8 | It is easy to understand how to use the portal without needing extensive training or instructions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.9 | Form instructions, requirements checklists, and deadline notices are clear and unambiguous. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.10 | The system alerts me helpfully if I miss required questions or upload the wrong file format. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.11 | The overall design, colors, and layout look modern, professional, and visually comfortable. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. RELIABILITY** | | |
| 4.12 | The portal worked smoothly during submission without freezing or crashing. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.13 | The system accurately preserves my entered application information and documents once submitted. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. SECURITY & DATA PRIVACY** | | |
| 4.14 | I feel confident that my personal information, grades, and documents are securely protected. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.15 | The system clearly informed me of my Data Privacy Act (R.A. 10173) rights prior to submission. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. COMMUNICATION & TRANSPARENCY** | | |
| 4.16 | Email notifications and dashboard alerts kept me promptly updated regarding my application progress. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.17 | If an application is returned for correction, the remarks clearly explain what documents must be updated. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **8. OVERALL SATISFACTION** | | |
| 4.18 | Overall, I am highly satisfied with my experience using the A.E.G.I.S. scholarship portal. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.19 | I would strongly prefer applying through this digital portal over traditional paper-based submissions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.20 | The system is ready for official university-wide implementation for all CLSU students. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

### Part 4.C: Student Applicant Feedback & Acceptance Sign-Off

**Features I Appreciated Most:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

**Suggestions for Improvement:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

<br>

| __________________________________________________ | __________________________________________________ |
| :---: | :---: |
| **Student Applicant Signature (Optional)**<br>Date: ________________________ | **Project Leaders / Student Researchers**<br>John Andrei Carillo / Noriel S. Gadiano / Joshua A. Razon<br>Date: ________________________ |

---

## SECTION 5: STATISTICAL SCORING GUIDE & INTERPRETATION BENCHMARKS

For statistical treatment of evaluation scores in the capstone manuscript (Chapters 3, 4, and 5), the following Likert scale interpretation benchmarks established by statistical conventions and ISO/IEC 25010 standards shall be applied:

### Statistical Scale & Verbal Interpretation Matrix

| Rating Range / Mean Interval | Verbal Interpretation (Technical / Quality) | Verbal Interpretation (Acceptability / End-User) | Qualitative Description & Capstone Interpretation |
| :---: | :--- | :--- | :--- |
| **4.21 – 5.00** | **Strongly Agree / Exemplary Quality** | **Very High Acceptability** | The software quality, architectural controls, and user experience significantly exceed operational requirements; flawless implementation. |
| **3.41 – 4.20** | **Agree / High Quality** | **High Acceptability** | The software satisfies all primary requirements and industry benchmarks with strong reliability and user satisfaction. |
| **2.61 – 3.40** | **Moderate Quality** | **Moderate Acceptability** | The system is functional and acceptable; minor architectural or cosmetic enhancements recommended. |
| **1.81 – 2.60** | **Disagree / Low Quality** | **Low Acceptability** | Notable technical deficiencies or usability hurdles exist; corrective remediation required. |
| **1.00 – 1.80** | **Strongly Disagree / Very Low Quality** | **Very Low Acceptability / Rejected** | Critical flaws, security breaches, or operational failures; system rejected in current form. |

### Formula for Dimension Mean Score:
$$\bar{X} = \frac{\sum_{i=1}^{n} X_i}{n}$$
Where:
- $\bar{X}$ = Mean score for the quality dimension
- $X_i$ = Individual evaluator rating for statement $i$
- $n$ = Total number of valid ratings (excluding `N/A`)

---
**END OF COMPREHENSIVE TESTING AND EVALUATION SUITE**
