# CENTRAL LUZON STATE UNIVERSITY
## College of Engineering • Department of Information Technology
**Science City of Muñoz, Nueva Ecija, Philippines**

---

# A.E.G.I.S. CAPSTONE TESTING AND EVALUATION FORM
## [STUDENT APPLICANT / END-USER ROLE]
### Student End-User Experience, Usability & System Acceptability based on ISO/IEC 25010:2023

**Project Title:** A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS  
**Document Code:** `CLSU-CEn-DIT-AEGIS-EVAL-STUDENT-2026`  
**Classification:** Institutional Capstone Compliance Standard  
**Advisers:** Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT  
**Project Researchers:** John Andrei Carillo, Noriel S. Gadiano, Joshua A. Razon  

---

### EVALUATOR & STUDENT METADATA

| Field | Details / Student Response | Field | Details / Student Response |
| :--- | :--- | :--- | :--- |
| **Student Name (Optional):** | __________________________________________________ | **College / Degree Program:** | ________________________ |
| **Year Level:** | `[ ]` 1st `[ ]` 2nd `[ ]` 3rd `[ ]` 4th / Higher | **Device Used for Testing:** | `[ ]` Laptop/PC `[ ]` Phone `[ ]` Tablet |
| **Date of Testing:** | October 2026 | **Testing Portal:** | Student Portal (`/student`) `v1.0.0` |

> [!NOTE]
> **REGULATORY COMPLIANCE & PRIVACY NOTICE:**  
> Participation is voluntary. Gathered evaluations will be used strictly for capstone research, software quality benchmarking, and institutional compliance under Republic Act No. 10173 (Data Privacy Act of 2012) and ISO/IEC 25010:2023. Personal information, if collected, is protected under the principle of data minimization.

---

## PART 1: SYSTEM TESTING AND ACCEPTANCE MATRIX

*Direction: Execute each student application workflow scenario in sequence. Mark each item as Pass, Fail, Needs Revision (Rev.), or N/A. Document comments in the Remarks column.*

| No. | Student Workflow | Test Scenario & Verification Protocol | Expected Student Experience | Actual Result | Verification Status | Remarks |
| :---: | :--- | :--- | :--- | :--- | :---: | :--- |
| **1** | **Registration & Profile Setup** | Register account using institutional `@clsu2.edu.ph` email, set a secure password, and complete academic profile (CLSU ID, College, Course, Year Level). | System enforces institutional email format, securely validates profile details, and grants access to student scholarship dashboard. | Account created; profile saved; verified student badge displayed. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Straightforward registration. |
| **2** | **Catalog Discovery & Filtering** | Browse available scholarships on `/scholarships`. Filter by College, Year Level, and minimum GWA requirements. | Catalog dynamically shows eligible grants, application deadlines, stipend amounts, and required documents. | Filtered instantly; grant details displayed clearly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Clear visibility of grant opportunities. |
| **3** | **Multi-Step Grant Application** | Select an open scholarship, complete program-specific questions, and review requirements checklist. | Step-by-step application form guides user smoothly; indicates progress and prevents skipping required questions. | Wizard guided through inputs clearly; form validation worked. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Well-structured form experience. |
| **4** | **Document Upload & Client Validation** | Upload Certificate of Grades (COG) and Certificate of Registration (COR). Test file format validation (PDF/JPG/PNG) and size limits. | Clear upload drag-and-drop zone; client-side validator rejects oversized or invalid file extensions with helpful instructions. | File upload smooth; document previews generated cleanly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Prevents submission of corrupt files. |
| **5** | **Data Privacy Consent & Final Submission** | Review submission summary, accept Data Privacy Act (R.A. 10173) agreement, and submit application. | Submission confirmed with instant success modal; status updates to 'Pending'; email confirmation received immediately. | Submission receipt confirmed; email confirmation delivered to inbox. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Transparent privacy declaration. |
| **6** | **Live Status Tracking & Announcements** | Track application status on Student Dashboard `/student/dashboard`. Check in-app notification drawer and portal announcements bulletin. | Live progress tracker visually illustrates current stage (`Pending` → `Under Review` → `Approved`); announcements feed informs about university grant updates. | Progress tracker clear; announcements board accessible. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Keeps students informed in real time. |
| **7** | **Correction of Deficient Application** | Open an application marked "Returned for Correction". Read evaluator deficiency remarks, re-upload corrected COG, and resubmit. | Deficiency instructions clearly explained; allows replacement of flagged documents without restarting entire application. | Correction remarks easy to follow; updated document submitted cleanly. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Transparent and fair remedy process. |
| **8** | **Approved Award Clearance Download** | View notification for an approved scholarship. Preview and download official 1-page Scholarship Award & Clearance Certificate. | High-quality 1-page certificate downloads with official university seal and QR code verification badge. | Official clearance PDF downloaded cleanly; QR code verifiable. | `[ ]` Pass<br>`[ ]` Fail<br>`[ ]` Rev.<br>`[ ]` N/A | Instant proof of scholarship grant. |

