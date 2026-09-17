/**
 * A.E.G.I.S. Capstone Automated Screenshot Generator with Incognito Contexts
 */
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://127.0.0.1:8000';
const OUT_DIR = path.join(__dirname, 'thesis_figures', 'screenshots');

if (!fs.existsSync(OUT_DIR)) {
    fs.mkdirSync(OUT_DIR, { recursive: true });
}

async function capture() {
    console.log('[*] Launching browser...');
    const browser = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--window-size=1440,900']
    });

    // ──────────────────────────────────────────
    // 1. PUBLIC / LOGIN PAGE
    // ──────────────────────────────────────────
    console.log('[1/10] Capturing Login Page...');
    const context1 = await browser.createBrowserContext();
    const page1 = await context1.newPage();
    await page1.setViewport({ width: 1440, height: 900 });
    await page1.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
    await page1.screenshot({ path: path.join(OUT_DIR, 'Figure_01_Login_Page.png') });
    await context1.close();

    // ──────────────────────────────────────────
    // 2. DIRECTOR (SUPERADMIN)
    // ──────────────────────────────────────────
    console.log('[*] Starting Director session...');
    const contextDir = await browser.createBrowserContext();
    const pageDir = await contextDir.newPage();
    await pageDir.setViewport({ width: 1440, height: 900 });

    await pageDir.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
    await pageDir.type('input[name="email"]', 'director@clsu.edu.ph');
    await pageDir.type('input[name="password"]', 'password');
    await Promise.all([
        pageDir.waitForNavigation({ waitUntil: 'networkidle2' }),
        pageDir.click('button[type="submit"]')
    ]);

    // Figure 02: Scholarship Programs
    console.log('[2/10] Capturing Scholarship Programs...');
    await pageDir.goto(`${BASE_URL}/superadmin/scholarships`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 800));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_02_Scholarship_Programs.png') });

    // Figure 03: Analytics Dashboard
    console.log('[3/10] Capturing Analytics Dashboard...');
    await pageDir.goto(`${BASE_URL}/superadmin/analytics`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 2000));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_03_Analytics_Dashboard.png') });

    // Figure 04: System Settings
    console.log('[4/10] Capturing System Settings...');
    await pageDir.goto(`${BASE_URL}/superadmin/settings`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 800));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_04_System_Settings.png') });

    // Figure 06: Staff Management
    console.log('[5/10] Capturing Staff Management...');
    await pageDir.goto(`${BASE_URL}/superadmin/staff`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 800));
    await pageDir.screenshot({ path: path.join(OUT_DIR, 'Figure_06_Staff_Management.png') });

    await contextDir.close();

    // ──────────────────────────────────────────
    // 3. ADMIN SESSION
    // ──────────────────────────────────────────
    console.log('[*] Starting Admin session...');
    const contextAdmin = await browser.createBrowserContext();
    const pageAdmin = await contextAdmin.newPage();
    await pageAdmin.setViewport({ width: 1440, height: 900 });

    await pageAdmin.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
    await pageAdmin.type('input[name="email"]', 'admin@clsu.edu.ph');
    await pageAdmin.type('input[name="password"]', 'password');
    await Promise.all([
        pageAdmin.waitForNavigation({ waitUntil: 'networkidle2' }),
        pageAdmin.click('button[type="submit"]')
    ]);

    // Figure 07: Admin Applications Queue
    console.log('[6/10] Capturing Admin Dashboard & Application Queue...');
    await pageAdmin.goto(`${BASE_URL}/admin/dashboard`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 1200));
    await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_07_Admin_Application_Queue.png') });

    // Figure 09: Application Review Detail
    console.log('[7/10] Capturing Application Review Detail & AI Scan...');
    const appLinks = await pageAdmin.$$eval('a[href*="/admin/review/"]', els => els.map(e => e.href));
    if (appLinks.length > 0) {
        await pageAdmin.goto(appLinks[0], { waitUntil: 'networkidle2' });
        await new Promise(r => setTimeout(r, 1500));
        await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_09_Application_Review_Detail.png') });
    } else {
        await pageAdmin.goto(`${BASE_URL}/admin/review/1`, { waitUntil: 'networkidle2' });
        await new Promise(r => setTimeout(r, 1500));
        await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_09_Application_Review_Detail.png') });
    }

    // Figure 11: Announcements Board
    console.log('[8/10] Capturing Announcements...');
    await pageAdmin.goto(`${BASE_URL}/admin/announcements`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 800));
    await pageAdmin.screenshot({ path: path.join(OUT_DIR, 'Figure_11_Announcements.png') });

    await contextAdmin.close();

    // ──────────────────────────────────────────
    // 4. STUDENT SESSION
    // ──────────────────────────────────────────
    console.log('[*] Starting Student session...');
    const contextStudent = await browser.createBrowserContext();
    const pageStudent = await contextStudent.newPage();
    await pageStudent.setViewport({ width: 1440, height: 900 });

    await pageStudent.goto(`${BASE_URL}/login`, { waitUntil: 'networkidle2' });
    // Click quick-fill demo student if exists or type credentials
    const demoStudentBtn = await pageStudent.$('button[onclick*="demoStudent"]');
    if (demoStudentBtn) {
        await demoStudentBtn.click();
        await new Promise(r => setTimeout(r, 300));
    } else {
        await pageStudent.type('input[name="email"]', 'juandelacruz0@clsu2.edu.ph');
        await pageStudent.type('input[name="password"]', 'password');
    }
    await Promise.all([
        pageStudent.waitForNavigation({ waitUntil: 'networkidle2' }),
        pageStudent.click('button[type="submit"]')
    ]);

    // Figure 13: Student Dashboard
    console.log('[9/10] Capturing Student Dashboard...');
    await pageStudent.goto(`${BASE_URL}/student/dashboard`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 800));
    await pageStudent.screenshot({ path: path.join(OUT_DIR, 'Figure_13_Student_Dashboard.png') });

    // Figure 14: Student Apply Form
    console.log('[10/10] Capturing Student Apply Form...');
    await pageStudent.goto(`${BASE_URL}/apply`, { waitUntil: 'networkidle2' });
    await new Promise(r => setTimeout(r, 800));
    await pageStudent.screenshot({ path: path.join(OUT_DIR, 'Figure_14_Student_Apply_Form.png') });

    await contextStudent.close();

    await browser.close();
    console.log('[OK] All 10 figures captured successfully in:', OUT_DIR);
}

capture().catch(err => {
    console.error('Error during capture:', err);
    process.exit(1);
});
