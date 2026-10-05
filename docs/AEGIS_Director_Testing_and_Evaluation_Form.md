# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. CAPSTONE TESTING AND EVALUATION FORM
## [OSA DIRECTOR / SUPER ADMINISTRATOR ROLE]
### Executive Governance, Policy Administration & Statutory Compliance Assessment based on ISO/IEC 25010:2023

**Project Title:** A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS  
**Document Code:** `CLSU-CEn-DIT-AEGIS-EVAL-DIR-2026`  
**Classification:** Institutional Capstone Compliance Standard  
**Advisers:** Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT  
**Project Researchers:** John Andrei Carillo, Noriel S. Gadiano, Joshua A. Razon  

---

### EVALUATOR & PROJECT METADATA

| Field | Details / Evaluator Response | Field | Details / Evaluator Response |
| :--- | :--- | :--- | :--- |
| **Director / Evaluator:** | __________________________________________________ | **Designation / Position:** | Director / Head, Office of Student Affairs |
| **Client Agency:** | Central Luzon State University — OSA | **Date of Evaluation:** | October 2026 |
| **Testing Portal:** | Executive Director Dashboard (`/superadmin/dashboard`) | **Software Build:** | Production Web Portal (`v1.0.0`) |

> [!NOTE]
> **REGULATORY COMPLIANCE & PRIVACY NOTICE:**  
> Participation is voluntary. Gathered evaluations will be used strictly for capstone research, software quality benchmarking, and institutional compliance under Republic Act No. 10173 (Data Privacy Act of 2012) and ISO/IEC 25010:2023. Personal information, if collected, is protected under the principle of data minimization.

---

## PART 1: SYSTEM TESTING AND ACCEPTANCE MATRIX

*Direction: Execute each executive governance test scenario in sequence. Mark each item as Pass, Fail, Needs Revision (Rev.), or N/A. Document observations in the Remarks column.*

| No. | Governance Domain | Test Scenario & Verification Protocol | Expected Governance Outcome | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Executive KPI Analytics & Dynamic Overhaul** | Navigate to `/superadmin/analytics`. Test 1-click active term filtering, slot quota capacity progress bars, GWA Grade Integrity Index, and forensic anomaly distribution. | Real-time counters compute accurately; slot quotas calculate against real caps; anomaly keys display readable titles; chart states adapt to active semester. | Metrics render dynamically; slot capacity accurate; active term pill filters cleanly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Provides actionable institutional oversight. |
| **2** | **Academic Term & Semester Governance** | Navigate to System Settings `/superadmin/settings`. Review active academic terms. Create new semester, switch active term, and inspect topbar badge synchronization. | System strictly enforces single active term rule; active term badge in topbar updates in real time; past terms archived without deleting student records. | Term creation and activation succeeded; topbar badge updated instantly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Ensures semester-based scholarship tracking. |
| **3** | **Program Lifecycle Governance** | Create a new institutional scholarship program, configure eligibility thresholds (minimum GWA, eligible colleges, maximum slots), and design custom application fields. | Program published instantly in scholarship catalog; quota enforced; custom form questions display correctly in student application form. | Program configured and visible to applicants; slot limits enforced. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Flexible program administration. |
| **4** | **Staff Account & Queue Delegation** | Issue secure email invitation token for a new OSA scholarship evaluator. Assign specific grant programs to evaluator. | Token-based activation link dispatched; invited evaluator registers securely; assigned queues route matching applications directly to evaluator. | Staff invitation received; role and program queues successfully assigned. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Prevents unauthorized staff account creation. |
| **5** | **Unified Communications & Broadcast Center** | Open Communications Center (`/admin/announcements`). Publish an urgent portal bulletin, compose an email broadcast with audience filters, and inspect delivery logs. | Portal announcement renders on student notice board; targeted email broadcast dispatches to filtered student cohorts; logs record delivery count. | Dual-channel communications executed; email logs confirmed dispatch. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Unified communication eliminates disparate tools. |
| **6** | **Statutory Compliance Export Hub** | Navigate to Compliance Export Hub. Select date range presets (`This Year`, `Last 30d`) and generate official CHED StuFAPs and DOST-SEI compliance reports in CSV and PDF. | Formatted masterlist exports generated with applicant demographics, approved grant amounts, GWA ratings, and verification clearance timestamps. | CSV and PDF exports generated instantaneously matching CHED format. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Fulfills statutory regulatory audits. |
| **7** | **System Settings & Forensics Calibration** | Access `/superadmin/settings`. Adjust AI Fraud Detection Threshold (e.g. 70%), GWA discrepancy tolerance (0.01), and MFA enforcement level. | System dynamically updates global settings table; newly processed applications evaluate against new calibration values immediately. | Settings updated cleanly; audit log recorded configuration change diff. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Flexible institutional parameter tuning. |
| **8** | **System Trash & Soft-Deletion Recovery** | Soft-delete a test scholarship program. Navigate to System Trash `/superadmin/trash`. Verify program recovery and permanent purge controls. | Soft-deleted entity hidden from public views; restored cleanly from Trash tab without data loss; audit log captures recovery event. | Trash recovery verified; integrity preserved across associated applications. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Prevents catastrophic accidental data loss. |

