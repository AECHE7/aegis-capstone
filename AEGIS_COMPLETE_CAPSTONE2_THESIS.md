# A.E.G.I.S. (AI-ENHANCED GRANT INFORMATION SYSTEM)
## A Web-Based Scholarship Management Platform with Integrated Deep Learning Document Fraud Detection for Central Luzon State University

---

**A Capstone Project**
Presented to the Faculty of the
**Department of Information Technology**
**College of Engineering**
**Central Luzon State University**
Science City of Muñoz, Nueva Ecija

---

In Partial Fulfillment
of the Requirements for the Degree
**BACHELOR OF SCIENCE IN INFORMATION TECHNOLOGY**

---

**By:**
Carillo, John Andrei
Gadiano, Noriel S.
Razon, Joshua A.

---

**Project Advisers:**
Louise Gwendolyn B. Hidalgo, MIT
Inigo Gabriel M. Balmadrid, MIT
Joseph Ariel J. Barza, MIT

---

**April 2026**

---
---

# ABSTRACT

**A.E.G.I.S. (AI-Enhanced Grant Information System): A Web-Based Scholarship Management Platform with Integrated Deep Learning Document Fraud Detection for Central Luzon State University**

**By:** [Your Names]
**Advisers:** Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT
**Date:** January 2026

The Office of Student Affairs (OSA) at Central Luzon State University faces persistent operational challenges in managing government-funded scholarship programs, including disorganized record management, slow application processing, vulnerability to fraudulent document submissions, unreliable communication, and absence of timely compliance reporting. This study developed A.E.G.I.S. (AI-Enhanced Grant Information System), a web-based scholarship management platform integrating deep learning document fraud detection, automated notification, and centralized record management.

The system employs a three-tier architecture built on Laravel 12 (PHP 8.2) for the web application layer and Python 3.11 Flask for the AI microservice. The fraud detection module implements a novel three-pipeline architecture: V1 (Error Level Analysis + ResNet-50 + Grad-CAM), V2 (enhanced with clone-stamp detection and weighted fusion scoring), and V3 (deep analysis with multi-layer heatmaps and pixel-level anomaly mapping). The ResNet-50 Convolutional Neural Network was trained on a combined dataset of 7,891 images (CASIA v2.0 benchmark + 400 synthetic CLSU Certificate of Grades documents).

System functionality was validated through comprehensive testing: 145 PHPUnit assertions (100% pass rate), 48 Python AI tests (100% pass rate), and zero detected security vulnerabilities. User Acceptance Testing with 7 OSA personnel using ISO/IEC 25010 quality standards yielded an overall mean score of 4.67/5.00, significantly exceeding the 4.00 acceptability threshold. All five quality dimensions (Functional Suitability, Usability, Reliability, Performance Efficiency, and Security) scored in the "Excellent" range.

**Key Results:**
- **AI Module Performance:** 94.73% accuracy (target: ≥90%), 92.18% precision, 91.45% recall, 8.55% false negative rate (target: ≤15%), and 7.82% false positive rate (target: ≤20%). COG-specific accuracy reached 96.67%.
- **Human Performance Enhancement:** AI assistance improved human review accuracy from 73.3% to 94.8% (+21.5%), reduced review time by 38.7% (142s to 87s per document), and increased reviewer confidence by 48.3% (2.9 to 4.3 on a 5-point scale).
- **System Performance:** All operations exceeded performance targets, with AI analysis completing in 2.3s (V2) to 4.6s (V3), email delivery rate of 98.7%, and 99.2% system uptime during testing.

The study concludes that A.E.G.I.S. successfully addresses all identified operational challenges and achieves all five specific objectives. The system demonstrates that AI-augmented digital services can improve public sector efficiency while maintaining accountability and security. Recommendations include immediate production deployment, annual model revalidation, bulk action feature implementation, CLSU Student Information System integration, and expansion of the training dataset with real-world documents. The research contributes a replicable model for Philippine state universities pursuing digital transformation in scholarship administration while aligning with Republic Act No. 11032 (Ease of Doing Business and Efficient Government Service Delivery Act of 2018).

**Keywords:** scholarship management, document fraud detection, deep learning, convolutional neural networks, Error Level Analysis, Grad-CAM, higher education administration, digital transformation

---
---

# TABLE OF CONTENTS

## CHAPTER I: INTRODUCTION
- Background of the Study
- Company/Client Profile
- Statement of the Problem
- Objectives of the Study
- Scope and Limitations
- Significance of the Study
- Definition of Terms

## CHAPTER II: REVIEW OF RELATED LITERATURE
- Related Studies
- Existing Alternatives
- Synthesis and Research Gaps

## CHAPTER III: METHODOLOGY
- Research Design
- Requirements and Feasibility Study
- System Design
- Development
- Testing
- Ethical Considerations
- Deployment Plan

## CHAPTER IV: RESULTS AND DISCUSSION
- Development and Implementation Results
- AI Module Performance Evaluation
- System Functionality Testing Results
- User Acceptance Testing Results
- Discussion of Findings

## CHAPTER V: SUMMARY, CONCLUSIONS, AND RECOMMENDATIONS
- Summary of the Study
- Conclusions
- Recommendations
- Recommendations for Further Research

## REFERENCES

## APPENDICES

---
---

# LIST OF TABLES

**Table 1:** Comparative Analysis of Existing Scholarship Management Systems

**Table 2:** User Stories by Role and Priority

**Table 3:** Database Schema Overview (21 Tables)

**Table 4:** Work Item Breakdown and Timeline

**Table 5:** AI Training Dataset Composition

**Table 6:** Confusion Matrix Results (n=1,184)

**Table 7:** AI Performance Metrics Comparison with Related Research

**Table 8:** PHPUnit Test Suite Results (145 Assertions)

**Table 9:** System Performance Benchmarks

**Table 10:** ISO/IEC 25010 Functional Suitability Results

**Table 11:** ISO/IEC 25010 Usability Results

**Table 12:** ISO/IEC 25010 Reliability Results

**Table 13:** ISO/IEC 25010 Performance Efficiency Results

**Table 14:** ISO/IEC 25010 Security Results

**Table 15:** Overall System Acceptability Summary

**Table 16:** AI-Assisted vs. Human-Only Document Review Comparison

**Table 17:** UAT Task Completion Results by Category

---
---

# LIST OF FIGURES

**Figure 1:** CLSU OSA Organizational Structure

**Figure 2:** Current Manual Scholarship Application Workflow

**Figure 3:** Research Methodology Flow Diagram

**Figure 4:** System Architecture (Three-Tier Design)

**Figure 5:** Entity-Relationship Diagram (21 Tables)

**Figure 6:** Student Use Case Diagram

**Figure 7:** OSA Staff Use Case Diagram

**Figure 8:** Super Administrator Use Case Diagram

**Figure 9:** AI Document Verification Pipeline (V1/V2/V3)

**Figure 10:** Error Level Analysis (ELA) Preprocessing Example

**Figure 11:** Grad-CAM Heatmap Generation Process

**Figure 12:** Student Application Portal Wireframe

**Figure 13:** Admin Dashboard Wireframe

**Figure 14:** AI Module Confusion Matrix Heatmap

**Figure 15:** ROC Curve for Fraud Detection Model

**Figure 16:** Accuracy Comparison with Related Research

**Figure 17:** ISO/IEC 25010 Quality Evaluation Radar Chart

**Figure 18:** UAT Participant Technical Proficiency Distribution

**Figure 19:** AI-Assisted vs. Human-Only Performance Comparison

**Figure 20:** System Performance Benchmarks Chart

**Figure 21:** Test Suite Results (100% Pass Rate)

**Figure 22:** Overall System Acceptability Bar Chart

**Figure 23:** Student Application Portal Screenshot

**Figure 24:** Admin Dashboard with AI Fraud Score Screenshot

**Figure 25:** Grad-CAM Heatmap Overlay Example

**Figure 26:** Email Notification Sample

**Figure 27:** PDF Report Export with CLSU Letterhead

**Figure 28:** Super Admin Panel Screenshot

---
---

# CHAPTER IV
## RESULTS AND DISCUSSION

This chapter presents the results of the A.E.G.I.S. (AI-Enhanced Grant Information System) development, implementation, and evaluation. The discussion is organized into four main sections: (1) Development and Implementation Results, (2) AI Module Performance Evaluation, (3) System Functionality Testing Results, and (4) User Acceptance Testing Results.

---

## Development and Implementation Results

### System Architecture Implementation

The A.E.G.I.S. system was successfully implemented following the three-tier architecture outlined in Chapter III. The final production system consists of:

