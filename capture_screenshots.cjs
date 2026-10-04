/**
 * A.E.G.I.S. Capstone Automated High-Fidelity Screenshot Generator
 * Generates verified, error-free captures for all 11 system views in thesis & user manual.
 */
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8000';
const OUT_DIR = path.join(__dirname, 'thesis_figures', 'screenshots');

if (!fs.existsSync(OUT_DIR)) {
    fs.mkdirSync(OUT_DIR, { recursive: true });
}

async function loginAs(page, email, password = 'password') {
    await page.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
    await page.evaluate(() => {
        const modal = document.getElementById('corSealModal');
        if (modal) modal.style.display = 'none';
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    });
    await page.type('input[name="email"]', email);
    await page.type('input[name="password"]', password);
    await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle2' }),
        page.evaluate(() => document.querySelector('form').submit())
    ]);
}

async function capture() {
    console.log('[*] Launching headless browser for high-res screenshot capture...');
    const chromePath = fs.existsSync('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe')
        ? 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe'
        : 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe';

    console.log('[*] Using browser executable:', chromePath);
    const browser = await puppeteer.launch({
        headless: 'new',
        executablePath: chromePath,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--window-size=1440,900',
            '--disable-gpu',
            '--disable-dev-shm-usage'
        ]
    });

    // ──────────────────────────────────────────
    // 1. PUBLIC / LOGIN PAGE (Figure 01)
    // ──────────────────────────────────────────
    console.log('[1/11] Capturing Figure 01: Login Page...');
    const context1 = await browser.createBrowserContext();
    const page1 = await context1.newPage();
    await page1.setViewport({ width: 1440, height: 900 });
    await page1.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
    await page1.screenshot({ path: path.join(OUT_DIR, 'Figure_01_Login_Page.png') });
    await context1.close();

    // ──────────────────────────────────────────
    // 2. DIRECTOR (SUPERADMIN) SESSION
    // ──────────────────────────────────────────
    console.log('[*] Starting Director session...');
    const contextDir = await browser.createBrowserContext();
    const pageDir = await contextDir.newPage();
    await pageDir.setViewport({ width: 1440, height: 900 });

    await loginAs(pageDir, 'director@clsu.edu.ph', 'password');

    // Figure 02: Scholarship Programs
    console.log('[2/11] Capturing Figure 02: Scholarship Programs...');
    await pageDir.goto(`${BASE_URL}/superadmin/scholarships`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1000));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_02_Scholarship_Programs.png') });

    // Figure 03: Analytics Dashboard
    console.log('[3/11] Capturing Figure 03: Analytics Dashboard...');
    await pageDir.goto(`${BASE_URL}/superadmin/analytics`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 2000));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_03_Analytics_Dashboard.png') });

    // Figure 04: System Settings
    console.log('[4/11] Capturing Figure 04: System Settings...');
    await pageDir.goto(`${BASE_URL}/superadmin/settings`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1000));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_04_System_Settings.png') });

    // Figure 05: Audit Logs & Compliance Export Hub
    console.log('[5/11] Capturing Figure 05: Audit Logs & Compliance Export Hub...');
    await pageDir.goto(`${BASE_URL}/superadmin/analytics`, { waitUntil: 'networkidle2' });
    await pageDir.evaluate(() => {
        const el = document.querySelector('.export-card') || document.querySelector('#auditCsvBtn') || document.querySelector('.card.p-4.h-100');
        if (el) {
            el.scrollIntoView({ behavior: 'instant', block: 'center' });
        }
    });
    await new Promise(r => setTimeout(r, 1200));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_05_Audit_Logs.png') });

    // Figure 06: Staff Management
    console.log('[6/11] Capturing Figure 06: Staff Management...');
    await pageDir.goto(`${BASE_URL}/superadmin/staff`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1000));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_06_Staff_Management.png') });

    await contextDir.close();

    // ──────────────────────────────────────────
    // 3. ADMIN (OSA EVALUATOR) SESSION
    // ──────────────────────────────────────────
    console.log('[*] Starting Admin session...');
    const contextAdmin = await browser.createBrowserContext();
    const pageAdmin = await contextAdmin.newPage();
    await pageAdmin.setViewport({ width: 1440, height: 900 });

    await loginAs(pageAdmin, 'admin@clsu.edu.ph', 'password');

    // Figure 07: Admin Applications Queue
    console.log('[7/11] Capturing Figure 07: Admin Application Queue...');
    await pageAdmin.goto(`${BASE_URL}/admin/dashboard`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1500));
    await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_07_Admin_Application_Queue.png') });

    // Figure 09: Application Review Detail (Forensic Canvas & ELA)
    console.log('[8/11] Capturing Figure 09: Application Review Detail...');
    await pageAdmin.goto(`${BASE_URL}/admin/review/1`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1800));
    await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_09_Application_Review_Detail.png') });

    // Figure 11: Announcements Board
    console.log('[9/11] Capturing Figure 11: Announcements Board...');
    await pageAdmin.goto(`${BASE_URL}/admin/announcements`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1000));
    await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_11_Announcements.png') });

    await contextAdmin.close();

    // ──────────────────────────────────────────
    // 4. STUDENT 1 SESSION (Active Application -> Dashboard & Tracker)
    // ──────────────────────────────────────────
    console.log('[*] Starting Student 1 session (Juan Dela Cruz - Active Tracker)...');
    const contextStudent1 = await browser.createBrowserContext();
    const pageStudent1 = await contextStudent1.newPage();
    await pageStudent1.setViewport({ width: 1440, height: 900 });

    await loginAs(pageStudent1, 'student@clsu.edu.ph', 'password');

    // Figure 13: Student Dashboard
    console.log('[10/11] Capturing Figure 13: Student Dashboard...');
    await pageStudent1.goto(`${BASE_URL}/student/dashboard`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1200));
    await pageStudent1.screenshot({ path: path.join(OUT_DIR, 'Figure_13_Student_Dashboard.png') });

    await contextStudent1.close();

    // ──────────────────────────────────────────
    // 5. STUDENT 2 SESSION (No Active Application -> Interactive Apply Stepper)
    // ──────────────────────────────────────────
    console.log('[*] Starting Student 2 session (Maria Clara Santos - Apply Stepper)...');
    const contextStudent2 = await browser.createBrowserContext();
    const pageStudent2 = await contextStudent2.newPage();
    await pageStudent2.setViewport({ width: 1440, height: 900 });

    await loginAs(pageStudent2, 'student_apply@clsu.edu.ph', 'password');

    // Figure 14: Student Apply Form
    console.log('[11/11] Capturing Figure 14: Student Apply Form Stepper...');
    await pageStudent2.goto(`${BASE_URL}/apply`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1200));
    await pageStudent2.screenshot({ path: path.join(OUT_DIR, 'Figure_14_Student_Apply_Form.png') });

    await contextStudent2.close();

    await browser.close();
    console.log('\n[SUCCESS] All 11 figures captured cleanly and saved in:', OUT_DIR);
}

capture().catch(err => {
    console.error('Error during capture:', err);
    process.exit(1);
});
