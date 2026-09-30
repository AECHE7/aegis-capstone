# -*- coding: utf-8 -*-
"""
Generate Role-Specific UAT Test Scripts & ISO/IEC 25010 Evaluation Documents
for A.E.G.I.S. Capstone Project (Central Luzon State University - Office of Student Affairs).

Roles generated:
1. docs/UAT_Test_Script_Student_Role.docx
2. docs/UAT_Test_Script_Staff_Role.docx
3. docs/UAT_Test_Script_Admin_Role.docx
"""

import os
import sys
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

# Colors
COLOR_PRIMARY_HEX = "1B4D2E"       # CLSU Forest Green
COLOR_SECONDARY_HEX = "B8860B"     # CLSU Gold / Dark Ochre
COLOR_DARK_HEX = "1F2937"          # Dark Charcoal
COLOR_LIGHT_BG_HEX = "F3F4F6"      # Very Light Gray
COLOR_BORDER_HEX = "D1D5DB"        # Border Gray
COLOR_TEXT_MUTED = "4B5563"        # Muted Gray

COLOR_PRIMARY = RGBColor(27, 77, 46)
COLOR_SECONDARY = RGBColor(184, 134, 11)
COLOR_DARK = RGBColor(31, 41, 55)
COLOR_MUTED = RGBColor(75, 85, 99)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    """Set inner padding for table cell in twips."""
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def set_cell_background(cell, fill_hex):
    """Set background color of a table cell."""
    shading_xml = f'<w:shd {nsdecls("w")} w:val="clear" w:color="auto" w:fill="{fill_hex}"/>'
    cell._tc.get_or_add_tcPr().append(parse_xml(shading_xml))

def set_table_borders(table, color="D1D5DB", sz="4", val="single"):
    """Set subtle, professional borders on a table."""
    tblPr = table._tbl.tblPr
    borders_xml = f'''
    <w:tblBorders {nsdecls("w")}>
        <w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
        <w:left w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
        <w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
        <w:right w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
        <w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
        <w:insideV w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>
    </w:tblBorders>
    '''
    tblPr.append(parse_xml(borders_xml))

def add_header(doc, role_title, subtitle, audience):
    """Add official CLSU letterhead and document title."""
    # CLSU Header
    p_clsu = doc.add_paragraph()
    p_clsu.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_clsu.paragraph_format.space_before = Pt(0)
    p_clsu.paragraph_format.space_after = Pt(2)
    
    run_clsu = p_clsu.add_run("CENTRAL LUZON STATE UNIVERSITY\n")
    run_clsu.bold = True
    run_clsu.font.name = "Arial"
    run_clsu.font.size = Pt(11)
    run_clsu.font.color.rgb = COLOR_PRIMARY

    run_coll = p_clsu.add_run("College of Engineering — Department of Information Technology\n")
    run_coll.bold = True
    run_coll.font.name = "Arial"
    run_coll.font.size = Pt(9.5)
    run_coll.font.color.rgb = COLOR_DARK

    run_loc = p_clsu.add_run("Science City of Muñoz, Nueva Ecija, Philippines 3120")
    run_loc.font.name = "Arial"
    run_loc.font.size = Pt(8.5)
    run_loc.font.color.rgb = COLOR_MUTED

    # Separator Line
    p_sep = doc.add_paragraph()
    p_sep.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sep.paragraph_format.space_before = Pt(4)
    p_sep.paragraph_format.space_after = Pt(12)
    run_sep = p_sep.add_run("—" * 58)
    run_sep.font.color.rgb = COLOR_SECONDARY
    run_sep.bold = True

    # Document Title Block
    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(0)
    p_title.paragraph_format.space_after = Pt(3)
    
    run_title = p_title.add_run(role_title.upper())
    run_title.bold = True
    run_title.font.name = "Arial"
    run_title.font.size = Pt(13)
    run_title.font.color.rgb = COLOR_PRIMARY

    p_sub = doc.add_paragraph()
    p_sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_before = Pt(0)
    p_sub.paragraph_format.space_after = Pt(2)
    run_sub = p_sub.add_run(subtitle)
    run_sub.font.name = "Arial"
    run_sub.font.size = Pt(10)
    run_sub.bold = True
    run_sub.font.color.rgb = COLOR_DARK

    p_aud = doc.add_paragraph()
    p_aud.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_aud.paragraph_format.space_before = Pt(0)
    p_aud.paragraph_format.space_after = Pt(14)
    run_aud = p_aud.add_run(f"Target Role: {audience} | Capstone 2 Software Evaluation")
    run_aud.font.name = "Arial"
    run_aud.font.size = Pt(8.5)
    run_aud.font.italic = True
    run_aud.font.color.rgb = COLOR_MUTED

def add_section_heading(doc, text):
    """Add a styled section heading."""
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.bold = True
    run.font.name = "Arial"
    run.font.size = Pt(11)
    run.font.color.rgb = COLOR_PRIMARY

def add_metadata_table(doc, fields):
    """Add participant metadata block."""
    table = doc.add_table(rows=len(fields), cols=2)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table)
    
    for i, (label, default_val) in enumerate(fields):
        row = table.rows[i]
        c0, c1 = row.cells[0], row.cells[1]
        c0.width = Inches(2.2)
        c1.width = Inches(4.5)
        set_cell_background(c0, COLOR_LIGHT_BG_HEX)
        set_cell_margins(c0, 80, 80, 120, 120)
        set_cell_margins(c1, 80, 80, 120, 120)
        
        p0 = c0.paragraphs[0]
        p0.paragraph_format.space_before = Pt(0)
        p0.paragraph_format.space_after = Pt(0)
        r0 = p0.add_run(label)
        r0.bold = True
        r0.font.name = "Arial"
        r0.font.size = Pt(9)
        r0.font.color.rgb = COLOR_DARK
        
        p1 = c1.paragraphs[0]
        p1.paragraph_format.space_before = Pt(0)
        p1.paragraph_format.space_after = Pt(0)
        r1 = p1.add_run(default_val)
        r1.font.name = "Arial"
        r1.font.size = Pt(9)
        r1.font.color.rgb = COLOR_MUTED
    
    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def add_instructions_block(doc, instructions, grading_scale):
    """Add testing instructions and grading scale."""
    add_section_heading(doc, "1. Testing Purpose & General Instructions")
    for inst in instructions:
        p = doc.add_paragraph(style='List Bullet')
        p.paragraph_format.space_before = Pt(1)
        p.paragraph_format.space_after = Pt(2)
        r = p.add_run(inst)
        r.font.name = "Arial"
        r.font.size = Pt(9)
        r.font.color.rgb = COLOR_DARK

    p_scale_lbl = doc.add_paragraph()
    p_scale_lbl.paragraph_format.space_before = Pt(6)
    p_scale_lbl.paragraph_format.space_after = Pt(4)
    r_sl = p_scale_lbl.add_run("Evaluation Rating Scale for Test Cases:")
    r_sl.bold = True
    r_sl.font.name = "Arial"
    r_sl.font.size = Pt(9.5)
    r_sl.font.color.rgb = COLOR_PRIMARY

    # Scale Table
    scale_table = doc.add_table(rows=len(grading_scale) + 1, cols=3)
    scale_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(scale_table)

    headers = ["Rating", "Code", "Definition & Operational Standard"]
    for j, h in enumerate(headers):
        cell = scale_table.cell(0, j)
        set_cell_background(cell, COLOR_PRIMARY_HEX)
        set_cell_margins(cell, 80, 80, 100, 100)
        p = cell.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r = p.add_run(h)
        r.bold = True
        r.font.name = "Arial"
        r.font.size = Pt(8.5)
        r.font.color.rgb = RGBColor(255, 255, 255)

    for i, (rating, code, definition) in enumerate(grading_scale):
        row = scale_table.rows[i + 1]
        for j, val in enumerate([rating, code, definition]):
            c = row.cells[j]
            set_cell_margins(c, 70, 70, 100, 100)
            if i % 2 == 1:
                set_cell_background(c, COLOR_LIGHT_BG_HEX)
            p = c.paragraphs[0]
            if j < 2:
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            r = p.add_run(val)
            r.font.name = "Arial"
            r.font.size = Pt(8.5)
            if j < 2:
                r.bold = True
                r.font.color.rgb = COLOR_DARK
            else:
                r.font.color.rgb = COLOR_MUTED

    scale_table.columns[0].width = Inches(1.8)
    scale_table.columns[1].width = Inches(0.8)
    scale_table.columns[2].width = Inches(4.1)

