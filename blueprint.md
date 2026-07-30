# A.E.G.I.S. Project Blueprint & Architectural Roadmap

## 1. Overview & Purpose
**A.E.G.I.S.** (Academic Evaluation & Grant Integrity System) is the official scholarship management and integrity auditing portal for the Central Luzon State University (CLSU) Office of Student Affairs (OSA).

The system streamlines scholarship applications, automated grade sheet (GWA) integrity checking via AI OCR scanning, student stipend tracking, and multi-tiered admin reviews (OSA Staff & Director).

---

## 2. Implemented Features & Technical Architecture

### Core Stack & Performance Pipeline
- **Backend Framework**: Laravel 12 (PHP 8.4+ with Bytecode OPcache)
- **Notification Routing Engine**: Real-time DB Notifications with automated target URL redirects upon click (`markAsRead(id, url)`).
- **Response Compression**: `GzipResponse` Middleware (80-85% payload size reduction, 1-Year Immutable Browser Asset Caching)
- **Frontend Architecture**: Blade Templating Engine, Vite Asset Pipeline, Bootstrap 5, FontAwesome 6, Tailwind CSS v3, Vanilla JavaScript
- **Databases**: SQLite (`database/database.sqlite` for local dev), PostgreSQL / Supabase (Staging & Production)
- **Security & Compliance**: Custom MFA (6-digit OTP), 30-Day Trusted Device Tokens (bound to UA hash), Column-Level AES Encryption at rest for student financial data, CSP & Security Headers Middleware.

---

## 3. Completed Fixes & Branding Enhancements

### Summary of Recent Enhancements
1. **Notification System Overhaul ([AuthController.php](file:///f:/aegis-capstone/app/Http/Controllers/AuthController.php), [app.blade.php](file:///f:/aegis-capstone/resources/views/layouts/app.blade.php))**:
   - Added target `url` routing to `NewAnnouncementNotification`, `ApplicationStatusNotification`, and `NewApplicationNotification`.
   - Updated dropdown click handler so clicking a notification marks it as read and **immediately navigates** the user to the target page (e.g. `/student/dashboard` or `/admin/review/{id}`).
2. **PWA Service Worker & Offline Support ([sw.js](file:///f:/aegis-capstone/public/sw.js))**:
   - Built custom PWA Service Worker with Network-First HTML caching and Offline Status Banner.
3. **Database Eager Loading Optimization ([AdminController.php](file:///f:/aegis-capstone/app/Http/Controllers/AdminController.php))**:
   - Eager loaded `scholarship` and `customFields` to eliminate N+1 queries.
4. **Student Application Draft Resilience ([apply.blade.php](file:///f:/aegis-capstone/resources/views/student/apply.blade.php))**:
   - Auto-saves form input values to `localStorage` and restores them if browser or connection drops.
