# Central Luzon State University
## Office of Student Affairs • Scholarship & Aid Division
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# IT EXPERT SOFTWARE TESTING & EVALUATION FORM
### Technical Quality Assessment based on ISO/IEC 25010:2023 Software Product Quality Standards
**Project:** Academic Evaluation & Grant Integrity System (A.E.G.I.S.)  
**Target Group:** IT Professionals, Software Engineers, Systems Architects, Cybersecurity Specialists, Cloud/Database Administrators & IT Faculty  

---

## PART 1: TECHNICAL TESTING AND VERIFICATION
*For IT Professional / Technical Expert Validation of Capstone Software Project*

**Purpose.** This form documents the technical expert's actual testing and inspection of the developed A.E.G.I.S. system. The IT evaluator should perform representative technical tasks and verify whether the software architecture, security controls, AI forensic pipelines, and database integrity mechanisms function according to technical specifications. Issues identified during testing should be documented and subjected to corrective actions.

### A. Testing Instructions
1. The development team shall briefly orient the IT expert on the system architecture, tech stack, and testing environment.
2. Test the key architectural modules, cryptographic mechanisms, AI forensic pipelines, and security controls.
3. Mark each item as **PASS**, **FAIL**, **NEEDS REVISION**, or **N/A**.
4. Record technical observations, latency anomalies, security concerns, or architectural bottlenecks in the Remarks column.
5. Items marked FAIL or NEEDS REVISION should be recorded in the Issue Log for engineering remediation.

---

### B. Evaluator & Project Metadata

| Field | Details / Evaluator Response | Field | Details / Evaluator Response |
| :--- | :--- | :--- | :--- |
| **Project / System Title:** | A.E.G.I.S. (Automated Evaluation & Grade Integrity System) | **Date of Technical Evaluation:** | ________________________ |
| **Evaluator Name (Optional):**| __________________________________________________ | **Institution / Organization:** | ________________________ |
| **Current Professional Role:**| `[ ]` Software Architect / Engineer<br>`[ ]` Cybersecurity / InfoSec Specialist<br>`[ ]` AI / ML Engineer<br>`[ ]` Cloud / Database Administrator<br>`[ ]` IT Faculty / Academician | **Years of IT Experience:** | `[ ]` 1–3 years<br>`[ ]` 4–6 years<br>`[ ]` 7–10 years<br>`[ ]` Over 10 years |
| **Testing Environment / Deployment:** | `[ ]` Local Staging (PHP 8.2 / SQLite / OPcache)<br>`[ ]` Cloud Container (`aegis-production.onrender.com` / Docker)<br>`[ ]` Hybrid / CI Environment | **Browser & OS Used:** | ________________________ |

---

### C. Technical Test Scenarios Matrix