def add_test_cases(doc, test_cases):
    """Add detailed test case tables."""
    add_section_heading(doc, "2. User Acceptance Test Cases & Execution Matrix")
    
    for idx, tc in enumerate(test_cases, 1):
        p_tctitle = doc.add_paragraph()
        p_tctitle.paragraph_format.space_before = Pt(10)
        p_tctitle.paragraph_format.space_after = Pt(3)
        p_tctitle.paragraph_format.keep_with_next = True
        
        r_tcid = p_tctitle.add_run(f"Test Case {idx}: [{tc['id']}] {tc['title']}")
        r_tcid.bold = True
        r_tcid.font.name = "Arial"
        r_tcid.font.size = Pt(10)
        r_tcid.font.color.rgb = COLOR_PRIMARY

        table = doc.add_table(rows=6, cols=2)
        table.alignment = WD_TABLE_ALIGNMENT.CENTER
        set_table_borders(table)

        row_defs = [
            ("Objective & User Story", f"Objective Ref: {tc['obj_ref']} | User Story: {tc['user_story']}\n{tc['objective']}"),
            ("Prerequisites", tc['prerequisites']),
            ("Test Procedure / Action Steps", tc['procedure']),
            ("Expected System Behavior", tc['expected']),
            ("Actual Result Observed", "[  ] Conformed fully to expected behavior\n[  ] Discrepancy observed (specify below)"),
            ("Status & Notes", "Status:  [  ] PASS (P)     [  ] PASS W/ OBS (PWO)     [  ] FAIL (F)     [  ] BLOCKED (B)\n\nTester Remarks / Issues:\n__________________________________________________________________________________")
        ]

        for r_i, (lbl, content) in enumerate(row_defs):
            row = table.rows[r_i]
            c0, c1 = row.cells[0], row.cells[1]
            c0.width = Inches(2.0)
            c1.width = Inches(4.7)
            set_cell_background(c0, COLOR_LIGHT_BG_HEX)
            set_cell_margins(c0, 70, 70, 100, 100)
            set_cell_margins(c1, 70, 70, 100, 100)

            p0 = c0.paragraphs[0]
            p0.paragraph_format.space_before = Pt(0)
            p0.paragraph_format.space_after = Pt(0)
            r0 = p0.add_run(lbl)
            r0.bold = True
            r0.font.name = "Arial"
            r0.font.size = Pt(8.5)
            r0.font.color.rgb = COLOR_DARK

            p1 = c1.paragraphs[0]
            p1.paragraph_format.space_before = Pt(0)
            p1.paragraph_format.space_after = Pt(0)
            r1 = p1.add_run(content)
            r1.font.name = "Arial"
            r1.font.size = Pt(8.5)
            r1.font.color.rgb = COLOR_DARK

        doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_iso25010_section(doc, dimensions):
    """Add ISO/IEC 25010:2023 evaluation questionnaire."""
    add_section_heading(doc, "3. ISO/IEC 25010:2023 Software Quality Evaluation")
    
    p_iso_desc = doc.add_paragraph()
    p_iso_desc.paragraph_format.space_before = Pt(0)
    p_iso_desc.paragraph_format.space_after = Pt(6)
    r_desc = p_iso_desc.add_run(
        "Please rate your agreement with each statement based on your direct interaction with the A.E.G.I.S. portal.\n"
        "Rating Scale: 5 = Strongly Agree (SA) | 4 = Agree (A) | 3 = Neutral (N) | 2 = Disagree (D) | 1 = Strongly Disagree (SD)"
    )
    r_desc.font.name = "Arial"
    r_desc.font.size = Pt(8.5)
    r_desc.font.color.rgb = COLOR_MUTED

    # Calculate total questions
    total_q = sum(len(items) for _, items in dimensions)
    table = doc.add_table(rows=total_q + len(dimensions) + 1, cols=7)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table)

    # Header Row
    headers = ["Criterion / Evaluation Item", "SA (5)", "A (4)", "N (3)", "D (2)", "SD (1)", "Remarks"]
    for j, h in enumerate(headers):
        cell = table.cell(0, j)
        set_cell_background(cell, COLOR_PRIMARY_HEX)
        set_cell_margins(cell, 80, 80, 70, 70)
        p = cell.paragraphs[0]
        if j > 0 and j < 6:
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r = p.add_run(h)
        r.bold = True
        r.font.name = "Arial"
        r.font.size = Pt(8)
        r.font.color.rgb = RGBColor(255, 255, 255)

    current_row = 1
    for dim_title, items in dimensions:
        # Category separator row
        dim_cell = table.cell(current_row, 0)
        # merge across all 7 cols
        for col_idx in range(1, 7):
            dim_cell.merge(table.cell(current_row, col_idx))
        set_cell_background(dim_cell, "E5E7EB")
        set_cell_margins(dim_cell, 60, 60, 100, 100)
        p_dim = dim_cell.paragraphs[0]
        r_dim = p_dim.add_run(dim_title.upper())
        r_dim.bold = True
        r_dim.font.name = "Arial"
        r_dim.font.size = Pt(8.5)
        r_dim.font.color.rgb = COLOR_PRIMARY
        current_row += 1

        for item_idx, item_text in enumerate(items, 1):
            row = table.rows[current_row]
            c0 = row.cells[0]
            c0.width = Inches(3.5)
            set_cell_margins(c0, 60, 60, 90, 90)
            p0 = c0.paragraphs[0]
            r0 = p0.add_run(item_text)
            r0.font.name = "Arial"
            r0.font.size = Pt(8)
            r0.font.color.rgb = COLOR_DARK

            # 5 rating check cols
            for col_i in range(1, 6):
                cell_opt = row.cells[col_i]
                cell_opt.width = Inches(0.45)
                set_cell_margins(cell_opt, 60, 60, 40, 40)
                p_opt = cell_opt.paragraphs[0]
                p_opt.alignment = WD_ALIGN_PARAGRAPH.CENTER
                r_box = p_opt.add_run("[  ]")
                r_box.font.name = "Arial"
                r_box.font.size = Pt(8)
                r_box.font.color.rgb = COLOR_MUTED

            c_rem = row.cells[6]
            c_rem.width = Inches(1.3)
            set_cell_margins(c_rem, 60, 60, 60, 60)
            p_rem = c_rem.paragraphs[0]
            r_rem = p_rem.add_run("")
            r_rem.font.name = "Arial"
            r_rem.font.size = Pt(8)

            current_row += 1

    doc.add_paragraph().paragraph_format.space_after = Pt(6)

def add_qualitative_and_signoff(doc, role_name, evaluator_label):
    """Add qualitative feedback boxes and formal sign-off."""
    add_section_heading(doc, "4. Qualitative User Feedback & General Observations")

    q_boxes = [
        "A. What specific features or aspects of the system did you find most intuitive and helpful?",
        "B. What difficulties, confusing steps, or system errors (if any) did you encounter during the test?",
        "C. What specific recommendations or enhancements do you propose for future system releases?"
    ]

    for q in q_boxes:
        p_q = doc.add_paragraph()
        p_q.paragraph_format.space_before = Pt(6)
        p_q.paragraph_format.space_after = Pt(2)
        p_q.paragraph_format.keep_with_next = True
        r_q = p_q.add_run(q)
        r_q.bold = True
        r_q.font.name = "Arial"
        r_q.font.size = Pt(9)
        r_q.font.color.rgb = COLOR_DARK

        # Empty lined box for handwriting/typing
        table_box = doc.add_table(rows=1, cols=1)
        table_box.alignment = WD_TABLE_ALIGNMENT.CENTER
        cell = table_box.cell(0, 0)
        cell.width = Inches(6.7)
        set_cell_background(cell, "FAFAFA")
        set_table_borders(table_box, color="D1D5DB")
        set_cell_margins(cell, 120, 120, 150, 150)
        p_b = cell.paragraphs[0]
        r_b = p_b.add_run("\n\n\n")
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # Sign-off block
    add_section_heading(doc, "5. Formal Acceptance & Verification Sign-Off")

    p_conf = doc.add_paragraph()
    p_conf.paragraph_format.space_before = Pt(2)
    p_conf.paragraph_format.space_after = Pt(12)
    r_conf = p_conf.add_run(
        "By signing below, the participant certifies that the tests documented herein were performed independently "
        "and that the ratings and observations reflect an accurate assessment of the A.E.G.I.S. portal under test."
    )
    r_conf.font.name = "Arial"
    r_conf.font.size = Pt(8.5)
    r_conf.font.italic = True
    r_conf.font.color.rgb = COLOR_MUTED

    table_sign = doc.add_table(rows=1, cols=2)
    table_sign.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table_sign, val="none")

    c_sign0, c_sign1 = table_sign.cell(0, 0), table_sign.cell(0, 1)
    c_sign0.width = Inches(3.3)
    c_sign1.width = Inches(3.3)

    p_s0 = c_sign0.paragraphs[0]
    p_s0.paragraph_format.space_before = Pt(10)
    p_s0.paragraph_format.space_after = Pt(2)
    p_s0.add_run("Evaluated by:\n\n\n\n").font.size = Pt(9)
    r_s0 = p_s0.add_run("_________________________________________\n")
    r_s0.bold = True
    p_s0.add_run(f"Signature over Printed Name ({evaluator_label})\nDate: ________________________").font.size = Pt(8.5)

    p_s1 = c_sign1.paragraphs[0]
    p_s1.paragraph_format.space_before = Pt(10)
    p_s1.paragraph_format.space_after = Pt(2)
    p_s1.add_run("Facilitated & Verified by:\n\n\n\n").font.size = Pt(9)
    r_s1 = p_s1.add_run("_________________________________________\n")
    r_s1.bold = True
    p_s1.add_run("Capstone Lead Researcher / Facilitator\nDate: ________________________").font.size = Pt(8.5)