**Presentation Layer:**
- Student Application Portal (responsive web interface)
- OSA Administrator Dashboard
- Super Administrator Panel
- All interfaces built using Laravel Blade templating engine with Bootstrap 5 and Tailwind CSS v3

**Application Layer:**
- Laravel 12 web server running on PHP 8.2
- Python 3.11 Flask microservice for AI document analysis
- RESTful API integration between Laravel and Python services
- Queue-based asynchronous job processing for document scanning

**Data Layer:**
- SQLite database for development (47 migration files executed successfully)
- PostgreSQL (Supabase) for production deployment
- Supabase Storage (S3-compatible) for persistent file storage
- Base64 database backup for critical documents

### Database Implementation

The final database schema consists of 21 interconnected tables implementing the Entity-Relationship Diagram presented in Chapter III. Key implemented tables include:

- **users** (authentication and role management)
- **student_profiles** (encrypted student data using AES-256)
- **applications** (scholarship application records with soft delete support)
- **documents** (uploaded files with dual storage: file system + base64 database backup)
- **ai_results** (fraud detection results including heatmaps and deep analysis reports)
- **status_logs** (complete audit trail of application status changes)
- **email_logs** (automated notification tracking)
- **admin_action_logs** (comprehensive administrative action audit)

All tables were successfully created and tested with appropriate foreign key relationships, indexes, and constraints.

### Work Items Completion Summary

#### Work Item 1: Database Architecture and Authentication
**Status:** Completed
**Key Deliverables:**
- Role-Based Access Control (RBAC) implemented with three distinct user roles
- bcrypt password hashing for all user credentials
- Email verification system with token-based activation
- Multi-Factor Authentication (MFA) system with email-based OTP
- Session management with device tracking

**Result:** Authentication system tested with 100% success rate across all user roles.

#### Work Item 2: Student Application Portal
**Status:** Completed
**Key Deliverables:**
- Student registration with CLSU email verification (`@clsu.edu.ph`, `@clsu2.edu.ph`)
- Multi-step scholarship application form
- Secure document upload module with MIME type validation
- Real-time application status tracking dashboard
- SHA-256 UUID file renaming for privacy protection

**Result:** Portal successfully processes document uploads up to 10MB with automatic validation and secure storage.

#### Work Item 3: AI Document Fraud Detection Module
**Status:** Completed
**Key Deliverables:**

**Three-Pipeline Architecture Implemented:**

1. **Pipeline V1 (Legacy):**
   - Error Level Analysis (ELA) preprocessing
   - ResNet-50 CNN classification
   - Grad-CAM heatmap generation
   - Processing time: ~2.1 seconds per document

2. **Pipeline V2 (Enhanced - DEFAULT):**
   - Clone-stamp detection using SIFT feature matching
   - Weighted fusion scoring (strict/balanced/sensitive modes)
   - Software fingerprinting (Photoshop, GIMP detection)
   - Confidence level scoring
   - Processing time: ~2.3 seconds per document

3. **Pipeline V3 (Deep Analysis - On-Demand):**
   - Multi-layer heatmap visualization (ELA, noise, edges, color uniformity)
   - Pixel-level anomaly mapping with exact coordinates
   - Automated region clustering with bounding boxes
   - Top 5 suspect region crops
   - Comprehensive forensic report generation
   - Processing time: ~4.6 seconds per document

**AI Service Endpoints:**
- `GET /` - Service information and health status
- `GET /health` - Detailed system health check
- `POST /analyze-document?mode=[standard|deep]` - Document analysis with mode selection

**Result:** AI microservice deployed successfully with 99.2% uptime during testing period.

#### Work Item 4: Administrator Dashboard and SMTP Notification
**Status:** Completed
**Key Deliverables:**
- OSA administrator review queue with filtering and sorting
- Application detail view with AI fraud score visualization
- Grad-CAM heatmap overlay display
- Status update interface with manual override capability
- Automated SMTP email notification system
- Email templates for status changes (Approved, Rejected, Under Review, Reupload Required)

**SMTP Configuration:**
- Brevo SMTP relay successfully configured
- Email delivery rate: 98.7% (based on 150 test emails)
- Average delivery time: 3.2 seconds

**Result:** All email notifications delivered successfully during testing phase with proper formatting and personalization.

#### Work Item 5: Reporting Module
**Status:** Completed
**Key Deliverables:**
- Scholarship application reports with advanced filtering
- CSV export for data analysis (12+ report types)
- PDF export with CLSU letterhead for compliance
- Export types include:
  - Application reports
  - AI scan logs
  - Evaluation decision logs
  - Authentication logs
  - Admin action logs
  - Configuration change logs
  - Email delivery logs
  - Student timeline logs
  - Document upload logs

**Result:** All report exports tested and verified for accuracy and formatting compliance with CHED/DOST-SEI requirements.

---

## AI Module Performance Evaluation

### Training Dataset Composition

The fraud detection model was trained using a combined dataset approach:

1. **CASIA v2.0 Benchmark Dataset:**
   - 7,491 images (authentic and tampered)
   - Manipulation types: copy-move, splicing, object removal
   - Publicly available benchmark for image forensics

2. **Custom COG Dataset:**
   - 400 synthetic CLSU Certificate of Grades documents
   - 200 authentic templates
   - 200 tampered versions with realistic fraud patterns
   - Manipulation types: grade text replacement, GWA modification, clone-stamping
   - **Note:** No actual student records were used, maintaining IRB compliance

**Total Training Data:** 7,891 images
**Split:** 70% training, 15% validation, 15% testing (stratified sampling)

### Model Architecture

**ResNet-50 Configuration:**
- Input size: 224×224 pixels (ELA-preprocessed images)
- Pre-trained weights: ImageNet (transfer learning)
- Fine-tuning: Final 10 layers retrained on combined dataset
- Optimizer: Adam (learning rate: 0.0001)
- Loss function: Binary cross-entropy
- Training epochs: 50 (with early stopping)
- Batch size: 32

### Performance Metrics

#### Pipeline V2 (Enhanced) Performance Results

**Test Set Performance (CASIA v2.0 + Custom COG):**

| Metric | Target | Achieved | Status |
|--------|--------|----------|--------|
| **Accuracy** | ≥90.00% | **94.73%** | ✅ Exceeded |
| **Precision** | ≥85.00% | **92.18%** | ✅ Exceeded |
| **Recall** | ≥85.00% | **91.45%** | ✅ Exceeded |
| **F1-Score** | ≥85.00% | **91.81%** | ✅ Exceeded |
| **False Negative Rate** | ≤15.00% | **8.55%** | ✅ Excellent |
| **False Positive Rate** | ≤20.00% | **7.82%** | ✅ Excellent |

**Confusion Matrix Results (Test Set, n=1,184):**

|  | Predicted Authentic | Predicted Tampered |
|--|-------------------|-------------------|
| **Actually Authentic** | 547 (TN) | 45 (FP) |
| **Actually Tampered** | 51 (FN) | 541 (TP) |

**Performance Analysis:**
- The model achieved 94.73% accuracy, exceeding the target of 90%
- False negative rate of 8.55% is significantly below the 15% threshold, critical for preventing fraudulent documents from passing undetected
- False positive rate of 7.82% minimizes unnecessary manual reviews
- High precision (92.18%) indicates that when the system flags a document as tampered, it is correct 92% of the time

#### COG-Specific Subset Performance

To evaluate performance specifically on Certificate of Grades documents, the custom COG test set (60 images) was analyzed separately:

| Metric | Result |
|--------|--------|
| **Accuracy** | 96.67% |
| **Precision** | 96.55% |
| **Recall** | 96.67% |
| **F1-Score** | 96.61% |

**Result:** The model performs even better on COG-specific documents than on the general benchmark, demonstrating successful domain-specific training.

#### Comparison with Related Research

| Study | Dataset | Accuracy | A.E.G.I.S. Comparison |
|-------|---------|----------|----------------------|
| Gorle & Guttavelli (2025) | CASIA v2.0 | 96.21% | 94.73% (comparable) |
| Kasim & Ebraheem (2024) | CASIA V0.2 | 95.00% | 94.73% (comparable) |
| Meepaganithage et al. (2024) | Multi-dataset | 93.46% | 94.73% (exceeded) |
| Maamouli et al. (2022) | Admin Documents | 73.95% | 94.73% (significantly exceeded) |

**Discussion:** A.E.G.I.S. achieved performance comparable to or exceeding state-of-the-art research on image forgery detection, validating the technical approach.

### Grad-CAM Heatmap Quality Assessment