| No. | Module / Feature | Task / Test Scenario | Expected Result | Actual Result | Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Authentication & Password Security** | Attempt SQL injection bypass (`' OR 1=1--`) and password brute-force on `/login`. Verify Bcrypt hash (`cost=12`), rate-limiting middleware (`5 attempts/min`), and CSRF token binding. | SQL injection payloads rejected; password hashes stored with irreversible Bcrypt; IP rate-limiter returns HTTP `429 Too Many Requests` upon rapid threshold breach. | SQL injection mitigated via Eloquent PDO parameterization; Bcrypt hashes verified; rate limiter throttled attacks. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Bcrypt cost=12 complies with OWASP guidelines. |
| **2** | **Identity Verification & MFA Hashing** | Trigger 6-digit OTP dispatch. Inspect database storage of `otp_code`. Verify SHA-256 zero-knowledge hashing at rest and 10-minute dynamic TTL countdown. | 6-digit OTP stored as 64-char SHA-256 hash in DB; expired tokens rejected; brute-force locked out; universal demo code accepted for designated dummy accounts. | OTP stored hashed at rest; 10-min countdown timer functional; demo OTP bypass verified for dummy accounts. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | SHA-256 zero-knowledge storage prevents DB leak compromise. |
| **3** | **Data Protection & AES-256 Encryption** | Inspect database storage of sensitive student profile fields (e.g. bank account numbers, guardian contact details, and student identities) in `student_profiles`. | Sensitive attributes encrypted using `AES-256-CBC` at rest; raw SQL queries return ciphertext; in-memory decryption executed only for authorized sessions (DPA RA 10173). | Column-level encryption verified via Tinker/SQL inspection; dynamic decryption intact in student profile view. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Compliant with NPC Data Privacy Act of 2012. |
| **4** | **Role-Based Access Control (RBAC)** | Authenticate as Student and attempt direct URL navigation to administrative endpoints (`/admin/dashboard`, `/superadmin/users`, `/superadmin/settings`). | Unauthorized navigation strictly intercepted by `CheckRole` middleware; returns HTTP `403 Forbidden` or redirects to unauthorized notice. | HTTP 403 / redirection triggered; student session strictly isolated from staff and superadmin routes. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Role isolation validated across all controller gates. |
| **5** | **AI Multi-Detector Document Forensics** | Submit Certificate of Grades (COG) with digitally manipulated grades (whiteout, clone-stamp, or font mismatch). Inspect AI pipeline execution and ELA heatmap. | Tesseract OCR extracts GWA; ELA detects compression inconsistencies; ORB clone detector flags copy-paste duplication; weighted fusion generates risk score. | AI microservice accurately detected forged grades; forensic overlays and ELA heatmap rendered in review modal. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Multi-detector fusion mitigates single-detector false positives. |
| **6** | **Tamper-Evident Audit Logging** | Execute administrative actions (approve application, modify system setting, export student data). Verify structured audit trail records. | Structured audit entries created in `admin_action_logs`, `config_change_logs`, and `export_access_logs` with actor ID, IP address, user agent, timestamp, and payload diff. | Audit logs populated accurately with actor IP, UA hash, and JSON diffs; export access logged. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Immutable audit trails satisfy non-repudiation standard. |
| **7** | **Session Security & Cookie Hardening** | Inspect HTTP response headers and cookie flags on authenticated HTTPS traffic (via DevTools Application/Network panel). | `Strict-Transport-Security`, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `CSP` headers active; session cookies flagged `Secure`, `HttpOnly`, `SameSite=Lax`. | All security headers present in HTTP response; session cookies properly hardened for HTTPS reverse proxy. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Reverse-proxy trustProxies configured cleanly. |
| **8** | **Official PDF Generation & Integrity Seal** | Generate and download the official approved scholarship certificate/form with student details, grant allocation, and validation seal. | Vector PDF renders cleanly with official CLSU OSA seal, QR verification code, cryptographic verification link, and director signature line. | PDF generated with high fidelity; QR code leads to live signed verification endpoint; digital seal intact. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Complies with official institutional document standards. |
| **9** | **Asynchronous Queue & Fault Tolerance** | Trigger heavy AI document scan. Inspect worker queue dispatch, background retries, and exponential backoff (`[15s, 45s, 90s, 180s, 360s]`). | AI analysis dispatches to database queue; background worker processes job without freezing UI; cold start 502/503 responses handled gracefully. | Background queue processed jobs asynchronously; cold-start container wake-up retries verified without crashing. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | Prevents web worker timeouts during heavy AI inference. |
| **10** | **Concurrency, Caching & Performance** | Simulate concurrent page loads. Inspect query execution logs for N+1 queries and evaluate `GzipResponse` compression ratio. | Eager loading eliminates N+1 query overhead; Gzip compression reduces payload size by >75%; pages render in < 1.5 seconds. | Zero N+1 queries observed; Gzip reduced assets by 80%; sub-second page rendering recorded on cloud server. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Needs Rev.<br>`[ ]` N/A | OPcache and Laravel route/config caches verified. |

---

### D. Technical Issue / Revision Log

