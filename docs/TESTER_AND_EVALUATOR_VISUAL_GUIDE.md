# Central Luzon State University
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S.
### Academic Evaluation & Grant Integrity System
## SYSTEM TESTER & EVALUATOR VISUAL OPERATIONS GUIDE
**Document Reference:** CLSU-OSA-AEGIS-TEG-2026-V1 │ **Release Version:** 1.0.0 (Production Release)  
**Target Audience:** UAT Testers, Software Quality Assurance Engineers, OSA Staff Evaluators, Thesis Defense Panelists, IT Expert Evaluators  

---

## 1. EVALUATION OVERVIEW & OBJECTIVES

### 1.1 Purpose of this Visual Guide
This visual guide is designed for **testers, evaluators, thesis defense committee members, and OSA stakeholders** to facilitate an intuitive, structured, and comprehensive evaluation of the A.E.G.I.S. platform.

By following this guide, evaluators will:
1. Understand the core security, functional, and AI forensic capabilities of the platform.
2. Execute role-based testing using pre-provisioned demo accounts across all three operational tiers (Student, Staff Evaluator, Director/SuperAdmin).
3. Visually verify expected UI behaviors, responsive layouts, automated notifications, and AI forensic feedback.
4. Systematically record evaluations based on the international **ISO/IEC 25010:2023 Software Product Quality Model**.

### 1.2 Evaluation Credentials & Environment Matrix

Evaluators can access the system on the local demonstration host (`http://127.0.0.1:8000`) or the production cloud domain (`https://clsu.osa.scholarship`).

| Testing Role | User Account / Email | Password | Pre-Configured State & Objective |
| :--- | :--- | :---: | :--- |
| **Student (Active)** | `student@clsu.edu.ph` | `password` | Has active Application #1 ("Under Review"). Used to evaluate the **5-Stage Pizza Tracker**, active grant summary card, and remarks. |
| **Student (Applicant)** | `student_apply@clsu.edu.ph` | `password` | Zero active applications. Used to evaluate the **3-Step Interactive Application Stepper**, GWA declarations, and document uploads. |
| **OSA Evaluator (Staff)** | `admin@clsu.edu.ph` | `password` | Assigned to all 4 scholarships. Used to evaluate the **Priority Risk Queue**, **4-Pillar Forensic Review Studio**, and **Fast Triage presets**. |
| **OSA Director (SuperAdmin)** | `director@clsu.edu.ph` | `password` | University Director role. Used to evaluate **Program Management**, **Executive Analytics / Radar**, **Compliance Export Hub**, and **Staff Delegation**. |

> [!NOTE]
> **Demo Environment Automation**: In local and demonstration environments, the system automatically marks demo emails as verified and bypasses email OTP dispatch, allowing evaluators to sign in seamlessly without waiting for email delivery.

---

## 2. TEST TRACK 1: STUDENT SCHOLARSHIP PORTAL

### Test Scenario S-1: Secure Authentication, MFA, & Trusted Device Binding
**Objective:** Verify that students can sign in securely and that trusted hardware cookies bypass repetitive two-factor challenges.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 1 — Institutional Login Portal & Security Options        |
| [File: thesis_figures/screenshots/Figure_01_Login_Page.png]                       |
+-----------------------------------------------------------------------------------+
```
![Figure 1: Institutional Login Portal](../../thesis_figures/screenshots/Figure_01_Login_Page.png)

#### Step-by-Step Test Procedure:
1. Open Google Chrome or Microsoft Edge and navigate to `/login`.
2. Input credentials:
   - Email: `student@clsu.edu.ph` `[1]`
   - Password: `password` `[1]`
3. Observe the login button behavior: when clicked, it displays an animated spinner (`Authenticating...`) and prevents duplicate form submissions.
4. **Expected Result:** The user is authenticated immediately and redirected to the personalized student dashboard.

---

### Test Scenario S-2: Lifecycle Progress Stepper (Pizza Tracker)
**Objective:** Verify that students can monitor their application progress in real time across the 5 standard milestones.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 2 — Student Dashboard & Real-Time Pizza Tracker          |
| [File: thesis_figures/screenshots/Figure_13_Student_Dashboard.png]                |
+-----------------------------------------------------------------------------------+
```
![Figure 2: Student Dashboard](../../thesis_figures/screenshots/Figure_13_Student_Dashboard.png)