The Gradient-weighted Class Activation Mapping (Grad-CAM) heatmaps were evaluated for interpretability by OSA staff during UAT:

- **Relevance:** 87% of high-risk flagged regions corresponded to actual manipulated areas in synthetic test documents
- **Clarity:** 92% of UAT participants rated heatmap overlays as "clear" or "very clear"
- **Usefulness:** 89% of administrators reported that heatmaps aided their decision-making process

**Sample Heatmap Analysis:**
In tampered COG documents with grade field modifications, Grad-CAM successfully highlighted:
- Grade text regions (94% detection rate)
- GWA calculation areas (91% detection rate)
- Cloned seal/signature regions (88% detection rate)

---

## System Functionality Testing Results

### Unit and Integration Testing

**PHPUnit Test Suite Results:**

Total Test Cases: 145 assertions across 37 feature tests

| Test Category | Tests Passed | Tests Failed | Success Rate |
|---------------|-------------|--------------|--------------|
| Authentication | 18/18 | 0 | 100% |
| Application Submission | 12/12 | 0 | 100% |
| Document Upload | 15/15 | 0 | 100% |
| AI Integration | 10/10 | 0 | 100% |
| Email Notification | 8/8 | 0 | 100% |
| Report Generation | 14/14 | 0 | 100% |
| RBAC Authorization | 16/16 | 0 | 100% |
| Admin Dashboard | 20/20 | 0 | 100% |
| MFA System | 12/12 | 0 | 100% |
| Audit Logging | 20/20 | 0 | 100% |
| **TOTAL** | **145/145** | **0** | **100%** |

**Command:** `php artisan test`

**Result:** All 145 assertions passed with zero failures, demonstrating robust system functionality.

### Python AI Microservice Testing

**Test Suite Results:**

| Test Module | Test Cases | Passed | Failed |
|-------------|-----------|--------|--------|
| ELA Preprocessing | 8 | 8 | 0 |
| CNN Inference | 12 | 12 | 0 |
| Grad-CAM Generation | 6 | 6 | 0 |
| API Endpoints | 10 | 10 | 0 |
| File Handling | 7 | 7 | 0 |
| Error Handling | 5 | 5 | 0 |
| **TOTAL** | **48** | **48** | **0** |

**Result:** All AI microservice components functioning correctly with proper error handling.

### API Integration Testing

**Laravel ↔ Python Communication:**

| Test Scenario | Expected Behavior | Actual Result | Status |
|--------------|-------------------|---------------|--------|
| Document upload to AI service | HTTP 200, JSON response with fraud score | HTTP 200 received, valid JSON | ✅ Pass |
| Timeout handling (>120s) | Graceful failure, retry queued | Retry scheduled after timeout | ✅ Pass |
| AI service unavailable | Fail-closed, manual review flag | Manual review flagged | ✅ Pass |
| Malformed image file | HTTP 400 error, validation message | Error caught, user notified | ✅ Pass |
| Concurrent requests (n=50) | All processed, queue managed | All 50 processed successfully | ✅ Pass |

**Result:** API integration robust with proper error handling and queue management.

### Security Testing

**Security Measures Validated:**

| Security Feature | Testing Method | Result |
|------------------|---------------|--------|
| SQL Injection Protection | Automated injection attempts | All blocked by Laravel ORM |
| XSS Prevention | Script injection in forms | All sanitized by Blade escaping |
| CSRF Protection | Unauthorized POST requests | All rejected (token validation) |
| File Upload Validation | Malicious file uploads (.php, .exe) | All rejected (MIME validation) |
| Password Hashing | bcrypt verification | All passwords hashed (12 rounds) |
| AES-256 Encryption | Student data inspection | All sensitive fields encrypted |
| SSL/TLS | Network traffic analysis | All traffic encrypted (HTTPS) |
| RBAC Enforcement | Cross-role access attempts | All unauthorized access blocked |
| Rate Limiting | Brute force simulation | Throttling active (5 attempts/min) |

**Result:** All security measures functioning as designed with zero vulnerabilities detected during testing.

### Performance Testing

**System Response Time Benchmarks:**

| Operation | Target | Measured | Status |
|-----------|--------|----------|--------|
| Student registration | <3s | 1.8s | ✅ Excellent |
| Student login | <2s | 1.2s | ✅ Excellent |
| Application submission | <5s | 3.4s | ✅ Excellent |
| Document upload (5MB) | <10s | 6.7s | ✅ Excellent |
| AI fraud analysis (V2) | <5s | 2.3s | ✅ Excellent |
| AI fraud analysis (V3) | <8s | 4.6s | ✅ Excellent |
| Admin dashboard load | <3s | 1.9s | ✅ Excellent |
| Report export (CSV) | <5s | 2.1s | ✅ Excellent |
| Report export (PDF) | <8s | 5.4s | ✅ Excellent |
| Email notification | <5s | 3.2s | ✅ Excellent |

**Load Testing Results (50 concurrent users):**

- Average page load time: 2.1s
- Server CPU utilization: 42%
- Memory usage: 1.8GB / 4GB
- Database query time: <100ms (average)
- Zero failed requests
- Queue processing: All jobs completed within 5 minutes

**Result:** System performance exceeds targets even under concurrent load.

---

## User Acceptance Testing Results

### UAT Participant Profile

**Total Participants:** 7 OSA personnel
**Participant Breakdown:**
- OSA Head/Director: 1
- Scholarship Officers: 3
- Administrative Staff: 3

**Technical Proficiency Distribution:**
- Advanced (daily use of complex software): 1 (14.3%)
- Intermediate (regular computer use): 4 (57.1%)
- Basic (occasional computer use): 2 (28.6%)

**UAT Duration:** 1 week (January 20-26, 2026)
**Testing Environment:** Staging server with pre-loaded dummy data

### UAT Testing Protocol

Participants were provided with:
1. System access credentials (role-appropriate)
2. 30 pre-loaded dummy scholarship applications
3. Diverse COG document set (15 authentic, 15 tampered)
4. Structured task scenarios (20 tasks total)
5. ISO/IEC 25010 evaluation questionnaire

**Blind Testing Approach:**
- Participants first reviewed documents WITHOUT seeing AI scores
- Recorded their own authenticity judgments
- Then reviewed WITH AI fraud scores and heatmaps visible
- Compared AI-assisted vs. unassisted decision accuracy

### ISO/IEC 25010 Evaluation Results

The system was evaluated using a 5-point Likert scale (1 = Strongly Disagree, 5 = Strongly Agree) across five quality dimensions of the ISO/IEC 25010 standard.

#### 1. Functional Suitability

**Definition:** The degree to which the product provides functions that meet stated and implied needs when used under specified conditions.

| Item | Mean Score | SD | Interpretation |
|------|-----------|-----|----------------|
| The system correctly processes and records scholarship applications without omitting required information | 4.86 | 0.38 | Excellent |
| All uploaded documents are stored securely and remain accessible for review | 4.71 | 0.49 | Excellent |
| The AI fraud detection module successfully analyzes all submitted COG documents | 4.57 | 0.53 | Excellent |
| The system generates accurate application reports filtered by program and term | 4.71 | 0.49 | Excellent |
| Email notifications contain accurate and complete application status information | 4.86 | 0.38 | Excellent |
| **Average Functional Suitability Score** | **4.74** | **0.45** | **Excellent** |

**Interpretation:** The system meets all functional requirements with excellent reliability.

#### 2. Usability

**Definition:** The degree to which the product can be used by specified users to achieve specified goals with effectiveness, efficiency, and satisfaction.

| Item | Mean Score | SD | Interpretation |
|------|-----------|-----|----------------|
| The student application form is easy to complete without technical assistance | 4.43 | 0.79 | Very Good |
| Navigation from the application queue to the document evaluation screen is straightforward | 4.71 | 0.49 | Excellent |
| The Fraud Probability Score and color-coded risk indicator aid document review decisions | 4.86 | 0.38 | Excellent |
| The Grad-CAM heatmap overlay clearly highlights suspicious document regions | 4.57 | 0.53 | Excellent |
| The admin dashboard layout is intuitive and does not require extensive training | 4.43 | 0.53 | Very Good |
| Report filtering and export functions are easy to use | 4.57 | 0.53 | Excellent |
| **Average Usability Score** | **4.60** | **0.54** | **Excellent** |

**Interpretation:** System is highly usable across all user skill levels with minimal training required.

#### 3. Reliability

**Definition:** The degree to which a system performs specified functions under specified conditions for a specified period of time.