# ==============================================================================
# 1. BUILD STUDENT ROLE DOCUMENT
# ==============================================================================
def generate_student_uat_doc():
    doc = docx.Document()
    
    # 1 inch margins
    for s in doc.sections:
        s.top_margin = Inches(1.0)
        s.bottom_margin = Inches(1.0)
        s.left_margin = Inches(0.9)
        s.right_margin = Inches(0.9)

    add_header(
        doc,
        role_title="USER ACCEPTANCE TESTING (UAT) SCRIPT & EVALUATION INSTRUMENT",
        subtitle="END-USER APPLICANT WORKFLOW & SYSTEM USABILITY VALIDATION",
        audience="Student Applicant Role (CLSU Undergraduates)"
    )

    metadata = [
        ("Participant Name:", ""),
        ("Student ID / Webmail:", "__________________ @clsu2.edu.ph"),
        ("College & Department:", "College of ______________________ / Dept. of ___________________"),
        ("Degree Program & Year Level:", "BS ____________________________ | Year Level: [  ] 1st  [  ] 2nd  [  ] 3rd  [  ] 4th"),
        ("Date & Time of Testing:", "________________________, 2026  | Time: ______:______"),
        ("Testing Device & OS:", "[  ] Laptop   [  ] Desktop   [  ] Smartphone   | OS: Windows / macOS / Android / iOS"),
        ("Web Browser & Version:", "[  ] Google Chrome   [  ] Microsoft Edge   [  ] Mozilla Firefox   [  ] Safari"),
        ("Test Environment URL:", "Cloud Staging Portal (https://clsu.osa.scholarship)"),
        ("Capstone Facilitator / Researcher:", "Joshua Razon / Noriel Gadiano / John Andrei Carillo II (BSIT 4-1)")
    ]
    add_metadata_table(doc, metadata)

    instructions = [
        "Log in using your designated CLSU student credentials or register with your institutional '@clsu2.edu.ph' webmail.",
        "Perform each test step exactly in the sequence described in the Test Cases table below.",
        "Verify system responsiveness, clarity of interface messages, ease of navigation, and accuracy of outputs.",
        "Record the outcome of each test case using the designated rating code (P, PWO, F, B) along with candid remarks.",
        "Upon completing the 8 test scenarios, accomplish the ISO/IEC 25010 Usability & System Quality Questionnaire.",
        "This evaluation contributes directly to the research assessment of Capstone Project A.E.G.I.S."
    ]
    grading_scale = [
        ("Pass", "P", "Test executed smoothly without errors; expected visual and data outputs were fully satisfied."),
        ("Pass with Observations", "PWO", "Core function worked successfully, but minor ergonomic, wording, or aesthetic issues were noted."),
        ("Fail", "F", "Function encountered a software exception, validation breakdown, freeze, or incorrect output."),
        ("Blocked", "B", "Test could not be initiated or completed due to a dependency failure in a preceding test step.")
    ]
    add_instructions_block(doc, instructions, grading_scale)

    student_tcs = [
        {
            "id": "TC-STU-01",
            "title": "Institutional Account Registration & Domain Enforcement",
            "obj_ref": "Objective 1 (Centralized Scholarship Management)",
            "user_story": "US-01 (As a student, I want to register using my institutional email...)",
            "objective": "Verify that account registration strictly requires an authentic '@clsu2.edu.ph' institutional domain and enforces secure password standards.",
            "prerequisites": "User is on the portal login page without an active session.",
            "procedure": "1. Navigate to `/register`.\n2. Attempt registration using an unauthorized email (e.g., `user@gmail.com`) and verify error handling.\n3. Input a valid `@clsu2.edu.ph` email, student number, and compliant password (min 8 chars with symbols/numbers).\n4. Submit registration form.",
            "expected": "Unauthorized email is rejected with an explicit security alert. Valid registration creates an account record, dispatches email verification notification, and redirects to dashboard onboarding."
        },
        {
            "id": "TC-STU-02",
            "title": "Student Academic Profile & Demographic Setup",
            "obj_ref": "Objective 1 (Centralized Management & Applicant Dossier)",
            "user_story": "US-02 (As a student, I want to manage my academic profile and GWA history...)",
            "objective": "Confirm that personal, demographic, college affiliation, and GWA records can be configured and updated with robust input validation.",
            "prerequisites": "Logged in as a registered student applicant.",
            "procedure": "1. Navigate to Student Profile (`/student/profile`).\n2. Fill in complete name, degree program, college, current year level, contact number, and address.\n3. Enter cumulative GWA (e.g., 1.45) and family annual income.\n4. Click 'Save Profile' and refresh the page.",
            "expected": "System validates GWA within academic range (1.00 - 5.00). Profile completeness badge updates to 100%. Data persists accurately across session reload."
        },
        {
            "id": "TC-STU-03",
            "title": "Scholarship Catalog Discovery & Eligibility Filtering",
            "obj_ref": "Objective 1 (Scholarship Discovery & Filtering)",
            "user_story": "US-03 (As a student, I want to search and filter available scholarships by criteria...)",
            "objective": "Evaluate student discovery of active scholarship programs using category filters, keyword search, and minimum eligibility criteria.",
            "prerequisites": "Profile configured; active scholarship programs exist in the system database.",
            "procedure": "1. Navigate to Scholarship Catalog (`/scholarships/catalog`).\n2. Enter search keywords (e.g. 'Tertiary', 'Honor', 'Alumni').\n3. Filter by category (Institutional, Government, Private/Corporate).\n4. View detailed scholarship guidelines, slot quota, and deadline badge.",
            "expected": "Search results filter instantly without full page reloading. Card UI clearly highlights minimum GWA, eligible colleges, and slot availability. Eligible grants display active 'Apply Now' buttons; ineligible programs explain criteria."
        },
        {
            "id": "TC-STU-04",
            "title": "Application Form Completion & Dynamic Custom Fields",
            "obj_ref": "Objective 1 (Paperless Application Lifecycle)",
            "user_story": "US-04 (As a student, I want to submit custom application requirements...)",
            "objective": "Verify the dynamic rendering of custom scholarship questionnaire fields and client-side draft auto-save.",
            "prerequisites": "Student selects an active scholarship program with defined custom questionnaire fields.",
            "procedure": "1. Click 'Apply Now' on a selected scholarship.\n2. Review pre-filled student demographic information.\n3. Complete dynamic custom fields (e.g. 'Father's Occupation', 'Annual Income Tier', 'Essay on Need').\n4. Intentionally reload browser mid-entry to test draft recovery.",
            "expected": "All custom fields render with appropriate control types (text, dropdown, numeric, textarea). Form draft recovers automatically from local storage. Required field validator prevents empty submissions."
        },
        {
            "id": "TC-STU-05",
            "title": "Supporting Document Upload & Pre-Submission Validation",
            "obj_ref": "Objective 1 & 2 (Secure Storage & AI Ingestion)",
            "user_story": "US-05 (As a student, I want to securely upload my COG and enrollment credentials...)",
            "objective": "Ensure file size, MIME-type, and document integrity checks properly guard the upload of Certificate of Grades (COG) and Certificate of Registration (COR).",
            "prerequisites": "Application form open at document upload section.",
            "procedure": "1. Attempt uploading an unsupported file format (e.g. `.exe` or `.txt`) and verify rejection.\n2. Attempt uploading an oversized file (>10 MB) and observe alert.\n3. Upload valid COG (PDF/PNG/JPEG) and valid COR.\n4. Click thumbnail to inspect document preview modal before finalizing.",
            "expected": "Unsupported formats and oversized files are blocked with clear error dialogs. Valid documents upload with progress feedback and render high-resolution previews for self-verification."
        },
        {
            "id": "TC-STU-06",
            "title": "Application Final Submission & Real-Time Tracking Dashboard",
            "obj_ref": "Objective 1 (End-to-End Application Lifecycle)",
            "user_story": "US-06 (As a student, I want to track my application progress in real time...)",
            "objective": "Verify receipt generation, status transition to 'Pending Review', and tracking visualization on the student dashboard.",
            "prerequisites": "All required form fields and documents completed.",
            "procedure": "1. Review submission summary and check the Data Privacy Act (R.A. 10173) agreement box.\n2. Click 'Submit Application'.\n3. Navigate to Student Dashboard (`/student/dashboard` or `/student/applications`).\n4. Inspect application status card and timeline progress bar.",
            "expected": "Confirmation alert confirms successful receipt with unique tracking ID. Application status displays 'Pending Review'. Progress tracker reflects current stage in the evaluation pipeline."
        },
        {
            "id": "TC-STU-07",
            "title": "Correction Resubmission Workflow on Deficiency Notice",
            "obj_ref": "Objective 1 & 3 (Review Feedback & Notification)",
            "user_story": "US-06 (As a student, I want to correct and resubmit flagged documents...)",
            "objective": "Confirm that an application marked 'Returned for Correction' by OSA staff allows the student to read remarks, re-upload documents, and resubmit.",
            "prerequisites": "Test application returned with deficiency remarks by OSA staff evaluator.",
            "procedure": "1. Open application marked 'Returned for Correction'.\n2. Read staff evaluator remarks (e.g. 'Uploaded COG is blurry; please submit official registrar copy').\n3. Re-upload replacement document.\n4. Click 'Resubmit Application'.",
            "expected": "Evaluator remarks are prominently displayed in amber alert banner. Only flagged documents/fields are unlocked for editing. Status updates cleanly to 'Resubmitted / Pending Review'."
        },
        {
            "id": "TC-STU-08",
            "title": "Notification Reception & Official Application Form PDF Download",
            "obj_ref": "Objective 3 & 4 (Automated Notification & PDF Certification)",
            "user_story": "US-06 & US-12 (As a student, I want to receive approval notice and export certified PDF...)",
            "objective": "Validate automatic notification delivery and generation of the cryptographically sealed official application PDF.",
            "prerequisites": "Application marked 'Approved' in system.",
            "procedure": "1. Check notification bell icon in top navigation bar.\n2. Verify receipt of automated email notification.\n3. Open approved application view and click 'Download Official Application Form'.\n4. Inspect generated PDF for CLSU OSA header, student data, and verification QR code.",
            "expected": "Notification bell displays unread badge. Email notification arrives promptly with approval details. PDF downloads cleanly, rendering official CLSU OSA seal, applicant profile, GWA, and functional QR code."
        }
    ]
    add_test_cases(doc, student_tcs)

    # ISO 25010 for Students
    student_iso = [
        ("Functional Suitability", [
            "The portal provides all necessary features to apply for CLSU scholarships without omitting required steps.",
            "The document upload process functions reliably for Certificates of Grades (COG) and Certificates of Registration (COR).",
            "The scholarship catalog accurately displays eligibility criteria, deadlines, and slot availability."
        ]),
        ("Usability & Learnability", [
            "The student application form is easy to complete without needing technical assistance or prior training.",
            "The application tracking dashboard clearly communicates the current status of my application at all times.",
            "Error messages and validation warnings are helpful, clear, and easy to understand.",
            "The user interface is visually clean, professional, and well-organized across all pages."
        ]),
        ("Reliability & Performance Efficiency", [
            "Pages and forms load quickly without noticeable freezing or lag during submission.",
            "Document uploads complete smoothly without timeouts or unexpected failures.",
            "The system preserves my entered data reliably even if I accidentally navigate away."
        ]),
        ("Security & Data Confidentiality", [
            "I can access only my own application records and cannot view other students' sensitive documents.",
            "The portal provides secure login and protects my personal and financial information in compliance with R.A. 10173."
        ])
    ]
    add_iso25010_section(doc, student_iso)
    add_qualitative_and_signoff(doc, "Student", "Student Applicant")

    out_path = os.path.abspath("docs/UAT_Test_Script_Student_Role.docx")
    doc.save(out_path)
    print(f"Generated: {out_path} ({os.path.getsize(out_path)} bytes)")