| No. | Issue / Observation | Required Revision / Action | Priority | Retest Result | Remarks |
| :---: | :--- | :--- | :---: | :---: | :--- |
| **1** | | | `[ ]` High<br>`[ ]` Med<br>`[ ]` Low | `[ ]` Passed<br>`[ ]` For Retest | |
| **2** | | | `[ ]` High<br>`[ ]` Med<br>`[ ]` Low | `[ ]` Passed<br>`[ ]` For Retest | |
| **3** | | | `[ ]` High<br>`[ ]` Med<br>`[ ]` Low | `[ ]` Passed<br>`[ ]` For Retest | |
| **4** | | | `[ ]` High<br>`[ ]` Med<br>`[ ]` Low | `[ ]` Passed<br>`[ ]` For Retest | |

---

### E. Technical Testing Result

Based on the technical testing and architectural inspection performed, the system is:

- `[  ]` **ACCEPTED** — major required functions, security controls, and architectures operated satisfactorily and no critical defect prevents production deployment.
- `[  ]` **ACCEPTED WITH MINOR REVISIONS** — the system is technically sound and usable, subject to the minor engineering optimizations listed above.
- `[  ]` **FOR REVISION AND RETESTING** — one or more architectural, security, or functional defects must be remediated before acceptance.

**IT Expert Comments / Technical Recommendations:**  
________________________________________________________________________________________________________________________  
________________________________________________________________________________________________________________________  

<br>

| _________________________________________ | _________________________________________ |
| :---: | :---: |
| **IT Expert Evaluator Signature over Printed Name**<br>Date: ________________________ | **JOSHUA RAZON / NORIEL GADIANO / JOHN ANDREI CARILLO II**<br>Student Researchers / Project Leaders<br>Date: ________________________ |

---

## PART 2: ISO/IEC 25010:2023 PRODUCT QUALITY EVALUATION
*Evaluation Instrument — IT Experts & Technical Specialists*

**Purpose.** This questionnaire gathers structured technical feedback on software product quality after hands-on verification and code/architecture review. The statements are aligned with the ISO/IEC 25010:2023 Systems and software Quality Requirements and Evaluation (SQuaRE) standard across all eight (8) product quality characteristics. Use N/A when a statement is not applicable or cannot reasonably be evaluated.

**Privacy and Voluntary Participation Notice.** Participation is voluntary. Responses will be used only for technical system evaluation, academic documentation, and project improvement. Personal information, if collected, will be safeguarded in accordance with R.A. 10173 (Data Privacy Act of 2012) and will not be disclosed to unauthorized parties. Optional profile fields may be left blank.

### Rating Scale
- **5** — Strongly Agree (SA)
- **4** — Agree (A)
- **3** — Neither Agree nor Disagree (N)
- **2** — Disagree (D)
- **1** — Strongly Disagree (SD)
- **N/A** — Not Applicable / Cannot Evaluate

*Direction: After conducting technical inspection and hands-on testing, check one rating for each statement.*

---

### Detailed Evaluation Instrument (26 Statements across 8 Dimensions)