---

## PART 2: ISO/IEC 25010:2023 EXECUTIVE QUALITY EVALUATION

*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | Evaluation Statement (Executive Governance Perspective) | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 1.1 | The system provides complete executive tools for managing university scholarship programs, evaluator staffing, and semester terms. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.2 | Real-time analytics, quota utilization tracking, and fraud indices produce accurate and dependable management information. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.3 | The system effectively automates administrative workloads that previously required tedious manual physical record collation. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 2.1 | Executive dashboards and analytics visualizations load swiftly without perceptible delay during operational hours. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.2 | Generation of extensive statutory compliance reports (CSV and PDF masterlists) executes rapidly. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. USABILITY (INTERACTION CAPABILITY)** | | |
| 3.1 | Executive navigation menus, metric summary cards, and quick filter pills are intuitive and straightforward to navigate. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.2 | The guided tour and onboarding cues effectively orient new administrators to portal capabilities. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.3 | Information presentation is visually polished, well-organized, and legible across desktop and laptop screens. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. RELIABILITY** | | |
| 4.1 | The system performs reliably without unexpected crashes, server errors, or interrupted operations. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.2 | Automated email broadcasts and applicant notifications deliver consistently without dropped messages. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. SECURITY & NON-REPUDIATION** | | |
| 5.1 | Access to confidential student financial and academic records is strictly safeguarded against unauthorized staff or external access. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 5.2 | Comprehensive 7-tier audit logs maintain complete accountability by recording every approval, rejection, and configuration update. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 5.3 | Multi-Factor Authentication (MFA) and trusted device controls offer dependable protection for administrative accounts. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. FLEXIBILITY & INSTITUTIONAL COMPLIANCE** | | |
| 6.1 | The system readily accommodates changing institutional scholarship rules, new grant programs, and unique application forms. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.2 | The system supports multi-semester management, allowing smooth transition between academic terms without data loss. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.3 | Exported reports conform to CHED StuFAPs and DOST-SEI institutional auditing and reporting standards. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. SAFETY & RISK REDUCTION** | | |
| 7.1 | The system provides clear confirmation dialogs before high-impact administrative actions (e.g. revoking grants, deleting terms). | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 7.2 | The System Trash repository safeguards against accidental permanent deletion of valuable scholarship programs and records. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

## PART 3: ACCEPTANCE RESULT & SIGN-OFF ENDORSEMENT

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

### Statistical Scoring Guide (Mean Range Interpretation)

| Mean Range | Verbal Interpretation | Qualitative Standard Description |
| :---: | :--- | :--- |
| **4.21 – 5.00** | **Strongly Agree / Very High Quality** | Exemplary implementation; significantly exceeds operational benchmarks. |
| **3.41 – 4.20** | **Agree / High Quality** | Robust implementation; meets all professional and operational standards. |
| **2.61 – 3.40** | **Moderate Quality** | Acceptable; minor architectural or cosmetic enhancements recommended. |
| **1.81 – 2.60** | **Disagree / Low Quality** | Substandard; notable technical or usability deficiencies require remediation. |
| **1.00 – 1.80** | **Strongly Disagree / Very Low Quality** | Critically deficient; rejected in current form. |

---
**END OF OSA DIRECTOR EVALUATION FORM**