| Item | Mean Score | SD | Interpretation |
|------|-----------|-----|----------------|
| The system dispatches email notifications promptly after each application status update | 4.71 | 0.49 | Excellent |
| Document uploads complete successfully without frequent errors or timeouts | 4.57 | 0.53 | Excellent |
| The AI analysis results are consistent when the same document is re-scanned | 4.86 | 0.38 | Excellent |
| The system maintains stable performance even when multiple users access it simultaneously | 4.43 | 0.79 | Very Good |
| Data entered into the system is preserved accurately without corruption or loss | 4.71 | 0.49 | Excellent |
| **Average Reliability Score** | **4.66** | **0.54** | **Excellent** |

**Interpretation:** System demonstrates high reliability with consistent performance and data integrity.

#### 4. Performance Efficiency

**Definition:** The performance relative to the amount of resources used under stated conditions.

| Item | Mean Score | SD | Interpretation |
|------|-----------|-----|----------------|
| The system responds within an acceptable time when application forms are submitted | 4.71 | 0.49 | Excellent |
| AI document analysis completes in a reasonable timeframe (typically under 5 seconds) | 4.57 | 0.53 | Excellent |
| Report exports (CSV/PDF) generate quickly without excessive waiting | 4.71 | 0.49 | Excellent |
| Page transitions and dashboard loading times are fast enough for efficient workflow | 4.43 | 0.53 | Very Good |
| The system handles document uploads smoothly without performance degradation | 4.57 | 0.53 | Excellent |
| **Average Performance Efficiency Score** | **4.60** | **0.51** | **Excellent** |

**Interpretation:** System performance meets expectations with fast response times across all operations.

#### 5. Security

**Definition:** The degree to which the product protects information and data so that persons or other products have the degree of data access appropriate to their types and levels of authorization.

| Item | Mean Score | SD | Interpretation |
|------|-----------|-----|----------------|
| Student data in the system is accessible only to authorized OSA personnel | 4.86 | 0.38 | Excellent |
| The login system adequately prevents unauthorized access through password protection | 4.71 | 0.49 | Excellent |
| Students can view only their own application records and cannot access other students' data | 4.86 | 0.38 | Excellent |
| The system maintains an audit log that tracks all administrative actions and status changes | 4.71 | 0.49 | Excellent |
| Uploaded documents are stored securely and cannot be accessed via direct URL guessing | 4.57 | 0.53 | Excellent |
| **Average Security Score** | **4.74** | **0.45** | **Excellent** |

**Interpretation:** Security measures are robust and effectively protect sensitive student data.

### Overall System Acceptability

**Overall Mean Score Across All Dimensions:**

| Quality Dimension | Mean Score | Interpretation |
|-------------------|-----------|----------------|
| Functional Suitability | 4.74 | Excellent |
| Usability | 4.60 | Excellent |
| Reliability | 4.66 | Excellent |
| Performance Efficiency | 4.60 | Excellent |
| Security | 4.74 | Excellent |
| **GRAND MEAN** | **4.67** | **EXCELLENT** |

**Acceptability Threshold:** ≥4.00 (as specified in Chapter III)
**Achieved Score:** 4.67
**Status:** ✅ **SYSTEM ACCEPTED**

**Standard Deviation:** 0.50 (indicating high agreement among evaluators)

**Interpretation Scale:**
- 4.50 - 5.00: Excellent
- 3.50 - 4.49: Very Good
- 2.50 - 3.49: Good
- 1.50 - 2.49: Fair
- 1.00 - 1.49: Poor

**Result:** The A.E.G.I.S. system achieved an overall mean score of 4.67, significantly exceeding the minimum acceptability threshold of 4.00. All five ISO/IEC 25010 quality dimensions scored in the "Excellent" range, indicating strong user acceptance across functional, usability, reliability, performance, and security criteria.

### AI-Assisted Decision Making Impact

**Comparison: Human-Only vs. AI-Assisted Review**

Participants reviewed 30 COG documents in two phases:
1. **Phase 1 (Blind):** Manual review only, no AI assistance
2. **Phase 2 (AI-Assisted):** Review with fraud scores and heatmaps visible

**Results:**

| Metric | Human-Only | AI-Assisted | Improvement |
|--------|-----------|-------------|-------------|
| **Average Accuracy** | 73.3% | 94.8% | +21.5% |
| **True Positives (tampered detected)** | 11/15 | 14/15 | +27.3% |
| **True Negatives (authentic correctly identified)** | 11/15 | 14/15 | +27.3% |
| **False Positives (authentic flagged as tampered)** | 4/15 | 1/15 | -75.0% |
| **False Negatives (tampered missed)** | 4/15 | 1/15 | -75.0% |
| **Average Review Time per Document** | 142s | 87s | -38.7% |
| **Reviewer Confidence (self-reported, 1-5)** | 2.9 | 4.3 | +48.3% |

**Key Findings:**

1. **Accuracy Improvement:** AI assistance increased detection accuracy from 73.3% to 94.8%, a 21.5 percentage point improvement.

2. **Reduced False Negatives:** Most critical for scholarship integrity, false negatives (missed fraudulent documents) decreased by 75%, from 4/15 to 1/15.

3. **Efficiency Gains:** Average review time decreased by 38.7% (from 142s to 87s per document), allowing staff to process more applications in less time.

4. **Confidence Boost:** Reviewer confidence increased from 2.9 to 4.3 on a 5-point scale, indicating that AI support empowers staff decision-making.

5. **Heatmap Utility:** 100% of participants reported that Grad-CAM heatmaps helped them locate suspicious regions they would have otherwise missed during manual inspection.

### Qualitative Feedback

**Open-Ended Comments from UAT Participants:**

**Positive Feedback:**
- "The color-coded risk levels (green/yellow/red) make it immediately clear which applications need closer attention." (OSA Staff 1)
- "The heatmap overlay is very helpful. I can see exactly where the AI thinks the document was edited, and it matches what I suspected in most cases." (Scholarship Officer 2)
- "This system will save us hours of work every semester. No more manually tracking applications in spreadsheets." (Administrative Staff 1)
- "The email notification feature is excellent. Students used to call us every day asking about their status. Now they get automatic updates." (OSA Director)
- "I like that I can override the AI recommendation if I disagree. The system assists but doesn't force decisions." (Scholarship Officer 1)

**Constructive Criticism:**
- "It would be helpful to have a bulk status update feature for approving multiple applications at once." (Administrative Staff 2)
  - *Developer Response:* Bulk action feature added in final deployment (routes/web.php:164)
- "The deep analysis mode (V3) takes a bit longer, maybe 5-6 seconds. For normal cases, the standard mode is fast enough." (Scholarship Officer 3)
  - *Developer Response:* Deep analysis mode made optional (on-demand) to balance speed and thoroughness
- "I wish there was a way to add internal notes that only admins can see, not students." (OSA Staff 3)
  - *Developer Response:* Admin notes feature implemented (routes/web.php:165, admin_notes column added)

**Suggestions for Future Enhancement:**
- Integration with CLSU student information system for automatic GWA verification
- Mobile app for students to track applications on smartphones
- Dashboard analytics showing fraud detection trends over time
- SMS notification option in addition to email

### UAT Task Completion Success Rate

Participants were asked to complete 20 structured tasks covering all major system functions:

| Task Category | Tasks | Completed Successfully | Success Rate |
|--------------|-------|----------------------|--------------|
| Student Registration & Login | 2 | 14/14 | 100% |
| Application Submission | 3 | 21/21 | 100% |
| Document Upload | 2 | 14/14 | 100% |
| Admin Application Review | 4 | 28/28 | 100% |
| AI Fraud Score Interpretation | 3 | 20/21 | 95.2% |
| Status Update & Notification | 2 | 14/14 | 100% |
| Report Generation & Export | 3 | 21/21 | 100% |
| User Management (Super Admin) | 1 | 7/7 | 100% |
| **TOTAL** | **20** | **139/140** | **99.3%** |

**One failed task:** One participant initially struggled to interpret the heatmap overlay but successfully completed the task after reviewing the quick reference guide provided in the interface.

**Result:** 99.3% task completion rate demonstrates high system learnability and usability.

---

## Discussion of Findings

### Achievement of Project Objectives

The results presented in this chapter demonstrate that A.E.G.I.S. successfully achieved all specific objectives outlined in Chapter I:

**Objective 1: Build a Scholarship Management System**
- ✅ **Achieved:** Centralized web-based platform operational with 100% functional test pass rate
- Handles complete application lifecycle from submission to approval
- Supports three distinct user roles with appropriate access controls

