# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. CAPSTONE TESTING AND EVALUATION FORM
## [IT EXPERT / TECHNICAL SPECIALIST ROLE]
### Technical Quality Assessment based on ISO/IEC 25010:2023 Software Product Quality Standards

**Project Title:** A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS  
**Document Code:** `CLSU-CEn-DIT-AEGIS-EVAL-IT-2026`  
**Classification:** Institutional Capstone Compliance Standard  
**Advisers:** Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT  
**Project Researchers:** John Andrei Carillo, Noriel S. Gadiano, Joshua A. Razon  

---

### EVALUATOR & PROJECT METADATA

| Field | Details / Evaluator Response | Field | Details / Evaluator Response |
| :--- | :--- | :--- | :--- |
| **IT Expert Evaluator:** | __________________________________________________ | **Designation / Position:** | ________________________ |
| **Institution / Organization:** | __________________________________________________ | **Specialization:** | `[ ]` Arch `[ ]` Sec `[ ]` AI `[ ]` Faculty |
| **Date of Testing:** | October 2026 | **Testing Build:** | Production Cloud Portal (`v1.0.0`) |

> [!NOTE]
> **REGULATORY COMPLIANCE & PRIVACY NOTICE:**  
> Participation is voluntary. Gathered evaluations will be used strictly for capstone research, software quality benchmarking, and institutional compliance under Republic Act No. 10173 (Data Privacy Act of 2012) and ISO/IEC 25010:2023. Personal information, if collected, is protected under the principle of data minimization.

---

## PART 1: SYSTEM TESTING AND ACCEPTANCE MATRIX

*Direction: Execute each technical test scenario in sequence. Mark each item as Pass, Fail, Needs Revision (Rev.), or N/A. Document observations or latency bottlenecks in the Remarks column.*