# ==============================================================================
# 2. BUILD STAFF EVALUATOR ROLE DOCUMENT
# ==============================================================================
def generate_staff_uat_doc():
    doc = docx.Document()
    
    for s in doc.sections:
        s.top_margin = Inches(1.0)
        s.bottom_margin = Inches(1.0)
        s.left_margin = Inches(0.9)
        s.right_margin = Inches(0.9)

    add_header(
        doc,
        role_title="USER ACCEPTANCE TESTING (UAT) SCRIPT & EVALUATION INSTRUMENT",
        subtitle="APPLICATION TRIAGE, AI FORENSIC AUDIT & DECISIONING WORKFLOW VALIDATION",
        audience="OSA Scholarship Evaluator / Staff Role"
    )

    metadata = [
        ("Evaluator Name:", ""),
        ("Designation / Position:", "[  ] Scholarship Officer   [  ] Administrative Staff   [  ] Technical Evaluator"),
        ("Office / Department:", "Office of Student Affairs (OSA) — Central Luzon State University"),
        ("Institutional Webmail:", "__________________ @clsu.edu.ph / @clsu2.edu.ph"),
        ("Date & Time of Testing:", "________________________, 2026  | Time: ______:______"),
        ("Testing Device & OS:", "[  ] Workstation Desktop   [  ] Laptop   | OS: Windows / macOS / Linux"),
        ("Web Browser & Version:", "[  ] Google Chrome   [  ] Microsoft Edge   [  ] Mozilla Firefox"),
        ("Assigned Test Data Set:", "30 Pre-loaded Applications (15 Authentic COGs, 15 Tampered COGs)"),
        ("Capstone Facilitator / Researcher:", "Joshua Razon / Noriel Gadiano / John Andrei Carillo II (BSIT 4-1)")
    ]
    add_metadata_table(doc, metadata)

    instructions = [
        "Log in using authorized OSA Staff Evaluator credentials at the cloud staging portal.",
        "Perform the 8 structured test cases in sequence, focusing on application queue triage, interactive canvas inspection, AI forensic verification, and decision dispatch.",
        "Execute the blind vs. AI-assisted review protocol using the pre-loaded document set to assess the impact of AI fraud scoring and Grad-CAM heatmaps on review accuracy.",
        "Record results as P, PWO, F, or B with explicit observations in the Remarks section.",
        "Accomplish the full 5-dimension ISO/IEC 25010:2023 evaluation instrument following testing."
    ]
    grading_scale = [
        ("Pass", "P", "Workflow completed without error; AI outputs, canvas controls, and notification triggers operated accurately."),
        ("Pass with Observations", "PWO", "Operational, but minor interface friction, wording clarity, or tool ergonomics could be improved."),
        ("Fail", "F", "System breakdown, failure in AI forensic pipeline, incorrect status transition, or notification failure."),
        ("Blocked", "B", "Step could not be completed due to failure of a prerequisite administrative function.")
    ]
    add_instructions_block(doc, instructions, grading_scale)

    staff_tcs = [
        {
            "id": "TC-STF-01",
            "title": "Secure Staff Authentication & Session Integrity",
            "obj_ref": "Objective 1 (RBAC Security & Authentication)",
            "user_story": "US-07 (As an OSA staff member, I want to securely log in to access the review queue...)",
            "objective": "Verify role-based access control, session security, and proper redirection to the Staff Review Queue.",
            "prerequisites": "User possesses registered OSA staff evaluator credentials.",
            "procedure": "1. Navigate to `/login`.\n2. Enter staff credentials and submit.\n3. Complete 6-digit OTP verification when prompted.\n4. Check 'Trust this device for 30 days' toggle.\n5. Verify post-login redirection to staff dashboard.",
            "expected": "System successfully authenticates staff account, creates encrypted session, and redirects to `/admin/dashboard`. Unauthorized student routes or super-admin only panels are restricted."
        },
        {
            "id": "TC-STF-02",
            "title": "Application Queue Triage & Multi-Criteria Filtering",
            "obj_ref": "Objective 1 (Centralized Queue Management)",
            "user_story": "US-07 (As an OSA staff member, I want to filter and search applications by program and status...)",
            "objective": "Ensure the evaluation queue allows sorting, keyword searching, and multi-criteria filtering across large application volumes.",
            "prerequisites": "Staff logged in; pre-loaded dummy applications exist in database.",
            "procedure": "1. Navigate to Application Review Queue (`/admin/applications`).\n2. Filter applications by Scholarship Program (e.g., 'University Academic Scholarship').\n3. Filter by Status: 'Pending Review', 'Under Evaluation', 'Returned for Correction'.\n4. Sort by GWA (Ascending) and Submission Date (Descending).",
            "expected": "Queue filters rapidly with active counts updating dynamically. Sorting by GWA and submission date arranges rows correctly. Status badges clearly differentiate stages."
        },
        {
            "id": "TC-STF-03",
            "title": "Comprehensive Applicant Dossier & Academic Assessment",
            "obj_ref": "Objective 1 (Comprehensive Applicant Dossier)",
            "user_story": "US-08 (As an OSA staff member, I want to view full student details, income, and GWA side-by-side...)",
            "objective": "Verify that all applicant demographic data, academic history, GWA, and custom field responses are legibly presented in the review canvas.",
            "prerequisites": "Select a pending application from the queue.",
            "procedure": "1. Click 'Review Application' on an applicant row (`/admin/review/{id}`).\n2. Review Student Profile pane (ID, Year Level, College, Degree, GWA, Socio-economic Bracket).\n3. Inspect custom program question responses (e.g. household income, essay answers).\n4. Check automated GWA eligibility indicator against minimum requirement.",
            "expected": "Dossier displays all data with clean visual hierarchy. Eligibility badge highlights whether student's GWA satisfies program cutoff (e.g. GWA 1.45 <= 1.75 requirement)."
        },
        {
            "id": "TC-STF-04",
            "title": "High-Resolution Document Inspection Canvas & Interactive Tools",
            "obj_ref": "Objective 1 & 2 (Document Inspection Canvas)",
            "user_story": "US-08 (As an OSA staff member, I want to zoom, rotate, and enhance COG images to verify grades...)",
            "objective": "Evaluate interactive canvas manipulation controls for close visual inspection of student Certificates of Grades (COG).",
            "prerequisites": "Application review canvas open with uploaded COG document.",
            "procedure": "1. In document viewer, test Zoom In (up to 400%) and Zoom Out controls.\n2. Click and drag to pan across high-resolution document canvas.\n3. Test 90-degree Rotation control.\n4. Toggle Color Inversion and Contrast Enhancement filters to examine watermarks and registrar seals.",
            "expected": "Canvas tools execute smoothly with zero frame lag. Image details remain crisp at high magnification. Contrast and inversion filters reveal fine stamp impressions and eraser marks clearly."
        },
        {
            "id": "TC-STF-05",
            "title": "AI Forensic Verification & Fraud Probability Score Interpretation",
            "obj_ref": "Objective 2 (AI Document Verification Module)",
            "user_story": "US-09 (As an OSA staff member, I want AI fraud detection to calculate a fraud risk score...)",
            "objective": "Assess the AI forensic pipeline output: Fraud Probability Score (0-100%), 3-Tier Risk Indicator, and EXIF software fingerprinting.",
            "prerequisites": "COG document loaded in review canvas.",
            "procedure": "1. Inspect AI Verification Card in the review sidebar.\n2. Observe Fraud Probability Score (0–100%) and Risk Tier Badge (Low Risk <35%, Moderate Risk 35-70%, High Risk >70%).\n3. Review EXIF Software Fingerprint analysis (checks for Photoshop, GIMP, Photopea, CamScanner).\n4. Verify that camera/scanner noise penalties are appropriately differentiated from malicious synthetic manipulation.",
            "expected": "AI module outputs clear numerical score and color-coded risk badge. EXIF inspection correctly distinguishes benign mobile scanners from desktop image editors, preventing false positives."
        },
        {
            "id": "TC-STF-06",
            "title": "Grad-CAM Saliency Heatmap & Error Level Analysis (ELA) Overlay",
            "obj_ref": "Objective 2 (Grad-CAM Visual Explanations)",
            "user_story": "US-09 (As an OSA staff member, I want visual heatmaps showing suspicious document regions...)",
            "objective": "Evaluate the visual clarity and utility of the Grad-CAM and Error Level Analysis (ELA) heatmaps in pinpointing localized grade tampering.",
            "prerequisites": "Document analyzed by AI forensic pipeline.",
            "procedure": "1. Toggle 'Grad-CAM Saliency Heatmap' overlay button on the document canvas.\n2. Adjust heatmap opacity slider from 0% to 100% to compare overlay with underlying text.\n3. Toggle 'Error Level Analysis (ELA)' view to detect compression artifact discrepancies.\n4. Examine flagged hotspots around grade numbers, GWA totals, and registrar signatures.",
            "expected": "Heatmap aligns with coordinate grid of document. Tampered grade entries display bright red/orange saliency hotspots, accurately directing reviewer attention to forged characters."
        },
        {
            "id": "TC-STF-07",
            "title": "Award Determination Decisioning & Fast-Triage Remarks Dispatch",
            "obj_ref": "Objective 1 & 3 (Decisioning & Email Notifications)",
            "user_story": "US-10 (As an OSA staff member, I want to approve, reject, or return applications with remarks...)",
            "objective": "Verify official decisioning workflow: status transitions, preset remarks selection, and automated email dispatch.",
            "prerequisites": "Staff has reviewed dossier and forensic analysis.",
            "procedure": "1. In Decision Action panel, select an action: [Approve], [Return for Correction], or [Reject].\n2. Select a Fast-Triage Preset Remark (e.g. 'Incomplete Grade Record', 'Blurry Registrar Seal') or type custom remarks.\n3. Confirm action modal.\n4. Verify immediate application status update and automated email dispatch trigger.",
            "expected": "Confirmation modal prevents accidental clicks. Rejection/Return enforces mandatory remarks. Status updates immediately in queue and triggers asynchronous email notification to student."
        },
        {
            "id": "TC-STF-08",
            "title": "Confidential Internal Notes & Review Audit Log Verification",
            "obj_ref": "Objective 1 & 4 (Staff Collaboration & Audit Trail)",
            "user_story": "US-10 & US-11 (As an OSA staff member, I want internal evaluator notes and an immutable audit trail...)",
            "objective": "Confirm that confidential internal staff notes are saved privately and that every evaluator action generates an immutable audit log entry.",
            "prerequisites": "Decision rendered on application.",
            "procedure": "1. Expand 'Internal Evaluator Notes' section on review page.\n2. Enter confidential observation (e.g. 'GWA verified with College Secretary via phone') and save.\n3. Log in as student applicant and confirm internal note is NOT visible to student.\n4. Inspect application history timeline to verify evaluator name, action, and timestamp are permanently recorded.",
            "expected": "Internal notes remain strictly hidden from student views. Timeline immutably records evaluator identity, timestamp, decision, and remarks for complete administrative accountability."
        }
    ]
    add_test_cases(doc, staff_tcs)

    # Full 5 Dimensions ISO 25010 for Staff
    staff_iso = [
        ("Functional Suitability", [
            "The system correctly processes and records scholarship applications without omitting required information.",
            "All uploaded student documents (COG, COR) remain securely accessible for evaluation.",
            "The AI fraud detection module successfully analyzes submitted COG documents and provides clear scores.",
            "The system accurately filters and categorizes applications by program, college, and review status.",
            "Automated email notifications contain accurate, complete, and timely status update information."
        ]),
        ("Usability & Interaction Capability", [
            "Navigation from the application queue to the document evaluation canvas is straightforward and intuitive.",
            "The Fraud Probability Score and color-coded risk indicators aid document review decisions effectively.",
            "The Grad-CAM heatmap overlay clearly highlights suspicious or edited document regions.",
            "The interactive canvas controls (zoom, pan, rotate, contrast inversion) function smoothly during inspection.",
            "Fast-Triage preset remarks streamline the process of returning or rejecting incomplete applications."
        ]),
        ("Reliability", [
            "The system dispatches email notifications promptly after each application status update.",
            "Document viewer renders high-resolution files consistently without crashes or rendering freezes.",
            "The AI forensic analysis results are consistent when the same document is re-scanned.",
            "Data entered into the system (notes, decisions, filters) is preserved accurately without corruption or loss."
        ]),
        ("Performance Efficiency", [
            "Application review pages and document images load within acceptable timeframes (<3 seconds).",
            "AI document forensic analysis completes in a reasonable timeframe (typically under 5 seconds).",
            "Page transitions and queue filtering are fast enough to support an efficient, high-volume review workflow."
        ]),
        ("Security & Auditability", [
            "Student academic and financial data is accessible only to authorized personnel.",
            "The login and session system adequately prevents unauthorized access through strong authentication.",
            "The system maintains an immutable audit log that tracks all staff decisions, overrides, and status changes.",
            "Internal staff notes are kept strictly confidential and inaccessible to unauthorized student accounts."
        ])
    ]
    add_iso25010_section(doc, staff_iso)

    # Blind vs AI-assisted review worksheet
    add_section_heading(doc, "4. AI-Assisted vs. Human-Only Document Review Comparison Worksheet")
    p_bl_desc = doc.add_paragraph()
    p_bl_desc.paragraph_format.space_before = Pt(0)
    p_bl_desc.paragraph_format.space_after = Pt(6)
    r_bld = p_bl_desc.add_run(
        "Protocol: As detailed in Chapter IV (Results & Discussion), evaluators inspect 10 sample COG documents in two phases:\n"
        "Phase 1: Record initial authenticity judgment WITHOUT viewing AI scores.\n"
        "Phase 2: Reveal AI Fraud Probability Score & Grad-CAM Heatmap, then record final decision and reviewer confidence."
    )
    r_bld.font.name = "Arial"
    r_bld.font.size = Pt(8.5)
    r_bld.font.color.rgb = COLOR_MUTED

    tbl_bld = doc.add_table(rows=11, cols=7)
    tbl_bld.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(tbl_bld)

    bld_headers = ["Doc ID", "Doc Type", "Phase 1 Judgment\n(No AI Assistance)", "Review\nTime (s)", "AI Fraud\nScore (%)", "Phase 2 Judgment\n(With AI Assistance)", "Confidence\n(1 to 5)"]
    for j, bh in enumerate(bld_headers):
        c = tbl_bld.cell(0, j)
        set_cell_background(c, COLOR_PRIMARY_HEX)
        set_cell_margins(c, 70, 70, 50, 50)
        p = c.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r = p.add_run(bh)
        r.bold = True
        r.font.name = "Arial"
        r.font.size = Pt(8)
        r.font.color.rgb = RGBColor(255, 255, 255)

    sample_docs = [
        ("COG-001", "Authentic COG (BSIT)", "[  ] Auth  [  ] Tampered", "____ s", "12.4%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-002", "Altered Grade (BSCE)", "[  ] Auth  [  ] Tampered", "____ s", "88.7%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-003", "Authentic COG (BSA)", "[  ] Auth  [  ] Tampered", "____ s", "08.1%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-004", "Forged Registrar Stamp", "[  ] Auth  [  ] Tampered", "____ s", "92.3%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-005", "Camera Noise Scan (BSEE)", "[  ] Auth  [  ] Tampered", "____ s", "24.5%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-006", "Photoshop Spliced GWA", "[  ] Auth  [  ] Tampered", "____ s", "95.6%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-007", "Authentic COG (BSBio)", "[  ] Auth  [  ] Tampered", "____ s", "14.2%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-008", "Altered Units/Subject", "[  ] Auth  [  ] Tampered", "____ s", "78.4%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-009", "Mobile CamScanner Auth", "[  ] Auth  [  ] Tampered", "____ s", "28.0%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5"),
        ("COG-010", "Deep Tampered Header", "[  ] Auth  [  ] Tampered", "____ s", "84.9%", "[  ] Auth  [  ] Tampered", "[  ] 1 [  ] 2 [  ] 3 [  ] 4 [  ] 5")
    ]

    for row_idx, data_tuple in enumerate(sample_docs, 1):
        row = tbl_bld.rows[row_idx]
        for col_idx, val in enumerate(data_tuple):
            c = row.cells[col_idx]
            set_cell_margins(c, 50, 50, 60, 60)
            if row_idx % 2 == 1:
                set_cell_background(c, COLOR_LIGHT_BG_HEX)
            p = c.paragraphs[0]
            if col_idx in [0, 1]:
                p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            else:
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            r = p.add_run(val)
            r.font.name = "Arial"
            r.font.size = Pt(7.5)
            r.font.color.rgb = COLOR_DARK

    doc.add_paragraph().paragraph_format.space_after = Pt(6)
    add_qualitative_and_signoff(doc, "Staff", "OSA Scholarship Evaluator")

    out_path = os.path.abspath("docs/UAT_Test_Script_Staff_Role.docx")
    doc.save(out_path)
    print(f"Generated: {out_path} ({os.path.getsize(out_path)} bytes)")