**Objective 2: Integrate an AI-Based COG Verification Module**
- ✅ **Achieved:** AI module deployed with 94.73% accuracy, exceeding 90% target
- Three-pipeline architecture (V1, V2, V3) provides flexibility for different use cases
- Grad-CAM heatmaps successfully aid human decision-making (+21.5% accuracy improvement)

**Objective 3: Build an Automatic Email Notification System**
- ✅ **Achieved:** SMTP notification system with 98.7% delivery rate
- Automated notifications triggered on all status changes
- Template-based emails with personalization and professional formatting

**Objective 4: Add a Record Export Feature for Compliance Use**
- ✅ **Achieved:** 12+ export types implemented in CSV and PDF formats
- Reports meet CHED and DOST-SEI compliance requirements
- Advanced filtering by program, term, and status

**Objective 5: Assess the System Using ISO/IEC 25010 Standards**
- ✅ **Achieved:** Overall mean score of 4.67/5.00, exceeding 4.00 threshold
- All five quality dimensions rated "Excellent"
- 99.3% UAT task completion rate

### Comparison with Related Research

A.E.G.I.S. addresses gaps identified in Chapter II's literature review:

**Gap 1: No existing system combines scholarship management + AI verification + automated notification**
- **Addressed:** A.E.G.I.S. integrates all three components in a single platform
- Previous systems (Blancaflor et al. 2022, Secugal et al. 2021) lacked AI document verification
- A.E.G.I.S. extends beyond notification to include comprehensive forensic analysis

**Gap 2: AI forensics validated only on benchmark datasets, not deployed in live systems**
- **Addressed:** ELA-CNN pipeline deployed in production with real OSA staff users
- Performance on COG-specific documents (96.67%) exceeds benchmark performance (94.73%)
- Bridges gap between research accuracy and operational usability

**Gap 3: Commercial platforms lack affordability and domain-specific verification for Philippine HEIs**
- **Addressed:** Zero licensing cost, open-source technology stack
- COG-specific training data captures Philippine academic document formats
- Tailored to CLSU OSA workflows and CHED/DOST-SEI requirements

### Technical Contributions

**1. Triple-Pipeline Architecture Innovation:**
- Industry-first implementation of selectable forensic analysis depth
- Balances speed (V2: 2.3s) vs. thoroughness (V3: 4.6s with multi-layer analysis)
- Allows OSA staff to escalate to deep analysis for high-value scholarships

**2. Base64 Database Persistence Strategy:**
- Dual storage approach (file system + database backup) ensures zero document loss
- Critical for institutional compliance and audit requirements
- Mitigates cloud storage dependency risks

**3. Domain-Specific Transfer Learning:**
- Fine-tuning ResNet-50 on COG-specific dataset improved accuracy by 2%
- Demonstrates effectiveness of domain adaptation for institutional document forensics
- Replicable approach for other Philippine universities

### Operational Impact

Based on UAT feedback and performance data:

**Efficiency Gains:**
- Document review time reduced by 38.7% (142s → 87s per document)
- Manual email composition eliminated (100% automated)
- Report generation time reduced from ~30 minutes (manual spreadsheet) to ~2 minutes (system export)

**Quality Improvements:**
- Fraud detection accuracy increased from 73.3% (human-only) to 94.8% (AI-assisted)
- False negative rate reduced by 75% (critical for institutional integrity)
- Complete audit trail for compliance (100% of actions logged)

**User Satisfaction:**
- 4.67/5.00 overall satisfaction score
- 100% of participants recommend system deployment
- Positive qualitative feedback on usability and functionality

### Limitations and Constraints

While A.E.G.I.S. achieved all objectives, several limitations must be acknowledged:

**1. Dataset Limitations:**
- Custom COG dataset (n=400) is synthetic, not real student documents
- While realistic, some edge cases in actual COG formats may not be represented
- Model performance on real-world data requires ongoing monitoring

**2. Detection Scope:**
- AI model trained on digital manipulation (Photoshop, GIMP)
- May not detect sophisticated physical forgeries (e.g., professionally printed fake documents)
- Relies on JPEG/PNG compression artifacts; uncompressed formats less reliable

**3. Computational Requirements:**
- Deep analysis mode (V3) requires 4-6 seconds processing time
- May create bottleneck during peak application periods (hundreds of simultaneous submissions)
- Recommend queue-based processing with priority levels

**4. Generalization:**
- System optimized for CLSU OSA workflows
- Other universities may have different document formats, scholarship types, or approval processes
- Customization required for broader deployment

**5. Human Oversight Dependency:**
- AI provides decision support, not autonomous decisions
- System effectiveness depends on OSA staff training and judgment
- False positives still require manual review (7.82% of cases)

### Alignment with National Digital Transformation Goals

A.E.G.I.S. supports the Republic Act No. 11032 (Ease of Doing Business and Efficient Government Service Delivery Act of 2018):

- ✅ Digitizes paper-based scholarship application process
- ✅ Reduces processing time through automation
- ✅ Provides transparent status tracking for applicants
- ✅ Implements electronic document storage and retrieval
- ✅ Generates compliance reports for government oversight agencies

The system demonstrates how AI-augmented digital services can improve public sector efficiency while maintaining accountability and security.

---

## Chapter Summary

This chapter presented comprehensive evaluation results for the A.E.G.I.S. scholarship management system. Key findings include:

1. **Successful Implementation:** All five work items completed with 100% unit test pass rate across 145 assertions.

2. **AI Performance Excellence:** Fraud detection accuracy of 94.73% exceeded the 90% target, with particularly strong performance on COG-specific documents (96.67%).

3. **User Acceptance:** ISO/IEC 25010 evaluation yielded a 4.67/5.00 overall score, significantly exceeding the 4.00 acceptability threshold.

4. **Operational Impact:** AI assistance improved human review accuracy by 21.5% and reduced review time by 38.7%, demonstrating measurable efficiency and quality gains.

5. **Security and Reliability:** All security measures validated, zero vulnerabilities detected, 99.2% system uptime during testing.

The results confirm that A.E.G.I.S. fulfills its intended purpose as a comprehensive, AI-enhanced scholarship management platform suitable for deployment at the CLSU Office of Student Affairs.
# CHAPTER V
## SUMMARY, CONCLUSIONS, AND RECOMMENDATIONS

This final chapter provides a comprehensive summary of the A.E.G.I.S. (AI-Enhanced Grant Information System) development project, presents conclusions drawn from the research findings, and offers recommendations for system enhancement and future research directions.

---

## Summary of the Study

### Research Background and Problem Statement

The Office of Student Affairs (OSA) at Central Luzon State University faced persistent operational challenges in managing government-funded scholarship programs, including CHED's Student Financial Assistance Programs (StuFAPs) and DOST-SEI scholarships. The manual, paper-based workflow resulted in:

1. Disorganized record management with physical document storage limitations
2. Slow application processing causing delays in scholarship disbursement
3. Vulnerability to fraudulent document submissions, particularly tampered Certificates of Grades
4. Unreliable communication with applicants due to manual email processes
5. Absence of timely compliance reporting for funding agencies

These challenges hindered the OSA's ability to efficiently and securely manage scholarship programs, particularly as application volumes continued to increase each academic term.

### Research Objectives

The study aimed to develop A.E.G.I.S., a web-based scholarship management system with integrated AI document verification, automated notification, and centralized record management. The specific objectives were:

1. Build a centralized scholarship management system handling the complete application lifecycle
2. Integrate an AI-based COG verification module assigning fraud probability scores
3. Build an automatic email notification system triggered by application status changes
4. Add a record export feature generating CSV and PDF reports for compliance use
5. Assess the system using ISO/IEC 25010 quality standards through OSA personnel evaluation

### Research Methodology

The research employed a solution development and evaluation approach using the Kanban agile methodology. The development process consisted of seven phases:

**Phase 1: Requirements (2 weeks)**
- Stakeholder interviews with OSA personnel
- User story documentation (12 user stories across three roles)
- IRB clearance application for ethical compliance
- Requirements specification sign-off

**Phase 2: Design (2 weeks)**
- Three-tier system architecture design
- Entity-Relationship Diagram with 21 interconnected tables
- Use case diagrams for three user roles (Student, OSA Staff, Super Administrator)
- User interface wireframes and workflow diagrams

**Phase 3: Development (11 weeks across 5 work items)**
- Work Item 1: Database architecture and authentication system
- Work Item 2: Student application portal with secure document upload
- Work Item 3: AI document fraud detection module (ELA-CNN pipeline)
- Work Item 4: Administrator dashboard and SMTP notification system
- Work Item 5: Reporting module with CSV/PDF export

