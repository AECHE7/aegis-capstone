#!/usr/bin/env python3
"""
A.E.G.I.S. System Tester & Evaluator Visual Operations Guide DOCX Generator
Generates a publication-grade, institutional Microsoft Word document (.docx)
with embedded high-resolution screenshots, formatted test scenario matrices,
callouts, and ISO/IEC 25010:2023 evaluation rubrics.
"""

import os
from docx import Document
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import qn, nsdecls

def set_cell_background(cell, fill_hex):
    """Set cell background color"""
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    """Set internal cell padding (in dxa: 20 dxa = 1 pt)"""
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def add_callout(doc, text, title="EVALUATOR NOTICE"):
    """Adds an institutional callout box with a styled left border"""
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    set_cell_background(cell, "F0FDF4") # Subtle emerald tint
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)
    
    # Left border styling in CLSU Green
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="none"/><w:left w:val="single" w:sz="24" w:space="0" w:color="0C4E2D"/><w:bottom w:val="none"/><w:right w:val="none"/></w:tcBorders>')
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(4)
    run_title = p.add_run(f"📌 {title}: ")
    run_title.bold = True
    run_title.font.name = "Arial"
    run_title.font.size = Pt(10)
    run_title.font.color.rgb = RGBColor(12, 78, 45) # CLSU Green
    
    run_text = p.add_run(text)
    run_text.font.name = "Arial"
    run_text.font.size = Pt(9.5)
    run_text.font.color.rgb = RGBColor(30, 41, 59)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_figure(doc, img_path, caption_text, width_inches=6.0):
    """Embeds an image centered with a standardized figure caption"""
    if os.path.exists(img_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(img_path, width=Inches(width_inches))
        
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_before = Pt(2)
        p_cap.paragraph_format.space_after = Pt(12)
        run_cap = p_cap.add_run(caption_text)
        run_cap.italic = True
        run_cap.font.name = "Arial"
        run_cap.font.size = Pt(9)
        run_cap.font.color.rgb = RGBColor(100, 116, 139) # text-muted
    else:
        p_err = doc.add_paragraph(f"[Image placeholder: {caption_text}]")
        p_err.italic = True

def build_evaluator_guide():
    print("Initializing A.E.G.I.S. Tester & Evaluator Visual Guide DOCX Generation...")
    doc = Document()
    
    # Configure 1-inch margins
    for sec in doc.sections:
        sec.top_margin = Inches(1.0)
        sec.bottom_margin = Inches(1.0)
        sec.left_margin = Inches(1.0)
        sec.right_margin = Inches(1.0)
        
    # Color palette
    CLSU_GREEN = RGBColor(12, 78, 45)
    CLSU_GOLD = RGBColor(217, 119, 6)
    DARK_SLATE = RGBColor(15, 23, 42)
    MUTED_GRAY = RGBColor(100, 116, 139)
    
    # ── COVER / TITLE HEADER ──────────────────────────────────────────────────
    p_inst = doc.add_paragraph()
    p_inst.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_inst.paragraph_format.space_before = Pt(12)
    p_inst.paragraph_format.space_after = Pt(2)
    run_inst = p_inst.add_run("CENTRAL LUZON STATE UNIVERSITY\nCOLLEGE OF ENGINEERING • DEPARTMENT OF INFORMATION TECHNOLOGY\nOFFICE OF STUDENT AFFAIRS (OSA)")
    run_inst.bold = True
    run_inst.font.name = "Arial"
    run_inst.font.size = Pt(10)
    run_inst.font.color.rgb = CLSU_GREEN
    
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(8)
    p_title.paragraph_format.space_after = Pt(4)
    run_title = p_title.add_run("A.E.G.I.S.")
    run_title.bold = True
    run_title.font.name = "Arial"
    run_title.font.size = Pt(26)
    run_title.font.color.rgb = CLSU_GREEN
    
    p_subtitle = doc.add_paragraph()
    p_subtitle.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_subtitle.paragraph_format.space_after = Pt(6)
    run_sub = p_subtitle.add_run("Academic Evaluation & Grant Integrity System")
    run_sub.bold = True
    run_sub.font.name = "Arial"
    run_sub.font.size = Pt(13)
    run_sub.font.color.rgb = DARK_SLATE
    
    p_doc = doc.add_paragraph()
    p_doc.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_doc.paragraph_format.space_after = Pt(18)
    run_doc = p_doc.add_run("SYSTEM TESTER & EVALUATOR VISUAL OPERATIONS MANUAL\nDocument Reference: CLSU-OSA-AEGIS-TEG-2026-V1 │ Production Release v1.0.0")
    run_doc.font.name = "Arial"
    run_doc.font.size = Pt(9.5)
    run_doc.font.color.rgb = MUTED_GRAY

    # Document Control Table
    table_ctrl = doc.add_table(rows=5, cols=2)
    table_ctrl.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_ctrl.autofit = False
    
    metadata = [
        ("Evaluation Purpose", "User Acceptance Testing (UAT), Capstone Defense Evaluation, & ISO/IEC 25010 Verification"),
        ("Target Evaluators", "Thesis Defense Panelists, IT Expert Evaluators, OSA Staff Evaluators, Student Testers"),
        ("Quality Standard", "ISO/IEC 25010:2023 Systems and Software Quality Requirements and Evaluation (SQuaRE)"),
        ("Testing Host / URL", "Local Server: http://127.0.0.1:8000 │ Cloud Portal: https://clsu.osa.scholarship"),
        ("Evaluation Period", "Academic Year 2025-2026 │ Production Release v1.0.0")
    ]
    
    for idx, (label, val) in enumerate(metadata):
        c0 = table_ctrl.cell(idx, 0)
        c1 = table_ctrl.cell(idx, 1)
        c0.width = Inches(2.2)
        c1.width = Inches(4.3)
        set_cell_margins(c0, 70, 70, 90, 90)
        set_cell_margins(c1, 70, 70, 90, 90)
        set_cell_background(c0, "F8FAFC")
        set_cell_background(c1, "FFFFFF")
        
        p0 = c0.paragraphs[0]
        p0.paragraph_format.space_after = Pt(2)
        r0 = p0.add_run(label)
        r0.bold = True
        r0.font.name = "Arial"
        r0.font.size = Pt(9)
        
        p1 = c1.paragraphs[0]
        p1.paragraph_format.space_after = Pt(2)
        r1 = p1.add_run(val)
        r1.font.name = "Arial"
        r1.font.size = Pt(9)

    doc.add_page_break()

    # ── SECTION 1: EVALUATION ARCHITECTURE & DEMO MATRIX ─────────────────────
    h1 = doc.add_heading("1. Evaluator Architecture & Pre-Configured Test Accounts", level=1)
    h1.paragraph_format.space_before = Pt(14)
    h1.paragraph_format.space_after = Pt(8)
    
    doc.add_paragraph(
        "A.E.G.I.S. is an automated scholarship administration and integrity auditing portal developed "
        "for the Central Luzon State University (CLSU) Office of Student Affairs (OSA). It streamlines "
        "application intake, verifies General Weighted Averages (GWA) via Optical Character Recognition (OCR), "
        "and detects digital document tampering using Error Level Analysis (ELA) combined with ResNet-50 "
        "Convolutional Neural Networks."
    )
    
    add_callout(
        doc,
        "All test accounts listed below are pre-provisioned in the test database with completed student profiles, "
        "assigned scholarship portfolios, and sample evaluation records. Evaluators can sign in directly using "
        "the standard password: password. In demo mode, MFA two-factor email verification is automatically passed.",
        "PRE-PROVISIONED DEMO ENVIRONMENT"
    )

    # Demo Accounts Table
    table_acc = doc.add_table(rows=5, cols=4)
    table_acc.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_acc.autofit = False
    
    acc_data = [
        ("Testing Role", "Login Email", "Password", "Pre-Configured State & Objective"),
        ("Student (Active)", "student@clsu.edu.ph", "password", "Active Application #1 ('Under Review'). Tests 5-stage Pizza Tracker & active grant card."),
        ("Student (Applicant)", "student_apply@clsu.edu.ph", "password", "Zero active applications. Tests 3-step interactive application form & document upload."),
        ("OSA Staff (Evaluator)", "admin@clsu.edu.ph", "password", "Assigned to all 4 scholarships. Tests Priority Queue, 4-pillar forensic review canvas, & actions."),
        ("Director (SuperAdmin)", "director@clsu.edu.ph", "password", "Full executive role. Tests Quotas, Executive Analytics Radar, Compliance Logs, & Staff.")
    ]
    
    for r_idx, row in enumerate(acc_data):
        for c_idx, val in enumerate(row):
            cell = table_acc.cell(r_idx, c_idx)
            set_cell_margins(cell, 70, 70, 80, 80)
            if r_idx == 0:
                set_cell_background(cell, "0C4E2D")
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(2)
                r = p.add_run(val)
                r.bold = True
                r.font.name = "Arial"
                r.font.size = Pt(8.5)
                r.font.color.rgb = RGBColor(255, 255, 255)
            else:
                set_cell_background(cell, "F8FAFC" if r_idx % 2 == 1 else "FFFFFF")
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(2)
                r = p.add_run(val)
                r.font.name = "Arial"
                r.font.size = Pt(8.5)

    doc.add_page_break()

    # ── SECTION 2: TEST TRACK 1 - STUDENT SCHOLARSHIP PORTAL ─────────────────
    h2 = doc.add_heading("2. Test Track 1: Student Scholarship Portal Walkthrough", level=1)
    h2.paragraph_format.space_before = Pt(14)
    h2.paragraph_format.space_after = Pt(8)

    doc.add_heading("2.1 Test Scenario S-1: Secure Authentication & Trusted Device Binding", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_01_Login_Page.png",
        "Figure 1: Institutional Login Portal & Security Options",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 1:\n"
        "• [1] Credentials Entry: Enter student@clsu.edu.ph and password.\n"
        "• [2] Animated Feedback: Observe the 'Authenticating...' spinner and automatic button disable preventing duplicate submissions.\n"
        "• [3] Trusted Device Checkbox: In production, checking 'Remember this device for 30 days' binds an encrypted cookie to the browser's User-Agent hash."
    )

    doc.add_heading("2.2 Test Scenario S-2: Lifecycle Progress Stepper (Pizza Tracker)", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_13_Student_Dashboard.png",
        "Figure 2: Student Dashboard & Real-Time Lifecycle Pizza Tracker",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 2:\n"
        "• [1] Pizza Tracker Stepper: Verify real-time milestone progress across 5 stages: Submitted (✓) → Under Review (Active Pulse) → Integrity Scanned → Approved → Disbursed.\n"
        "• [2] Active Grant Card: Verify grant details for DOST-SEI Merit Scholarship, 2nd Semester AY 2025-2026, and verified GWA (1.45).\n"
        "• [3] Evaluator Remarks Card: Review official feedback from OSA evaluators without requiring physical in-person office visits."
    )

    doc.add_heading("2.3 Test Scenario S-3: 3-Step Interactive Application Stepper", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_14_Student_Apply_Form.png",
        "Figure 3: Student 3-Step Interactive Application Stepper",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 3 (Sign in as student_apply@clsu.edu.ph):\n"
        "• [1] Step 1 (Program Selection): Select desired scholarship program and observe dynamic criteria and renewal caps.\n"
        "• [2] Step 2 (Academic Declarations): Enter declared GWA (e.g. 1.45) with client-side validation enforcing the 1.00–3.00 scale.\n"
        "• [3] Step 3 (Document Upload): Attach Certificate of Grades (COG) with 10 MB limit check and automatic draft saving.\n"
        "• [4] After-Hours Guard: Applications submitted after 5:00 PM PHT display the 'Queued for Next Business Day' operational badge."
    )

    doc.add_page_break()

    # ── SECTION 3: TEST TRACK 2 - OSA EVALUATOR (STAFF) CONSOLE ──────────────
    h3 = doc.add_heading("3. Test Track 2: OSA Evaluator (Staff) Console Walkthrough", level=1)
    h3.paragraph_format.space_before = Pt(14)
    h3.paragraph_format.space_after = Pt(8)

    doc.add_heading("3.1 Test Scenario E-1: Priority Risk Queue & Workload Triage", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png",
        "Figure 4: OSA Evaluator Application Queue & Triage Console",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 4 (Sign in as admin@clsu.edu.ph):\n"
        "• [1] Metrics Summary Bar: View real-time counters for Pending, Under Review, Approved, Rejected, and Archived applications.\n"
        "• [2] Workload Filtering: Filter between 'Assigned to Me' and 'All Applications' across assigned scholarship portfolios.\n"
        "• [3] Priority Risk Sorting: Choose 'Priority Risk' to bubble high fraud score applications (≥ 70.0%) to the top of the queue.\n"
        "• [4] Review Applicant: Click 'Review Applicant' on Application #1 to open the forensic review studio."
    )

    doc.add_heading("3.2 Test Scenario E-2: 4-Pillar Forensic Review Studio & ELA Canvas", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_09_Application_Review_Detail.png",
        "Figure 5: 4-Pillar Forensic Review Studio & ELA Heatmap Canvas",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 5:\n"
        "• [1] Forensic Canvas Layers: Toggle between Original, Annotated (CAM), ELA Compression Map, and Noise Consistency.\n"
        "• [2] Opacity Slider: Adjust blending slider (0% to 100%) to overlay the thermal ELA heatmap directly onto the student transcript.\n"
        "• [3] 4-Pillar Decision Framework: Review Pillar 1 (Syntax Gate), Pillar 2 (OCR GWA Consistency 35%), Pillar 3 (Compression 50%), and Pillar 4 (Metadata Provenance 15%).\n"
        "• [4] Triage Decision: Select Approved or Rejected, add private staff notes, and save with non-repudiation audit logging."
    )

    doc.add_heading("3.3 Test Scenario E-3: Institutional Announcement Management", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_11_Announcements.png",
        "Figure 6: Institutional Announcement Management Console",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 6:\n"
        "• [1] Broadcast Notifications: Evaluators can broadcast deadline notices, missing grade slip warnings, and stipend release schedules.\n"
        "• [2] Audience Targeting: Filter announcements between 'All Users' or 'Students Only'."
    )

    doc.add_page_break()

    # ── SECTION 4: TEST TRACK 3 - OSA DIRECTOR GOVERNANCE ─────────────────────
    h4 = doc.add_heading("4. Test Track 3: OSA Director (SuperAdmin) Governance Walkthrough", level=1)
    h4.paragraph_format.space_before = Pt(14)
    h4.paragraph_format.space_after = Pt(8)

    doc.add_heading("4.1 Test Scenario D-1: Scholarship Program & Quota Management", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_02_Scholarship_Programs.png",
        "Figure 7: Scholarship Program Configuration & Quotas",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 7 (Sign in as director@clsu.edu.ph):\n"
        "• [1] Program Creation: Define funding sources, minimum GWA cutoffs, and maximum renewal counts.\n"
        "• [2] Quota Caps: Set maximum beneficiaries per academic term or leave open.\n"
        "• [3] Status Toggling: Toggling a program off immediately hides it from new student intake options."
    )

    doc.add_heading("4.2 Test Scenario D-2: Executive Analytics & ISO/IEC 25010 Quality Radar", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png",
        "Figure 8: Executive Analytics Dashboard & ISO 25010 Quality Radar",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 8:\n"
        "• [1] Executive KPIs: Grade Integrity Index, Turnaround Velocity in days, and total disbursed grants.\n"
        "• [2] College Distribution: Allocation breakdown across all 9 CLSU academic colleges.\n"
        "• [3] ISO 25010 Quality Radar: Live radar chart aggregating evaluator satisfaction across Functional Suitability, Usability, Reliability, and Security."
    )

    doc.add_heading("4.3 Test Scenario D-3: Compliance Export Hub & 7-Tier Audit Trail", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_05_Audit_Logs.png",
        "Figure 9: Compliance Export Hub & Evaluator Audit Table",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 9:\n"
        "• [1] Compliance Export Hub: Generate official CSV and PDF audit logs across 7 security tiers.\n"
        "• [2] Recent Evaluator Decisions: Audit evaluator actions with timestamp, decision badge, and link to authentic applicant PDF forms."
    )

    doc.add_heading("4.4 Test Scenario D-4: Dynamic System Settings & Universal Controls", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_04_System_Settings.png",
        "Figure 10: Dynamic System Settings & Forensics Calibration",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 10:\n"
        "• [1] AI Sensitivity Threshold: Calibrate default fraud threshold (70.0%) and GWA tolerance (0.01).\n"
        "• [2] Universal Device Revocation: Instantly terminate all remembered 30-day browser sessions across the university."
    )

    doc.add_heading("4.5 Test Scenario D-5: Staff Delegation & Role Management", level=2)
    add_figure(
        doc,
        "thesis_figures/screenshots/Figure_06_Staff_Management.png",
        "Figure 11: Staff Delegation & Role Management Console",
        5.8
    )
    doc.add_paragraph(
        "Operational Verification for Figure 11:\n"
        "• [1] Staff Portfolio Assignment: Partition application volume by assigning specific scholarship programs to evaluators.\n"
        "• [2] Secure Invitation Tokens: Provision new staff via cryptographically signed single-use invitation emails."
    )

    doc.add_page_break()

    # ── SECTION 5: ISO/IEC 25010:2023 EVALUATOR RATING RUBRIC ────────────────
    h5 = doc.add_heading("5. Standardized ISO/IEC 25010:2023 Evaluator Rating Form", level=1)
    h5.paragraph_format.space_before = Pt(14)
    h5.paragraph_format.space_after = Pt(8)

    doc.add_paragraph(
        "Evaluators are requested to rate the software quality across the eight (8) standard characteristics "
        "defined in ISO/IEC 25010:2023. Use the 5-point Likert scale: "
        "5 = Strongly Agree (SA) │ 4 = Agree (A) │ 3 = Neutral (N) │ 2 = Disagree (D) │ 1 = Strongly Disagree (SD)."
    )

    # ISO Rating Table
    table_iso = doc.add_table(rows=9, cols=3)
    table_iso.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_iso.autofit = False
    
    iso_data = [
        ("ISO/IEC 25010 Characteristic", "Evaluation Statement", "Rating (1–5)"),
        ("1. Functional Suitability", "The system provides all required functions for scholarship intake, AI document verification, evaluator triage, and director governance.", " "),
        ("2. Performance Efficiency", "The system responds rapidly (page load < 1.5s), processes AI document scans asynchronously, and handles queue loads without UI freezing.", " "),
        ("3. Compatibility", "The web portal operates consistently across modern web browsers (Chrome, Edge, Firefox, Safari) and renders cleanly on mobile viewports.", " "),
        ("4. Usability", "The user interface is visually polished, intuitive, and easy to navigate with the 5-stage Pizza Tracker, 3-step application form, and clear callouts.", " "),
        ("5. Reliability", "The system exhibits high fault tolerance, gracefully handles offline/cold-start AI scenarios, and auto-saves student form drafts.", " "),
        ("6. Security", "The system enforces strong role-based access control (RBAC), multi-factor authentication, AES-256 student data encryption, and tamper-evident audit trails.", " "),
        ("7. Maintainability", "The system follows modern MVC architecture with modular controllers, reusable Blade components, and comprehensive automated test suites.", " "),
        ("8. Portability", "The application is containerized with Docker multi-stage builds and deploys reliably across local, staging, and cloud production environments.", " ")
    ]
    
    for r_idx, row in enumerate(iso_data):
        for c_idx, val in enumerate(row):
            cell = table_iso.cell(r_idx, c_idx)
            set_cell_margins(cell, 80, 80, 90, 90)
            if r_idx == 0:
                set_cell_background(cell, "0C4E2D")
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(2)
                r = p.add_run(val)
                r.bold = True
                r.font.name = "Arial"
                r.font.size = Pt(9)
                r.font.color.rgb = RGBColor(255, 255, 255)
            else:
                set_cell_background(cell, "F8FAFC" if r_idx % 2 == 1 else "FFFFFF")
                p = cell.paragraphs[0]
                p.paragraph_format.space_after = Pt(2)
                r = p.add_run(val)
                r.font.name = "Arial"
                r.font.size = Pt(8.5)
                if c_idx == 2:
                    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
                    r.bold = True

    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # Evaluator Signature Block
    table_sig = doc.add_table(rows=2, cols=2)
    table_sig.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_sig.autofit = False
    
    c_s0 = table_sig.cell(0, 0)
    c_s1 = table_sig.cell(0, 1)
    c_s0.width = Inches(3.2)
    c_s1.width = Inches(3.2)
    set_cell_margins(c_s0, 60, 60, 80, 80)
    set_cell_margins(c_s1, 60, 60, 80, 80)
    
    p_s0 = c_s0.paragraphs[0]
    p_s0.add_run("Evaluator Signature over Printed Name:\n\n_________________________________________\nDate: ________________________")
    
    p_s1 = c_s1.paragraphs[0]
    p_s1.add_run("Project Leaders / Researchers:\n\nJOHN ANDREI CARILLO / NORIEL S. GADIANO / JOSHUA A. RAZON\nDate: October 2026")

    # Save to target destination
    out_dir = "docs"
    os.makedirs(out_dir, exist_ok=True)
    out_path = os.path.join(out_dir, "AEGIS_TESTER_AND_EVALUATOR_VISUAL_GUIDE.docx")
    doc.save(out_path)
    print(f"Successfully generated evaluator visual guide DOCX: {out_path}")
    
    # Also save a copy to the root for immediate access
    root_path = "AEGIS_TESTER_AND_EVALUATOR_VISUAL_GUIDE.docx"
    doc.save(root_path)
    print(f"Successfully saved root copy: {root_path}")

if __name__ == "__main__":
    build_evaluator_guide()