#### Step-by-Step Test Procedure:
1. Sign in as `student@clsu.edu.ph`.
2. Review the **Pizza Tracker Stepper `[1]`**:
   - Milestone 1: *Submitted* (Completed with checkmark).
   - Milestone 2: *Under Review* (Active pulse indicator indicating staff assignment).
   - Milestones 3–5: *Integrity Scanned*, *Approved*, and *Disbursed*.
3. Review the **Active Grant Summary Card `[2]`**:
   - Program Name: *DOST-SEI Merit Scholarship*.
   - Academic Term: *2nd Semester AY 2025-2026*.
   - Verified GWA: `1.45`.
4. Review the **Evaluator Remarks Card `[3]`**: Staff feedback regarding document verification is clearly visible.
5. **Expected Result:** The student dashboard provides an instantaneous, transparent view of application status without requiring physical inquiries at OSA.

---

### Test Scenario S-3: 3-Step Interactive Application Stepper
**Objective:** Verify that new applicants experience a guided, validation-guarded application intake workflow.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 3 — Student 3-Step Application Form                      |
| [File: thesis_figures/screenshots/Figure_14_Student_Apply_Form.png]               |
+-----------------------------------------------------------------------------------+
```
![Figure 3: Student Application Form Stepper](../../thesis_figures/screenshots/Figure_14_Student_Apply_Form.png)

#### Step-by-Step Test Procedure:
1. Sign in as `student_apply@clsu.edu.ph` and click **"Apply for Scholarship"** or visit `/apply`.
2. **Step 1: Program Selection `[1]`**: Select *DOST-SEI Merit Scholarship* or *University Scholar*. Notice that criteria, maximum renewal limits, and GWA thresholds are displayed dynamically.
3. **Step 2: Academic Declarations `[2]`**: Input previous semester General Weighted Average (e.g., `1.45`). Notice the client-side validation enforcing the Philippine grading scale (1.00 to 3.00).
4. **Step 3: Document Upload `[3]`**: Attach a digital copy of the Certificate of Grades (COG). Verify file size constraints (Max 10 MB) and accepted MIME types (PDF, JPG, PNG).
5. **Expected Result:** The step indicators smoothly transition between 1, 2, and 3, saving progress automatically. Submissions made after 5:00 PM PHT display the *Queued for Next Business Day* badge.

---

## 3. TEST TRACK 2: OSA EVALUATOR (STAFF) CONSOLE

### Test Scenario E-1: Application Queue Triage & Priority Risk Sorting
**Objective:** Verify that staff evaluators can filter, search, and prioritize incoming applications based on AI fraud scores.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 4 — OSA Evaluator Application Queue & Triage Console     |
| [File: thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png]          |
+-----------------------------------------------------------------------------------+
```
![Figure 4: Admin Application Queue](../../thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png)

#### Step-by-Step Test Procedure:
1. Sign in as `admin@clsu.edu.ph`.
2. Inspect the **Metrics Summary Row `[1]`**: Verify counts for *Pending*, *Under Review*, *Approved*, *Rejected*, and *Archived*.
3. Test the **Workload Filter `[2]`**: Switch between *Assigned to Me* and *All Applications*.
4. Test the **Risk Priority Filter `[3]`**: Select *Priority Risk* to verify that applications with high tampering scores (≥ 70.0%) appear at the top.
5. Click **"Review Applicant" `[4]`** on Application #1 to enter the forensic studio.
6. **Expected Result:** The triage table updates with zero latency, correctly isolating assigned scholarship queues.

---

