# Central Luzon State University
## Office of Student Affairs • Scholarship & Aid Division
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# IT EXPERT SOFTWARE TESTING & EVALUATION FORM
### Technical Quality Assessment based on ISO/IEC 25010 Software Engineering Standards
**Project:** Academic Evaluation & Grant Integrity System (A.E.G.I.S.)  
**Target Group:** IT Professionals, Software Engineers, Systems Architects, Cybersecurity Specialists & IT Faculty  

---

## I. IT Expert Profile & Evaluator Credentials

| Field | Detail / Evaluator Response |
| :--- | :--- |
| **Evaluator Name (Optional / Confidential):** | __________________________________________________ |
| **Designation / Current Role:** | __________________________________________________ |
| **Institution / Company / Agency:** | __________________________________________________ |
| **Highest Educational Attainment:** | `[ ]` BS in IT / CS / CpE / IS<br>`[ ]` Master's Degree (MSIT / MIT / MCS)<br>`[ ]` Doctorate Degree (Ph.D. / DIT)<br>`[ ]` Other: _____________________________________ |
| **Area of Technical Specialization:** | `[ ]` Software Architecture & Web Engineering<br>`[ ]` Cybersecurity, InfoSec & Cryptography<br>`[ ]` Artificial Intelligence, Machine Learning & Forensics<br>`[ ]` Database Systems & Cloud Architecture<br>`[ ]` Quality Assurance, Testing & DevSecOps<br>`[ ]` Academician / IT Faculty |
| **Years of Professional IT Experience:** | `[ ]` 1 to 3 years &nbsp;&nbsp;&nbsp;&nbsp; `[ ]` 4 to 6 years &nbsp;&nbsp;&nbsp;&nbsp; `[ ]` 7 to 10 years &nbsp;&nbsp;&nbsp;&nbsp; `[ ]` Over 10 years |
| **Certifications & Affiliations:** | `[ ]` None &nbsp;&nbsp; `[ ]` AWS / Cloud Certified &nbsp;&nbsp; `[ ]` CISSP / CEH / Security+<br>`[ ]` Oracle / DB &nbsp;&nbsp; `[ ]` Agile / Scrum Master &nbsp;&nbsp; `[ ]` Other: ______________ |

---

## II. Hands-On Technical Verification Test Matrix

> **Evaluation Protocol:**  
> Please execute the following hands-on verification test scenarios across the A.E.G.I.S. system. Record your verdict for each scenario as **[P] Pass**, **[F] Fail**, or **[NA] Not Applicable**, along with observed execution latency and technical comments.

