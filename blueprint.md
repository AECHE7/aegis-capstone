# A.E.G.I.S. Capstone Blueprint

This document serves as the single source of truth for the A.E.G.I.S. Capstone project, detailing its purpose, system architecture, implemented design system, current capabilities, and development roadmap.

---

## 1. Project Purpose & Core Capabilities

A.E.G.I.S. (Academic Evaluation & Grade Integrity System) is a full-stack Laravel web application integrated with a deep learning python microservice. It is designed to modernize and secure the scholarship application process for Central Luzon State University (CLSU).

### Core Features
- **Normalized Scholarship Management:** Super Admins can configure scholarships, establish minimum General Weighted Average (GWA) rules, and track real-time analytics.
- **Secure Student Applications:** Students can upload digital Certificates of Grades (COGs) and submit application forms.
- **Deep Learning Forgery Detection:** Integrated python Flask service processes uploaded documents through:
  - **Error Level Analysis (ELA):** Detects digital tampering by identifying compression mismatch anomalies.
  - **ResNet-50 Classifier:** Evaluates ELA outputs to estimate fraud probabilities.
  - **Grad-CAM Heatmaps:** Generates visual heatmaps highlighting exactly where the academic records were edited.
- **Audit Trails & Security:** Ensures all admin evaluations (Approve/Reject) log the specific evaluator, status timestamps, and remarks.

---

## 2. Technical System Architecture

```mermaid
graph TB
    subgraph Laravel Application (Port 8000)
        web[web.php Router] --> auth[Auth Middleware]
        auth --> student[Student Portal: ApplicationController]
        auth --> admin[Admin Portal: AdminController]
        auth --> super[SuperAdmin Portal: SuperAdminController]
        db[(SQLite Database)] <--> LaravelModels[Eloquent Models]
    end

    subgraph Deep Learning Microservice (Port 5000)
        flask[app.py Flask API] --> ela[ELA Preprocessing]
        ela --> cnn[ResNet-50 Model]
        cnn --> gradcam[Grad-CAM Visual Generator]
    end

    admin -->|Synchronous HTTP Post| flask
    flask -->|JSON Results + Heatmap Path| admin
```

---

## 3. Design System & Styling Rules

The front-end is built using **Blade Templating**, **Tailwind CSS**, and **Alpine.js**.

- **Typography:** Uses Google Fonts (Inter / Outfit) for expressiveness and readability.
- **Color Palette:** Curated HSL colors tailored to professional dark modes and dynamic status grids.
- **Shadows & Depth:** Multi-layered drop shadows are applied to layout cards to give them a premium, "lifted" appearance.
- **Visual Micro-animations:** Subtle hover-states on primary/secondary buttons and dashboard top-cards.

---

## 4. Current Implementation Status

All core MVC components are fully operational:
- **Models:** Normalized schemas for `User`, `StudentProfile`, `Scholarship`, `Application`, `Document`, `AIResult`, and `StatusLog`.
- **Database:** Seeded with 30 applications spanning various status flows (Pending, Approved, Rejected) and fraud scores.
- **Controllers & Routing:** Complete authorization structure for Student, Admin, and SuperAdmin roles.
- **Vite compilation:** Frontend build configuration compiled to production assets under `/public/build`.

---

## 5. Optimal Roadmap & Execution Plan

To transition the project from its current MVP setup to a robust, production-ready system, we outline the following sprints:

### Phase 1: Microservice Model Initialization (Immediate)
- **Goal:** Enable the AI analysis backend.
- **Steps:**
  1. Initialize the Python environment in the `aegis-ai` directory.
  2. Execute the `train_model.py` script to generate a synthetic dataset of authentic and tampered Certificates of Grades (COGs), train the ResNet-50 network, and save the binary model as `aegis_resnet50_v1.keras`.
  3. Start the Flask service (`python app.py`) to handle incoming HTTP scan requests.

### Phase 2: Asynchronous Scan Pipeline (Optimal Architecture Choice)
- **Goal:** Prevent the Laravel request loop from blocking while waiting for deep learning inference.
- **Steps:**
  1. Refactor `AdminController@runScan` to dispatch a Laravel queued job (`ScanDocumentJob`).
  2. Configure a local queue worker (e.g. SQLite database driver or Redis).
  3. Implement Alpine.js polling or Laravel Echo (websockets) in the Admin Review interface to dynamically update the UI once the scan results are persisted in `a_i_results`.

### Phase 3: Testing & Code Cleanup
- **Goal:** Ensure code stability and remove old boilerplate code.
- **Steps:**
  1. Remove deprecated default Laravel Breeze tests (`tests/Feature/Auth/*` and `tests/Feature/ProfileTest.php`) since auth is handled by the custom `AuthController`.
  2. Write feature tests covering roles-based dashboard access, scholarship GWA boundary checks, CSV export formats, and the status update audit trail.

### Phase 4: Notifications & Filtered Reporting (Current)
- **Goal:** Connect the email notification loop and implement full filtering for CSV and PDF compliance exports.
- **Steps:**
  1. Modify `AdminController@updateStatus` to dispatch the `ApplicationStatusMail` template.
  2. Create full filtering inputs (Search, Scholarship, Status, Year) on the Admin Dashboard queue.
  3. Wire the CSV and PDF export routes to `ReportController` and apply query parameters to support compliance-ready, filtered outputs.
  4. Design a summary statistics section in the PDF output with CLSU letterheads and signature blocks.

