# A.E.G.I.S. (Academic Evaluation & Grant Integrity System)
# STANDARDIZED SYSTEM USER & INSTRUCTIONAL OPERATIONS MANUAL
**Document Reference: CLSU-OSA-AEGIS-UIM-2026-V1**  
**Issuing Authority: Office of Student Affairs (OSA) & Department of Information Technology**  
**Central Luzon State University, Science City of Muñoz, Nueva Ecija, Philippines**  
**Applicable System Version: Production Release v1.0.0 (October 2026)**

---

## DOCUMENT CONTROL & COMPLIANCE

| Attribute | Specification |
| :--- | :--- |
| **System Classification** | Institutional Web Portal & AI Forensics Microservice |
| **Legal Compliance** | Republic Act No. 10173 (Data Privacy Act of 2012) & R.A. 11032 (Ease of Doing Business Act) |
| **Security Standard** | OWASP Top 10 Enterprise Compliance, AES-256-CBC At-Rest Encryption |
| **Target Stakeholders** | CLSU Enrolled Students, OSA Scholarship Evaluators (Staff), OSA Director (SuperAdmin), System Administrators |

---

## TABLE OF CONTENTS
1. [General System Architecture & Access Requirements](#1-general-system-architecture--access-requirements)
2. [Module 1: Student Portal Operations Manual](#2-module-1-student-portal-operations-manual)
   - 2.1 [Registration & Institutional Email Verification](#21-registration--institutional-email-verification)
   - 2.2 [Two-Factor Authentication & Device Trust](#22-two-factor-authentication--device-trust)
   - 2.3 [Profile Completion (Cascading Address & Degree Catalog)](#23-profile-completion-cascading-address--degree-catalog)
   - 2.4 [Student Dashboard & Lifecycle Tracker](#24-student-dashboard--lifecycle-tracker)
   - 2.5 [Submitting a Scholarship Application (3-Step Stepper)](#25-submitting-a-scholarship-application-3-step-stepper)
   - 2.6 [Document Re-uploads, Appeals, and Cancellations](#26-document-re-uploads-appeals-and-cancellations)
   - 2.7 [Downloading Official Approved Application PDF](#27-downloading-official-approved-application-pdf)
3. [Module 2: OSA Evaluator (Staff) Operations Manual](#3-module-2-osa-evaluator-staff-operations-manual)
   - 3.1 [Account Activation via Secure Token](#31-staff-account-activation-via-secure-token)
   - 3.2 [Navigating the Application Queue & Filters](#32-navigating-the-application-queue--filters)
   - 3.3 [The 4-Pillar Explainable Forensic Review Studio](#33-the-4-pillar-explainable-forensic-review-studio)
   - 3.4 [GWA Discrepancy & Anomaly Interpretation](#34-gwa-discrepancy--anomaly-interpretation)
   - 3.5 [Rendering Decisions: Approval, Rejection, and Private Notes](#35-rendering-decisions-approval-rejection-and-private-notes)
   - 3.6 [Announcement Board Management](#36-announcement-board-management)
4. [Module 3: OSA Director (SuperAdmin) Operations Manual](#4-module-3-osa-director-superadmin-operations-manual)
   - 4.1 [Scholarship Program Creation & Quota Management](#41-scholarship-program-creation--quota-management)
   - 4.2 [Academic Terms Lifecycle](#42-academic-terms-lifecycle)
   - 4.3 [Executive Analytics Dashboard & ISO/IEC 25010 Quality Metrics](#43-executive-analytics-dashboard--isoiec-25010-quality-metrics)
   - 4.4 [Staff Management & Workload Delegation](#44-staff-management--workload-delegation)
   - 4.5 [Dynamic System Configuration & AI Sensitivity](#45-dynamic-system-configuration--ai-sensitivity)
   - 4.6 [7-Tier Compliance Audits & Official Log Exports](#46-7-tier-compliance-audits--official-log-exports)
   - 4.7 [Master Account Gateway & Director Ownership Transfer](#47-master-account-gateway--director-ownership-transfer)
5. [Module 4: System Administration & DevOps Reference](#5-module-4-system-administration--devops-reference)
   - 5.1 [Container Infrastructure & Web Server Architecture](#51-container-infrastructure--web-server-architecture)
   - 5.2 [Database Maintenance & Schema Migrations](#52-database-maintenance--schema-migrations)
   - 5.3 [Production Monitoring & Error Boundaries](#53-production-monitoring--error-boundaries)
6. [Troubleshooting & Frequently Asked Questions (FAQ)](#6-troubleshooting--frequently-asked-questions-faq)

---

## 1. GENERAL SYSTEM ARCHITECTURE & ACCESS REQUIREMENTS

A.E.G.I.S. is an automated scholarship administration portal designed to streamline application intake, automate verification of student academic transcripts (Certificate of Grades - COG), detect digital document forgery using Error Level Analysis (ELA) and Convolutional Neural Networks (ResNet-50), and enforce accountability across the grant lifecycle.

### 1.1 Technical & Browser Prerequisites
- **Recommended Browsers**: Google Chrome (v110+), Mozilla Firefox (v115+), Microsoft Edge (v110+), Apple Safari (v16+).
- **Supported Formats for Document Upload**: High-resolution image files (`.jpg`, `.jpeg`, `.png`) or portable document formats (`.pdf`).
- **Maximum Upload Payload**: 10.0 MB per uploaded document.
- **Network Bandwidth**: Minimum 512 Kbps internet connection (mobile data or campus Wi-Fi).

---

## 2. MODULE 1: STUDENT PORTAL OPERATIONS MANUAL

### 2.1 Registration & Institutional Email Verification
1. Access the official URL: `https://your-domain.clsu.edu.ph/register`.
2. Enter your complete legal name, valid CLSU student email address (`@clsu.edu.ph`), and a secure password (minimum 8 characters with numbers, symbols, and uppercase letters).
3. Click **"Create Student Account"**.
4. The system transmits a cryptographically signed verification link to your CLSU inbox. Open your university Gmail account, locate the message from `noreply@clsu-aegis.ph`, and click **"Verify Email Address"**.
5. Once verified, the system automatically redirects you to the Multi-Factor Authentication Gateway.

---

### 2.2 Two-Factor Authentication & Device Trust
```
+-------------------------------------------------------------------------------+
| Figure 1: Institutional Login & Two-Factor Authentication (MFA) Interface     |
| [File: thesis_figures/screenshots/Figure_01_Login_Page.png]                   |
+-------------------------------------------------------------------------------+
```
![Figure 1: Institutional Login Interface](../../thesis_figures/screenshots/Figure_01_Login_Page.png)

#### Operational Procedure:
1. Navigate to the login portal `[1]`.
2. Enter your registered email address and password `[2]`.
3. Click **"Sign In to A.E.G.I.S."** `[3]`.
4. When prompted for Multi-Factor Authentication:
   - A single-use 6-digit verification code (valid for 10 minutes) is sent to your inbox.
   - Enter the 6-digit code.
   - *(Optional)* Check **"Remember this trusted device for 30 days"** if you are accessing from a private, personal computer. This issues a cryptographically signed token bound to your browser User-Agent hash.
   - Click **"Verify & Proceed"**.

---

### 2.3 Profile Completion (Cascading Address & Degree Catalog)
Before filing your initial scholarship application, university regulations require a one-time student profile completion:
1. **CLSU Student ID Number**: Enter in the standardized `XX-XXXX` format (e.g., `23-1234`). Non-digits are automatically stripped, and hyphens are auto-formatted. The system computes a SHA-256 blind index hash to guarantee institutional uniqueness without exposing plaintext identifiers.
2. **Academic Unit & Degree Program**:
   - Select your official College (e.g., *College of Engineering*, *College of Agriculture*).
   - The **Degree Program** dropdown dynamically cascades with all CHED-accredited curricula mapped to that college.
3. **Permanent Residential Address (Live PSGC API)**:
   - Select **Province** (priority pinned to Nueva Ecija and Region III).
   - Select **City / Municipality** (all 32 municipalities of Nueva Ecija pre-loaded).
   - Select **Barangay** (all Science City of Muñoz barangays indexed).
   - Enter Street Address / House Number.
4. **Emergency Contact Details**: Provide the legal guardian's name and a valid Philippine 11-digit mobile number (`09XXXXXXXXX`).
5. Click **"Save Profile & Authorize Consent"** to encrypt your records in compliance with R.A. 10173.

---

### 2.4 Student Dashboard & Lifecycle Tracker
```
+-------------------------------------------------------------------------------+
| Figure 2: Student Dashboard & Visual Lifecycle Pizza Tracker                  |
| [File: thesis_figures/screenshots/Figure_13_Student_Dashboard.png]            |
+-------------------------------------------------------------------------------+
```
![Figure 2: Student Dashboard](../../thesis_figures/screenshots/Figure_13_Student_Dashboard.png)

#### Operational Elements:
- **Pizza Tracker Progress Stepper `[1]`**: Visually depicts real-time progression through five key milestones:
  - *Submitted*: Application received by the system.
  - *Under Review*: Assigned to an OSA Evaluator for forensic audit.
  - *Integrity Scanned*: AI document analysis concluded.
  - *Approved / Awarded*: Official grant granted by OSA Director.
  - *Disbursed*: Stipend released or ready for payroll claim.
- **Active Grant Summary Card `[2]`**: Displays the scholarship name, current semester, stipend grant amount, and renewal count.
- **Official Status Remarks `[3]`**: Staff feedback, submission timestamps, and notifications regarding incomplete documents or office hour queues.

---

### 2.5 Submitting a Scholarship Application (3-Step Stepper)
```
+-------------------------------------------------------------------------------+
| Figure 3: Student 3-Step Interactive Application Stepper                      |
| [File: thesis_figures/screenshots/Figure_14_Student_Apply_Form.png]           |
+-------------------------------------------------------------------------------+
```
![Figure 3: Student Application Form](../../thesis_figures/screenshots/Figure_14_Student_Apply_Form.png)

#### Step-by-Step Procedure:
1. Click **"Apply for Scholarship"** on the dashboard navigation bar.
2. **Step 1: Program Selection**:
   - Choose the target scholarship program (e.g., *DOST-SEI Merit Scholarship*, *University Scholar*, *Tulong Dunong Program*).
   - Review minimum GWA requirement, semester availability, and renewal limits.
3. **Step 2: Academic Declarations**:
   - Enter your General Weighted Average (GWA) for the prior academic semester (e.g., `1.45`). Note: In the Philippine grading system, `1.00` is the highest possible grade, and `3.00` is passing.
   - Complete custom program-specific questions (e.g., annual family income, solo parent status).
4. **Step 3: Document Upload (Certificate of Grades)**:
   - Attach your official digital Certificate of Grades (COG) or clear photographic scan.
   - Ensure the image is well-lit, uncropped, and all subject grades and final GWA are legible.
5. Click **"Submit Application to OSA"**.
   - *Note on After-Hours Submissions*: If submitted outside standard OSA office hours (Monday to Friday, 8:00 AM – 5:00 PM PHT), the system automatically flags the submission as *Queued for Next Business Day* and displays the next opening schedule.

---

### 2.6 Document Re-uploads, Appeals, and Cancellations
- **Canceling an Application**: If your application is in *Pending* or *Under Review* status and you wish to retract it, click **"Cancel Application"**.
- **Permanent Withdrawal**: Soft-deleted pending applications may be permanently purged by clicking **"Withdraw & Delete"**.
- **Re-uploading Requested Documents**: If an evaluator requests an updated image due to blurry scanning, navigate to your dashboard banner and click **"Upload Clear Copy"**.

---

### 2.7 Downloading Official Approved Application PDF
Once your scholarship application status changes to **Approved**, a green badge activates on your dashboard:
1. Click **"Download Official Approval Form (PDF)"**.
2. The system renders an institutional PDF bearing the official CLSU seal, application reference number, academic standing, verified GWA, evaluator signature line, and stipend claim instructions.
3. Print or store this document for presentation during physical stipend release.

---

## 3. MODULE 2: OSA EVALUATOR (STAFF) OPERATIONS MANUAL

### 3.1 Staff Account Activation via Secure Token
1. OSA Staff accounts are provisioned exclusively through authorized director invitations.
2. Upon receiving your invitation email, click **"Activate Staff Account"**.
3. Confirm your legal name, university employee credentials, and configure your password.
4. Verify via Multi-Factor Authentication (OTP) to enter the Admin Workspace.

---

### 3.2 Navigating the Application Queue & Filters
```
+-------------------------------------------------------------------------------+
| Figure 4: OSA Evaluator Application Queue & Triage Console                    |
| [File: thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png]      |
+-------------------------------------------------------------------------------+
```
![Figure 4: Evaluator Application Queue](../../thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png)

#### Operational Workflow:
- **Summary Metrics Bar `[1]`**: Displays current total pending applications, under-review applications, approved scholars, and high-risk flags.
- **Program & Status Filter `[2]`**: Filter by scholarship program, academic term, review status (*Pending*, *Under Review*, *Approved*, *Rejected*, *Archived*), or assignment (*Assigned to Me* vs. *Unassigned*).
- **Priority Sorting Engine `[3]`**:
  - *Priority Risk*: Surfaces applications with AI fraud scores $\ge 70.0\%$ to the top of the queue for immediate inspection.
  - *GWA (High-to-Low / Low-to-High)*: Sorts applicants mathematically by academic standing.
  - *Oldest Submissions*: Ensures First-In, First-Out (FIFO) compliance with R.A. 11032 processing speed standards.
- Click **"Review Applicant"** `[4]` to launch the Forensic Decision Studio.

---

### 3.3 The 4-Pillar Explainable Forensic Review Studio
```
+-------------------------------------------------------------------------------+
| Figure 5: Forensic Review Studio & ELA Heatmap Analysis                       |
| [File: thesis_figures/screenshots/Figure_09_Application_Review_Detail.png]    |
+-------------------------------------------------------------------------------+
```
![Figure 5: Forensic Review Studio](../../thesis_figures/screenshots/Figure_09_Application_Review_Detail.png)

The A.E.G.I.S. review studio implements an **Explainable Forensic Decision Framework (EFDF)** that breaks down digital authenticity into four transparent pillars:

| Pillar | Inspection Domain | Contribution Weight | Detection Method |
| :--- | :--- | :--- | :--- |
| **Pillar 1** | **Document Syntax Gate** | Gatekeeper | Rejects non-document memes, cartoons, or unrelated graphic uploads. |
| **Pillar 2** | **OCR Grade Consistency** | 35% | Tesseract OCR extracts GWA and compares against declared value. |
| **Pillar 3** | **Compression & ELA Forensics** | 50% | Error Level Analysis (ELA) + ResNet-50 CNN pinpoints altered pixel clusters. |
| **Pillar 4** | **Metadata Provenance** | 15% | Native EXIF inspector flags image editing software (Photoshop, Photopea). |

#### Visual Inspection Controls:
1. **Interactive Document Viewer `[1]`**: Zoom, pan, and inspect the raw submitted transcript.
2. **ELA Forensic Heatmap Overlay `[2]`**: Displays color-coded thermal activation. Bright magenta/yellow hotspots highlight digital splicing, altered grade digits, or font inconsistencies.
3. **Pillar Metric Scorecard `[3]`**: Displays individual confidence scores and flagged anomaly indicators.

---

### 3.4 GWA Discrepancy & Anomaly Interpretation
The system validates declared vs. extracted grades using a justifiable 4-tier discrepancy engine:
- **Tier 1 — Verified Match ($\Delta \le 0.01$)**: Declared grade matches transcript OCR exactly. No penalty applied.
- **Tier 2 — Minor OCR Variance ($\Delta \le 0.05$)**: Informational advisory flag indicating potential font rounding or single-digit misread. Manual eye check recommended; no fraud accusation.
- **Tier 3 — Grade Inflation ($\text{Declared} < \text{Extracted}$)**: Student declared a better numeric grade than what the transcript reflects. System flags application as *Tampered (Grade Discrepancy)* with a 99.00% fraud score.
- **Tier 4 — Inverse Variance ($\text{Declared} > \text{Extracted}$)**: Student entered a worse grade than earned. Flagged as *Data Entry Input Variance* for staff correction.

---

### 3.5 Rendering Decisions: Approval, Rejection, and Private Notes
1. **1-Click Remarks Presets**: Select from standardized remarks:
   - *GWA verified and authentic transcript confirmed.*
   - *Approved for grant allocation subject to physical COG verification.*
   - *Rejected: Discrepancy between declared GWA and scanned grade sheet.*
   - *Revision requested: Please provide an uncropped, high-resolution scan.*
2. **Private Evaluator Notes**: Record internal staff notes invisible to the student for peer auditing.
3. **Execution**: Click **"Approve Application"** or **"Reject Application"**. The applicant receives an automated email notification with your decision and instructions.

---

### 3.6 Announcement Board Management
```
+-------------------------------------------------------------------------------+
| Figure 6: Institutional Announcement Management Console                       |
| [File: thesis_figures/screenshots/Figure_11_Announcements.png]                |
+-------------------------------------------------------------------------------+
```
![Figure 6: Announcements Manager](../../thesis_figures/screenshots/Figure_11_Announcements.png)

1. Navigate to `/admin/announcements`.
2. Click **"Post New Announcement"** `[1]`.
3. Enter title, priority level (*Normal*, *Important*, *Urgent*), content body, and target audience (*All Students*, *Specific College*).
4. Click **"Publish Announcement"** `[2]` to post immediately to student dashboard feeds.

---

## 4. MODULE 3: OSA DIRECTOR (SUPERADMIN) OPERATIONS MANUAL

### 4.1 Scholarship Program Creation & Quota Management
```
+-------------------------------------------------------------------------------+
| Figure 7: Scholarship Program Configuration & Quotas                          |
| [File: thesis_figures/screenshots/Figure_02_Scholarship_Programs.png]         |
+-------------------------------------------------------------------------------+
```
![Figure 7: Scholarship Programs](../../thesis_figures/screenshots/Figure_02_Scholarship_Programs.png)

1. Navigate to `/superadmin/scholarships`.
2. Click **"Create Scholarship Program"** `[1]`.
3. Define program parameters:
   - **Program Name & Funding Source**: e.g., *CHED Tulong Dunong Program (Government Funded)*.
   - **Minimum GWA Cutoff**: e.g., `1.75`.
   - **Maximum Allowed Renewals**: e.g., `4 semesters`.
   - **Stipend Allocation per Scholar**: e.g., `₱15,000.00 / semester`.
   - **Slot Quota**: Cap maximum beneficiaries or leave open.
4. **Dynamic Custom Fields**: Add custom criteria inputs (e.g., *Certificate of Indigency Upload*, *Barangay Certificate*).
5. Toggle program status to **Active** to open applications to the student body.

---

### 4.2 Academic Terms Lifecycle
1. Access Academic Terms under Director Settings.
2. Define the current semester (e.g., *1st Semester A.Y. 2026-2027*).
3. Exactly one academic term may be flagged as `is_active = true`. Activating a new term automatically closes expired scholarship programs and initializes fresh application limits.

---

### 4.3 Executive Analytics Dashboard & ISO/IEC 25010 Quality Metrics
```
+-------------------------------------------------------------------------------+
| Figure 8: Executive Analytics Dashboard & Quality Radar                       |
| [File: thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png]          |
+-------------------------------------------------------------------------------+
```
![Figure 8: Analytics Dashboard](../../thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png)

#### Executive Intelligence Metrics:
- **Grade Integrity Index `[1]`**: University-wide mean integrity score of all awarded grants ($100\% - \text{Avg Fraud Probability}$).
- **Evaluation Cycle Velocity `[2]`**: Mean days elapsed from student submission to official decision.
- **College Distribution Density `[3]`**: Breakdown of grant awards across all 9 CLSU academic colleges.
- **GWA Distribution Curve `[4]`**: Density histogram categorizing scholars into Excellent (`1.00 - 1.25`), Very Good (`1.26 - 1.50`), and Satisfactory brackets.
- **ISO/IEC 25010 Evaluator Ratings `[5]`**: Aggregated quality scores across Functional Suitability, Usability, Reliability, and Security.

---

### 4.4 Staff Management & Workload Delegation
```
+-------------------------------------------------------------------------------+
| Figure 9: Staff Delegation & Role Management Console                          |
| [File: thesis_figures/screenshots/Figure_06_Staff_Management.png]             |
+-------------------------------------------------------------------------------+
```
![Figure 9: Staff Management](../../thesis_figures/screenshots/Figure_06_Staff_Management.png)

1. Navigate to `/superadmin/staff`.
2. Click **"Invite Staff Member"** `[1]`. Enter the evaluator's institutional email address.
3. **Scholarship Assignment Delegation `[2]`**: Assign specific scholarship programs to evaluators to partition workload. Staff members only view and triage applicants belonging to their assigned programs.
4. **Status Control `[3]`**: Temporarily deactivate or revoke accounts when personnel reassignments occur.

---

### 4.5 Dynamic System Configuration & AI Sensitivity
```
+-------------------------------------------------------------------------------+
| Figure 10: Dynamic System Settings & Forensics Calibration                    |
| [File: thesis_figures/screenshots/Figure_04_System_Settings.png]              |
+-------------------------------------------------------------------------------+
```
![Figure 10: System Settings](../../thesis_figures/screenshots/Figure_04_System_Settings.png)

1. Navigate to `/superadmin/settings`.
2. **AI Fraud Detection Threshold**: Adjust the minimum score required before an application is flagged as high-risk (Default: `70.0%`).
3. **GWA Discrepancy Tolerance**: Set OCR mathematical mismatch tolerance (Default: `0.01`).
4. **MFA Security Enforcement**: Select policy level (*Enforce for All Users*, *Enforce for Staff & Students*, *Optional*).
5. **Universal Security Reset**: Click **"Revoke All Trusted Devices"** to immediately invalidate all 30-day remembered browser sessions across the institution.

---

### 4.6 7-Tier Compliance Audits & Official Log Exports
```
+-------------------------------------------------------------------------------+
| Figure 11: System Audit Trail & Compliance Log Center                         |
| [File: thesis_figures/screenshots/Figure_05_Audit_Logs.png]                   |
+-------------------------------------------------------------------------------+
```
![Figure 11: Audit Logs Console](../../thesis_figures/screenshots/Figure_05_Audit_Logs.png)

A.E.G.I.S. maintains an immutable, tamper-evident audit trail capturing every system interaction:

| Audit Tier | Captured Events | Export Formats |
| :--- | :--- | :--- |
| **Authentication Logs** | Login successes, failures, MFA verifications, session revocations. | CSV / PDF |
| **Admin Action Logs** | Application approvals, rejections, notes updates, and status shifts. | CSV / PDF |
| **AI Scan Logs** | ELA scores, detected software, OCR extractions, and latency logs. | CSV / PDF |
| **Evaluation Decisions** | Formal grant award decisions with evaluator employee IDs. | CSV / PDF |
| **Config Change Logs** | Modifications to fraud thresholds, terms, and security policies. | CSV / PDF |
| **Email Delivery Logs** | Brevo SMTP statuses, recipient timestamps, and delivery errors. | CSV / PDF |
| **Export Access Logs** | Tracks personnel who download student PII and CSV datasets. | CSV / PDF |

---

### 4.7 Master Account Gateway & Director Ownership Transfer
- **Master Role Switcher**: Institutional administrators holding Master credentials can switch between Director, Staff, and Student perspectives using the sidebar role switcher without re-authenticating.
- **Director Ownership Transfer**: When the OSA Directorship transitions, the Master Administrator initiates an atomic ownership invitation (`/master/director-transfer`) to rebind the SuperAdmin role to the incoming Director's verified email.

---

## 5. MODULE 4: SYSTEM ADMINISTRATION & DEVOPS REFERENCE

### 5.1 Container Infrastructure & Web Server Architecture
- **Web Application Container**: Built upon a multi-stage Alpine Linux image running PHP 8.4-FPM, Nginx FastCGI proxy, and OPcache.
- **Process Supervisor (Supervisord)**: Simultaneously monitors PHP-FPM, Nginx on port 10000, and two background queue workers (`php artisan queue:work --tries=3`).
- **Cold-Start Resilience**: The `/ai/wake` and `/scheduler/run` endpoints maintain keep-warm pings to eliminate container spin-up delays.

---

### 5.2 Database Maintenance & Schema Migrations
- **Primary Database Engine**: PostgreSQL (Supabase / Render) in production; SQLite for local development.
- **Migration Execution**: Schema updates are applied strictly using `php artisan migrate --force`.
- **Database Composite Indexes**:
  - `idx_apps_status_archived (status, is_archived)`
  - `idx_apps_assigned_status (assigned_to, status)`
  - `idx_apps_user_term (user_id, academic_term_id)`
  - `idx_apps_created_at (created_at)`

---

### 5.3 Production Monitoring & Error Boundaries
- **Sentry Integration**: Global application errors, unhandled exceptions, and slow database queries are reported in real time via Sentry Laravel SDK.
- **Branded Error Boundaries**: Unhandled exceptions render the branded `500.blade.php`, `404.blade.php`, or `403.blade.php` templates while returning JSON error payloads for AJAX requests.

---

## 6. TROUBLESHOOTING & FREQUENTLY ASKED QUESTIONS (FAQ)

### Q1: I am not receiving my 6-digit MFA verification email.
**A**: Check your spam/junk folder. Ensure your email matches your official `@clsu.edu.ph` address. Evaluators can verify Brevo delivery status under `/superadmin/email-logs`.

### Q2: My uploaded Certificate of Grades was flagged as "Tampered".
**A**: Ensure your photo is not heavily edited, cropped through third-party mobile suites, or compressed multiple times. Take a clean, original photograph of the official paper transcript under natural light.

### Q3: Why does my application say "Queued for Evaluation"?
**A**: Applications submitted after regular office hours (5:00 PM PHT) or over the weekend are safely queued and prioritized on the next business morning at 8:00 AM.

---
**END OF STANDARDIZED USER & INSTRUCTIONAL OPERATIONS MANUAL**