### Test Scenario E-2: 4-Pillar Forensic Review Studio & ELA Inspection
**Objective:** Verify that staff can inspect document tampering signatures using the Explainable Forensic Decision Framework (EFDF).

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 5 — 4-Pillar Forensic Studio & ELA Heatmap Canvas        |
| [File: thesis_figures/screenshots/Figure_09_Application_Review_Detail.png]        |
+-----------------------------------------------------------------------------------+
```
![Figure 5: Forensic Review Studio](../../thesis_figures/screenshots/Figure_09_Application_Review_Detail.png)

#### Step-by-Step Test Procedure:
1. In Application #1 Review page (`/admin/review/1`), inspect the **Interactive Multi-Spectrum Canvas `[1]`**.
2. Click through the forensic layer switchers:
   - **Original**: Full photographic reproduction of student's transcript.
   - **Annotated (CAM)**: Class Activation Map highlighting regions of convolutional interest.
   - **ELA Compression Map**: Error Level Analysis highlighting JPEG resave discrepancies.
   - **Noise Consistency**: Laplacian variance map showing splicing artifacts.
3. Test the **Overlay Opacity Slider `[2]`**: Slide from 0% to 100% to blend the forensic heatmap directly over the student's document.
4. Inspect the **4-Pillar Forensic Decision Framework Cards `[3]`**:
   - *Pillar 1: Syntax Gate* — Valid transcript structure verified.
   - *Pillar 2: OCR Consistency (35%)* — Extracted GWA (`1.45`) matches Declared GWA (`1.45`) with 0.00 discrepancy.
   - *Pillar 3: Compression Forensics (50%)* — Low ELA variance (0.042) confirms uniform JPEG quantization.
   - *Pillar 4: Metadata Provenance (15%)* — Hardware flatbed scanner signature verified without image-editing software flags.
5. Test the **Evaluation Actions**: Select *Approved*, enter optional remarks, and save.
6. **Expected Result:** The evaluation status updates instantaneously, and an official audit log entry is written to the database.

---

### Test Scenario E-3: Institutional Announcement Board
**Objective:** Verify that evaluators can publish deadline notices and updates to all student portals.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 6 — Institutional Announcement Management Console        |
| [File: thesis_figures/screenshots/Figure_11_Announcements.png]                    |
+-----------------------------------------------------------------------------------+
```
![Figure 6: Announcements Manager](../../thesis_figures/screenshots/Figure_11_Announcements.png)

#### Step-by-Step Test Procedure:
1. Navigate to `/admin/announcements`.
2. Inspect existing announcements: *AY 2025-2026 2nd Semester Intake Schedule*, *Stipend Claim Guidelines*.
3. Test the announcement creator: Click **"Create Announcement"**, input title, content, target audience (`All` or `Students Only`), and save.
4. **Expected Result:** The announcement appears immediately on the admin console and propagates to the student notification center.

---

## 4. TEST TRACK 3: OSA DIRECTOR (SUPERADMIN) GOVERNANCE

### Test Scenario D-1: Scholarship Program & Quota Management
**Objective:** Verify that the director can create new grant programs, set minimum GWA cutoffs, and calibrate semester quota limits.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 7 — Scholarship Program Configuration & Quotas          |
| [File: thesis_figures/screenshots/Figure_02_Scholarship_Programs.png]             |
+-----------------------------------------------------------------------------------+
```
![Figure 7: Scholarship Programs Management](../../thesis_figures/screenshots/Figure_02_Scholarship_Programs.png)

#### Step-by-Step Test Procedure:
1. Sign in as `director@clsu.edu.ph` and navigate to `/superadmin/scholarships`.
2. Review the institutional program catalog: *DOST-SEI Merit*, *University Scholar*, *College Scholar*, *CHED Tulong Dunong*.
3. Verify that each program card displays:
   - Minimum GWA requirement (e.g. `1.45` for University Scholar).
   - Maximum renewal cycles allowed.
   - Real-time application intake counter and active beneficiary quotas.
4. Toggle an active scholarship program status (Open vs Closed).
5. **Expected Result:** Closed scholarship programs are immediately hidden from student application intake options.

---

### Test Scenario D-2: Executive Analytics & ISO/IEC 25010 Quality Radar
**Objective:** Verify that executive leadership can inspect macro-level institutional metrics, college distribution curves, and real-time evaluator quality ratings.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 8 — Executive Analytics & ISO 25010 Quality Radar        |
| [File: thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png]              |
+-----------------------------------------------------------------------------------+
```
![Figure 8: Executive Analytics Dashboard](../../thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png)