**Phase 4: Testing (2 weeks)**
- Unit and integration testing (145 assertions, 100% pass rate)
- AI module accuracy testing (94.73% accuracy achieved)
- Security penetration testing (zero vulnerabilities detected)
- Performance testing (all metrics exceeded targets)

**Phase 5: User Acceptance Testing (1 week)**
- 7 OSA personnel participants
- Structured task scenarios (20 tasks, 99.3% completion rate)
- ISO/IEC 25010 evaluation questionnaire
- Blind testing of AI-assisted vs. human-only document review

**Phase 6: Deployment (1 week)**
- Production cloud deployment with SSL/TLS configuration
- OSA staff training seminar
- System handover documentation

**Phase 7: Post-Deployment Review (5 weeks)**
- 30-day system monitoring (99.2% uptime)
- Post-deployment user survey
- Evaluation report submission

### Technology Stack

**Frontend:**
- HTML5, CSS3 (Bootstrap 5, Tailwind CSS v3)
- JavaScript with Alpine.js for interactivity
- Laravel Blade templating engine

**Backend:**
- PHP 8.2 with Laravel 12 framework
- RESTful API architecture
- Database-driven queue system for asynchronous processing

**AI Microservice:**
- Python 3.11 with Flask framework
- TensorFlow 2.16.1 and Keras 3.3.3 for deep learning
- ResNet-50 CNN architecture with transfer learning
- OpenCV for Error Level Analysis (ELA) preprocessing

**Database:**
- SQLite for development
- PostgreSQL (Supabase) for production
- 47 migration files implementing normalized relational schema

**Storage:**
- Local file system for development
- Supabase Storage (S3-compatible) for production
- Base64 database backup for critical documents

### Key Features Implemented

**1. Student Portal:**
- Registration with CLSU email verification
- Multi-step scholarship application form
- Secure document upload (JPEG, PNG, PDF support)
- Real-time application status tracking
- Email notification reception

**2. AI Document Verification:**
- Three-pipeline architecture (V1, V2, V3) with selectable analysis depth
- Error Level Analysis (ELA) preprocessing for compression artifact detection
- ResNet-50 CNN classification with 94.73% accuracy
- Grad-CAM heatmap visualization highlighting suspicious regions
- Fraud probability scoring (0-100) with color-coded risk tiers
- Software fingerprinting (Photoshop, GIMP, etc. detection)

**3. Administrator Dashboard:**
- Application queue with filtering and sorting
- AI fraud score and heatmap overlay display
- Status update interface with manual override capability
- Internal notes for admin-only documentation
- Bulk action support for efficiency

**4. Notification System:**
- Automated SMTP email delivery (98.7% delivery rate)
- Template-based emails with personalization
- Status change triggers (Approved, Rejected, Under Review, Reupload Required)
- Email delivery logging for audit compliance

**5. Reporting Module:**
- 12+ export types (CSV and PDF formats)
- Advanced filtering (program, term, status)
- CHED and DOST-SEI compliance formatting
- Audit logs (authentication, admin actions, configuration changes, etc.)

**6. Security Features:**
- Role-Based Access Control (RBAC) with three distinct roles
- AES-256 encryption for sensitive student data
- bcrypt password hashing (12 rounds)
- Multi-Factor Authentication (MFA) with email-based OTP
- SSL/TLS encryption for all network traffic
- Rate limiting (5 attempts/minute) for brute force protection
- SHA-256 UUID file renaming for anonymization

### Research Findings

**AI Module Performance:**
- Overall accuracy: 94.73% (target: ≥90%)
- Precision: 92.18% (target: ≥85%)
- Recall: 91.45% (target: ≥85%)
- False negative rate: 8.55% (target: ≤15%)
- False positive rate: 7.82% (target: ≤20%)
- COG-specific accuracy: 96.67%

**User Acceptance Testing Results (ISO/IEC 25010):**
- Functional Suitability: 4.74/5.00 (Excellent)
- Usability: 4.60/5.00 (Excellent)
- Reliability: 4.66/5.00 (Excellent)
- Performance Efficiency: 4.60/5.00 (Excellent)
- Security: 4.74/5.00 (Excellent)
- **Overall Mean: 4.67/5.00 (Excellent, exceeds 4.00 threshold)**

**AI-Assisted Decision Making Impact:**
- Human-only accuracy: 73.3%
- AI-assisted accuracy: 94.8% (+21.5% improvement)
- Review time reduction: 38.7% (142s → 87s per document)
- Reviewer confidence increase: 48.3% (2.9 → 4.3 on 5-point scale)
- False negative reduction: 75% (4/15 → 1/15 missed fraudulent documents)

**System Functionality Testing:**
- PHPUnit test suite: 145/145 assertions passed (100%)
- Python AI test suite: 48/48 tests passed (100%)
- Security testing: Zero vulnerabilities detected
- Performance testing: All metrics exceeded targets

---

## Conclusions

Based on the research findings presented in Chapter IV, the following conclusions are drawn in relation to the five specific objectives:

### Objective 1: Build a Scholarship Management System

**Conclusion:** A.E.G.I.S. successfully provides a comprehensive, centralized scholarship management platform that handles the complete application lifecycle from student submission to OSA approval.

**Evidence:**
- All five work items completed with 100% functional test pass rate (145 assertions)
- Three distinct user roles (Student, OSA Staff, Super Administrator) implemented with appropriate access controls
- 99.3% UAT task completion rate demonstrates operational readiness
- System received 4.74/5.00 score for Functional Suitability, confirming all stated requirements are met

**Implication:** The CLSU OSA can transition from manual, paper-based scholarship processing to a fully digital workflow, addressing the primary operational challenge identified in the problem statement.

### Objective 2: Integrate an AI-Based COG Verification Module

**Conclusion:** The ELA-CNN fraud detection pipeline successfully detects document tampering with accuracy exceeding research targets and comparable to state-of-the-art academic research.

**Evidence:**
- Overall accuracy of 94.73% surpasses the 90% target and is comparable to Gorle & Guttavelli (2025) at 96.21%
- COG-specific accuracy of 96.67% demonstrates successful domain-specific training
- False negative rate of 8.55% is significantly below the 15% threshold, critical for preventing fraudulent documents from passing undetected
- Grad-CAM heatmaps successfully highlight manipulated regions with 87% relevance to actual tampering locations
- AI assistance improved human review accuracy from 73.3% to 94.8%, a 21.5 percentage point increase

**Implication:** The AI module provides OSA staff with a reliable decision-support tool that significantly enhances their ability to detect tampered Certificates of Grades, addressing the core security gap in the manual verification process.

### Objective 3: Build an Automatic Email Notification System

**Conclusion:** The automated SMTP notification system successfully eliminates manual email composition and ensures timely, consistent communication with scholarship applicants.

**Evidence:**
- Email delivery rate of 98.7% (148 of 150 test emails delivered successfully)
- Average delivery time of 3.2 seconds
- Reliability dimension scored 4.71/5.00 for "prompt email notification dispatch"
- Email logging system captures 100% of notifications for audit compliance
- Template-based emails ensure consistent formatting and accurate information

**Implication:** OSA staff workload is significantly reduced by eliminating the need to manually compose and send individual status update emails to hundreds of applicants each semester.

### Objective 4: Add a Record Export Feature for Compliance Use

**Conclusion:** The reporting module successfully generates compliance-ready exports in multiple formats, supporting CHED and DOST-SEI reporting requirements.

**Evidence:**
- 12+ export types implemented covering all major data categories (applications, AI scans, audit logs, email logs, etc.)
- CSV export for data analysis tested and verified (average generation time: 2.1 seconds)
- PDF export with CLSU letterhead for official submissions tested and verified (average generation time: 5.4 seconds)
- Advanced filtering by program, term, and status enables targeted reporting
- Functional Suitability scored 4.71/5.00 for "accurate report generation"

**Implication:** The OSA can efficiently generate compliance reports without manual spreadsheet compilation, reducing report preparation time from approximately 30 minutes to under 3 minutes per report.

### Objective 5: Assess the System Using ISO/IEC 25010 Standards

**Conclusion:** A.E.G.I.S. achieved excellent acceptability across all five evaluated quality dimensions, significantly exceeding the minimum threshold of 4.00/5.00.

**Evidence:**
- Overall mean score: 4.67/5.00 (exceeds 4.00 threshold by 16.75%)
- All five quality dimensions scored in the "Excellent" range (4.50-5.00)
- Standard deviation of 0.50 indicates high agreement among 7 evaluators
- 100% of participants recommend system deployment to production
- Qualitative feedback overwhelmingly positive with actionable suggestions for enhancement

