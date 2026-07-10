---
name: aegis-architecture
description: Guide to the A.E.G.I.S. Capstone project architecture, database models, security mechanisms, UAT workflows, and coding conventions.
---

# A.E.G.I.S. Architecture & Development Skill

Welcome to **A.E.G.I.S.** (Academic Evaluation & Grant Integrity System), the official Scholarship Management Portal for the Central Luzon State University (CLSU) Office of Student Affairs (OSA). This document provides an architectural mapping, database reference, and key engineering conventions to guide your development.

---

## 🏛️ System Overview

A.E.G.I.S. automates the submission, verification, and evaluation of student scholarship applications. It utilizes AI document scanning to check transcripts/grades (GWA) and flags anomalies (discrepancies, edit trails, resolution issues) to prevent fraud.

### Tech Stack:
* **Backend**: Laravel 12 / PHP 8.2+
* **Frontend**: HTML5, Vanilla JavaScript, Bootstrap 5, FontAwesome 6, and Tailwind CSS v3 (compiled via Vite)
* **Local Database**: SQLite (`database/database.sqlite`)
* **Staging/Production Database**: PostgreSQL (Supabase)

---

## 🗄️ Database Schema & Entities

A.E.G.I.S. uses a structured relation schema. Key models include:

### 1. User & Profiles
* `User`: System accounts with roles (`student`, `admin`, `superadmin`). `admin` represents OSA Staff, and `superadmin` represents the OSA Director.
* `StudentProfile`: Maps to a `User` (role student). Contains `clsu_id_number`, `college`, `course`, `year_level`, and **encrypted bank details** (e.g., account number, bank name).
* `UserInvitation`: Secure activation token system for OSA Staff members, enforcing expiration limits and single-use registration.

### 2. Academic Terms & Scholarships
* `AcademicTerm`: Semester and academic year definitions. Exactly one term is marked `is_active = true` at any time.
* `Scholarship`: Defines grant programs (e.g., DOST, CHED, University Scholar). Configurable with `min_gwa_required`, `status` (Active/Inactive), `max_renewals`, and `stipend_amount`.
* `ScholarshipField` / `ApplicationField`: Supports custom program-specific application inputs (e.g., uploading a "Certificate of Indigency" or checking "Solo Parent Status").

### 3. Applications & Integrity Scans
* `Application`: Tracks student submissions. Statuses: `pending`, `under_review`, `approved`, `rejected`, `cancelled`.
* `Document`: Files uploaded by students (e.g., Grade Sheets, Tax Returns). Stores file path, file size, mime type, document type, SHA-256 checksums, and verification event logs.
* `AIResult`: Results from the AI grade extractor. Stores GWA extraction confidence, extraction accuracy, verification matches, and flags for **anomaly indicators** (such as low-res blur, font discrepancies, high edit probability).

### 4. Global Settings
* `Setting`: Key-value configuration storage. Controls global options:
  * `mfa_enforcement`: MFA levels (`all`, `students`, `none`).
  * `ai_fraud_threshold`: Minimum confidence score before flagging fraud risk.
  * `gwa_discrepancy_tolerance`: GWA mathematical mismatch tolerance (default `0.01`).

### 5. Compliance & Logging
* `AuthLog`: Captures authentication events (`login_success`, `login_failed`, `mfa_verified`, `device_trusted`).
* `AdminActionLog`: Tracks administrative changes (status reviews, data updates).
* `ConfigChangeLog`: Records global settings changes (e.g., changing AI thresholds).
* `ExportAccessLog`: Audits CSV/PDF export operations for student records.
* `EmailLog`: Logs Brevo SMTP delivery statuses and error descriptions to debug mail drops.

---

## 🔒 Security Architectures & Workflows

A.E.G.I.S. maintains strict compliance and security controls:

### 1. Multi-Factor Authentication (MFA)
* When logging in, users receive a 6-digit OTP code in their email (expires after 10 minutes).
* **Bypass cases**:
  1. MFA is disabled globally in settings.
  2. The user has a valid 30-day remembered device cookie.
  3. Pre-seeded dummy accounts (`admin@clsu.edu.ph` and `director@clsu.edu.ph`) bypass MFA in testing environments.

### 2. Trusted Devices (MFA Bypass)
* Checking "Remember this device" writes a secure 30-day token.
* Tokens are bound to the client's `User-Agent` hash. Changing browsers or devices invalidates the bypass.

### 3. Data Encryption at Rest
* Sensitive columns in `student_profiles` (like bank details, contact numbers) are stored encrypted.
* Accessors and mutators in the `StudentProfile` model handle automatic decryption on retrieval using Laravel's encryption keys.

### 4. Clickjacking & Cloud IDE Support
* The `SecurityHeaders` middleware injects security policy headers (HSTS, CSP, X-Frame-Options).
* In local development or UAT/staging, the middleware **automatically relaxes** `X-Frame-Options` and CSP `frame-ancestors` (allows `*`) to permit previews inside Cloud IDE frames (like Google IDX). In production, it enforces strict `SAMEORIGIN` locks.

---

## 🛠️ UAT & Deployment Guidelines

When deploying updates to Render/Supabase, follow these guidelines to prevent environment breakage:

### 1. Database Portability
* Do not write MySQL-specific raw queries (e.g. `SET FOREIGN_KEY_CHECKS=0`). Since production uses PostgreSQL (Supabase) and local uses SQLite, all database actions must be database-engine agnostic.

### 2. Seeder Order
* When seeding UAT database resets, tables must be cleared in child-to-parent dependency order (e.g., delete `AIResult`, `Document`, `StatusLog`, `StudentProfile` before deleting `User` or `Application`).

### 3. Vite Assets Compile
* Because compiled assets inside `/public/build` are gitignored by default, and some production build containers lack node/npm packages, compile the assets locally using `npm run build` and check the compiled `public/build` assets into Git before pushing to Render.

### 4. Email logs
* Always log Brevo SMTP outcomes. If an email fails, update `EmailLog` with `status = failed` and the exception message so that admins can easily troubleshoot connection drops.

---

## 📂 Project Directory Structure Reference

For a complete breakdown of all files, folders, and controllers in the project, see the [A.E.G.I.S. Directory Structure Reference](file:///E:/aegis-capstone/.agents/skills/aegis-architecture/references/directory_structure.md).