| No. | Architectural Domain | Test Scenario & Verification Protocol | Expected Technical Outcome | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Authentication & Password Hardening** | Inject SQL syntax payloads (`' OR 1=1--`) and brute-force password attempts on `/login`. Verify Bcrypt password hashing (`cost=12`), rate-limiting middleware (`5 attempts/min`), and CSRF token binding. | Injection rejected via Eloquent PDO binding; passwords irreversibly hashed; IP rate-limiter returns HTTP `429 Too Many Requests` on 6th failed attempt. | SQL injection blocked; Bcrypt cost verified; rate limiter throttled attacks. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | OWASP Top 10 A03/A07 compliant. |
| **2** | **MFA & Zero-Knowledge Hashing** | Trigger 6-digit OTP delivery. Inspect database column `otp_code` in `users`. Verify SHA-256 cryptographic hashing at rest, 10-minute dynamic TTL, and bypass guards. | OTP stored strictly as 64-character SHA-256 hash; expired OTP rejected; demo OTP accepted only for configured test fixtures. | SHA-256 hash verified at rest; 10-min countdown timer functional; demo OTP bypass verified. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Prevents plaintext OTP exposure in DB dumps. |
| **3** | **AES-256 Column Encryption** | Inspect raw database contents for sensitive student attributes (`clsu_id_number`, contact numbers, guardian information) in `student_profiles`. | Data stored as AES-256-CBC ciphertext; raw database queries return unintelligible ciphertext; decrypted in-memory only for authenticated sessions. | Column encryption verified via database inspection; runtime Eloquent accessors decrypt smoothly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Complies with R.A. 10173 at-rest security. |
| **4** | **RBAC & Authorization Gates** | Authenticate as Student and attempt direct URL navigation to privileged endpoints (`/admin/dashboard`, `/superadmin/users`, `/superadmin/settings`, `/superadmin/analytics`). | HTTP `403 Forbidden` or redirection to unauthorized error page triggered by `CheckRole` middleware; zero administrative data leaked. | Access strictly blocked by middleware; role segregation maintained. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Strong principle of least privilege. |
| **5** | **AI Forensic ELA-CNN Pipeline** | Submit a tampered Certificate of Grades with edited numerical grades and cloned university seal. Trigger forensic analysis. Inspect ELA computation (Q=95), ResNet-50 inference, and Grad-CAM explainability heatmap. | System outputs Error Level Analysis difference map, returns Fraud Probability Score (0–100%), assigns risk tier (Low, Moderate, High), and overlays Grad-CAM heatmap highlighting edited grade bounding box. | ELA-CNN generated accurate FPS; Grad-CAM heatmap clearly localized manipulated regions; human decision override intact. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | High forensic explainability for human decision support. |
| **6** | **Tamper-Evident Audit Logging** | Perform critical administrative actions (approve application, modify global setting, export student data). Inspect log tables (`admin_action_logs`, `config_change_logs`, `export_access_logs`). | Immutable log entries created with actor ID, IP address, user-agent hash, timestamp, and JSON before/after payload diffs. | Structured logs accurately captured actor details and state diffs; exports logged. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Satisfies statutory non-repudiation requirements. |
| **7** | **Session & Transport Hardening** | Inspect HTTP response headers and session cookie attributes via browser DevTools on authenticated HTTPS connection. | Response includes `Strict-Transport-Security`, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `CSP`; cookies flagged `Secure`, `HttpOnly`, `SameSite=Lax`. | Security headers verified; cookie hardening confirmed in production configuration. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Mitigates clickjacking, XSS, and MIME-sniffing. |
| **8** | **Official PDF & Isolated Print Engine** | Generate 1-page official applicant evaluation form with verification checklist, QR clearance code, and university seal. Trigger print engine. | 1-page letter PDF generated matching web preview 1:1; isolated iframe print engine executes without blank pages or layout clipping; QR code leads to signed verification endpoint. | High-fidelity PDF generated; isolated iframe print clean; QR validation route functional. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Meets institutional documentation standards. |
| **9** | **Asynchronous Queue & Fault Recovery** | Dispatch heavy batch AI forensic requests. Inspect database job queue, worker execution, and exponential backoff retry schedules (`[15s, 45s, 90s, 180s, 360s]`). | Asynchronous queue worker picks up tasks without blocking web UI thread; cold-start timeouts and microservice reconnections handled gracefully. | Job processed asynchronously; retry backoff handled cold-start connection delays gracefully. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Decouples heavy AI compute from HTTP cycle. |
| **10** | **Database Portability & Asset Optimization** | Verify database migrations execute seamlessly across SQLite (dev) and PostgreSQL (prod). Evaluate eager loading query efficiency and Gzip compression on static assets. | Schema portable without database-specific syntax; zero N+1 queries during application list rendering; Gzip compression achieves >75% asset reduction. | Schema executed without error; eager loading verified in Laravel Debugbar; sub-second page rendering recorded. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | High maintainability and dev/prod parity. |

---

## PART 2: ISO/IEC 25010:2023 PRODUCT QUALITY EVALUATION

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
| 4.4 | The user interface provides modern typography, high contrast, and responsive viewports adhering to accessibility standards. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
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

## PART 3: ACCEPTANCE RESULT & SIGN-OFF ENDORSEMENT

#### Overall System Acceptance Decision
- `[  ]` **ACCEPTED** — Major required functions, security controls, and architectures operated satisfactorily; ready for live production deployment.
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

### Statistical Scoring Guide (Mean Range Interpretation)

| Mean Range | Verbal Interpretation | Qualitative Standard Description |
| :---: | :--- | :--- |
| **4.21 – 5.00** | **Strongly Agree / Very High Quality** | Exemplary implementation; significantly exceeds operational benchmarks. |
| **3.41 – 4.20** | **Agree / High Quality** | Robust implementation; meets all professional and operational standards. |
| **2.61 – 3.40** | **Moderate Quality** | Acceptable; minor architectural or cosmetic enhancements recommended. |
| **1.81 – 2.60** | **Disagree / Low Quality** | Substandard; notable technical or usability deficiencies require remediation. |
| **1.00 – 1.80** | **Strongly Disagree / Very Low Quality** | Critically deficient; rejected in current form. |

---
**END OF IT EXPERT EVALUATION FORM**