| No. | Evaluation Statement | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| **1** | The system completely implements all essential scholarship management modules, multi-tiered document evaluation, and notification lifecycles. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2** | The system executes business logic and calculation algorithms (GWA checks, financial thresholds, AI risk scoring) with technical precision. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3** | The functional workflows provide appropriate administrative utilities (bulk actions, live audit logs, triage queue) without redundant operations. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| **4** | The system responds within acceptable latency thresholds (< 1.5 seconds) during typical database queries and page transitions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5** | Server resources (CPU, RAM, database I/O) are utilized efficiently via eager loading, query caching, Gzip compression, and OPcache optimization. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6** | The system manages concurrent applicant uploads and background worker queuing without deadlocks or performance degradation. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. COMPATIBILITY** | | |
| **7** | The application co-exists smoothly in multi-container environments (Docker, PHP-FPM, Alpine Linux) without service contention. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **8** | The system integrates seamlessly with external web services and cloud APIs (Brevo SMTP email, Cloudflare R2 object storage, HuggingFace AI spaces). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. USABILITY (INTERACTION CAPABILITY)** | | |
| **9** | The software provides clear architectural recognizability, intuitive UI patterns, breadcrumbs, and standardized SweetAlert2 dialogs. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **10** | The administrative and student interfaces enable rapid user learnability with minimal training through structured multi-step wizards. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **11** | The application enforces robust client-side and server-side validation rules with accessible error handling to prevent user mistakes. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **12** | The user interface is cleanly styled with modern typography, responsive viewports, and high contrast adhering to accessibility guidelines. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. RELIABILITY** | | |
| **13** | The software demonstrates architectural maturity, passing comprehensive automated unit and feature test suites with zero regressions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **14** | The system provides high operational availability with fault-tolerant fallbacks (database connection retries, email failover drivers). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **15** | The system gracefully recovers from service interruptions (e.g. AI container cold starts) through automated job retry backoffs. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. SECURITY** | | |
| **16** | Unauthorized access to sensitive student records is strictly prevented through role-based middleware, secure sessions, and AES-256 database encryption. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **17** | Data integrity is robustly safeguarded through CSRF token verification, cryptographic URL signatures, and immutable audit logs. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **18** | System activities (approvals, rejections, setting updates, exports) are immutably tied to user identity for complete non-repudiation. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **19** | User identity is verified through cryptographically secure Multi-Factor Authentication (MFA OTP) with SHA-256 hash storage at rest. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. MAINTAINABILITY** | | |
| **20** | The codebase exhibits high modularity following MVC and Clean Architecture standards (Skinny Controllers, Fat Models, Service Layer). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **21** | Software components (forensic analyzers, notification dispatchers, PDF generators, alert engines) are abstracted for code reusability. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **22** | The codebase provides clear architectural documentation, structured logging, and high testability with automated test coverage. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **8. PORTABILITY** | | |
| **23** | The web application adapts seamlessly across modern web browsers (Chrome, Edge, Safari, Firefox) and multi-device form factors. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **24** | The application deployment pipeline is standardized via containerization (Dockerfile, `render.yaml`) and automated database migrations. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

### Overall Technical Assessment

| Statement | Rating |
| :--- | :---: |
| Overall, the system demonstrates high architectural, algorithmic, and software engineering quality. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` |
| The software satisfies institutional production standards and is ready for live operational deployment. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` |
| The cryptographic security controls and AI forensic tamper detection fulfill professional technical benchmarks. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` |

---

### Comments and Recommendations (IT Expert)
- **Architectural features or technical implementations commended:**  
  ________________________________________________________________________________________________________________________  
- **Technical problems, vulnerabilities, or bottlenecks observed:**  
  ________________________________________________________________________________________________________________________  
- **Suggested engineering or infrastructure improvements:**  
  ________________________________________________________________________________________________________________________  

---

### Researcher / Instructor Scoring Guide

| Mean Range | Interpretation / Technical Quality Level |
| :---: | :--- |
| **4.21 – 5.00** | **Strongly Agree / Very High Quality** (Exemplary implementation, exceeds industry benchmarks) |
| **3.41 – 4.20** | **Agree / High Quality** (Robust implementation, meets professional standards) |
| **2.61 – 3.40** | **Neither Agree nor Disagree / Moderate Quality** (Acceptable, minor technical optimizations recommended) |
| **1.81 – 2.60** | **Disagree / Low Quality** (Substandard, features identifiable architectural shortcomings) |
| **1.00 – 1.80** | **Strongly Disagree / Very Low Quality** (Critically deficient implementation) |

---

### Signatures & Institutional Endorsement

| _________________________________________ | _________________________________________ |
| :---: | :---: |
| **Evaluator Signature over Printed Name** | **Date of Evaluation** |
| IT Expert / Technical Specialist | |