#### Step-by-Step Test Procedure:
1. Navigate to `/superadmin/analytics`.
2. Inspect the **Key Performance Indicators (KPIs) `[1]`**:
   - Total Grant Disbursed volume.
   - Mean Grade Integrity Index (100% - Avg Fraud Score).
   - Turnaround Velocity (Average evaluation turnaround in days).
3. Inspect the **Charts & Visualizations `[2]`**:
   - College Distribution Bar Chart (allocations across all 9 CLSU colleges).
   - GWA Density Histogram (applicant academic ratings curve).
   - ISO/IEC 25010 Software Quality Radar Chart (live evaluator perception).
4. **Expected Result:** All charts render with interactive tooltips and dynamic color-coded data series.

---

### Test Scenario D-3: Compliance Export Hub & 7-Tier Audit Trail
**Objective:** Verify that state audit history can be generated across all 7 compliance tiers in official CSV and PDF formats.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 9 — Compliance Export Hub & Evaluator Audit Table        |
| [File: thesis_figures/screenshots/Figure_05_Audit_Logs.png]                       |
+-----------------------------------------------------------------------------------+
```
![Figure 9: Compliance Export Hub](../../thesis_figures/screenshots/Figure_05_Audit_Logs.png)

#### Step-by-Step Test Procedure:
1. On `/superadmin/analytics`, scroll to the **Compliance Export Hub `[1]`**.
2. Select a date range filter (`From` and `To`).
3. Click **"Export CSV"** and **"Export PDF"** across the tiered compliance categories:
   - *Tier 1*: Application Status Logs, AI Document Scan Results, Admin Evaluation Decisions.
   - *Tier 2*: Login & Authentication Logs, Admin Action Audit Trail.
   - *Tier 3*: Configuration Change Logs, Data Export Access Logs.
4. Inspect the **Recent Evaluator Decisions Table `[2]`**: Verify that applicant name, program, decision badge, evaluator ID, and official form link are present.
5. **Expected Result:** Browser initiates instant downloads of standardized CSV and institutional PDF documents matching COA and CHED audit standards.

---

### Test Scenario D-4: Dynamic System Settings & Universal Security Controls
**Objective:** Verify that global AI sensitivity, GWA tolerance, and university-wide device trust can be calibrated dynamically.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 10 — Dynamic System Settings & Forensics Calibration     |
| [File: thesis_figures/screenshots/Figure_04_System_Settings.png]                  |
+-----------------------------------------------------------------------------------+
```
![Figure 10: System Settings](../../thesis_figures/screenshots/Figure_04_System_Settings.png)

#### Step-by-Step Test Procedure:
1. Navigate to `/superadmin/settings`.
2. Inspect the **Forensic Engine Parameters `[1]`**:
   - AI Fraud Score Threshold (Default: `70.0%`).
   - GWA Discrepancy Tolerance Margin (Default: `0.01`).
3. Inspect the **Authentication & Access Controls `[2]`**:
   - MFA Enforcement Mode: *Enforce for All*, *Staff Only*, or *Optional*.
4. Test the **Universal Device Revocation Button `[3]`**: Clicking *Revoke All Active Trusted Devices* immediately clears all remembered device cookies across all users.
5. **Expected Result:** Configuration updates take effect across all application gates without server restart.

---

### Test Scenario D-5: Staff Delegation & Workload Partitioning
**Objective:** Verify that the director can invite evaluators via cryptographically signed tokens and assign scholarship portfolios.

```
+-----------------------------------------------------------------------------------+
| Visual Reference: Figure 11 — Staff Delegation & Role Management Console          |
| [File: thesis_figures/screenshots/Figure_06_Staff_Management.png]                 |
+-----------------------------------------------------------------------------------+
```
![Figure 11: Staff Management](../../thesis_figures/screenshots/Figure_06_Staff_Management.png)