---

## PART 2: ISO/IEC 25010:2023 END-USER QUALITY EVALUATION

*Rating Scale: 5 = Strongly Agree (SA), 4 = Agree (A), 3 = Neither Agree nor Disagree (N), 2 = Disagree (D), 1 = Strongly Disagree (SD), N/A = Not Applicable*

| No. | Evaluation Statement (Student Applicant Perspective) | Rating |
| :---: | :--- | :---: |
| **1. FUNCTIONAL SUITABILITY** | | |
| 1.1 | The portal provides all functions needed to find, apply for, and monitor university scholarships. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.2 | The system accurately calculates my submitted GWA and reflects my academic eligibility. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 1.3 | The online process eliminates the need to submit physical paper folders at the university office. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **2. PERFORMANCE EFFICIENCY** | | |
| 2.1 | Application pages, catalogs, and status trackers load quickly on my device. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 2.2 | Uploading documents (grade sheets, registration cards) completes without long waiting times. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **3. COMPATIBILITY & MOBILE RESPONSIVENESS** | | |
| 3.1 | The system functions properly on my smartphone, tablet, or laptop browser. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 3.2 | The text, buttons, and upload forms remain readable and easy to tap on smaller mobile screens. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **4. INTERACTION CAPABILITY (USABILITY)** | | |
| 4.1 | It is easy to understand how to use the portal without needing extensive training or instructions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.2 | Form instructions, requirements checklists, and deadline notices are clear and unambiguous. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.3 | The system alerts me helpfully if I miss required questions or upload the wrong file format. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 4.4 | The overall design, colors, and layout look modern, professional, and visually comfortable. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **5. RELIABILITY** | | |
| 5.1 | The portal worked smoothly during submission without freezing or crashing. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 5.2 | The system accurately preserves my entered application information and documents once submitted. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **6. SECURITY & DATA PRIVACY** | | |
| 6.1 | I feel confident that my personal information, grades, and documents are securely protected. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 6.2 | The system clearly informed me of my Data Privacy Act (R.A. 10173) rights prior to submission. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **7. COMMUNICATION & TRANSPARENCY** | | |
| 7.1 | Email notifications and dashboard alerts kept me promptly updated regarding my application progress. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 7.2 | If an application is returned for correction, the remarks clearly explain what documents must be updated. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| **8. OVERALL SATISFACTION** | | |
| 8.1 | Overall, I am highly satisfied with my experience using the A.E.G.I.S. scholarship portal. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 8.2 | I would strongly prefer applying through this digital portal over traditional paper-based submissions. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |
| 8.3 | The system is ready for official university-wide implementation for all CLSU students. | `[ ] 5` `[ ] 4` `[ ] 3` `[ ] 2` `[ ] 1` `[ ] N/A` |

---

## PART 3: ACCEPTANCE RESULT & SIGN-OFF ENDORSEMENT

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

### Statistical Scoring Guide (Mean Range Interpretation)

| Mean Range | Verbal Interpretation | Qualitative Standard Description |
| :---: | :--- | :--- |
| **4.21 – 5.00** | **Strongly Agree / Very High Acceptability** | Exemplary implementation; significantly exceeds expectations. |
| **3.41 – 4.20** | **Agree / High Acceptability** | Robust implementation; meets all student needs and standards. |
| **2.61 – 3.40** | **Moderate Acceptability** | Acceptable; minor cosmetic enhancements recommended. |
| **1.81 – 2.60** | **Disagree / Low Acceptability** | Substandard; usability hurdles require remediation. |
| **1.00 – 1.80** | **Strongly Disagree / Very Low Acceptability** | Critically deficient; rejected in current form. |

---
**END OF STUDENT APPLICANT EVALUATION FORM**