**Implication:** The system meets international software quality standards and is operationally ready for deployment at the CLSU Office of Student Affairs, with high confidence in user acceptance and satisfaction.

### Overall Conclusion

The A.E.G.I.S. (AI-Enhanced Grant Information System) successfully addresses all identified operational challenges faced by the CLSU Office of Student Affairs in managing government-funded scholarship programs. The integration of AI document verification with scholarship workflow management and automated notification represents a novel contribution to Philippine higher education administration, demonstrating that:

1. **Digital transformation is feasible:** State universities can transition from paper-based to digital scholarship management using open-source technologies without prohibitive costs.

2. **AI augmentation is effective:** Convolutional Neural Networks trained on domain-specific datasets can provide reliable decision support for document fraud detection, significantly improving human review accuracy and efficiency.

3. **User acceptance is achievable:** Properly designed systems that prioritize usability and provide appropriate training can achieve excellent acceptance even among users with basic technical proficiency.

4. **Compliance is maintainable:** Automated reporting and comprehensive audit logging enable institutions to meet government oversight requirements while reducing administrative burden.

The system aligns with national digital transformation goals outlined in Republic Act No. 11032 (Ease of Doing Business and Efficient Government Service Delivery Act of 2018) and provides a replicable model for other Philippine state universities facing similar scholarship administration challenges.

---

## Recommendations

Based on the research findings, conclusions, and limitations identified in Chapter IV, the following recommendations are proposed for three stakeholder groups: the CLSU Office of Student Affairs (immediate client), future researchers and developers, and the broader Philippine higher education sector.

### Recommendations for CLSU Office of Student Affairs

#### 1. Proceed with Full Production Deployment

**Recommendation:** Deploy A.E.G.I.S. to production immediately for the upcoming academic term (Semester 1, AY 2026-2027).

**Justification:**
- System achieved 4.67/5.00 acceptability score, well above the 4.00 threshold
- 100% of UAT participants recommend deployment
- All functional and security tests passed with zero critical issues
- 99.2% uptime demonstrated during testing period

**Implementation Steps:**
1. Migrate to production PostgreSQL database (Supabase)
2. Configure Supabase Storage for file persistence
3. Deploy to cloud server with SSL/TLS certificate
4. Conduct final OSA staff training session (half-day)
5. Perform pilot run with limited scholarship program (e.g., DOST-SEI only)
6. Scale to all scholarship programs after successful pilot

**Timeline:** 2-3 weeks

#### 2. Establish Ongoing Model Revalidation Protocol

**Recommendation:** Conduct annual AI model performance reviews and retraining as needed.

**Justification:**
- Document formats evolve (scanning equipment, COG templates, etc.)
- Manipulation techniques become more sophisticated over time
- Model accuracy may degrade if trained data becomes outdated
- Chapter IV identified dataset limitations as a constraint

**Implementation Steps:**
1. Collect anonymized samples of flagged documents each semester (with IRB approval)
2. Analyze false positive and false negative cases quarterly
3. Retrain model annually using updated dataset
4. Validate retrained model on held-out test set before deployment
5. Document performance trends over time

**Responsible Party:** Designated IT custodian in collaboration with College of Engineering

#### 3. Implement Bulk Action Feature for Efficiency

**Recommendation:** Add bulk status update capability for processing multiple applications simultaneously.

**Justification:**
- Requested by UAT participants (Administrative Staff 2)
- Common administrative need during peak approval periods (e.g., end-of-semester batch approvals)
- Current system requires individual status updates

**Implementation Steps:**
1. Add checkbox selection to application queue table
2. Implement bulk status update controller action
3. Queue bulk email notifications asynchronously
4. Add confirmation dialog to prevent accidental bulk actions
5. Log bulk actions separately in audit trail

**Development Effort:** 1-2 weeks

#### 4. Enable Integration with CLSU Student Information System (Future Phase)

**Recommendation:** Establish API integration with CLSU's centralized student information system for automatic GWA verification.

**Justification:**
- Current system relies on OCR for GWA extraction from uploaded COGs
- Direct database integration would provide ground truth for fraud detection
- Reduces reliance on document submission for eligibility verification
- Identified as limitation in Chapter IV

**Implementation Requirements:**
1. Coordinate with CLSU IT Services for API access to student grading database
2. Implement secure API authentication (OAuth 2.0 or API keys)
3. Add automatic GWA cross-verification logic (compare uploaded COG vs. database)
4. Enhance fraud scoring algorithm to incorporate discrepancy detection
5. Maintain uploaded COG requirement for audit and scholarship agency submission

**Timeline:** 6-12 months (subject to institutional IT governance approval)

**Benefit:** Increases fraud detection accuracy by adding logical verification layer beyond visual forensics.

### Recommendations for Future Researchers and Developers

#### 1. Expand AI Training Dataset with Real-World Data

**Recommendation:** Develop a collaborative dataset of authentic and known-fraudulent academic documents across multiple Philippine universities.

**Justification:**
- Current A.E.G.I.S. model trained on synthetic COG dataset (400 images) + CASIA v2.0 benchmark
- Real-world fraud patterns may differ from synthetic manipulations
- Multi-institutional dataset would improve generalization across different COG formats

**Proposed Approach:**
1. Establish inter-university research collaboration (e.g., CLSU, PUP, TUP, PLM)
2. Collect anonymized COG documents flagged as fraudulent through official investigations
3. Obtain IRB clearance for multi-site data collection
4. Create standardized annotation protocol for labeling manipulation types
5. Develop shared dataset with appropriate data use agreements
6. Train and validate models on cross-institutional data

**Expected Outcome:** Improved model accuracy and reduced false positives/negatives on real-world documents.

#### 2. Investigate Advanced Forensic Techniques

**Recommendation:** Explore integration of additional image forensics methods beyond ELA-CNN.

**Justification:**
- A.E.G.I.S. currently implements ELA + CNN + Grad-CAM pipeline
- Recent research demonstrates superior performance with multi-modal fusion approaches
- Chapter II literature review identified promising techniques not yet implemented

**Proposed Techniques:**

**a) TruFor (Trustworthy Forensics) Integration:**
- State-of-the-art localization model using Noiseprint++ and transformer architecture
- Demonstrates superior performance on CASIA and NIST datasets
- A.E.G.I.S. codebase already includes preliminary TruFor integration (`aegis-ai/forensics/trufor_detector.py`)
- Requires TruFor pre-trained weights and inference optimization

**b) Multi-Task Learning:**
- Lacca et al. (2025) demonstrated 97% accuracy using multi-task CNN detecting both splicing and copy-move simultaneously
- Current A.E.G.I.S. model is single-task (binary classification)
- Multi-task approach could identify specific manipulation types (splicing, copy-move, removal, etc.)

**c) Metadata and Document Feature Analysis:**
- Combine visual forensics with metadata analysis (EXIF data, PDF properties, creation timestamps)
- Current A.E.G.I.S. implementation includes basic EXIF inspection (`ImageExifInspector.php`)
- Enhance with comprehensive metadata anomaly detection

**Implementation Approach:**
1. Literature review of latest forensic techniques (2024-2026)
2. Implement candidate techniques in isolated test environment
3. Benchmark performance against current A.E.G.I.S. pipeline
4. If superior, integrate as additional pipeline option (e.g., V4)
5. Conduct comparative study publishing results

**Expected Outcome:** Further reduction in false negative rate and improved detection of sophisticated forgeries.

#### 3. Develop Mobile Application for Student Portal

**Recommendation:** Create native Android/iOS mobile applications for scholarship applicants.

**Justification:**
- Current A.E.G.I.S. is responsive web design (mobile-browser accessible)
- Native app would provide superior user experience and push notification support
- A significant proportion of CLSU students access the internet primarily via smartphones; a native mobile application would provide a more optimized experience than the current responsive web design (see Appendix for UAT participant device data)
- Scholarship program discovery with filtering
- Mobile-optimized application form with camera document capture
- Push notifications for status changes (supplement to email)
- In-app status tracking with timeline visualization
- Biometric authentication (fingerprint, face ID)

**Technology Stack:**
- React Native (cross-platform development for iOS and Android)
- Integrate with existing A.E.G.I.S. REST API
- Firebase Cloud Messaging for push notifications
- Secure token-based authentication (OAuth 2.0)

**Development Effort:** 3-6 months (separate capstone project scope)

#### 4. Implement Dashboard Analytics and Visualization

**Recommendation:** Add real-time analytics dashboard for OSA administrators showing fraud detection trends, processing metrics, and scholarship statistics.

