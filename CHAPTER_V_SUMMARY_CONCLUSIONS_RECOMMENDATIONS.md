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
- HTML5, CSS3 (Bootstrap 5, Tailwind CSS v4)
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
- MySQL 8.0-ready for production
- 47 migration files implementing normalized relational schema

**Storage:**
- Local file system for development
- Cloudflare R2 (AWS S3-compatible) for production
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
1. Migrate to production MySQL database
2. Configure Cloudflare R2 cloud storage
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
- 73% of CLSU students access internet primarily via smartphones (estimated)
- Identified as future enhancement suggestion by UAT participants

**Proposed Features:**
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
