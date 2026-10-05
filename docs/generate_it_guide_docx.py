import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

def create_it_guide():
    doc = docx.Document()
    for s in doc.sections:
        s.top_margin = Inches(0.8)
        s.bottom_margin = Inches(0.8)
        s.left_margin = Inches(0.8)
        s.right_margin = Inches(0.8)

    # Header
    p_inst = doc.add_paragraph()
    p_inst.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r1 = p_inst.add_run('CENTRAL LUZON STATE UNIVERSITY\n')
    r1.font.name = 'Arial'
    r1.font.size = Pt(11)
    r1.font.bold = True
    r1.font.color.rgb = RGBColor(15, 61, 35)

    r2 = p_inst.add_run('College of Engineering • Department of Information Technology\nScience City of Muñoz, Nueva Ecija, Philippines\n')
    r2.font.name = 'Arial'
    r2.font.size = Pt(9.5)
    r2.font.color.rgb = RGBColor(80, 80, 80)

    p_title = doc.add_paragraph()
    p_title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_t = p_title.add_run('A.E.G.I.S. IT EXPERT TECHNICAL EVALUATION & INSTRUCTIONAL MANUAL\n')
    r_t.font.name = 'Arial'
    r_t.font.size = Pt(13)
    r_t.font.bold = True
    r_t.font.color.rgb = RGBColor(15, 61, 35)

    r_sub = p_title.add_run('Comprehensive Technical Reference for Systems Architects, Security Auditors & Faculty Evaluators\nDocument Code: CLSU-CEn-DIT-AEGIS-GUIDE-IT-2026 • ISO/IEC 25010:2023 Standard')
    r_sub.font.name = 'Arial'
    r_sub.font.size = Pt(9)
    r_sub.font.italic = True
    r_sub.font.color.rgb = RGBColor(100, 100, 100)

    doc.add_paragraph('_________________________________________________________________________________')

    # Section 1
    doc.add_heading('1. Executive Technical Overview', level=2)
    doc.add_paragraph(
        'A.E.G.I.S. (AI-Enhanced Grant Information System) is an enterprise scholarship governance platform '
        'engineered for the Central Luzon State University (CLSU) Office of Student Affairs (OSA). It transitions traditional, '
        'manual paper scholarship intake into an automated, tamper-evident digital ecosystem.'
    )
    p_spec = doc.add_paragraph()
    p_spec.add_run('• Web & Backend Tier: ').font.bold = True
    p_spec.add_run('Laravel 12.x running on PHP 8.2+ with Blade templating, Vanilla CSS, Tailwind CSS, and Bootstrap 5.\n')
    p_spec.add_run('• Forensic AI Microservice: ').font.bold = True
    p_spec.add_run('Python 3.11 Flask API running dual-pipeline analysis: Error Level Analysis (ELA 95%), ResNet-50 CNN classification, SIFT keypoint clone-stamp matching, and Grad-CAM localized heatmaps.\n')
    p_spec.add_run('• Database & Storage Tier: ').font.bold = True
    p_spec.add_run('SQLite (Dev/Staging) / PostgreSQL & MySQL 8.0 (Prod) with AES-256-CBC column-level encryption and Cloudflare R2 / S3 persistent storage with Base64 DB backup.\n')
    p_spec.add_run('• Statutory Controls: ').font.bold = True
    p_spec.add_run('R.A. 10173 (Data Privacy Act of 2012) compliant, SHA-256 zero-knowledge OTP hashing at rest, immutable action logging, role-based authorization gates.')

    # Section 2
    doc.add_heading('2. System Access & Endpoints Directory', level=2)
    t_end = doc.add_table(rows=6, cols=3)
    t_end.alignment = WD_TABLE_ALIGNMENT.CENTER
    end_headers = ['Environment / Route', 'Access URL / Endpoint', 'Technical Purpose']
    for i, h in enumerate(end_headers):
        cell = t_end.cell(0, i)
        cell.text = h
        cell.paragraphs[0].runs[0].font.bold = True
        shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="0F3D23"/>')
        cell._tc.get_or_add_tcPr().append(shading)
        cell.paragraphs[0].runs[0].font.color.rgb = RGBColor(255, 255, 255)

    end_data = [
        ('Live Cloud Staging (Render)', 'https://aegis-capstone.onrender.com', 'Primary live evaluation instance with full features active.'),
        ('Local Development Instance', 'http://localhost:8000', 'Local fallback environment for offline/LAN evaluation.'),
        ('System Health Check API', 'https://aegis-capstone.onrender.com/api/health-check', 'JSON endpoint reporting DB latency, queue state, and memory.'),
        ('Public Scholarship Catalog', 'https://aegis-capstone.onrender.com/scholarships', 'Public scholarship discovery without authentication requirement.'),
        ('Application QR Verification', 'https://aegis-capstone.onrender.com/verify/application/{code}', 'Validates tamper-evident cryptographic QR clearance codes.'),
    ]
    for r_idx, row in enumerate(end_data, start=1):
        for c_idx, val in enumerate(row):
            t_end.cell(r_idx, c_idx).text = val

    # Section 3
    doc.add_heading('3. Complete Test Accounts & Credentials Directory', level=2)
    t_acc = doc.add_table(rows=6, cols=4)
    t_acc.alignment = WD_TABLE_ALIGNMENT.CENTER
    acc_headers = ['Role / Identity', 'Email Address', 'Password', 'MFA Security & Evaluation Behavior']
    for i, h in enumerate(acc_headers):
        cell = t_acc.cell(0, i)
        cell.text = h
        cell.paragraphs[0].runs[0].font.bold = True
        shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="0F3D23"/>')
        cell._tc.get_or_add_tcPr().append(shading)
        cell.paragraphs[0].runs[0].font.color.rgb = RGBColor(255, 255, 255)

    acc_data = [
        ('SuperAdmin / OSA Director', 'director@clsu.edu.ph', 'password', 'Auto-Bypassed (Instant login). Full governance, term switch, audit logs.'),
        ('Master Administrator', 'gadianoriel07@gmail.com', 'password', 'Auto-Bypassed. Master gateway, system maintenance, security controls.'),
        ('OSA Staff / Administrator', 'admin@clsu.edu.ph', 'password', 'Auto-Bypassed. Intake review queue, AI inspection, decision overrides.'),
        ('Student 1 (Active History)', 'student@clsu.edu.ph', 'password', 'Demo OTP: 123456 or 000000. Juan Dela Cruz (ID: 22-1234). Status tracker.'),
        ('Student 2 (Fresh /apply)', 'student_apply@clsu.edu.ph', 'password', 'Demo OTP: 123456 or 000000. Maria Clara Santos (ID: 23-5678). Live submission.'),
    ]
    for r_idx, row in enumerate(acc_data, start=1):
        for c_idx, val in enumerate(row):
            t_acc.cell(r_idx, c_idx).text = val

    # Section 4
    doc.add_heading('4. Test Artifacts & Forensic Documents Repository', level=2)
    doc.add_paragraph(
        'Evaluators have multiple convenient ways to obtain the ground truth Certificate of Grades (COG) test fixtures for evaluating '
        'the AI Document Forensics module (TC-IT-05) and testing live student submission (TC-STU-02):'
    )
    p_inapp = doc.add_paragraph()
    p_inapp.add_run('• In-App Review Screen Toolbar Dropdown (Recommended): ').bold = True
    p_inapp.add_run(
        'When logged in as Administrator (admin@clsu.edu.ph), open any review screen (/admin/review/{id}). '
        'In the top header toolbar beside "View Form" and "Form PDF", click the "Test COG Fixtures" dropdown button '
        'to download either "Authentic COG (GWA 2.75)" or "Tampered COG (Edited GWA 1.00)" in 1 click.'
    )
    p_direct = doc.add_paragraph()
    p_direct.add_run('• Direct Web Access & Local Paths: ').bold = True
    p_direct.add_run('Evaluators may also download the assets directly from the web or inspect local repository files:')

    t_doc = doc.add_table(rows=3, cols=3)
    t_doc.alignment = WD_TABLE_ALIGNMENT.CENTER
    doc_headers = ['Document Fixture', 'Download URL & Local Repository Path', 'Forensic Characteristics & Expected AI Verdict']
    for i, h in enumerate(doc_headers):
        cell = t_doc.cell(0, i)
        cell.text = h
        cell.paragraphs[0].runs[0].font.bold = True
        shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="0F3D23"/>')
        cell._tc.get_or_add_tcPr().append(shading)
        cell.paragraphs[0].runs[0].font.color.rgb = RGBColor(255, 255, 255)

    doc_data = [
        ('Authentic CLSU COG (GWA 2.75)',
         'https://aegis-capstone.onrender.com/samples/authentic_clsu_cog.jpg\n(Local: public/samples/authentic_clsu_cog.jpg)',
         'Genuine CLSU Certificate of Grades with GWA 2.75. Uniform pixel noise floor, unaltered grade blocks, consistent DCT quantization. Expected: Authentic (< 35% tampering probability), Low Risk.'),
        ('Tampered CLSU COG (Edited GWA 1.00)',
         'https://aegis-capstone.onrender.com/samples/tampered_clsu_cog.jpg\n(Local: public/samples/tampered_clsu_cog.jpg)',
         'Digitally spliced grade modification altered from 2.75 to 1.00 (Latin Honors forgery). High-frequency ELA compression boundaries and copy-move artifacts. Expected: High Tampering Risk (> 70%), High Risk.'),
    ]
    for r_idx, row in enumerate(doc_data, start=1):
        for c_idx, val in enumerate(row):
            t_doc.cell(r_idx, c_idx).text = val

    # Section 5
    doc.add_heading('5. Step-by-Step Technical Verification Procedures (TC-IT-01 to TC-IT-10)', level=2)
    protocols = [
        ('TC-IT-01: Authentication Hardening, SQL Injection & Brute Force Throttling',
         '• Route: POST /login\n'
         '• Injection Test: Submit email "\' OR 1=1--" with any password. Verify Eloquent PDO binding neutralizes injection without leaking database errors.\n'
         '• Throttling Test: Submit 6 consecutive wrong passwords within 60 seconds. Verify rate-limiter returns HTTP 429 Too Many Requests.\n'
         '• Password Hashing: Inspect users table in database; verify Bcrypt cost=12 hashing.'),

        ('TC-IT-02: Multi-Factor Authentication & Zero-Knowledge OTP Storage',
         '• Route: POST /login -> POST /mfa/verify\n'
         '• Test: Sign in as student@clsu.edu.ph / password. Observe redirection to /login/mfa.\n'
         '• Database Inspection: Inspect otp_code column in users table. Verify it is stored strictly as a 64-char SHA-256 hash. Plaintext OTP is NEVER stored at rest.\n'
         '• Dynamic TTL: Verify 10-minute dynamic countdown (10:00). Input demo code 123456 or 000000 to authenticate.'),

        ('TC-IT-03: AES-256 Column Encryption at Rest',
         '• Model: App\\Models\\StudentProfile\n'
         '• Inspection: Query raw database records: SELECT clsu_id_number, contact_number FROM student_profiles.\n'
         '• Verification: Values are stored as AES-256-CBC ciphertexts starting with base64 JSON payload (eyJpdiI6...). Values are decrypted in-memory only during authenticated sessions.'),

        ('TC-IT-04: Role-Based Access Control (RBAC) & Boundary Isolation',
         '• Account: student@clsu.edu.ph / password\n'
         '• Test: As Student, manually navigate to /admin/dashboard, /superadmin/scholarships, /superadmin/users, /superadmin/audit-logs, /superadmin/settings.\n'
         '• Expected: CheckRole middleware strictly intercepts every request; returns HTTP 403 Forbidden or safe redirection without DOM state leakage.'),

        ('TC-IT-05: AI Forensic ELA-CNN Pipeline & Grad-CAM Explainability',
         '• Route: GET /admin/review/{id}\n'
         '• Account: admin@clsu.edu.ph / password\n'
         '• Fixture Shortcut: Click "Test COG Fixtures" dropdown in the review toolbar to download Authentic (2.75) or Tampered (1.00) test COGs.\n'
         '• Inspection: Open application review screen. Verify Fraud Probability Score (0-100%), risk tier badge (Low, Moderate, High), ELA difference map, and Grad-CAM localized heatmap overlay.\n'
         '• Autonomy: Verify human decision override: Admin can approve or reject with mandatory justification logging.'),

        ('TC-IT-06: Tamper-Evident Audit Logging & Statutory Non-Repudiation',
         '• Route: GET /superadmin/audit-logs\n'
         '• Account: director@clsu.edu.ph / password\n'
         '• Inspection: Trigger an administrative action (e.g. approve an application, modify active semester). Navigate to audit logs.\n'
         '• Verification: Verify structured log entries containing Actor ID, Email, IP address, User-Agent hash, timestamp, and JSON before/after state diffs.'),

        ('TC-IT-07: Transport Security & Session Hardening',
         '• Inspection: Open Browser DevTools (F12) > Network tab on any authenticated page.\n'
         '• Headers: Verify Strict-Transport-Security, X-Frame-Options: SAMEORIGIN, X-Content-Type-Options: nosniff, and CSP directives.\n'
         '• Cookies: Verify session cookies are flagged HttpOnly, Secure, and SameSite=Lax.'),

        ('TC-IT-08: High-Fidelity 1-Page PDF & Isolated Iframe Print Engine',
         '• Route: In /admin/review/{id}, click "Generate Evaluation Sheet" or "Print Preview".\n'
         '• Verification: Verify 1-page letter layout without clipping; includes CLSU seal, verification checklist, and cryptographic QR code.\n'
         '• QR Verification: Scan or open /verify/application/{code}; verify official verification clearance page.'),

        ('TC-IT-09: Asynchronous Queuing & Fault Recovery',
         '• Verification: Document uploads and heavy forensic inference are dispatched to database queue workers (php artisan queue:work).\n'
         '• Fault Tolerance: Exponential retry backoffs ([15s, 45s, 90s, 180s, 360s]) absorb AI microservice cold-start connection timeouts.'),

        ('TC-IT-10: Database Portability & Asset Optimization',
         '• Verification: Database migrations execute identically on SQLite and PostgreSQL.\n'
         '• Eager Loading: Zero N+1 query overhead via Eloquent with(...) eager loading.\n'
         '• Bundling: Production Vite compiled bundles in /public/build/assets/ achieve >75% size reduction under Gzip compression.')
    ]
    for title, body in protocols:
        p = doc.add_paragraph()
        r = p.add_run(f'{title}\n')
        r.font.bold = True
        r.font.size = Pt(10.5)
        p.add_run(body)

    # Section 6
    doc.add_heading('6. Database Inspection SQL Reference', level=2)
    p_sql = doc.add_paragraph()
    p_sql.add_run('Evaluators inspecting the backend database directly may use the following SQL queries:\n\n')
    p_sql.add_run(
        '-- 1. Inspect AES-256 Column Encryption (Raw Ciphertext)\n'
        'SELECT id, user_id, clsu_id_number, contact_number, emergency_contact_number FROM student_profiles LIMIT 2;\n\n'
        '-- 2. Inspect SHA-256 Zero-Knowledge OTP Storage (64-char Hash)\n'
        'SELECT id, name, email, otp_code, otp_expires_at FROM users WHERE role = \'student\';\n\n'
        '-- 3. Inspect Audit Logs (Actor, IP, User-Agent, JSON Diff)\n'
        'SELECT id, user_id, action, ip_address, user_agent, created_at FROM admin_action_logs ORDER BY id DESC LIMIT 5;\n\n'
        '-- 4. Inspect Academic Term State (Active Term Governance)\n'
        'SELECT id, semester, academic_year, is_active FROM academic_terms ORDER BY is_active DESC;'
    )
    p_sql.runs[1].font.name = 'Consolas'
    p_sql.runs[1].font.size = Pt(8.5)

    # Section 7
    doc.add_heading('7. ISO/IEC 25010:2023 Product Quality Rating Guide', level=2)
    doc.add_paragraph(
        'Please score the 24 technical statements in Part 2 of the evaluation form across all eight (8) standard ISO/IEC 25010 quality characteristics:\n'
        '• 1. Functional Suitability (Completeness, Calculation Correctness, Technical Appropriateness)\n'
        '• 2. Performance Efficiency (Time Behavior <1.5s, Resource Utilization, Concurrent Queue Capacity)\n'
        '• 3. Compatibility (Multi-Container Co-existence, Brevo SMTP API, Cloudflare R2 Interoperability)\n'
        '• 4. Usability (Interface Consistency, Input Validation, Accessibility, Guided Tour)\n'
        '• 5. Reliability (Fault Tolerance, Recoverability, Regression Stability)\n'
        '• 6. Security (AES-256 Encryption at Rest, SHA-256 OTP Hashing, RBAC Isolation, Non-Repudiation)\n'
        '• 7. Maintainability (Modularity, Service Layer Abstraction, Automated Test Coverage with 36 Assertions)\n'
        '• 8. Portability (Cross-Browser Adaptability across Chrome, Edge, Safari, Firefox, and Cloud Containerization)\n\n'
        'Rating Scale: 5 = Strongly Agree, 4 = Agree, 3 = Neither Agree nor Disagree, 2 = Disagree, 1 = Strongly Disagree, N/A = Not Applicable.'
    )

    # Section 8
    doc.add_heading('8. Acceptance Endorsement & Sign-Off Instructions', level=2)
    doc.add_paragraph(
        'Upon completing the technical test scenarios and rating the ISO 25010 questionnaire:\n'
        '1. Mark your overall system acceptance decision in Part 3 of the form (Accepted, Accepted with Minor Revisions, or For Revision).\n'
        '2. Record any technical commendations, observations, or engineering recommendations.\n'
        '3. Affix your physical or digital signature, designation, and date on Page 5 of the evaluation document.'
    )

    target_path = 'docs/AEGIS_IT_Expert_Evaluation_Instructional_Guide.docx'
    alt_path = 'docs/AEGIS_IT_Expert_Evaluation_Instructional_Guide_Updated.docx'
    try:
        doc.save(target_path)
        print(f'Successfully generated complete {target_path}')
    except PermissionError:
        doc.save(alt_path)
        print(f'Note: {target_path} is currently open in Word. Successfully generated updated copy at {alt_path}')

if __name__ == '__main__':
    create_it_guide()