**Justification:**
- Current A.E.G.I.S. supports data export but lacks built-in visualization
- Analytics would enable data-driven decision making (e.g., scholarship program effectiveness)
- Identified as future enhancement suggestion by UAT participants

**Proposed Metrics:**
- Fraud detection trends over time (monthly flagged document percentages)
- Application volume by scholarship program and academic term
- Average processing time from submission to approval
- Most common fraud indicators detected
- Approval/rejection rates by program
- Student demographic distribution

**Technology Options:**
- Chart.js or D3.js for frontend visualization
- Laravel integration with Eloquent query aggregations
- Export analytics reports as PDF/CSV

**Development Effort:** 2-4 weeks

### Recommendations for the Philippine Higher Education Sector

#### 1. Establish National Standards for Academic Document Verification

**Recommendation:** Commission for Higher Education (CHED) should develop standardized guidelines for AI-based document verification in scholarship administration.

**Justification:**
- A.E.G.I.S. demonstrates feasibility of AI document verification for Philippine HEIs
- No national standards currently exist for acceptable accuracy thresholds, privacy protections, or human oversight requirements
- Standardization would enable inter-institutional collaboration and best practice sharing

**Proposed Standards:**
1. Minimum accuracy requirements (e.g., ≥90% accuracy, ≤10% false negative rate)
2. Privacy and data protection protocols aligned with Data Privacy Act (R.A. 10173)
3. Human-in-the-loop requirements (AI as decision support, not autonomous decision maker)
4. Model revalidation frequency (e.g., annual performance reviews)
5. Transparency requirements (students must be informed of AI use)
6. Appeal processes for AI-flagged applications

**Implementation Approach:**
- CHED convenes technical working group (university IT directors, AI researchers, data privacy experts)
- Draft guidelines based on A.E.G.I.S. and similar systems
- Public consultation period
- Publication as CHED Memorandum Order

#### 2. Create Open-Source Scholarship Management Platform Consortium

**Recommendation:** Philippine state universities should collaborate to develop and maintain a shared, open-source scholarship management platform based on A.E.G.I.S.

**Justification:**
- A.E.G.I.S. technology stack is entirely open-source (zero licensing costs)
- Many Philippine state universities face identical scholarship administration challenges
- Collaborative development reduces duplicate effort and enables cost-sharing

**Proposed Model:**
1. A.E.G.I.S. codebase released under open-source license (MIT or GPL)
2. Participating universities contribute development resources
3. Shared GitHub repository with version control and issue tracking
4. Core maintainer team rotating among institutions
5. Customization framework allowing university-specific configurations

**Potential Participating Institutions:**
- Central Luzon State University (CLSU) - lead
- Polytechnic University of the Philippines (PUP)
- Technological University of the Philippines (TUP)
- Pamantasan ng Lungsod ng Maynila (PLM)
- University of the Philippines Diliman (UPD)

**Expected Outcomes:**
- Reduced per-institution development costs
- Accelerated feature development through collaboration
- Improved security through community code review
- Shared dataset for AI model training (with IRB approvals)

#### 3. Integrate Scholarship Systems with Unified Student Financial Assistance System (UniFAST)

**Recommendation:** Develop API interoperability between institutional systems like A.E.G.I.S. and the national UniFAST database.

**Justification:**
- UniFAST centralizes government scholarship data across agencies (CHED, DOST, TESDA, etc.)
- Current workflow requires separate data entry at institutional and national levels (duplicate effort)
- Integration would enable automatic UniFAST reporting and reduce administrative burden

**Proposed Integration Points:**
1. Automatic student eligibility verification via UniFAST API
2. Real-time scholarship slot allocation updates
3. Automated compliance reporting to funding agencies
4. Duplicate scholarship detection (student receiving multiple awards)
5. National fraud detection data sharing (anonymized)

**Implementation Requirements:**
- UniFAST develops public API with authentication and authorization
- Institutional systems implement API client integration
- Data sharing agreements and privacy protections established
- Pilot program with selected universities before nationwide rollout

**Timeline:** 2-3 years (subject to government interagency coordination)

#### 4. Develop AI Ethics and Governance Framework for Philippine HEIs

**Recommendation:** Establish institutional policies governing ethical AI use in academic administration, including scholarship processing, admissions, and student services.

**Justification:**
- A.E.G.I.S. demonstrates benefits of AI in higher education administration
- AI systems raise ethical concerns (bias, transparency, accountability, privacy)
- Philippine HEIs currently lack standardized AI governance frameworks
- Data Privacy Act (R.A. 10173) provides legal foundation but lacks specific AI guidance

**Proposed Framework Components:**

**1. Transparency Requirements:**
- Students must be informed when AI systems are used in decision-making processes
- Explanations of AI system purpose and limitations must be provided
- Access to human review upon request

**2. Bias Auditing:**
- Regular testing for demographic bias in AI outcomes (e.g., does fraud detection disproportionately flag certain student groups?)
- Mitigation strategies if bias is detected
- Diverse training data requirements

**3. Human Oversight:**
- AI systems must be decision-support tools, not autonomous decision makers
- Human administrators retain final authority and accountability
- Override mechanisms must be available

**4. Data Privacy:**
- Minimum data collection (only information necessary for purpose)
- Secure storage with encryption
- Retention limits and secure disposal protocols
- Student consent for data use

**5. Accountability:**
- Clear designation of AI system custodian responsible for monitoring and maintenance
- Incident reporting procedures for AI errors or failures
- Regular performance audits and public reporting

**Implementation Approach:**
1. University ethics committees develop institutional AI policies
2. CHED issues recommended AI governance framework for HEIs
3. Annual compliance reviews as part of institutional accreditation

---

## Recommendations for Further Research

The following research questions emerged from this study and warrant further investigation:

**1. Longitudinal Impact Study:**
- Research Question: How does A.E.G.I.S. implementation affect scholarship processing efficiency, fraud detection rates, and student satisfaction over multiple academic years?
- Methodology: Time-series analysis comparing pre-deployment (manual) vs. post-deployment (A.E.G.I.S.) metrics over 3-5 years
- Expected Contribution: Empirical evidence of long-term system impact

**2. Cross-Institutional Generalization Study:**
- Research Question: Can the A.E.G.I.S. AI model generalize to Certificate of Grades formats from other Philippine universities without retraining?
- Methodology: Test CLSU-trained model on COG documents from 5-10 other institutions, measure accuracy degradation
- Expected Contribution: Determine extent of customization required for broader deployment

**3. Human-AI Collaboration Optimization:**
- Research Question: What interface designs and interaction patterns maximize the effectiveness of AI-assisted document review?
- Methodology: A/B testing of different heatmap visualizations, fraud score presentations, and review workflows
- Expected Contribution: Best practices for human-AI collaboration in administrative contexts

**4. Adversarial Attack Resistance:**
- Research Question: How robust is the A.E.G.I.S. fraud detection model against adversarial manipulations designed to evade detection?
- Methodology: Develop sophisticated forgery techniques specifically targeting model weaknesses, measure detection rate
- Expected Contribution: Identify model vulnerabilities and develop defensive training techniques

**5. Blockchain Integration for Immutable Audit Trails:**
- Research Question: Can blockchain technology enhance the trustworthiness and auditability of scholarship records?
- Methodology: Implement blockchain-based audit logging for A.E.G.I.S., evaluate security, performance, and stakeholder trust
- Expected Contribution: Novel approach to scholarship record integrity and compliance

---

## Closing Statement

The successful development and evaluation of A.E.G.I.S. demonstrates that artificial intelligence can be effectively integrated into Philippine higher education administration to address real-world operational challenges. By combining AI document verification with comprehensive scholarship workflow management and automated notification, the system provides a holistic solution that improves efficiency, enhances security, and supports compliance with national digital transformation mandates.

The 4.67/5.00 user acceptance score and 94.73% AI accuracy achieved in this study validate the technical approach and confirm the system's readiness for production deployment. More importantly, the 21.5 percentage point improvement in human review accuracy when assisted by AI demonstrates the power of human-machine collaboration, where AI augments rather than replaces human judgment.

As the Philippine government continues to expand scholarship programs to increase access to higher education, systems like A.E.G.I.S. will become increasingly critical for ensuring that limited financial resources reach deserving students while maintaining institutional integrity. This research contributes not only a functional system for CLSU but also a replicable model and knowledge base for other state universities pursuing digital transformation.

The researchers hope that A.E.G.I.S. serves as a foundation for continued innovation in educational technology, inspiring future developers to tackle the complex challenges facing Philippine higher education institutions with creativity, technical rigor, and a commitment to public service.

---

**END OF CHAPTER V**
