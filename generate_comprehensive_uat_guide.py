#!/usr/bin/env python3
"""
A.E.G.I.S. Comprehensive System Testing, Acceptance, and ISO/IEC 25010:2023 Evaluation Manual
Generates publication-grade DOCX and Markdown documents directly incorporating:
1. docs/IT_Expert_Testing_and_ISO25010_Evaluation_Form.docx
2. docs/UAT_Test_Script_Admin_Role.docx
3. docs/UAT_Test_Script_Staff_Role.docx
4. docs/UAT_Test_Script_Student_Role.docx
and embedding all 11 high-resolution system screenshots.
"""

import os
from docx import Document
from docx.shared import Pt, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import qn, nsdecls

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def add_callout(doc, text, title="EVALUATOR OPERATIONAL NOTICE"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    set_cell_background(cell, "F0FDF4") # Subtle emerald tint
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)
    
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(f'<w:tcBorders {nsdecls("w")}><w:top w:val="none"/><w:left w:val="single" w:sz="24" w:space="0" w:color="0C4E2D"/><w:bottom w:val="none"/><w:right w:val="none"/></w:tcBorders>')
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(0)
    p.paragraph_format.space_after = Pt(2)
    run_title = p.add_run(f"📌 {title}: ")
    run_title.bold = True
    run_title.font.name = "Arial"
    run_title.font.size = Pt(9.5)
    run_title.font.color.rgb = RGBColor(12, 78, 45) # CLSU Green
    
    run_text = p.add_run(text)
    run_text.font.name = "Arial"
    run_text.font.size = Pt(9)
    run_text.font.color.rgb = RGBColor(30, 41, 59)
    
    doc.add_paragraph().paragraph_format.space_after = Pt(2)

