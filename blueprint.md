# A.E.G.I.S. Project Blueprint & Architectural Roadmap

## 1. Overview & Purpose
**A.E.G.I.S.** (Academic Evaluation & Grant Integrity System) is the official scholarship management and integrity auditing portal for the Central Luzon State University (CLSU) Office of Student Affairs (OSA).

The system streamlines scholarship applications, automated grade sheet (GWA) integrity checking via AI OCR scanning, student stipend tracking, and multi-tiered admin reviews (OSA Staff & Director).

---

## 2. Implemented Features & Technical Architecture

### Core Stack
- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Frontend Architecture**: Blade Templating Engine, Vite Asset Pipeline, Bootstrap 5, FontAwesome 6, Tailwind CSS v3, Vanilla JavaScript
- **Databases**: SQLite (`database/database.sqlite` for local dev), PostgreSQL / Supabase (Staging & Production)
- **Security & Compliance**: Custom MFA (6-digit OTP), 30-Day Trusted Device Tokens (bound to UA hash), Column-Level AES Encryption at rest for student financial data, CSP & Security Headers Middleware.

### System Capabilities & Modules
1. **User Authentication & Role Management**:
   - Roles: `student`, `admin` (OSA Staff), `superadmin` (OSA Director).
   - Secure activation token invitation system (`UserInvitation`) for administrative onboarding.
   - **Dedicated Mobile Auth Views**: Touch-optimized Mobile Login & Registration views hiding heavy desktop sidebars on smartphones, featuring 16px iOS-friendly inputs, numeric keypads (`inputmode`), and sticky single-thumb login buttons.
   - **Relocated Account Logout**: Moved Logout action button into the Account Settings (`/profile/security`) page, keeping sidebars clean and focused on primary portal navigation.
2. **App-Like Mobile Navigation & PWA Shell**:
   - Fixed Bottom Navigation bar (`mobile-nav.blade.php`) for one-thumb screen switching (`d-md-none`).
   - Mobile slide-up Bottom Sheet Drawer component (`mobile-sheet.blade.php`).
   - Web App Manifest (`public/manifest.json`) supporting "Add to Home Screen" standalone app mode.
3. **Scholarship & Application Engine**:
   - Configurable grant programs, academic term management, custom program-specific application fields.
   - Student application submission with direct mobile camera capture (`capture="environment"`) for photo-scanning transcripts.
4. **AI Fraud Detection & Document Verification**:
   - Document upload verification with SHA-256 integrity checksums.
   - AI OCR GWA extractor flagging resolution anomalies, font mismatches, low confidence scores, and grade manipulation trails.
5. **Audit & Compliance System**:
   - System logging (`AuthLog`, `AdminActionLog`, `ConfigChangeLog`, `ExportAccessLog`, `EmailLog`).
   - Reporting and CSV/PDF export capability.

---

## 3. Completed Fixes & Branding Enhancements

### Summary of Completed Enhancements
1. **Base64 Inline Email Logo Embedding ([layout.blade.php](file:///f:/aegis-capstone/resources/views/emails/layout.blade.php))**:
   - Embedded CLSU logo as Base64 `data:image/png;base64,...` inside email HTML headers, guaranteeing the logo displays immediately in Gmail, Outlook, Yahoo, and mobile email clients without image proxy blocks.
2. **Relocated Logout Action ([sidebar.blade.php](file:///f:/aegis-capstone/resources/views/layouts/sidebar.blade.php), [change_password.blade.php](file:///f:/aegis-capstone/resources/views/auth/change_password.blade.php))**:
   - Removed Logout button from the navigation sidebar and created a dedicated Account Session / Sign Out card inside Account Settings (`/profile/security`).
3. **System-Wide CLSU Logo Integration ([Setting.php](file:///f:/aegis-capstone/app/Models/Setting.php))**:
   - Added centralized `Setting::getLogoUrl()` helper to dynamically deliver official CLSU seal image across all views, sidebars, mobile headers, and outbound HTML email notifications.
4. **Production Notification URLs ([CustomResetPasswordNotification.php](file:///f:/aegis-capstone/app/Notifications/CustomResetPasswordNotification.php), [render.yaml](file:///f:/aegis-capstone/render.yaml))**:
   - Configured `APP_URL: https://aegis-capstone.onrender.com` in `render.yaml`.
5. **Render 502 Bad Gateway Cold Start Fix ([start.sh](file:///f:/aegis-capstone/start.sh))**:
   - Resolved HTTP 502 Bad Gateway during Render container startup after sleep/inactivity.