### Phase 5: Client Enhancements (Privacy & UAT Integration) - [COMPLETED]
- **Goal:** Secure student data and build integrated UAT evaluation panels for ISO/IEC 25010 compliance.
- **Steps:**
  1. Apply AES-256 Eloquent database casts to `StudentProfile` attributes.
  2. Create UAT feedback table migrations, models, controllers, and routes.
  3. Inject the floating evaluation modal into the master application layout.
  4. Build the UAT ratings summary module on the Super Admin Analytics dashboard.

#### Phase 6: Thesis Alignment & Security Hardening - [COMPLETED]
- **Goal:** Resolve remaining gaps between current codebase and the capstone thesis specifications.
- **Steps:**
  1. **Schema Normalization:** Add `academic_terms` and `email_logs` tables and models. Add audit columns to `status_logs` and `document_type` to `documents`.
  2. **Audit & Log Generation:** Track all status changes in `status_logs` and notifications in `email_logs` in real time.
  3. **SHA-256 UUID Upload Renaming:** Enforce anonymous file renaming on upload to protect student identity at rest.
  4. **Forensics Classification Thresholds:** Align classification score boundaries (`p = 0.50` or 50% threshold) and risk-tier badge calculations with Chapter III diagrams.
  5. **Vertical Timeline UI:** Render dynamic status log transitions on the student dashboard.

### Phase 7: Comprehensive Gap Analysis & UAT Mock Asset Verification - [COMPLETED]
- **Goal:** Analyze the capstone thesis manuscript vs. the codebase, create technical handover documentation, and fix broken image links in seeded UAT data.
- **Steps:**
  1. **Auditing:** Conduct a detailed review comparing Chapter I-III claims to codebase functionality (completed).
  2. **Technical Handover:** Produce a System Administration Manual (`system_admin_manual.md`) outlining hosting, process supervision (Supervisor), queue execution, and model retraining (completed).
  3. **Mock Asset Seeding:** Patch `UatSeeder.php` to copy mock image files automatically, eliminating 404 broken images on the admin evaluation review dashboard (completed).
  4. **Verification:** Validate stability with a complete PHPUnit test run (completed).

### Phase 8: Form Submission Stalling Fix - [COMPLETED]
- **Goal:** Resolve front-end submit button lock preventing student application submission.
- **Steps:**
  1. **Identify Bug:** Located synchronous disabling of the submit button (`btn.disabled = true`), which cancels form submission (completed).
  2. **Remediation:** Introduce a micro-delay (`setTimeout`) in `showLoading()` to allow the native form submission event to fire before the button is disabled (completed).
  3. **Verification:** Verify with feature test suites (completed).

### Phase 9: AI Pipeline Script Alignment - [COMPLETED]
- **Goal:** Align the Python retraining script `train_model.py` with the 70/15/15 split and data augmentation specifications detailed in Chapter III.
- **Steps:**
  1. **Split Refactoring:** Partition synthetic COG data into `train`, `val`, and `test` directories matching a 70/15/15 ratio.
  2. **Data Augmentation:** Configure `rotation_range=15` and `horizontal_flip=True` in `ImageDataGenerator` for the training subset.
  3. **Performance Metrics:** Add evaluation code to output final test accuracy, precision, and recall metrics matching Chapter III targets.

### Phase 10: Python 3.11 Virtual Environment Setup - [COMPLETED]
- **Goal:** Set up a dedicated Python 3.11 virtual environment for the AI microservice to support TensorFlow and Keras.
- **Steps:**
  1. **Initialization:** Create a virtual environment inside `aegis-ai/venv` using the preinstalled Python 3.11 binary at `C:\Python311\python.exe`.
  2. **Install Dependencies:** Upgrade pip and install packages listed in `requirements.txt`.
  3. **Verification:** Confirm that TensorFlow 2.16.1 and Keras 3.3.3 import correctly.

### Phase 11: AI Model Training Execution - [COMPLETED]
- **Goal:** Execute the training script to generate the synthetic COG dataset, train the ResNet-50 network, and save it in the Keras 3-compliant `.keras` format.
- **Steps:**
  1. **Update Code paths:** Modify `train_model.py` and `app.py` to target `aegis_resnet50_v1.keras` instead of `aegis_resnet50_v1.h5` (completed).
  2. **Execution:** Launch `train_model.py` using the Python 3.11 virtual environment under `$env:PYTHONIOENCODING="utf-8"` (completed).
  3. **Monitoring:** Verify training progresses across 10 epochs and achieves the target metric scores (Accuracy >= 90%, Precision/Recall >= 85%) (completed).
  4. **Verification:** Confirm that the output model `aegis_resnet50_v1.keras` is successfully generated, loadable, and evaluate it on the test partition (completed).
### Phase 12: Automatic AI Scan Triggering on Admin Review - [COMPLETED]
- **Goal:** Improve user experience by automatically running the AI forensics scan when an admin opens a student application for evaluation.
- **Steps:**
  1. **Update Controller:** Modify `AdminController@review` to detect if the student application has an uploaded document and has not yet been processed by the AI classifier.
  2. **Trigger Scan:** Auto-create the placeholder `AIResult` with `scanning` classification and dispatch the `ScanDocumentJob` in the background immediately.
  3. **Verification:** Wrote integration test `test_reviewing_unscanned_application_auto_triggers_scan` in `DocumentScanTest.php` and verified that the queue worker correctly dispatches the scan automatically on page load.