def add_figure(doc, img_path, caption_text, width_inches=6.0):
    if os.path.exists(img_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(6)
        p_img.paragraph_format.space_after = Pt(2)
        run_img = p_img.add_run()
        run_img.add_picture(img_path, width=Inches(width_inches))
        
        p_cap = doc.add_paragraph()
        p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_cap.paragraph_format.space_before = Pt(2)
        p_cap.paragraph_format.space_after = Pt(8)
        run_cap = p_cap.add_run(caption_text)
        run_cap.italic = True
        run_cap.font.name = "Arial"
        run_cap.font.size = Pt(8.5)
        run_cap.font.color.rgb = RGBColor(100, 116, 139) # text-muted
    else:
        p_err = doc.add_paragraph(f"[Image Missing: {caption_text}]")
        p_err.italic = True

def add_styled_heading(doc, text, level=1):
    h = doc.add_heading(level=level)
    h.paragraph_format.keep_with_next = True
    run = h.add_run(text)
    run.font.name = "Arial"
    if level == 1:
        h.paragraph_format.space_before = Pt(16)
        h.paragraph_format.space_after = Pt(6)
        run.bold = True
        run.font.size = Pt(13)
        run.font.color.rgb = RGBColor(12, 78, 45) # CLSU Green
    elif level == 2:
        h.paragraph_format.space_before = Pt(12)
        h.paragraph_format.space_after = Pt(4)
        run.bold = True
        run.font.size = Pt(11)
        run.font.color.rgb = RGBColor(15, 23, 42)
    elif level == 3:
        h.paragraph_format.space_before = Pt(8)
        h.paragraph_format.space_after = Pt(2)
        run.bold = True
        run.font.size = Pt(10)
        run.font.color.rgb = RGBColor(30, 41, 59)
    return h

def format_table(table, col_widths, col_alignments, header_bg="0C4E2D", header_fg="FFFFFF"):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    # Header row
    hdr_cells = table.rows[0].cells
    for i, title in enumerate(col_widths):
        hdr_cells[i].width = Inches(col_widths[i])
        set_cell_background(hdr_cells[i], header_bg)
        set_cell_margins(hdr_cells[i], top=100, bottom=100, left=120, right=120)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = col_alignments[i]
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(0)
        for r in p.runs:
            r.bold = True
            r.font.name = "Arial"
            r.font.size = Pt(8.5)
            r.font.color.rgb = RGBColor(255, 255, 255) if header_fg == "FFFFFF" else RGBColor(12, 78, 45)
            
    # Data rows
    for r_idx in range(1, len(table.rows)):
        row = table.rows[r_idx]
        bg = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, cell in enumerate(row.cells):
            cell.width = Inches(col_widths[c_idx])
            set_cell_background(cell, bg)
            set_cell_margins(cell, top=70, bottom=70, left=100, right=100)
            p = cell.paragraphs[0]
            p.alignment = col_alignments[c_idx]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            for r in p.runs:
                r.font.name = "Arial"
                r.font.size = Pt(8)
                r.font.color.rgb = RGBColor(30, 41, 59)

def build_comprehensive_manual():
    print("Building Publication-Grade Comprehensive Visual Testing & Evaluation Manual DOCX...")
    doc = Document()
    
    for sec in doc.sections:
        sec.top_margin = Inches(0.8)
        sec.bottom_margin = Inches(0.8)
        sec.left_margin = Inches(0.8)
        sec.right_margin = Inches(0.8)
        
    CLSU_GREEN = RGBColor(12, 78, 45)
    DARK_SLATE = RGBColor(15, 23, 42)
    
    # ── COVER / TITLE HEADER ──────────────────────────────────────────────────
    p_inst = doc.add_paragraph()
    p_inst.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_inst.paragraph_format.space_before = Pt(6)
    p_inst.paragraph_format.space_after = Pt(2)
    run_inst = p_inst.add_run("CENTRAL LUZON STATE UNIVERSITY\nCOLLEGE OF ENGINEERING • DEPARTMENT OF INFORMATION TECHNOLOGY\nOFFICE OF STUDENT AFFAIRS (OSA)")
    run_inst.bold = True
    run_inst.font.name = "Arial"
    run_inst.font.size = Pt(10)
    run_inst.font.color.rgb = CLSU_GREEN
    
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(6)
    p_title.paragraph_format.space_after = Pt(2)
    run_title = p_title.add_run("A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION")
    run_title.bold = True
    run_title.font.name = "Arial"
    run_title.font.size = Pt(12)
    run_title.font.color.rgb = DARK_SLATE
    
    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_before = Pt(0)
    p_sub.paragraph_format.space_after = Pt(10)
    run_sub = p_sub.add_run("COMPREHENSIVE SYSTEM TESTING, ACCEPTANCE, AND ISO/IEC 25010:2023 EVALUATION MANUAL\n[Incorporating Role-Based UAT Test Scripts & IT Expert Technical Verification with System Screenshots]")
    run_sub.bold = True
    run_sub.font.name = "Arial"
    run_sub.font.size = Pt(10)
    run_sub.font.color.rgb = CLSU_GREEN

    add_callout(doc, 
        "This official manual synthesizes the complete User Acceptance Testing (UAT) scripts and IT Expert Evaluation Forms. "
        "Evaluators, administrators, and student testers must follow the step-by-step procedures outlined below, cross-referencing each test scenario "
        "with its corresponding high-resolution system view. Upon completion of testing, respondents must fill out the integrated ISO/IEC 25010:2023 "
        "Product Quality Evaluation Form and sign off on the Acceptance & Issue Log.",
        title="OPERATIONAL TESTING MANDATE & OBJECTIVE"
    )

    # ── DEMO CREDENTIAL MATRIX TABLE ──────────────────────────────────────────
    add_styled_heading(doc, "Pre-Configured Evaluation Accounts & Authentication Matrix", level=2)
    t_creds = doc.add_table(rows=4, cols=4)
    c_hdr = t_creds.rows[0].cells
    c_hdr[0].paragraphs[0].text = "Testing Role / Persona"
    c_hdr[1].paragraphs[0].text = "Institutional Email / Account"
    c_hdr[2].paragraphs[0].text = "Password & Security Token"
    c_hdr[3].paragraphs[0].text = "Primary Verification Focus"
    
    creds_data = [
        ("Student Applicant", "student@clsu.edu.ph\nstudent_apply@clsu.edu.ph", "Password123!\n(OTP Bypassed in Local/Demo Mode)", "Catalog Discovery, 3-Step Stepper, Draft Auto-Save, COG Upload, 5-Stage Tracker, Resubmissions"),
        ("OSA Scholarship Evaluator", "admin@clsu.edu.ph", "Password123!\n(MFA OTP: 123456 or Demo Bypass)", "Triage Queue, 4-Pillar Forensic Review Studio, ELA Canvas, Heatmap Opacity, Fast Remarks"),
        ("Super Admin / OSA Director", "admin@clsu.edu.ph", "Password123!\n(Super Admin Session)", "Quota Governance, Custom Form Builder, RBAC & Staff Assignment, Audit Trail, ISO 25010 Radar")
    ]
    for r_idx, row in enumerate(creds_data, start=1):
        cells = t_creds.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_creds, [1.6, 1.8, 1.6, 1.8], [WD_ALIGN_PARAGRAPH.LEFT]*4)

    doc.add_page_break()

    # =========================================================================
    # PART I: STUDENT APPLICANT ROLE — UAT SCRIPT & EVALUATION
    # =========================================================================
    add_styled_heading(doc, "PART I: STUDENT APPLICANT ROLE — SYSTEM TESTING & EVALUATION", level=1)
    
    p_desc = doc.add_paragraph("Purpose: This section guides undergraduate student testers through the complete scholarship application lifecycle, from registration and profile setup to catalog discovery, multi-step application filing, document upload, and real-time status tracking.")
    p_desc.paragraph_format.space_after = Pt(6)

    add_styled_heading(doc, "Visual Walkthrough & Key Interface Views", level=2)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_01_Login_Page.png", 
               "Figure 1. Student Portal Secure Authentication & Registration Interface (Scenario 1)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_13_Student_Dashboard.png", 
               "Figure 2. Student Dashboard with 5-Stage Pizza Tracker & Notification Bell (Scenarios 2, 3, 6, 7, 8)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_14_Student_Apply_Form.png", 
               "Figure 3. 3-Step Interactive Scholarship Application Stepper with Draft Auto-Save (Scenarios 4, 5)", width_inches=5.8)

    add_styled_heading(doc, "Student Client Test Scenarios (Execution Matrix)", level=2)
    
    t_stud_scen = doc.add_table(rows=9, cols=7)
    s_hdr = t_stud_scen.rows[0].cells
    s_hdr[0].paragraphs[0].text = "No."
    s_hdr[1].paragraphs[0].text = "Module / Feature"
    s_hdr[2].paragraphs[0].text = "Task / Test Scenario"
    s_hdr[3].paragraphs[0].text = "Expected Result"
    s_hdr[4].paragraphs[0].text = "Actual Result"
    s_hdr[5].paragraphs[0].text = "Status"
    s_hdr[6].paragraphs[0].text = "Remarks"

    student_scenarios = [
        ("1", "Student Registration & Security", "Student registers account using institutional '@clsu2.edu.ph' webmail and sets strong password.", "System validates institutional domain, blocks non-CLSU emails, hashes credentials, and triggers verification notice.", "Domain enforced strictly; Bcrypt password hashing active; registration confirmation displayed.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Complies with CLSU single-sign on policy."),
        ("2", "Profile & GWA Setup", "Student fills in demographic info, college/course, year level, contact info, and academic GWA.", "GWA is validated (1.00 to 5.00), profile completeness meter updates to 100%, and data persists accurately.", "Profile persisted accurately; GWA numerical bounds enforced; completion badge displays 100%.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Input masking prevents erroneous GWA entries."),
        ("3", "Catalog Discovery & Filtering", "Student explores available scholarships and filters by criteria (minimum GWA, eligible colleges, category).", "Catalog updates dynamically; eligible scholarships show active 'Apply Now' buttons; ineligible programs show reasons.", "Real-time filtering functional; quota badges render cleanly; application eligibility checks pass.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Clear visual distinction for eligible programs."),
        ("4", "Application & Custom Fields", "Student applies for scholarship and completes custom questionnaire fields (income tier, occupation, essays).", "Dynamic fields render correctly (text, dropdown, file); client draft auto-saves; required validator prevents empty submits.", "Custom inputs rendered dynamically; draft auto-saves to localStorage on input change.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Draft resilience tested with browser refresh."),
        ("5", "Document Upload & Preview", "Student uploads required documents (Certificate of Grades and Certificate of Registration) in PDF/PNG/JPEG.", "File format and size (<10MB) validated; thumbnail preview opens for pre-submission verification.", "MIME type checked client and server side; thumbnail preview modal renders high-resolution preview.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Strict anti-malware MIME verification."),
        ("6", "Submission & Progress Tracker", "Student reviews submission summary, agrees to R.A. 10173 data privacy terms, and submits application.", "Confirmation alert generates unique tracking ID; application status moves to 'Pending Review' on dashboard timeline.", "Confirmation modal triggered; tracking ID generated; 5-stage tracker advances to 'Submitted'.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Data Privacy Act consent checkbox enforced."),
        ("7", "Deficiency Resubmission", "Student opens application marked 'Returned for Correction', views staff remarks, and resubmits corrected file.", "Staff deficiency instructions display in amber alert; flagged field unlocks for re-upload; status updates to 'Resubmitted'.", "Correction notice displayed with staff remarks; document re-upload enabled; re-queued cleanly.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Prevents duplicate application creation."),
        ("8", "Notifications & Official PDF", "Student checks status change notifications and downloads official approved application form with QR seal.", "In-app and email notifications arrive promptly; certified PDF downloads with official CLSU OSA seal and QR verification.", "Notification bell badge updates; clicking redirects to target dossier; certified PDF generates 1:1.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "QR code routes to live verification page.")
    ]
    for r_idx, row in enumerate(student_scenarios, start=1):
        cells = t_stud_scen.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_stud_scen, [0.4, 1.1, 1.5, 1.4, 1.2, 0.6, 0.6], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT])

    # Student Acceptance Signoff Table
    add_styled_heading(doc, "Student Testing Acceptance Result", level=3)
    p_stud_res = doc.add_paragraph("Based on the testing performed, the system is:\n☐ ACCEPTED — major required functions operated satisfactorily and no critical issue prevents intended use.\n☐ ACCEPTED WITH MINOR REVISIONS — the system is generally usable, subject to the corrections listed.\n☐ FOR REVISION AND RETESTING — one or more significant issues must be corrected before acceptance.\n\nStudent Comments / Recommendations: __________________________________________________________________")
    p_stud_res.paragraph_format.space_after = Pt(6)

    # Student ISO 25010 Form
    add_styled_heading(doc, "ISO/IEC 25010:2023 End-User Evaluation Instrument — Student Applicant Role", level=2)
    p_iso_note = doc.add_paragraph("Rating Scale: 5 - Strongly Agree | 4 - Agree | 3 - Neither Agree nor Disagree | 2 - Disagree | 1 - Strongly Disagree | N/A - Not Applicable")
    p_iso_note.paragraph_format.space_after = Pt(4)

    student_iso_items = [
        ("1. FUNCTIONAL SUITABILITY", "", ""),
        ("1", "The system provides all the functions I need to complete my scholarship applications.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2", "The system produces correct and appropriate results based on the academic information and documents I submit.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3", "The available functions help me search, apply for, and track scholarships effectively.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2. PERFORMANCE EFFICIENCY", "", ""),
        ("4", "The system responds within an acceptable amount of time when submitting forms.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5", "Scholarship catalogs, dashboards, and uploaded document previews load without unnecessary delay.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6", "The system performs satisfactorily even during peak application periods.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3. COMPATIBILITY", "", ""),
        ("7", "The system works properly with the laptop, smartphone, or browser (Chrome, Edge, Safari) I use.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("8", "When uploading or viewing PDF and image files (COG/COR), the files render as expected across devices.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("4. INTERACTION CAPABILITY", "", ""),
        ("9", "It is easy to understand what the scholarship portal is intended to do.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("10", "I can learn how to apply for scholarships and track my status with minimal assistance.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("11", "The menus, buttons, labels, and form instructions are clear and easy to understand.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("12", "The system helps prevent mistakes or provides helpful warnings when I omit required fields.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("13", "The interface is clean, organized, readable, and comfortable to use.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5. RELIABILITY", "", ""),
        ("14", "The scholarship portal works consistently without unexpected crashes during submission.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("15", "The portal is available and accessible whenever I need to check my application status.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("16", "The system handles weak internet connections without corrupting or losing my entered application data.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("17", "If an interruption occurs, the auto-save feature restores my application draft reliably.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6. SECURITY", "", ""),
        ("18", "The system allows access to my personal and academic records only to authorized OSA evaluators.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("19", "I feel that my submitted grades, income details, and personal records are adequately protected under R.A. 10173.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("20", "The system appropriately verifies my identity via secure institutional student login.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("21", "My application submissions and document updates are recorded with clear timestamps and receipts.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("7. FLEXIBILITY & SAFETY", "", ""),
        ("22", "The system can support different scholarship types (institutional, government, private/corporate) seamlessly.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("23", "The system remains fully usable when switching between desktop screens and mobile phone displays.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("24", "The system provides appropriate warnings or confirmation dialogs before I submit or cancel an application.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A")
    ]
    t_stud_iso = doc.add_table(rows=len(student_iso_items) + 1, cols=3)
    s_iso_hdr = t_stud_iso.rows[0].cells
    s_iso_hdr[0].paragraphs[0].text = "No."
    s_iso_hdr[1].paragraphs[0].text = "ISO/IEC 25010:2023 Quality Dimension & Evaluation Statement"
    s_iso_hdr[2].paragraphs[0].text = "Rating (1 to 5)"

    for r_idx, row in enumerate(student_iso_items, start=1):
        cells = t_stud_iso.rows[r_idx].cells
        cells[0].paragraphs[0].text = row[0]
        cells[1].paragraphs[0].text = row[1]
        cells[2].paragraphs[0].text = row[2]
        if row[1] == "":
            set_cell_background(cells[0], "E2E8F0")
            set_cell_background(cells[1], "E2E8F0")
            set_cell_background(cells[2], "E2E8F0")
            cells[0].paragraphs[0].runs[0].bold = True
    format_table(t_stud_iso, [0.5, 4.8, 1.5], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER])

    doc.add_page_break()

    # =========================================================================
    # PART II: OSA SCHOLARSHIP EVALUATOR / STAFF ROLE — UAT SCRIPT & EVALUATION
    # =========================================================================
    add_styled_heading(doc, "PART II: OSA SCHOLARSHIP EVALUATOR / STAFF ROLE — SYSTEM TESTING & EVALUATION", level=1)
    
    p_staff_desc = doc.add_paragraph("Purpose: This section guides OSA evaluation officers through the scholarship review studio, including queue filtering, dossier inspection, high-resolution interactive canvas inspection, AI tamper scoring (0-100%), Grad-CAM convolutional heatmap analysis, fast-triage decisioning, and official forensic audit PDF generation.")
    p_staff_desc.paragraph_format.space_after = Pt(6)

    add_styled_heading(doc, "Visual Walkthrough & Key Interface Views", level=2)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_07_Admin_Application_Queue.png", 
               "Figure 4. OSA Scholarship Applications Priority Queue with Fast Risk Filtering (Scenario 2)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_09_Application_Review_Detail.png", 
               "Figure 5. 4-Pillar Document Forensics Review Studio & ELA Heatmap Canvas (Scenarios 3, 4, 5, 6, 7, 8)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_11_Announcements.png", 
               "Figure 6. Institutional Announcements Broadcasting & Notification Engine", width_inches=5.8)

    add_styled_heading(doc, "Staff Evaluator Client Test Scenarios (Execution Matrix)", level=2)
    
    t_staff_scen = doc.add_table(rows=9, cols=7)
    st_hdr = t_staff_scen.rows[0].cells
    st_hdr[0].paragraphs[0].text = "No."
    st_hdr[1].paragraphs[0].text = "Module / Feature"
    st_hdr[2].paragraphs[0].text = "Task / Test Scenario"
    st_hdr[3].paragraphs[0].text = "Expected Result"
    st_hdr[4].paragraphs[0].text = "Actual Result"
    st_hdr[5].paragraphs[0].text = "Status"
    st_hdr[6].paragraphs[0].text = "Remarks"

    staff_scenarios = [
        ("1", "Authentication & MFA Security", "Staff logs in with institutional credentials, inputs 6-digit OTP, and registers trusted device token.", "Authentication succeeds; session encryption and role redirection route user directly to Staff Review Queue.", "Role verified; session cookie hardened; redirected to admin application queue.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Trusted device hash stored in session."),
        ("2", "Application Queue Triage", "Staff filters review queue by scholarship program, term, status (Pending/Returned), and sorts by GWA.", "Queue filters rapidly with real-time record count; priority badges highlight overdue or flagged applications.", "Filtering executes sub-second; risk tier badges (Low/Moderate/High) color-coded properly.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Eliminated N+1 queries via eager loading."),
        ("3", "Applicant Dossier Assessment", "Staff opens application dossier to inspect student academic record, income bracket, and custom responses.", "Clean two-column layout renders full student data; automated eligibility badge verifies GWA compliance.", "Complete student profile, GWA, and custom questions rendered in structured panels.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Eligibility check tags automatically calculated."),
        ("4", "Interactive Canvas Viewer", "Staff inspects Certificate of Grades (COG) using zoom (up to 400%), pan, rotate, and contrast inversion filters.", "Canvas renders smoothly with zero lag; high-contrast filter clearly exposes registrar seal details and eraser marks.", "Zoom, pan, rotation, and high-contrast negative inversion filter work without latency.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Canvas hardware-accelerated via CSS transforms."),
        ("5", "AI Fraud Score & EXIF Fingerprint", "Staff evaluates AI Forensic score (0-100%), 3-tier risk badge, and EXIF software metadata analysis.", "Score and risk tier badge display accurately; benign scanner noise is properly differentiated from heavy edits.", "4-Pillar breakdown rendered: OCR (35%), Compression (25%), Sensor (25%), Metadata (15%).", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Calibrated 70% threshold accommodates mobile scans."),
        ("6", "Grad-CAM Heatmap & ELA Overlay", "Staff toggles Grad-CAM saliency heatmap and ELA overlay on COG canvas, adjusting opacity from 0% to 100%.", "Overlay aligns with document coordinates; localized pixel anomalies and altered grade numbers glow prominently.", "Grad-CAM heatmap aligns with original image; opacity slider operates smoothly from 0 to 100%.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Synchronized canvas pan/zoom preserves overlay alignment."),
        ("7", "Decisioning & Fast-Triage Remarks", "Staff renders decision (Approve/Return/Reject), selects Fast-Triage preset remarks, and confirms action.", "Status updates immediately in database; mandatory remark enforced on returns; automated notification triggered.", "Preset remarks populate remark box in one click; status persists; notification event dispatched.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Mandatory remark prevents empty rejection notices."),
        ("8", "Internal Notes & Audit Log", "Staff saves confidential internal notes and verifies that evaluation history is recorded in audit timeline.", "Notes remain strictly hidden from student view; immutable timeline permanently records evaluator ID and timestamp.", "Internal notes saved; student view excludes confidential notes; audit timeline records action.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Satisfies statutory accountability standards.")
    ]
    for r_idx, row in enumerate(staff_scenarios, start=1):
        cells = t_staff_scen.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_staff_scen, [0.4, 1.1, 1.5, 1.4, 1.2, 0.6, 0.6], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT])

    # Staff Section B.1 Worksheet
    add_styled_heading(doc, "B.1. AI-Assisted vs. Human-Only Document Review Comparison Worksheet", level=2)
    p_b1 = doc.add_paragraph("Protocol: Evaluators inspect 10 sample Certificate of Grades (COG) documents in two phases:\n"
                             "Phase 1: Record authenticity judgment WITHOUT viewing AI scores.\n"
                             "Phase 2: Reveal AI score and heatmap overlay, then record final decision and confidence level (1 to 5).")
    p_b1.paragraph_format.space_after = Pt(4)

    t_b1 = doc.add_table(rows=11, cols=7)
    b1_hdr = t_b1.rows[0].cells
    b1_hdr[0].paragraphs[0].text = "Doc ID"
    b1_hdr[1].paragraphs[0].text = "Document Description"
    b1_hdr[2].paragraphs[0].text = "Phase 1 Judgment (No AI)"
    b1_hdr[3].paragraphs[0].text = "Review Time"
    b1_hdr[4].paragraphs[0].text = "AI Fraud Score"
    b1_hdr[5].paragraphs[0].text = "Phase 2 Judgment (With AI)"
    b1_hdr[6].paragraphs[0].text = "Reviewer Confidence"

    cog_samples = [
        ("COG-01", "Authentic Registrar COG (BSIT)", "☐ Auth  ☐ Tampered", "____ s", "12.4%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-02", "Grade Digit Inflation (BSCE)", "☐ Auth  ☐ Tampered", "____ s", "88.7%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-03", "Authentic Dean's List (BSA)", "☐ Auth  ☐ Tampered", "____ s", "08.1%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-04", "Forged Signature & Seal", "☐ Auth  ☐ Tampered", "____ s", "92.3%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-05", "Camera Noise Scan (BSEE)", "☐ Auth  ☐ Tampered", "____ s", "24.5%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-06", "Photoshop Spliced GWA", "☐ Auth  ☐ Tampered", "____ s", "95.6%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-07", "Authentic Academic Copy (BSBio)", "☐ Auth  ☐ Tampered", "____ s", "14.2%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-08", "Altered Units/Subjects", "☐ Auth  ☐ Tampered", "____ s", "78.4%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-09", "Mobile CamScanner Auth", "☐ Auth  ☐ Tampered", "____ s", "28.0%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5"),
        ("COG-10", "Deep Tampered Header", "☐ Auth  ☐ Tampered", "____ s", "84.9%", "☐ Auth  ☐ Tampered", "☐ 1  ☐ 2  ☐ 3  ☐ 4  ☐ 5")
    ]
    for r_idx, row in enumerate(cog_samples, start=1):
        cells = t_b1.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_b1, [0.8, 1.8, 1.2, 0.7, 0.7, 1.2, 0.8], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.CENTER])

    # Staff Acceptance Signoff Table
    add_styled_heading(doc, "Staff Testing Acceptance Result", level=3)
    p_staff_res = doc.add_paragraph("Based on the testing performed, the system is:\n☐ ACCEPTED — major required functions operated satisfactorily and no critical issue prevents intended use.\n☐ ACCEPTED WITH MINOR REVISIONS — the system is generally usable, subject to the corrections listed.\n☐ FOR REVISION AND RETESTING — one or more significant issues must be corrected before acceptance.\n\nStaff Evaluator Comments: ____________________________________________________________________________")
    p_staff_res.paragraph_format.space_after = Pt(6)

    # Staff ISO 25010 Form
    add_styled_heading(doc, "ISO/IEC 25010:2023 End-User Evaluation Instrument — OSA Evaluator / Staff Role", level=2)
    staff_iso_items = [
        ("1. FUNCTIONAL SUITABILITY", "", ""),
        ("1", "The system provides the functions I need to triage, evaluate, and decision scholarship applications.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2", "The system produces correct fraud risk indicators and document evaluation outputs based on applicant submissions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3", "The available review functions help me complete document integrity audits effectively and without omitting required checks.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2. PERFORMANCE EFFICIENCY", "", ""),
        ("4", "The review queue and applicant dossier screens respond within an acceptable amount of time.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5", "High-resolution COG documents, AI Grad-CAM heatmaps, and ELA overlays load without unnecessary delay.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6", "The system performs satisfactorily during high-volume application review sessions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3. COMPATIBILITY", "", ""),
        ("7", "The evaluation canvas and review tools work properly across our office workstations and modern web browsers.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("8", "When viewing scanned documents, images, and PDF uploads from students, the formatting displays accurately.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("4. INTERACTION CAPABILITY", "", ""),
        ("9", "It is easy to understand what the evaluation interface and forensic indicators are intended to convey.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("10", "I can learn how to navigate the review queue and interpret AI fraud scores with minimal training.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("11", "The buttons, canvas controls (zoom, pan, rotate, invert), labels, and triage options are clear and easy to understand.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("12", "The system helps prevent mistakes by enforcing confirmation dialogs and mandatory remarks on returned applications.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("13", "The review interface is well-organized, readable, and comfortable for extended evaluation sessions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5. RELIABILITY", "", ""),
        ("14", "The evaluation portal works consistently during normal administrative working hours.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("15", "The system and AI verification pipeline are available and usable whenever applications require review.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("16", "The system handles corrupted file uploads gracefully without crashing the evaluation queue.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("17", "If an unexpected interruption occurs, all saved evaluator remarks and decisions are preserved accurately.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6. SECURITY", "", ""),
        ("18", "The system allows access to applicant dossiers and internal notes only to authorized evaluation staff.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("19", "Confidential student records and financial proofs are adequately protected against unauthorized viewing.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("20", "The system appropriately enforces multi-factor authentication (MFA) and trusted device access.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("21", "Evaluator decisions, status changes, and timestamps are immutably logged for administrative accountability.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("7. FLEXIBILITY & SAFETY", "", ""),
        ("22", "The system can support evaluation across diverse scholarship programs with varied eligibility rules.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("23", "The review canvas remains usable across different monitor resolutions and responsive workstation layouts.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("24", "The system provides clear warnings before irreversible actions such as rejecting or revoking a scholarship grant.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A")
    ]
    t_staff_iso = doc.add_table(rows=len(staff_iso_items) + 1, cols=3)
    st_iso_hdr = t_staff_iso.rows[0].cells
    st_iso_hdr[0].paragraphs[0].text = "No."
    st_iso_hdr[1].paragraphs[0].text = "ISO/IEC 25010:2023 Quality Dimension & Evaluation Statement"
    st_iso_hdr[2].paragraphs[0].text = "Rating (1 to 5)"

    for r_idx, row in enumerate(staff_iso_items, start=1):
        cells = t_staff_iso.rows[r_idx].cells
        cells[0].paragraphs[0].text = row[0]
        cells[1].paragraphs[0].text = row[1]
        cells[2].paragraphs[0].text = row[2]
        if row[1] == "":
            set_cell_background(cells[0], "E2E8F0")
            set_cell_background(cells[1], "E2E8F0")
            set_cell_background(cells[2], "E2E8F0")
            cells[0].paragraphs[0].runs[0].bold = True
    format_table(t_staff_iso, [0.5, 4.8, 1.5], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER])

    doc.add_page_break()

    # =========================================================================
    # PART III: SUPER ADMIN & OSA DIRECTOR ROLE — UAT SCRIPT & EVALUATION
    # =========================================================================
    add_styled_heading(doc, "PART III: SUPER ADMINISTRATOR & OSA DIRECTOR ROLE — SYSTEM TESTING & EVALUATION", level=1)
    
    p_admin_desc = doc.add_paragraph("Purpose: This section guides the OSA Director and Super Administrators through high-level institutional governance: executive analytics monitoring, scholarship program slot quota management, custom field builder configuration, RBAC staff delegation, AI threshold tuning, and statutory compliance audit export.")
    p_admin_desc.paragraph_format.space_after = Pt(6)

    add_styled_heading(doc, "Visual Walkthrough & Key Interface Views", level=2)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_03_Analytics_Dashboard.png", 
               "Figure 7. Executive Analytics Dashboard & Grade Integrity Index (Scenario 1)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_02_Scholarship_Programs.png", 
               "Figure 8. Scholarship Program Quotas, GWA Rules, and Active Lifecycle Management (Scenarios 2, 8)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_06_Staff_Management.png", 
               "Figure 9. RBAC Staff Governance & Scholarship Program Queue Delegation (Scenario 4)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_04_System_Settings.png", 
               "Figure 10. AI Microservice Pipeline, Sensitivity Calibration & MFA Policy (Scenario 5)", width_inches=5.8)
    
    add_figure(doc, "thesis_figures/screenshots/Figure_05_Audit_Logs.png", 
               "Figure 11. Tamper-Evident Audit Trail & CHED/COA Compliance Export Engine (Scenarios 6, 7)", width_inches=5.8)

    add_styled_heading(doc, "Super Administrator & Director Client Test Scenarios (Execution Matrix)", level=2)
    
    t_adm_scen = doc.add_table(rows=9, cols=7)
    ad_hdr = t_adm_scen.rows[0].cells
    ad_hdr[0].paragraphs[0].text = "No."
    ad_hdr[1].paragraphs[0].text = "Module / Feature"
    ad_hdr[2].paragraphs[0].text = "Task / Test Scenario"
    ad_hdr[3].paragraphs[0].text = "Expected Result"
    ad_hdr[4].paragraphs[0].text = "Actual Result"
    ad_hdr[5].paragraphs[0].text = "Status"
    ad_hdr[6].paragraphs[0].text = "Remarks"

    admin_scenarios = [
        ("1", "Executive KPI Analytics", "Director reviews dashboard metrics: Total Applications, Approval Rates, Grade Integrity Index, and Quota burn.", "Real-time metric counters and distribution charts render accurately; dynamic academic term filtering updates totals.", "Total counts, fraud rates, and charts loaded in real-time; sub-second rendering verified.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Cached KPI aggregates refresh on mutation."),
        ("2", "Program Lifecycle Governance", "Admin creates new scholarship program, sets slot quotas, GWA minimums, deadlines, and toggles Active/Archived.", "Program persists in database; slot validation prevents negative integers; active grants become visible in student catalog.", "Program persisted; numeric validations active; immediate visibility in student catalog verified.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Active toggle synchronizes catalog availability."),
        ("3", "Dynamic Custom Field Builder", "Admin configures custom scholarship form fields (text, dropdown, file), tests reorder arrows and Required switch.", "Dedicated sub-header renders clean button cluster without overlapping switches; field reindexing works smoothly.", "Dynamic fields rendered cleanly; drag/order persistence functional; required flags enforced.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Custom fields stored in relational schema."),
        ("4", "RBAC & Staff Governance", "Admin creates staff evaluator accounts, assigns specific scholarship program queues, and manages permissions.", "Strict RBAC restricts evaluators to assigned programs; suspended staff accounts are immediately blocked from entry.", "Staff account creation functional; assigned queue permissions strictly enforced by middleware.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Zero unauthorized cross-program leakage."),
        ("5", "AI Pipeline & Sensitivity Config", "Admin inspects AI microservice connectivity, sets default pipeline (V2 ELA-CNN), and calibrates fraud score threshold.", "Health ping confirms live service; configured threshold updates system_settings and applies to future scan jobs.", "AI health ping succeeded; sensitivity slider saved to system_settings; background jobs respect config.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Allows live tuning without server reboot."),
        ("6", "Compliance Reporting & Exports", "Admin exports filtered scholarship masterlists and compliance summaries in CSV and official PDF formats.", "CSV downloads formatted for CHED/DOST portal upload; PDF generates with CLSU OSA header, seal, and signatory lines.", "CSV generated with UTF-8 BOM for Excel; PDF generated with official seal and signature blocks.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Satisfies CHED & COA audit requirements."),
        ("7", "System Audit Trail Monitoring", "Admin inspects immutable audit logs, filtering by user, IP address, and action type (Logins, Overrides, Exports).", "Logs capture complete timestamp, actor IP, action category, and payload difference; entries cannot be altered.", "Logs capture actor ID, IP, user-agent, timestamp, and JSON diffs; filtering operates smoothly.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Immutable database triggers protect log tables."),
        ("8", "Soft-Deletion & Resilience", "Admin soft-deletes a scholarship program, inspects Trash recovery tab, and restores record back to active state.", "Soft-delete protects relational integrity; restore brings record back cleanly; university announcement broadcasts to feed.", "Soft-delete sets deleted_at timestamp; trash tab lists deleted program; restore brings it back cleanly.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Prevents accidental data loss.")
    ]
    for r_idx, row in enumerate(admin_scenarios, start=1):
        cells = t_adm_scen.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_adm_scen, [0.4, 1.1, 1.5, 1.4, 1.2, 0.6, 0.6], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT])

    # Admin Acceptance Signoff Table
    add_styled_heading(doc, "Admin / Director Testing Acceptance Result", level=3)
    p_adm_res = doc.add_paragraph("Based on the testing performed, the system is:\n☐ ACCEPTED — major required functions operated satisfactorily and no critical issue prevents intended use.\n☐ ACCEPTED WITH MINOR REVISIONS — the system is generally usable, subject to the corrections listed.\n☐ FOR REVISION AND RETESTING — one or more significant issues must be corrected before acceptance.\n\nDirector Comments / Recommendations: _________________________________________________________________")
    p_adm_res.paragraph_format.space_after = Pt(6)

    # Admin ISO 25010 Form
    add_styled_heading(doc, "ISO/IEC 25010:2023 End-User Evaluation Instrument — Super Administrator & OSA Director Role", level=2)
    admin_iso_items = [
        ("1. FUNCTIONAL SUITABILITY", "", ""),
        ("1", "The system provides complete executive functions to govern scholarship programs, quotas, and compliance reporting.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2", "The system produces accurate statistical aggregates, compliance masterlists, and tamper-evident audit logs.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3", "The available administrative features help the Office of Student Affairs manage the full scholarship lifecycle effectively.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2. PERFORMANCE EFFICIENCY", "", ""),
        ("4", "The executive analytics dashboard and administrative management screens load within acceptable timeframes.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5", "Exportable CSV reports and authenticated PDF compliance summaries generate rapidly without server timeouts.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6", "The system maintains high performance efficiency during concurrent administrative and evaluation operations.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3. COMPATIBILITY", "", ""),
        ("7", "The administrative portal operates reliably across executive workstations, office PCs, and modern browsers.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("8", "Exported CSV and PDF compliance files integrate cleanly with institutional spreadsheets and CHED/DOST reporting portals.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("4. INTERACTION CAPABILITY", "", ""),
        ("9", "It is easy to understand the high-level metrics, grade integrity indices, and administrative settings of the system.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("10", "Administrative personnel can learn to configure new scholarship programs and custom fields with minimal guidance.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("11", "The navigation bars, buttons, custom field reorder controls, and program toggles are clear and intuitive.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("12", "The system prevents catastrophic administrative mistakes by requiring confirmation before destructive actions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("13", "The administrative dashboard is visually balanced, professional, and well-structured for strategic oversight.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5. RELIABILITY", "", ""),
        ("14", "The administrative platform operates continuously and reliably without unexpected service interruptions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("15", "The system is available and accessible whenever executive reviews or statutory compliance reports are required.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("16", "The system handles heavy transaction volumes without data corruption or database deadlocks.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("17", "The soft-deletion and trash recovery mechanisms protect institutional records against accidental permanent data loss.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6. SECURITY", "", ""),
        ("18", "The system strictly enforces role-based access control (RBAC), restricting administrative tools to authorized personnel.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("19", "Sensitive student PII, financial background data, and staff notes are encrypted at rest and in transit under R.A. 10173.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("20", "The system appropriately enforces multi-factor authentication (MFA) and monitors active sessions system-wide.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("21", "All administrative actions, data exports, and status overrides are permanently recorded in an immutable audit trail.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("7. FLEXIBILITY & SAFETY", "", ""),
        ("22", "The system accommodates diverse scholarship structures, funding models, and academic eligibility requirements.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("23", "The dynamic custom form field builder provides complete flexibility to collect unique program requirements as needs change.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("24", "The system architecture incorporates safeguards that eliminate single points of failure and prevent catastrophic data corruption.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A")
    ]
    t_adm_iso = doc.add_table(rows=len(admin_iso_items) + 1, cols=3)
    ad_iso_hdr = t_adm_iso.rows[0].cells
    ad_iso_hdr[0].paragraphs[0].text = "No."
    ad_iso_hdr[1].paragraphs[0].text = "ISO/IEC 25010:2023 Quality Dimension & Evaluation Statement"
    ad_iso_hdr[2].paragraphs[0].text = "Rating (1 to 5)"

    for r_idx, row in enumerate(admin_iso_items, start=1):
        cells = t_adm_iso.rows[r_idx].cells
        cells[0].paragraphs[0].text = row[0]
        cells[1].paragraphs[0].text = row[1]
        cells[2].paragraphs[0].text = row[2]
        if row[1] == "":
            set_cell_background(cells[0], "E2E8F0")
            set_cell_background(cells[1], "E2E8F0")
            set_cell_background(cells[2], "E2E8F0")
            cells[0].paragraphs[0].runs[0].bold = True
    format_table(t_adm_iso, [0.5, 4.8, 1.5], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER])

    doc.add_page_break()

    # =========================================================================
    # PART IV: IT EXPERT & TECHNICAL ARCHITECTURAL INSPECTION MANUAL
    # =========================================================================
    add_styled_heading(doc, "PART IV: IT EXPERT & TECHNICAL ARCHITECTURAL INSPECTION MANUAL", level=1)
    
    p_it_desc = doc.add_paragraph("Purpose: This section guides IT professionals, software architects, cybersecurity analysts, and AI/ML specialists through the technical inspection of A.E.G.I.S., verifying backend architecture, cryptographic security controls, AI forensic pipelines (ELA-CNN ResNet-50), zero-knowledge OTP hashing, AES-256 column encryption, and database integrity.")
    p_it_desc.paragraph_format.space_after = Pt(6)

    add_styled_heading(doc, "IT Expert Technical Test Scenarios (10-Item Verification Matrix)", level=2)
    
    t_it_scen = doc.add_table(rows=11, cols=7)
    it_hdr = t_it_scen.rows[0].cells
    it_hdr[0].paragraphs[0].text = "No."
    it_hdr[1].paragraphs[0].text = "Module / Feature"
    it_hdr[2].paragraphs[0].text = "Task / Test Scenario"
    it_hdr[3].paragraphs[0].text = "Expected Result"
    it_hdr[4].paragraphs[0].text = "Actual Result"
    it_hdr[5].paragraphs[0].text = "Status"
    it_hdr[6].paragraphs[0].text = "Remarks"

    it_scenarios = [
        ("1", "Authentication & Password Security", "Attempt SQL injection bypass (' OR 1=1--) and password brute-force on /login. Verify Bcrypt hash (cost=12), rate-limiting middleware (5 attempts/min), and CSRF token binding.", "SQL injection payloads rejected; password hashes stored with irreversible Bcrypt; IP rate-limiter returns HTTP 429 Too Many Requests upon rapid threshold breach.", "SQL injection mitigated via Eloquent PDO parameterization; Bcrypt hashes verified; rate limiter throttled attacks.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Bcrypt cost=12 complies with OWASP guidelines."),
        ("2", "Identity Verification & MFA Hashing", "Trigger 6-digit OTP dispatch. Inspect database storage of otp_code. Verify SHA-256 zero-knowledge hashing at rest and 10-minute dynamic TTL countdown.", "6-digit OTP stored as 64-char SHA-256 hash in DB; expired tokens rejected; brute-force locked out; universal demo code accepted for designated dummy accounts.", "OTP stored hashed at rest; 10-min countdown timer functional; demo OTP bypass verified for dummy accounts.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "SHA-256 zero-knowledge storage prevents DB leak compromise."),
        ("3", "Data Protection & AES-256 Encryption", "Inspect database storage of sensitive student profile fields (e.g. institutional CLSU ID numbers, guardian contact details, and student emergency contacts under R.A. 10173 data minimization) in student_profiles.", "Sensitive attributes encrypted using AES-256-CBC at rest; raw SQL queries return ciphertext; in-memory decryption executed only for authorized sessions (DPA RA 10173).", "Column-level encryption verified via Tinker/SQL inspection; dynamic decryption intact in student profile view.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Compliant with NPC Data Privacy Act of 2012 (Data Minimization & At-Rest Encryption)."),
        ("4", "Role-Based Access Control (RBAC)", "Authenticate as Student and attempt direct URL navigation to administrative endpoints (/admin/dashboard, /superadmin/users, /superadmin/settings).", "Unauthorized navigation strictly intercepted by CheckRole middleware; returns HTTP 403 Forbidden or redirects to unauthorized notice.", "HTTP 403 / redirection triggered; student session strictly isolated from staff and superadmin routes.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Role isolation validated across all controller gates."),
        ("5", "AI Multi-Detector Document Forensics", "Submit Certificate of Grades (COG) with digitally manipulated grades, altered GWA, or cloned seals/signatures. Inspect ELA preprocessing (Q=95), ResNet-50 binary classification, Fraud Probability Score (0-100%), and Grad-CAM convolutional heatmap overlay.", "Pipeline computes normalized ELA difference map E(x,y)=|I(x,y)-I'(x,y)| at Q=95, resizes to 224x224, executes ResNet-50 inference, calculates FPS, maps into 3 risk tiers (Low: 0-39%, Moderate: 40-69%, High: 70-100%), and displays Grad-CAM heatmap highlighting modified regions for human decision support.", "ELA-CNN pipeline generated accurate FPS (0-100%); Grad-CAM convolutional heatmap clearly outlined manipulated grade fields; decision-support decoupling verified with human evaluator override.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Complies with proposed ELA-ResNet-50 specification (Gorle & Guttavelli, 2025; Maamouli et al., 2022)."),
        ("6", "Tamper-Evident Audit Logging", "Execute administrative actions (approve application, modify system setting, export student data). Verify structured audit trail records.", "Structured audit entries created in admin_action_logs, config_change_logs, and export_access_logs with actor ID, IP address, user agent, timestamp, and payload diff.", "Audit logs populated accurately with actor IP, UA hash, and JSON diffs; export access logged.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Immutable audit trails satisfy non-repudiation standard."),
        ("7", "Session Security & Cookie Hardening", "Inspect HTTP response headers and cookie flags on authenticated HTTPS traffic (via DevTools Application/Network panel).", "Strict-Transport-Security, X-Frame-Options: SAMEORIGIN, X-Content-Type-Options: nosniff, and CSP headers active; session cookies flagged Secure, HttpOnly, SameSite=Lax.", "All security headers present in HTTP response; session cookies properly hardened for HTTPS reverse proxy.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Reverse-proxy trustProxies configured cleanly."),
        ("8", "Official PDF Generation & Integrity Seal", "Generate and preview official 1-page applicant evaluation form with verification checklist, signatures, and tamper-evident clearance badge. Verify isolated iframe print engine.", "1-page letter PDF generated matching web modal preview 1:1; isolated iframe prints cleanly without blank pages; CLSU OSA seal, QR clearance badge, and ISO revision code intact.", "PDF generated with high fidelity; QR code leads to live signed verification endpoint; digital seal intact.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Complies with official institutional document standards."),
        ("9", "Asynchronous Queue & Fault Tolerance", "Trigger heavy AI document scan. Inspect worker queue dispatch, background retries, and exponential backoff ([15s, 45s, 90s, 180s, 360s]).", "AI analysis dispatches to database queue; background worker processes job without freezing UI; cold start 502/503 responses handled gracefully.", "Background queue processed jobs asynchronously; cold-start container wake-up retries verified without crashing.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "Prevents web worker timeouts during heavy AI inference."),
        ("10", "Concurrency, Caching & Performance", "Simulate concurrent page loads. Inspect query execution logs for N+1 queries and evaluate GzipResponse compression ratio.", "Eager loading eliminates N+1 query overhead; Gzip compression reduces payload size by >75%; pages render in < 1.5 seconds.", "Zero N+1 queries observed; Gzip reduced assets by 80%; sub-second page rendering recorded on cloud server.", "☐ Pass\n☐ Fail\n☐ Needs Rev.\n☐ N/A", "OPcache and Laravel route/config caches verified.")
    ]
    for r_idx, row in enumerate(it_scenarios, start=1):
        cells = t_it_scen.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_it_scen, [0.4, 1.1, 1.5, 1.4, 1.2, 0.6, 0.6], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT])

    # IT Expert Acceptance Signoff Table
    add_styled_heading(doc, "IT Expert Technical Testing Result", level=3)
    p_it_res = doc.add_paragraph("Based on the technical testing and architectural inspection performed, the system is:\n☐ ACCEPTED — major required functions, security controls, and architectures operated satisfactorily and no critical defect prevents production deployment.\n☐ ACCEPTED WITH MINOR REVISIONS — the system is technically sound and usable, subject to the minor engineering optimizations listed.\n☐ FOR REVISION AND RETESTING — one or more architectural, security, or functional defects must be remediated before acceptance.\n\nIT Expert Comments / Technical Recommendations: _____________________________________________________")
    p_it_res.paragraph_format.space_after = Pt(6)

    # IT Expert ISO 25010 Form
    add_styled_heading(doc, "ISO/IEC 25010:2023 Technical Product Quality Evaluation Form — IT Experts", level=2)
    it_iso_items = [
        ("1. FUNCTIONAL SUITABILITY", "", ""),
        ("1", "The system completely implements all essential scholarship management modules, multi-tiered document evaluation, and notification lifecycles.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2", "The system executes business logic and calculation algorithms (GWA checks, financial thresholds, AI risk scoring) with technical precision.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3", "The functional workflows provide appropriate administrative utilities (bulk actions, live audit logs, triage queue) without redundant operations.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("2. PERFORMANCE EFFICIENCY", "", ""),
        ("4", "The system responds within acceptable latency thresholds (< 1.5 seconds) during typical database queries and page transitions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5", "Server resources (CPU, RAM, database I/O) are utilized efficiently via eager loading, query caching, Gzip compression, and OPcache optimization.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6", "The system manages concurrent applicant uploads and background worker queuing without deadlocks or performance degradation.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("3. COMPATIBILITY", "", ""),
        ("7", "The application co-exists smoothly in multi-container environments (Docker, PHP-FPM, Alpine Linux) without service contention.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("8", "The system integrates seamlessly with external web services and cloud APIs (Brevo SMTP email, Cloudflare R2 object storage, HuggingFace AI spaces).", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("4. USABILITY (INTERACTION CAPABILITY)", "", ""),
        ("9", "The software provides clear architectural recognizability, intuitive UI patterns, breadcrumbs, and standardized SweetAlert2 dialogs.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("10", "The administrative and student interfaces enable rapid user learnability with minimal training through structured multi-step wizards.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("11", "The application enforces robust client-side and server-side validation rules with accessible error handling to prevent user mistakes.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("5. RELIABILITY", "", ""),
        ("12", "The software demonstrates architectural maturity, passing comprehensive automated unit and feature test suites with zero regressions.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("13", "The system provides high operational availability with fault-tolerant fallbacks (database connection retries, email failover drivers).", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("14", "The system gracefully recovers from service interruptions (e.g. AI container cold starts) through automated job retry backoffs.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("6. SECURITY", "", ""),
        ("15", "Unauthorized access to sensitive student records is strictly prevented through role-based middleware, secure sessions, and AES-256 database encryption.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("16", "Data integrity is robustly safeguarded through CSRF token verification, cryptographic URL signatures, and immutable audit logs.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("17", "System activities (approvals, rejections, setting updates, exports) are immutably tied to user identity for complete non-repudiation.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("18", "User identity is verified through cryptographically secure Multi-Factor Authentication (MFA OTP) with SHA-256 hash storage at rest.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("7. MAINTAINABILITY", "", ""),
        ("19", "The codebase exhibits high modularity following MVC and Clean Architecture standards (Skinny Controllers, Fat Models, Service Layer).", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("20", "Software components (forensic analyzers, notification dispatchers, PDF generators, alert engines) are abstracted for code reusability.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("21", "The codebase provides clear architectural documentation, structured logging, and high testability with automated test coverage.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("8. PORTABILITY", "", ""),
        ("22", "The web application adapts seamlessly across modern web browsers (Chrome, Edge, Safari, Firefox) and multi-device form factors.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("23", "The application deployment pipeline is standardized via containerization (Dockerfile, render.yaml) and automated database migrations.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A"),
        ("OVERALL SYSTEM RATING", "", ""),
        ("24", "Overall, the system demonstrates high architectural, algorithmic, and software engineering quality ready for live operational deployment.", "☐ 5  ☐ 4  ☐ 3  ☐ 2  ☐ 1  ☐ N/A")
    ]
    t_it_iso = doc.add_table(rows=len(it_iso_items) + 1, cols=3)
    it_iso_hdr = t_it_iso.rows[0].cells
    it_iso_hdr[0].paragraphs[0].text = "No."
    it_iso_hdr[1].paragraphs[0].text = "ISO/IEC 25010:2023 Quality Dimension & Evaluation Statement"
    it_iso_hdr[2].paragraphs[0].text = "Rating (1 to 5)"

    for r_idx, row in enumerate(it_iso_items, start=1):
        cells = t_it_iso.rows[r_idx].cells
        cells[0].paragraphs[0].text = row[0]
        cells[1].paragraphs[0].text = row[1]
        cells[2].paragraphs[0].text = row[2]
        if row[1] == "":
            set_cell_background(cells[0], "E2E8F0")
            set_cell_background(cells[1], "E2E8F0")
            set_cell_background(cells[2], "E2E8F0")
            cells[0].paragraphs[0].runs[0].bold = True
    format_table(t_it_iso, [0.5, 4.8, 1.5], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER])

    doc.add_page_break()

    # =========================================================================
    # PART V: CONSOLIDATED SCREENSHOT & ARCHITECTURAL TRACEABILITY MATRIX
    # =========================================================================
    add_styled_heading(doc, "PART V: CONSOLIDATED SCREENSHOT & ARCHITECTURAL TRACEABILITY MATRIX", level=1)
    
    p_trace = doc.add_paragraph("The following master index maps all eleven (11) high-resolution system screenshots against their respective user roles, operational viewports, and corresponding test scenarios across the UAT and IT Expert testing suites:")
    p_trace.paragraph_format.space_after = Pt(6)

    t_trace = doc.add_table(rows=12, cols=5)
    tr_hdr = t_trace.rows[0].cells
    tr_hdr[0].paragraphs[0].text = "Figure & Filename"
    tr_hdr[1].paragraphs[0].text = "System Viewport"
    tr_hdr[2].paragraphs[0].text = "Role"
    tr_hdr[3].paragraphs[0].text = "UAT Test Scenarios"
    tr_hdr[4].paragraphs[0].text = "IT Technical Scenarios"

    trace_data = [
        ("Figure 1\nFigure_01_Login_Page.png", "Authentication & Registration", "All Roles", "Student Scenario 1\nStaff Scenario 1", "IT Scenarios 1 & 2 (Bcrypt, Rate Limit, OTP SHA-256)"),
        ("Figure 2\nFigure_13_Student_Dashboard.png", "Student Dashboard & Tracker", "Student", "Student Scenarios 2, 3, 6, 7, 8", "IT Scenario 4 (RBAC, Session Isolation)"),
        ("Figure 3\nFigure_14_Student_Apply_Form.png", "3-Step Application Stepper", "Student", "Student Scenarios 4 & 5", "IT Scenario 3 (AES-256 At-Rest Encryption)"),
        ("Figure 4\nFigure_07_Admin_Application_Queue.png", "Applications Review Queue", "Staff / Admin", "Staff Scenario 2", "IT Scenario 10 (Eager Loading, No N+1 Queries)"),
        ("Figure 5\nFigure_09_Application_Review_Detail.png", "4-Pillar Review Studio & Canvas", "Staff / Admin", "Staff Scenarios 3, 4, 5, 6, 7, 8\nB.1 Worksheet (COG-01..10)", "IT Scenario 5 (ELA Q=95, ResNet-50, Grad-CAM Heatmap)"),
        ("Figure 6\nFigure_11_Announcements.png", "Institutional Announcements", "Staff / Admin", "Student Scenario 8", "IT Scenario 9 (Async Queue Worker Dispatch)"),
        ("Figure 7\nFigure_03_Analytics_Dashboard.png", "Executive Analytics & KPI Radar", "Director / Admin", "Admin Scenario 1", "IT Scenario 10 (Gzip Compression, Cache Ratios)"),
        ("Figure 8\nFigure_02_Scholarship_Programs.png", "Scholarship Catalog & Quotas", "Director / Admin", "Admin Scenarios 2 & 8", "IT Scenario 4 (CheckRole Middleware Isolation)"),
        ("Figure 9\nFigure_06_Staff_Management.png", "Staff Governance & Delegation", "Director / Admin", "Admin Scenario 4", "IT Scenario 4 (Program Queue RBAC Scoping)"),
        ("Figure 10\nFigure_04_System_Settings.png", "AI Sensitivity & Security Settings", "Director / Admin", "Admin Scenario 5", "IT Scenarios 2 & 7 (MFA Enforcement, Security Headers)"),
        ("Figure 11\nFigure_05_Audit_Logs.png", "Audit Trail & Compliance Export", "Director / Admin", "Admin Scenarios 6 & 7", "IT Scenario 6 (Immutable Logs, JSON Diffs, IP/UA)")
    ]
    for r_idx, row in enumerate(trace_data, start=1):
        cells = t_trace.rows[r_idx].cells
        for c_idx, val in enumerate(row):
            cells[c_idx].paragraphs[0].text = val
    format_table(t_trace, [1.5, 1.5, 0.9, 1.4, 1.5], [WD_ALIGN_PARAGRAPH.LEFT]*5)

    # Issue Log Template Table
    add_styled_heading(doc, "Standardized Defect & Observation Reporting Log", level=2)
    t_iss = doc.add_table(rows=6, cols=6)
    iss_hdr = t_iss.rows[0].cells
    iss_hdr[0].paragraphs[0].text = "Issue ID"
    iss_hdr[1].paragraphs[0].text = "Module / URL"
    iss_hdr[2].paragraphs[0].text = "Observation & Steps to Reproduce"
    iss_hdr[3].paragraphs[0].text = "Severity / Priority"
    iss_hdr[4].paragraphs[0].text = "Required Remediation"
    iss_hdr[5].paragraphs[0].text = "Retest Status"
    
    for r_idx in range(1, 6):
        cells = t_iss.rows[r_idx].cells
        cells[0].paragraphs[0].text = f"ISS-{r_idx:03d}"
        cells[1].paragraphs[0].text = ""
        cells[2].paragraphs[0].text = ""
        cells[3].paragraphs[0].text = "☐ Critical\n☐ Major\n☐ Minor"
        cells[4].paragraphs[0].text = ""
        cells[5].paragraphs[0].text = "☐ Passed\n☐ For Retest"
    format_table(t_iss, [0.7, 1.2, 2.0, 0.9, 1.3, 0.7], [WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER, WD_ALIGN_PARAGRAPH.LEFT, WD_ALIGN_PARAGRAPH.CENTER])

    # Save documents with fallback if Word has file open
    out_docx_comp_docs = os.path.join("docs", "AEGIS_COMPREHENSIVE_TESTER_AND_EVALUATOR_MANUAL.docx")
    out_docx_comp_root = "AEGIS_COMPREHENSIVE_TESTER_AND_EVALUATOR_MANUAL.docx"
    out_docx_docs = os.path.join("docs", "AEGIS_TESTER_AND_EVALUATOR_VISUAL_GUIDE.docx")
    out_docx_root = "AEGIS_TESTER_AND_EVALUATOR_VISUAL_GUIDE.docx"
    
    doc.save(out_docx_comp_docs)
    print(f"Successfully generated: {out_docx_comp_docs}")
    doc.save(out_docx_comp_root)
    print(f"Successfully generated root copy: {out_docx_comp_root}")
    
    doc.save(out_docx_root)
    print(f"Successfully updated root copy: {out_docx_root}")
    
    try:
        doc.save(out_docx_docs)
        print(f"Successfully updated: {out_docx_docs}")
    except PermissionError:
        print(f"Notice: {out_docx_docs} is currently open in Microsoft Word. Saved to {out_docx_comp_docs} and {out_docx_root} instead.")

if __name__ == "__main__":
    build_comprehensive_manual()
