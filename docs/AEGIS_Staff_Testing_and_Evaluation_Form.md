# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. CAPSTONE TESTING AND EVALUATION FORM
## [OSA SCHOLARSHIP EVALUATOR / STAFF ROLE]
### Operational Intake, Document Verification & AI Decision-Support Assessment based on ISO/IEC 25010:2023

**Project Title:** A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS  
**Document Code:** `CLSU-CEn-DIT-AEGIS-EVAL-STAFF-2026`  
**Classification:** Institutional Capstone Compliance Standard  
**Advisers:** Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT  
**Project Researchers:** John Andrei Carillo, Noriel S. Gadiano, Joshua A. Razon  

---

### EVALUATOR & PROJECT METADATA

| Field | Details / Evaluator Response | Field | Details / Evaluator Response |
| :--- | :--- | :--- | :--- |
| **Staff Evaluator:** | __________________________________________________ | **Designation / Position:** | OSA Scholarship Evaluator / Staff Officer |
| **Unit / Section:** | Student Welfare & Scholarship Division | **Date of Evaluation:** | October 2026 |
| **Testing Module:** | Evaluator Application Queue (`/admin/applications`) | **Software Build:** | Production Web Portal (`v1.0.0`) |

> [!NOTE]
> **REGULATORY COMPLIANCE & PRIVACY NOTICE:**  
> Participation is voluntary. Gathered evaluations will be used strictly for capstone research, software quality benchmarking, and institutional compliance under Republic Act No. 10173 (Data Privacy Act of 2012) and ISO/IEC 25010:2023. Personal information, if collected, is protected under the principle of data minimization.

---

## PART 1: SYSTEM TESTING AND ACCEPTANCE MATRIX

*Direction: Execute each operational test scenario in sequence. Mark each item as Pass, Fail, Needs Revision (Rev.), or N/A. Document observations in the Remarks column.*

| No. | Operational Domain | Test Scenario & Verification Protocol | Expected Operational Outcome | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Application Triage & Assigned Queue** | Access `/admin/applications`. Filter queue by program, status (`Under Review`, `Returned`), college, and search by student ID number. | Queue instantaneously filters matching submissions; priority badges display clearly; after-hours submission flags visible. | Instant filter results; assigned programs filtered accurately. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Streamlines daily intake organization. |
| **2** | **Deep-Dive Review Canvas & Dual Viewer** | Open applicant review canvas (`/admin/review/{id}`). Inspect side-by-side document inspection pane with zoom, pan, and contrast inversion controls. | High-resolution preview of Certificate of Grades (COG) and Certificate of Registration (COR) renders smoothly with fluid zoom and pan. | Document viewer operated cleanly; zoom and invert controls functional. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Eliminates need for external PDF viewers. |
| **3** | **Automated OCR Grade Parsing & GWA Check** | Inspect OCR extracted grade sheet table. Verify parsed course codes, credit units, numerical grades, and computed GWA against student declared GWA. | Parsed grade table highlights any mathematical discrepancies exceeding tolerance (0.01) with distinct warning badges. | Extracted grades displayed accurately; discrepancy flagged when mismatch induced. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Reduces manual calculator tallying time. |
| **4** | **AI Forensic ELA-CNN & Heatmap Inspection** | Inspect AI Analysis Dossier on the review canvas. Review Fraud Probability Score (FPS), Risk Tier badge (Low, Moderate, High), and toggle Grad-CAM heatmap overlay. | Color-coded risk tier displays prominently; Grad-CAM heatmap highlights suspicious altered pixel clusters (e.g. modified grade numbers); human evaluator retains final override authority. | FPS and risk tier clearly displayed; Grad-CAM heatmap overlay toggled smoothly; human decision independence intact. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Transparent AI decision-support without automation bias. |
| **5** | **Application Decisioning (Approve / Reject / Return)** | Process application decisions: (a) Approve grant, (b) Reject with standard justification, or (c) Return for Correction with specific deficiency remarks. | Decision modal requires mandatory justification for rejections/returns; updates status immediately in database; deducts slot quota upon approval. | Decisions submitted smoothly; validation prevented empty remarks on returns. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Structured decision lifecycle. |
| **6** | **Automated Notification Dispatch** | Render an application decision. Verify applicant receives automated status email and in-app bell notification drawer update. | Immediate automated email dispatched via Brevo SMTP containing official status, remarks, and next steps; bell drawer updates badge count. | Automated notification dispatched successfully; delivery recorded in email log. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Eliminates manual individualized emailing. |
| **7** | **Official 1-Page Evaluation PDF Printing** | Click "Generate Official Evaluation Form". Inspect modal preview and trigger print engine via isolated iframe. | Form renders in standard 1-page letter format with CLSU OSA seal, applicant credentials, checklist, QR verification badge, and signature lines without blank pages. | Form printed cleanly in 1 page; QR badge legible; iframe prevented main page disruption. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Publication-ready paper records when required. |
| **8** | **Deficiency Resubmission Re-Evaluation** | Open an application resubmitted by a student following a "Returned for Correction" status. Verify updated files and audit history. | History drawer displays revision timeline; newly uploaded documents replace deficient files; evaluator can review corrections and approve. | Revision timeline clearly tracked previous remarks and newly uploaded files. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Facilitates equitable student remedy. |