#### Step-by-Step Test Procedure:
1. Navigate to `/superadmin/staff`.
2. Inspect the **Staff Directory `[1]`**: Verify name, email, account status (Active / Deactivated), and assigned scholarship programs.
3. Test **Program Assignment `[2]`**: Check or uncheck assigned scholarship programs for an evaluator and click save.
4. **Expected Result:** The evaluator's queue dynamically filters to display only their assigned scholarship portfolios.

---

## 5. STANDARDIZED ISO/IEC 25010:2023 EVALUATOR RATING RUBRIC

Evaluators are invited to rate the system across all **eight (8) quality characteristics** of the ISO/IEC 25010:2023 standard using the 5-point Likert scale:

| Scale Value | Qualitative Interpretation | Description |
| :---: | :--- | :--- |
| **5** | **Strongly Agree (SA)** | The system exceeds specifications; operates flawlessly with superior user experience. |
| **4** | **Agree (A)** | The system satisfies specifications; functions properly with minor, non-blocking room for enhancement. |
| **3** | **Neutral / Undecided (N)** | The system meets minimum requirements; acceptable performance but requires refinement. |
| **2** | **Disagree (D)** | The system exhibits noticeable limitations or minor functional inconsistencies. |
| **1** | **Strongly Disagree (SD)** | The system fails to meet acceptable requirements; critical deficiencies observed. |

---

### Evaluator Scoring Form

| ISO/IEC 25010 Characteristic | Evaluation Statement | Rating (1–5) | Evaluator Remarks & Observations |
| :--- | :--- | :---: | :--- |
| **1. Functional Suitability** | The system provides all required functions for scholarship intake, AI document verification, evaluator triage, and director governance. | `[ ]` | |
| **2. Performance Efficiency** | The system responds rapidly (page load < 1.5s), processes AI document scans asynchronously, and handles queue loads without UI freezing. | `[ ]` | |
| **3. Compatibility** | The web portal operates consistently across modern web browsers (Chrome, Edge, Firefox, Safari) and renders cleanly on mobile viewports. | `[ ]` | |
| **4. Usability** | The user interface is visually polished, intuitive, and easy to navigate with the 5-stage Pizza Tracker, 3-step application form, and clear callouts. | `[ ]` | |
| **5. Reliability** | The system exhibits high fault tolerance, gracefully handles offline/cold-start AI scenarios, and auto-saves student form drafts. | `[ ]` | |
| **6. Security** | The system enforces strong role-based access control (RBAC), multi-factor authentication, AES-256 student data encryption, and tamper-evident audit trails. | `[ ]` | |
| **7. Maintainability** | The system follows modern MVC architecture with modular controllers, reusable Blade components, and comprehensive automated test suites. | `[ ]` | |
| **8. Portability** | The application is containerized with Docker multi-stage builds and deploys reliably across local, staging, and cloud production environments. | `[ ]` | |

---

## 6. TESTER OBSERVATION & INCIDENT REPORTING MATRIX

If an evaluator or tester observes any defect, visual artifact, or recommendation during verification, please record it in the matrix below:

| Ref ID | Subsystem / View | Observed Behavior or Issue | Severity (`High` / `Med` / `Low`) | Recommended Enhancement | Verification Status |
| :---: | :--- | :--- | :---: | :--- | :---: |
| **OBS-01** | Student Apply Form | Example: File upload drag-and-drop feedback | `Low` | Add visual border color change on drag-over. | `[✓] Implemented` |
| **OBS-02** | Forensic Studio | Example: Opacity slider granularity | `Low` | Support 1% slider step adjustments for overlay. | `[✓] Implemented` |
| **OBS-03** | | | | | |
| **OBS-04** | | | | | |

---

**End of A.E.G.I.S. System Tester & Evaluator Visual Guide**  
*Central Luzon State University • Office of Student Affairs • Capstone Project 2026*