# ==============================================================================
# 3. BUILD ADMIN / DIRECTOR ROLE DOCUMENT
# ==============================================================================
def generate_admin_uat_doc():
    doc = docx.Document()
    
    for s in doc.sections:
        s.top_margin = Inches(1.0)
        s.bottom_margin = Inches(1.0)
        s.left_margin = Inches(0.9)
        s.right_margin = Inches(0.9)

    add_header(
        doc,
        role_title="USER ACCEPTANCE TESTING (UAT) SCRIPT & SYSTEM ACCEPTANCE INSTRUMENT",
        subtitle="EXECUTIVE GOVERNANCE, PROGRAM MANAGEMENT & COMPLIANCE VALIDATION",
        audience="Super Administrator & OSA Director Role"
    )

    metadata = [
        ("Evaluator Name:", ""),
        ("Administrative Title:", "[  ] Director, Office of Student Affairs   [  ] System Super Administrator"),
        ("Office / Institutional Unit:", "Office of Student Affairs (OSA) / Management Information Systems"),
        ("Official Webmail:", "__________________ @clsu.edu.ph"),
        ("Date & Time of Testing:", "________________________, 2026  | Time: ______:______"),
        ("Testing Device & OS:", "[  ] Executive Workstation   [  ] Secure Laptop   | OS: Windows / macOS"),
        ("Web Browser & Version:", "[  ] Google Chrome   [  ] Microsoft Edge   [  ] Mozilla Firefox"),
        ("Evaluation Authority:", "Final System Acceptance Sign-Off for Capstone 2 Deployment Handover"),
        ("Capstone Facilitator / Researcher:", "Joshua Razon / Noriel Gadiano / John Andrei Carillo II (BSIT 4-1)")
    ]
    add_metadata_table(doc, metadata)

    instructions = [
        "Log in using Super Administrator / OSA Director credentials.",
        "Perform the 8 administrative test scenarios, covering executive analytics, scholarship creation, dynamic form configuration, RBAC, AI settings, statutory reporting, and audit logging.",
        "Verify system scalability, quota governance, data privacy controls, and compliance export formatting against CHED/DOST requirements.",
        "Evaluate system quality using the ISO/IEC 25010:2023 instrument.",
        "Complete Section 5 (Formal System Acceptance Determination) to declare whether A.E.G.I.S. is accepted for institutional deployment."
    ]
    grading_scale = [
        ("Pass", "P", "Administrative capability executed successfully with full data integrity and correct security enforcement."),
        ("Pass with Observations", "PWO", "Function succeeded, but suggestions exist for executive dashboard charts, export layout, or usability."),
        ("Fail", "F", "Failure in programmatic CRUD, custom field builder collision, export failure, or privilege leak."),
        ("Blocked", "B", "Administrative test blocked due to underlying database, microservice, or server dependency failure.")
    ]
    add_instructions_block(doc, instructions, grading_scale)

    admin_tcs = [
        {
            "id": "TC-ADM-01",
            "title": "Executive KPI Analytics & Strategic Intelligence Dashboard",
            "obj_ref": "Objective 1 & 4 (Executive Decision Support)",
            "user_story": "US-11 (As an administrator, I want executive KPI analytics on applications, fraud rates, and quotas...)",
            "objective": "Verify the calculation and visualization of key performance indicators: Total Applications, Approval Rates, Grade Integrity Index, Fraud Detection Rates, and College Distribution.",
            "prerequisites": "Logged in as Super Administrator / Director.",
            "procedure": "1. Navigate to Executive Analytics (`/superadmin/analytics` or `/admin/dashboard`).\n2. Inspect real-time KPI counter cards: Total Applications, Active Grants, Total Grantees, Tamper Alerts.\n3. Filter analytics by Academic Year and Semester.\n4. Inspect interactive charts: Application Volume by College, Fraud Risk Distribution, and Review Cycle Time.",
            "expected": "KPI cards render accurate metric sums with high-contrast styling. Dynamic filtering by academic term recalculates charts seamlessly without data discrepancies."
        },
        {
            "id": "TC-ADM-02",
            "title": "Scholarship Program Creation, Quota, & Lifecycle Governance",
            "obj_ref": "Objective 1 (Complete Scholarship Lifecycle)",
            "user_story": "US-11 (As an administrator, I want to create, configure, and manage scholarship programs and quotas...)",
            "objective": "Ensure new scholarship programs can be created, configured with eligibility rules and slot quotas, and managed across their lifecycle (Active / Inactive / Archived).",
            "prerequisites": "Super Administrator access.",
            "procedure": "1. Navigate to Scholarship Management (`/superadmin/scholarships`).\n2. Click 'Add New Scholarship Program'.\n3. Input program name, funding agency, academic term, total slot quota, minimum GWA threshold, and deadline.\n4. Save program and toggle status between Active and Inactive.",
            "expected": "New program persists in database. Slot quota validator prevents negative/invalid values. Active programs immediately become discoverable in student catalog; inactive programs are hidden from new applicants."
        },
        {
            "id": "TC-ADM-03",
            "title": "Custom Dynamic Form Field Builder & Requirement Configuration",
            "obj_ref": "Objective 1 (Dynamic Application Configuration)",
            "user_story": "US-11 (As an administrator, I want to configure dynamic custom form fields for specific scholarships...)",
            "objective": "Test the custom form field configurator: adding dynamic fields (text, dropdown, file upload), reordering with arrows, setting mandatory flags, and verifying clean UI layout.",
            "prerequisites": "Open Scholarship Program creation or edit modal.",
            "procedure": "1. In program modal, navigate to 'Custom Form Fields' section.\n2. Add Field 1 (Text: 'Father Occupation'), Field 2 (Select: '4Ps Beneficiary [Yes/No]'), Field 3 (File: 'Barangay Indigency').\n3. Reorder fields using Up (↑) and Down (↓) arrows; verify field numbering badges sync.\n4. Toggle 'Required' switch on and off.\n5. Save program and preview student application form.",
            "expected": "Sub-header bar and button cluster render cleanly without overlapping labels or toggle switches. Reordering reindexes fields smoothly. Custom fields render correctly on student submission interface."
        },
        {
            "id": "TC-ADM-04",
            "title": "Role-Based Access Control (RBAC) & Staff User Management",
            "obj_ref": "Objective 1 (RBAC & User Governance)",
            "user_story": "US-11 (As an administrator, I want to manage staff accounts and role permissions...)",
            "objective": "Confirm administrative capability to invite/register staff evaluators, assign designated scholarship programs, and enforce privilege boundaries.",
            "prerequisites": "Super Administrator privileges.",
            "procedure": "1. Navigate to User Management (`/superadmin/users`).\n2. Create a new staff evaluator account.\n3. Assign specific scholarship programs to the staff member.\n4. Test password reset dispatch and temporary account suspension toggle.\n5. Log in as newly created staff member to verify restricted scope.",
            "expected": "Staff accounts are created with encrypted temporary credentials. Evaluator can only access assigned scholarship queues. Deactivated accounts are blocked from system entry immediately."
        },
        {
            "id": "TC-ADM-05",
            "title": "AI Forensics Service Configuration & Sensitivity Tuning",
            "obj_ref": "Objective 2 (AI Module Configuration)",
            "user_story": "US-11 (As an administrator, I want to configure AI detection thresholds and microservice pipelines...)",
            "objective": "Verify administrator control over AI microservice health monitoring, pipeline selection (V1 Fast, V2 Standard ELA-CNN, V3 Deep Multi-Pass), and fraud threshold calibration.",
            "prerequisites": "Super Administrator settings access.",
            "procedure": "1. Navigate to AI Configuration / System Settings (`/superadmin/settings`).\n2. Inspect AI Microservice connection health badge.\n3. Configure default analysis pipeline (V2 Standard ELA-CNN).\n4. Adjust default high-risk fraud threshold (e.g. 70.0%) and save settings.",
            "expected": "Microservice ping confirms live connectivity. Updated pipeline and threshold parameters save to `system_settings` table and apply to subsequent document evaluation jobs."
        },
        {
            "id": "TC-ADM-06",
            "title": "Statutory Compliance Reporting & Multi-Format Record Export Engine",
            "obj_ref": "Objective 4 (Record Export for Compliance Use)",
            "user_story": "US-12 (As an administrator, I want to export compliant CSV and PDF reports for CHED/DOST...)",
            "objective": "Validate generation and export of official scholarship masterlists and compliance summaries in CSV and PDF formats.",
            "prerequisites": "Applications exist across multiple scholarship programs and terms.",
            "procedure": "1. Navigate to Reporting & Exports (`/admin/reports` or `/superadmin/analytics`).\n2. Select Report Type: 'CHED Scholarship Grantee Masterlist'.\n3. Filter by Academic Year and Program.\n4. Click 'Export to CSV'; open and inspect column formatting in spreadsheet software.\n5. Click 'Export to PDF'; inspect layout for official CLSU OSA letterhead, summary stats, and signature lines.",
            "expected": "CSV exports download within 3 seconds with headers matching CHED/DOST-SEI specifications. PDF generates cleanly within 5-8 seconds, displaying institutional styling, summary tables, and authorized signatory blocks."
        },
        {
            "id": "TC-ADM-07",
            "title": "Tamper-Evident System Audit Trail & Security Event Monitoring",
            "obj_ref": "Objective 1 & 5 (Security & Auditability)",
            "user_story": "US-11 (As an administrator, I want an immutable audit log of all system activities...)",
            "objective": "Verify that all administrative activities, user authentications, application status overrides, and file exports are permanently recorded in the audit trail.",
            "prerequisites": "System has recorded operational events.",
            "procedure": "1. Navigate to Audit Logs (`/superadmin/audit-logs`).\n2. Filter logs by Action Type (Login, Status Update, Program Creation, File Export).\n3. Search logs by User ID or IP Address.\n4. Inspect detailed audit payload modal showing old vs. new values for a status override.",
            "expected": "Audit trail captures timestamp, actor ID, action category, IP address, and payload difference. Records cannot be edited, overwritten, or cleared by staff, ensuring complete evidentiary integrity."
        },
        {
            "id": "TC-ADM-08",
            "title": "Soft-Deletion, Trash Recovery, & System Branding Governance",
            "obj_ref": "Objective 1 (System Maintenance & Data Resilience)",
            "user_story": "US-11 (As an administrator, I want soft-delete protection and system branding controls...)",
            "objective": "Confirm data resilience through soft-deletion and restoration of scholarship programs/applications, as well as institutional announcement broadcasting.",
            "prerequisites": "Super Administrator access.",
            "procedure": "1. Soft-delete a test scholarship program.\n2. Navigate to 'Archived / Trash' tab; verify program is listed.\n3. Click 'Restore Program' and confirm restoration in active table.\n4. Post a university-wide scholarship announcement via Announcement Manager.",
            "expected": "Soft-deleted records are safely preserved without cascading foreign key violations. Restore brings program back to active status. Broadcasted announcement immediately appears on student dashboard."
        }
    ]
    add_test_cases(doc, admin_tcs)

    # Full ISO 25010 for Admin / Director
    admin_iso = [
        ("Functional Suitability", [
            "The system completely addresses the end-to-end scholarship lifecycle from program creation to grantee certification.",
            "The report generation engine produces accurate, filtered masterlists meeting CHED and DOST-SEI compliance requirements.",
            "The dynamic custom field builder successfully configures specialized scholarship requirements.",
            "The automated notification system operates reliably upon all administrative status updates."
        ]),
        ("Usability & Learnability", [
            "The administrative and analytics dashboard layout is intuitive and does not require extensive training.",
            "Report filtering and export functions are easy to configure and execute.",
            "The custom form field builder and program configuration controls are ergonomic and well-labeled.",
            "System warnings and confirmation dialogs prevent accidental data loss or premature status changes."
        ]),
        ("Reliability & Scalability", [
            "The system maintains stable performance even when handling concurrent application submissions.",
            "Data entered into the system is preserved accurately without corruption, truncation, or database loss.",
            "Background queue jobs (emails, AI scans, exports) complete reliably without silent failures.",
            "The soft-delete and recovery mechanisms protect institutional records against accidental deletion."
        ]),
        ("Performance Efficiency", [
            "Executive analytics and dashboard charts render rapidly (<3 seconds) upon initial load.",
            "PDF compliance reports and CSV masterlists generate within acceptable time limits (<8 seconds).",
            "Database queries and table filtering remain responsive as application records scale."
        ]),
        ("Security & Compliance (R.A. 10173)", [
            "Student academic and financial records are strictly protected with multi-layered role-based access controls.",
            "Multi-factor authentication (MFA) and trusted device tokens provide robust perimeter security.",
            "The immutable audit log maintains comprehensive, tamper-evident records of all administrative actions.",
            "The system fully complies with the Data Privacy Act of 2012 (R.A. 10173) in processing student data."
        ])
    ]
    add_iso25010_section(doc, admin_iso)

    # Section 4: Qualitative
    add_section_heading(doc, "4. Executive Feedback & Strategic Recommendations")
    q_admin = [
        "A. Strategic Value: How effectively does A.E.G.I.S. address the manual bottlenecks, fraudulent submissions, and reporting challenges historically faced by CLSU OSA?",
        "B. Operational Considerations: What workflow adjustments or institutional policy alignments are recommended prior to campus-wide deployment?",
        "C. Enhancement Priorities: What future integrations (e.g. CLSU SIS GWA auto-sync, Landbank stipend API, SMS gateway) should be prioritized in subsequent project phases?"
    ]
    for q in q_admin:
        p_q = doc.add_paragraph()
        p_q.paragraph_format.space_before = Pt(6)
        p_q.paragraph_format.space_after = Pt(2)
        p_q.paragraph_format.keep_with_next = True
        r_q = p_q.add_run(q)
        r_q.bold = True
        r_q.font.name = "Arial"
        r_q.font.size = Pt(9)
        r_q.font.color.rgb = COLOR_DARK

        table_box = doc.add_table(rows=1, cols=1)
        table_box.alignment = WD_TABLE_ALIGNMENT.CENTER
        cell = table_box.cell(0, 0)
        cell.width = Inches(6.7)
        set_cell_background(cell, "FAFAFA")
        set_table_borders(table_box, color="D1D5DB")
        set_cell_margins(cell, 120, 120, 150, 150)
        p_b = cell.paragraphs[0]
        r_b = p_b.add_run("\n\n\n")
        doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # Section 5: Formal System Acceptance Determination
    add_section_heading(doc, "5. Formal System Acceptance Determination & Institutional Sign-Off")

    p_dec_intro = doc.add_paragraph()
    p_dec_intro.paragraph_format.space_before = Pt(2)
    p_dec_intro.paragraph_format.space_after = Pt(6)
    r_di = p_dec_intro.add_run(
        "Based on the results of User Acceptance Testing (UAT) and the ISO/IEC 25010 Product Quality Evaluation, "
        "the Office of Student Affairs (OSA) hereby declares the following acceptance determination for the A.E.G.I.S. software project:"
    )
    r_di.font.name = "Arial"
    r_di.font.size = Pt(8.5)
    r_di.font.color.rgb = COLOR_DARK

    table_dec = doc.add_table(rows=4, cols=2)
    table_dec.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table_dec)

    dec_options = [
        ("[  ] ACCEPTED WITHOUT RESERVATION", "The software satisfies all functional, security, usability, and compliance requirements outlined in Chapters I–III of the capstone thesis. Recommended for institutional production rollout."),
        ("[  ] ACCEPTED WITH MINOR OBSERVATIONS", "The software meets core operational requirements and is accepted for production subject to resolving non-critical UI/ergonomic observations noted in this document."),
        ("[  ] PROVISIONAL ACCEPTANCE (RE-TESTING REQUIRED)", "The software demonstrates essential capabilities but requires technical remediation of specified defects before formal deployment sign-off."),
        ("[  ] NOT ACCEPTED", "The software fails to satisfy minimum functional, accuracy, or security thresholds specified in the project requirements.")
    ]

    for d_i, (opt_title, opt_desc) in enumerate(dec_options):
        row = table_dec.rows[d_i]
        c0, c1 = row.cells[0], row.cells[1]
        c0.width = Inches(3.0)
        c1.width = Inches(3.7)
        set_cell_margins(c0, 60, 60, 80, 80)
        set_cell_margins(c1, 60, 60, 80, 80)
        if d_i == 0:
            set_cell_background(c0, "ECFDF5") # subtle green
        elif d_i % 2 == 1:
            set_cell_background(c0, COLOR_LIGHT_BG_HEX)

        p0 = c0.paragraphs[0]
        r0 = p0.add_run(opt_title)
        r0.bold = True
        r0.font.name = "Arial"
        r0.font.size = Pt(8)
        r0.font.color.rgb = COLOR_PRIMARY if d_i == 0 else COLOR_DARK

        p1 = c1.paragraphs[0]
        r1 = p1.add_run(opt_desc)
        r1.font.name = "Arial"
        r1.font.size = Pt(8)
        r1.font.color.rgb = COLOR_MUTED

    doc.add_paragraph().paragraph_format.space_after = Pt(12)

    # Formal Signatures
    table_exec_sign = doc.add_table(rows=2, cols=2)
    table_exec_sign.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(table_exec_sign, val="none")

    # Row 0: Client & Adviser
    c_e0 = table_exec_sign.cell(0, 0)
    c_e1 = table_exec_sign.cell(0, 1)
    c_e0.width = Inches(3.3)
    c_e1.width = Inches(3.3)

    p_e0 = c_e0.paragraphs[0]
    p_e0.paragraph_format.space_before = Pt(6)
    p_e0.paragraph_format.space_after = Pt(2)
    p_e0.add_run("Accepted & Approved by:\n\n\n\n").font.size = Pt(8.5)
    r_e0 = p_e0.add_run("_________________________________________\n")
    r_e0.bold = True
    p_e0.add_run("Director / Head, Office of Student Affairs (OSA)\nCentral Luzon State University\nDate: ________________________").font.size = Pt(8)

    p_e1 = c_e1.paragraphs[0]
    p_e1.paragraph_format.space_before = Pt(6)
    p_e1.paragraph_format.space_after = Pt(2)
    p_e1.add_run("Concurred by Capstone Adviser:\n\n\n\n").font.size = Pt(8.5)
    r_e1 = p_e1.add_run("_________________________________________\n")
    r_e1.bold = True
    p_e1.add_run("Capstone Project Faculty Adviser\nDepartment of Information Technology, CLSU\nDate: ________________________").font.size = Pt(8)

    # Row 1: Researchers
    c_e2 = table_exec_sign.cell(1, 0)
    c_e3 = table_exec_sign.cell(1, 1)
    c_e2.width = Inches(3.3)
    c_e3.width = Inches(3.3)

    p_e2 = c_e2.paragraphs[0]
    p_e2.paragraph_format.space_before = Pt(16)
    p_e2.paragraph_format.space_after = Pt(2)
    p_e2.add_run("Developed & Endorsed by:\n\n\n\n").font.size = Pt(8.5)
    r_e2 = p_e2.add_run("_________________________________________\n")
    r_e2.bold = True
    p_e2.add_run("Joshua Razon / Noriel Gadiano / John Andrei Carillo II\nLead Student Researchers (BSIT 4-1)\nDate: ________________________").font.size = Pt(8)

    p_e3 = c_e3.paragraphs[0]
    p_e3.paragraph_format.space_before = Pt(16)
    p_e3.paragraph_format.space_after = Pt(2)
    p_e3.add_run("Noted by IT Department Chair:\n\n\n\n").font.size = Pt(8.5)
    r_e3 = p_e3.add_run("_________________________________________\n")
    r_e3.bold = True
    p_e3.add_run("Department Chairperson\nDepartment of Information Technology, CLSU\nDate: ________________________").font.size = Pt(8)

    out_path = os.path.abspath("docs/UAT_Test_Script_Admin_Role.docx")
    doc.save(out_path)
    print(f"Generated: {out_path} ({os.path.getsize(out_path)} bytes)")

if __name__ == "__main__":
    print("Beginning generation of role-specific UAT test scripts...")
    generate_student_uat_doc()
    generate_staff_uat_doc()
    generate_admin_uat_doc()
    print("All 3 role-specific UAT test scripts generated successfully.")