| Test ID | Technical Test Scenario | Execution Procedure & Technical Inspection | Expected Technical Standard | Verdict & Remarks |
| :---: | :--- | :--- | :--- | :---: |
| **TC-TECH-01** | **Authentication & Cryptographic Security** | Attempt SQL Injection (`' OR 1=1--`) and brute-force bypass on `/login`. Inspect password hash storage algorithm and rate-limiting middleware (`5 attempts / min`). | SQL injection payload rejected; passwords stored with irreversible Bcrypt (`cost=12`); IP rate-limiter returns HTTP `429 Too Many Requests`. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-02** | **Multi-Factor Authentication (MFA) & Hashing** | Trigger OTP verification flow. Inspect database column `otp_code`. Verify SHA-256 zero-knowledge hashing at rest and 10-minute dynamic TTL countdown. | 6-digit OTP stored as 64-char SHA-256 hash in DB; expired tokens rejected; brute-force locked out; universal demo OTP accepted for designated dummy accounts. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-03** | **Data Protection & Column-Level Encryption** | Inspect database storage of sensitive student profile fields (e.g. bank account numbers, guardian details, and identity documents) in `student_profiles`. | Sensitive columns encrypted with `AES-256-CBC`; raw database records display ciphertext; dynamically decrypted in-memory only for authorized sessions (DPA RA 10173). | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-04** | **Role-Based Access Control (RBAC) & Boundary Isolation** | Authenticate as Student and attempt direct URL navigation to administrative endpoints (`/admin/dashboard`, `/superadmin/users`, `/superadmin/settings`). | HTTP 403 Forbidden or redirect to unauthorized notice; route middleware strictly isolates role boundaries; staff assignments restrict application scope. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-05** | **AI Multi-Detector Image Forensics Pipeline** | Upload a Certificate of Grades (COG) with digitally altered grades (whiteout, clone-stamp, or font mismatch). Inspect AI scan output, ELA heatmap, and OCR score. | Tesseract OCR extracts GWA; ELA detects compression inconsistencies; ORB clone detector flags copy-paste duplication; weighted fusion generates risk score. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-06** | **Tamper-Evident Audit Logging & Accountability** | Execute administrative actions (approve application, modify system setting, export student data). Verify structured audit trail records. | Logs immutably recorded in `admin_action_logs`, `config_change_logs`, and `export_access_logs` with actor ID, IP address, user agent, timestamp, and payload diff. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-07** | **Session Security & Cookie Hardening** | Inspect HTTP response headers and cookie flags on authenticated HTTPS traffic (via DevTools Application/Network panel). | `Strict-Transport-Security`, `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `CSP` headers active; session cookies flagged `Secure`, `HttpOnly`, `SameSite=Lax`. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |
| **TC-TECH-08** | **Concurrency, Caching & Performance Efficiency** | Simulate high concurrent page loads and document uploads. Test queue worker asynchronous processing for background AI scans. | Response compression (Gzip) active; N+1 queries eliminated via eager loading; heavy AI scan jobs handled asynchronously via queue worker without blocking UI. | `[ ]` Pass<br>`[ ]` Fail<br>Latency: _____ms |

---

## III. ISO/IEC 25010 Software Quality Evaluation Instrument

### Rating Scale & Descriptive Guidelines

| Scale | Rating Option | Statistical Mean Range | Qualitative Description & Evaluation Standard |
| :---: | :--- | :---: | :--- |
| **5** | **Strongly Agree (SA)** | 4.20 – 5.00 | Exemplary implementation; exceeds industry benchmarks with zero architectural defects. |
| **4** | **Agree (A)** | 3.40 – 4.19 | Robust implementation; meets all standard technical specifications with negligible observations. |
| **3** | **Moderately Agree (MA)**| 2.60 – 3.39 | Acceptable implementation; satisfies fundamental requirements but has minor technical room for optimization. |
| **2** | **Disagree (D)** | 1.80 – 2.59 | Substandard implementation; features identifiable architectural shortcomings or vulnerability risks. |
| **1** | **Strongly Disagree (SD)**| 1.00 – 1.79 | Critically deficient implementation; fails to meet baseline software quality and security standards. |

---

### Evaluation Statements across 8 ISO/IEC 25010 Dimensions

#### 1. Functional Suitability
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **FS-01** | **Functional Completeness** | The system covers all specified scholarship management tasks, including application submission, multi-tiered document evaluation, status tracking, and announcement distribution. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **FS-02** | **Functional Correctness** | The system executes all computation routines accurately, including GWA pre-screening, income thresholds, academic standing validation, and AI risk scoring. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **FS-03** | **Functional Appropriateness** | The implemented technical features directly facilitate and streamline administrative scholarship workflows without extraneous or redundant operations. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 2. Performance Efficiency
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **PE-01** | **Time Behaviour** | System response times for standard transactions (page renders, application queries, state updates) consistently execute within acceptable latency thresholds (< 1.5 seconds). | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **PE-02** | **Resource Utilization** | Server CPU, RAM, and database I/O resources are consumed efficiently through eager loading, query optimization, response compression (Gzip), and OPcache execution. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **PE-03** | **Capacity & Throughput** | The system effectively manages concurrent applicant uploads and background worker queuing without transaction deadlocks or performance degradation. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 3. Compatibility
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **CO-01** | **Co-existence** | The software operates stably in shared hosting environments and multi-container architectures without conflicting with co-located services or system daemons. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **CO-02** | **Interoperability** | The application integrates seamlessly with external web services and APIs (e.g. Brevo SMTP email delivery, Cloudflare R2 object storage, HuggingFace AI endpoints). | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 4. Usability
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **US-01** | **Appropriateness Recognisability** | The user interface utilizes intuitive design patterns, breadcrumbs, status badges, and semantic layouts that allow users to readily understand system capabilities. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **US-02** | **Learnability** | New students, OSA staff, and administrative evaluators can operate the application with minimal onboarding training via intuitive form wizards and contextual tips. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **US-03** | **User Error Protection** | Interactive inputs feature comprehensive client-side and server-side validation rules, unified reconfirmation dialogues (SweetAlert2), and clear error feedback to mitigate user mistakes. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 5. Reliability
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **RE-01** | **Maturity** | The system demonstrates high operational stability, passing comprehensive automated unit/feature test suites (250+ test cases) without unhandled exceptions. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **RE-02** | **Fault Tolerance** | The system gracefully handles external dependency interruptions (e.g. AI cold start backoff, email failover drivers, and database busy retries) without crashing. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **RE-03** | **Recoverability** | In the event of network disruption or transaction failure, the system preserves state integrity and allows rapid resumption of interrupted workflows. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 6. Security
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **SE-01** | **Confidentiality** | Unauthorized access to private student data is strictly prevented through role-based access control, cryptographic session cookies, and AES-256 column-level database encryption. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **SE-02** | **Integrity** | The system prevents unauthorized modification of applicant records, evaluation scores, and audit trails through CSRF protection, signed URLs, and tamper-evident logging. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **SE-03** | **Non-repudiation** | System transactions (approvals, rejections, settings updates, user deletions) are immutably tied to the executing identity with IP, user agent, and timestamp metadata. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **SE-04** | **Authenticity** | User identity is robustly confirmed through cryptographically secure Multi-Factor Authentication (MFA OTP) with SHA-256 hash storage at rest and brute-force mitigation. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 7. Maintainability
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **MA-01** | **Modularity** | The codebase is structured according to clear architectural boundaries (Controllers, Services, Jobs, Models, Middleware) ensuring changes to one module have minimal unintended impacts. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **MA-02** | **Reusability** | Core components (alert systems, forensic analyzers, notification broadcasters, PDF generators) are abstracted into modular services and Blade components for reuse. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **MA-03** | **Analysability & Testability** | The application provides clear diagnostic logging, audit trails, and automated test fixtures enabling rapid troubleshooting and regression testing. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

#### 8. Portability
| Item Code | Sub-Characteristic | Technical Evaluation Statement | 5 | 4 | 3 | 2 | 1 |
| :---: | :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **PO-01** | **Adaptability** | The application adapts seamlessly across modern web browsers (Chrome, Edge, Firefox, Safari) and screen sizes (Desktop, Tablet, Mobile) with fluid responsiveness. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| **PO-02** | **Installability** | The application deployment pipeline is standardized via containerization (Docker), environment configuration (`.env`), and automated database migration/seeding scripts. | `[ ]` | `[ ]` | `[ ]` | `[ ]` | `[ ]` |

---

## IV. Qualitative Technical Assessment & Remarks

**1. Key Architectural & Technical Strengths of the System:**  
*(What aspects of the system's software architecture, security mechanisms (MFA/AES), or AI forensic pipeline stand out as commendable?)*  
```text


```

**2. Areas for Technical Refinement, Scalability, or Security Hardening:**  
*(What optimizations, architectural improvements, or additional security controls do you suggest prior to widespread university deployment?)*  
```text


```

**3. Observations on AI Tamper Detection Accuracy & Usability in Academic Administration:**  
*(How viable is the Explainable Forensic Decision Framework (EFDF) and multi-detector pipeline for aiding human evaluators at OSA?)*  
```text


```

---

## V. Overall Technical Verdict & Deployment Endorsement

- `[  ]` **FULLY ENDORSED:** The system satisfies all ISO/IEC 25010 software quality benchmarks and is ready for institutional deployment.
- `[  ]` **CONDITIONALLY ENDORSED:** The system meets fundamental requirements; minor technical refinements recommended before full deployment.
- `[  ]` **NOT RECOMMENDED:** Significant architectural or security vulnerabilities exist that must be remediated prior to reconsidering deployment.

<br><br>

| _________________________________________ | _________________________________________ |
| :---: | :---: |
| **Evaluator Signature Over Printed Name** | **Date of Technical Evaluation** |