---

## PART 2: ISO/IEC 25010:2023 OPERATIONAL QUALITY EVALUATION

*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | Evaluation Statement (Operational Evaluator Perspective) | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 1.1 | The review canvas provides all tools necessary to evaluate student credentials, inspect documents, and render decisions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.2 | OCR grade extraction and GWA discrepancy checks accurately detect mismatches between student input and official documents. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.3 | The triage filtering tools enable efficient organization of applications by scholarship program and processing status. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 2.1 | Uploaded document images and PDF previews open swiftly without delaying the evaluation workflow. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.2 | Recording an evaluation decision and updating the application queue occurs instantly upon submission. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. USABILITY (INTERACTION CAPABILITY)** | | |
| 3.1 | The layout of the evaluation screen is logical, well-structured, and comfortable for extended daily use. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.2 | Document zoom, pan, and rotation controls are easy and intuitive to operate. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.3 | The interface effectively prevents accidental decisions by requiring explicit confirmation and remarks. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. AI FORENSIC UTILITY & EXPLAINABILITY** | | |
| 4.1 | The Fraud Probability Score (0–100%) and color-coded risk tier badges provide clear, actionable guidance during review. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.2 | The Grad-CAM explainability heatmap overlay helps locate specific suspicious areas on modified documents. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.3 | The system appropriately positions AI as a decision-support aid, ensuring that the human evaluator retains full authority. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. RELIABILITY & ERROR RECOVERY** | | |
| 5.1 | The application queue operates consistently without losing draft notes or entered evaluator comments. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 5.2 | Automated notification emails trigger reliably whenever an application status changes. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. SECURITY & DATA PRIVACY** | | |
| 6.1 | Student personal contact numbers, guardian records, and academic files are shielded from unauthorized exposure. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.2 | Every evaluator action is logged with an immutable timestamp, ensuring fairness and accountability. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. SAFETY** | | |
| 7.1 | The system requires mandatory correction remarks before returning an application, preventing student confusion. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 7.2 | The system issues clear warnings before permanent rejection of an applicant's scholarship grant. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

## PART 3: ACCEPTANCE RESULT & SIGN-OFF ENDORSEMENT

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

### Statistical Scoring Guide (Mean Range Interpretation)

| Mean Range | Verbal Interpretation | Qualitative Standard Description |
| :---: | :--- | :--- |
| **4.21 – 5.00** | **Strongly Agree / Very High Quality** | Exemplary implementation; significantly exceeds operational benchmarks. |
| **3.41 – 4.20** | **Agree / High Quality** | Robust implementation; meets all professional and operational standards. |
| **2.61 – 3.40** | **Moderate Quality** | Acceptable; minor architectural or cosmetic enhancements recommended. |
| **1.81 – 2.60** | **Disagree / Low Quality** | Substandard; notable technical or usability deficiencies require remediation. |
| **1.00 – 1.80** | **Strongly Disagree / Very Low Quality** | Critically deficient; rejected in current form. |

---
**END OF OSA STAFF EVALUATION FORM**
