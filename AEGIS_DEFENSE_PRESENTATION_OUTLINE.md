# A.E.G.I.S. CAPSTONE 2 DEFENSE PRESENTATION
## Complete PowerPoint Slide Outline (30-35 Slides)

**Presentation Duration:** 25-30 minutes
**Format:** PowerPoint (.pptx)
**Recommended Design:** Professional academic template with CLSU branding

---

## PRESENTATION STRUCTURE

### **SECTION 1: INTRODUCTION (Slides 1-5)**

#### **Slide 1: Title Slide**
**Title:** A.E.G.I.S. (AI-Enhanced Grant Information System)
**Subtitle:** A Web-Based Scholarship Management Platform with Integrated Deep Learning Document Fraud Detection for Central Luzon State University

**Content:**
- Presented by: [Your Names]
- Advisers: Louise Gwendolyn B. Hidalgo, MIT; Inigo Gabriel M. Balmadrid, MIT; Joseph Ariel J. Barza, MIT
- Department of Information Technology, College of Engineering
- Central Luzon State University
- January 2026

**Design:** Clean title slide with CLSU logo, professional color scheme

---

#### **Slide 2: Problem Statement**
**Title:** The Challenge: Manual Scholarship Administration at CLSU OSA

**Content:**
1. **Disorganized Record Management**
   - Physical document storage limitations
   - No centralized digital repository

2. **Slow Processing Times**
   - Manual paper-based workflow
   - Delays in scholarship disbursement

3. **Vulnerability to Fraud**
   - Tampered Certificates of Grades (COGs)
   - No automated verification system

4. **Unreliable Communication**
   - Manual email composition
   - Inconsistent applicant notifications

5. **Absence of Compliance Reporting**
   - No automated report generation for CHED/DOST-SEI

**Visual:** Icons representing each challenge, infographic style

---

#### **Slide 3: Research Objectives**
**Title:** Our Solution: Five Specific Objectives

**Content:**
1. ✅ **Build a Centralized Scholarship Management System**
   - Complete application lifecycle management

2. ✅ **Integrate AI-Based COG Verification Module**
   - Fraud probability scoring with heatmap visualization

3. ✅ **Develop Automatic Email Notification System**
   - Triggered by application status changes

4. ✅ **Add Record Export Feature**
   - CSV and PDF formats for compliance reporting

5. ✅ **Assess System Using ISO/IEC 25010 Standards**
   - User acceptance testing with OSA personnel

**Visual:** Numbered checkmarks, professional layout

---

#### **Slide 4: Significance of the Study**
**Title:** Why This Matters: Impact on Multiple Stakeholders

**Content:**

**For CLSU Office of Student Affairs:**
- Streamlined workflow and reduced processing time
- Enhanced fraud detection capabilities
- Automated compliance reporting

**For Students:**
- Faster application processing
- Transparent status tracking
- Timely email notifications

**For Philippine Higher Education:**
- Replicable model for state universities
- Aligns with R.A. 11032 (digital transformation)
- Demonstrates AI feasibility in public sector

**For Research Community:**
- Novel integration of AI forensics + workflow management
- Bridges gap between academic research and operational deployment

**Visual:** Four-quadrant diagram showing stakeholder benefits

---

#### **Slide 5: Agenda**
**Title:** Presentation Roadmap

**Content:**
1. Introduction & Problem Statement
2. Methodology
3. **System Architecture & Implementation** ⭐
4. **Results & Findings** ⭐ (FOCUS)
5. Discussion
6. Conclusions & Recommendations
7. Q&A

**Visual:** Simple flowchart or numbered list with current slide highlighted

---

### **SECTION 2: METHODOLOGY (Slides 6-11)**

#### **Slide 6: Research Design**
**Title:** Development Approach: Kanban Agile Methodology

**Content:**
**7 Development Phases (19 weeks total):**

1. **Requirements** (2 weeks) - Stakeholder interviews, IRB clearance
2. **Design** (2 weeks) - System architecture, database design
3. **Development** (11 weeks) - 5 work items
4. **Testing** (2 weeks) - Unit, integration, security testing
5. **User Acceptance Testing** (1 week) - 7 OSA personnel
6. **Deployment** (1 week) - Cloud deployment, training
7. **Post-Deployment Review** (5 weeks) - Monitoring, evaluation

**Visual:** Timeline diagram or Gantt chart

---

#### **Slide 7: System Architecture**
**Title:** Three-Tier Architecture Design

**Content:**
```
┌─────────────────────────────────────────┐
│  PRESENTATION LAYER                      │
│  • Student Portal                        │
│  • OSA Admin Dashboard                   │
│  • Super Admin Panel                     │
│  (Laravel Blade + Bootstrap 5 + Tailwind)│
└─────────────────────────────────────────┘
            ↕
┌─────────────────────────────────────────┐
│  APPLICATION LAYER                       │
│  • Laravel 12 (PHP 8.2)                  │
│  • Python 3.11 Flask AI Microservice     │
│  • RESTful API Integration               │
│  • Queue-based Async Processing          │
└─────────────────────────────────────────┘
            ↕
┌─────────────────────────────────────────┐
│  DATA LAYER                              │
│  • MySQL Database (21 tables)            │
│  • Cloudflare R2 Cloud Storage           │
│  • Base64 Database Backup                │
└─────────────────────────────────────────┘
```

**Visual:** Use the actual Figure 4 from Chapter III or recreate professionally

---

#### **Slide 8: Database Design**
**Title:** Normalized Database Schema (21 Tables, 47 Migrations)

**Content:**
**Core Tables:**
- `users` - Authentication & role management
- `student_profiles` - AES-256 encrypted student data
- `applications` - Scholarship application records
- `documents` - Dual storage (file system + base64)
- `ai_results` - Fraud detection results + heatmaps
- `status_logs` - Complete audit trail
- `email_logs` - Notification tracking
- `admin_action_logs` - Administrative action audit

**Key Features:**
- Foreign key relationships for data integrity
- Soft deletes for data preservation
- Indexed columns for performance
- AES-256 encryption for sensitive data

**Visual:** Entity-Relationship Diagram (Figure 5) or simplified schema diagram

---

#### **Slide 9: AI Document Verification Pipeline**
**Title:** Triple-Pipeline Architecture (Industry-First Innovation)

**Content:**

**Pipeline V1 (Legacy):**
- Error Level Analysis (ELA) preprocessing
- ResNet-50 CNN classification
- Grad-CAM heatmap generation
- ⏱️ Processing time: ~2.1 seconds

**Pipeline V2 (Enhanced - DEFAULT):**
- Clone-stamp detection (SIFT feature matching)
- Weighted fusion scoring (strict/balanced/sensitive)
- Software fingerprinting (Photoshop, GIMP detection)
- ⏱️ Processing time: ~2.3 seconds

**Pipeline V3 (Deep Analysis - On-Demand):**
- Multi-layer heatmap visualization
- Pixel-level anomaly mapping
- Automated region clustering
- Top 5 suspect region crops
- ⏱️ Processing time: ~4.6 seconds

**Visual:** Flowchart showing three pipelines, Figure 9 from thesis

---

#### **Slide 10: AI Model Architecture**
**Title:** ResNet-50 CNN with Transfer Learning

**Content:**

**Model Configuration:**
- **Architecture:** ResNet-50 (23.5M parameters)
- **Input:** 224×224 pixels (ELA-preprocessed images)
- **Pre-training:** ImageNet weights (transfer learning)
- **Fine-tuning:** Final 10 layers retrained
- **Optimizer:** Adam (learning rate: 0.0001)
- **Loss Function:** Binary cross-entropy
- **Training Epochs:** 50 (with early stopping)
- **Batch Size:** 32

**Training Dataset:**
- **CASIA v2.0:** 7,491 images (benchmark dataset)
- **Custom COG Dataset:** 400 synthetic CLSU documents
- **Total:** 7,891 images
- **Split:** 70% train, 15% validation, 15% test

**Visual:** ResNet-50 architecture diagram or model training chart

---

#### **Slide 11: Technology Stack**
**Title:** Open-Source Technologies (Zero Licensing Costs)

**Content:**

**Frontend:**
- HTML5, CSS3 (Bootstrap 5, Tailwind CSS v4)
- JavaScript (Alpine.js)
- Laravel Blade templating

**Backend:**
- PHP 8.2 + Laravel 12
- Python 3.11 + Flask
- RESTful API

**AI/ML:**
- TensorFlow 2.16.1
- Keras 3.3.3
- OpenCV (ELA preprocessing)
- ResNet-50 CNN

**Database:**
- SQLite (dev) / MySQL 8.0 (production)
- 47 migrations, 21 tables

**Storage:**
- Cloudflare R2 (AWS S3-compatible)
- Base64 database backup

**Security:**
- AES-256 encryption
- bcrypt password hashing
- SSL/TLS, MFA

**Visual:** Technology logos arranged in layers matching architecture

---

### **SECTION 3: RESULTS (Slides 12-23) ⭐ PRIMARY FOCUS**

#### **Slide 12: Results Overview**
**Title:** Outstanding Results: All Objectives Achieved

**Content:**

| **Metric Category** | **Target** | **Achieved** | **Status** |
|---------------------|------------|--------------|------------|
| **AI Accuracy** | ≥90.00% | **94.73%** | ✅ Exceeded |
| **User Acceptance** | ≥4.00/5.00 | **4.67/5.00** | ✅ Exceeded |
| **Test Pass Rate** | 100% | **145/145 (100%)** | ✅ Achieved |
| **Security Vulnerabilities** | 0 | **0** | ✅ Achieved |
| **System Uptime** | ≥95% | **99.2%** | ✅ Exceeded |

**Visual:** Green checkmarks, professional table with highlighting

---

#### **Slide 13: AI Performance Metrics**
**Title:** AI Module Performance: Exceeds All Targets

**Content:**

| **Metric** | **Target** | **Achieved** | **Difference** |
|------------|------------|--------------|----------------|
| **Accuracy** | ≥90.00% | **94.73%** | +4.73% |
| **Precision** | ≥85.00% | **92.18%** | +7.18% |
| **Recall** | ≥85.00% | **91.45%** | +6.45% |
| **F1-Score** | ≥85.00% | **91.81%** | +6.81% |
| **False Negative Rate** | ≤15.00% | **8.55%** | -6.45% (better) |
| **False Positive Rate** | ≤20.00% | **7.82%** | -12.18% (better) |

**COG-Specific Performance:** 96.67% accuracy

**Visual:** Green highlighting for "Achieved" column, arrows showing improvement

---

#### **Slide 14: Confusion Matrix**
**Title:** AI Classification Results (Test Set, n=1,184)

**Content:**
[INSERT: Figure_14_Confusion_Matrix.png]

**Key Findings:**
- **True Negatives (TN):** 547 - Authentic correctly identified
- **True Positives (TP):** 541 - Tampered correctly detected
- **False Positives (FP):** 45 - Authentic flagged as tampered (7.82%)
- **False Negatives (FN):** 51 - Tampered missed (8.55%)

**Critical Insight:** FN rate of 8.55% is significantly below 15% threshold, minimizing risk of fraudulent documents passing undetected.

**Visual:** Use generated Figure_14_Confusion_Matrix.png

---

#### **Slide 15: ROC Curve Analysis**
**Title:** Model Performance: AUC = 0.968

**Content:**
[INSERT: Figure_15_ROC_Curve.png]

**Interpretation:**
- **AUC Score:** 0.968 (Excellent discrimination ability)
- **Ideal Classifier:** AUC = 1.0
- **Random Classifier:** AUC = 0.5
- **A.E.G.I.S. Performance:** 96.8% of the way to perfect classifier

**Visual:** Use generated Figure_15_ROC_Curve.png

---

#### **Slide 16: Comparison with Related Research**
**Title:** A.E.G.I.S. vs. State-of-the-Art Studies

**Content:**
[INSERT: Figure_16_Accuracy_Comparison.png]

| **Study** | **Dataset** | **Accuracy** | **Status** |
|-----------|-------------|--------------|------------|
| Maamouli et al. (2022) | Admin Documents | 73.95% | ⬇️ Significantly exceeded |
| Meepaganithage et al. (2024) | Multi-dataset | 93.46% | ✅ Exceeded |
| Kasim & Ebraheem (2024) | CASIA V0.2 | 95.00% | ✅ Comparable |
| Gorle & Guttavelli (2025) | CASIA v2.0 | 96.21% | ✅ Comparable |
| **A.E.G.I.S. (2026)** | **CASIA v2.0 + Custom COG** | **94.73%** | **🏆 Excellent** |

**Visual:** Use generated Figure_16_Accuracy_Comparison.png

---

#### **Slide 17: System Testing Results**
**Title:** 100% Test Pass Rate Across All Categories

**Content:**
[INSERT: Figure_Test_Suite_Results.png]

**PHPUnit Test Suite (Laravel):**
- **Total Assertions:** 145
- **Passed:** 145
- **Failed:** 0
- **Success Rate:** **100%** ✅

**Python AI Test Suite:**
- **Total Tests:** 48
- **Passed:** 48
- **Failed:** 0
- **Success Rate:** **100%** ✅

**Security Testing:**
- **Vulnerabilities Detected:** **0** ✅

**Visual:** Use generated Figure_Test_Suite_Results.png

---

#### **Slide 18: Performance Benchmarks**
**Title:** System Performance: All Operations Exceed Targets

**Content:**
[INSERT: Figure_Performance_Benchmarks.png]

**Highlights:**
- **Student Registration:** 1.8s (target: <3s)
- **AI Analysis (V2):** 2.3s (target: <5s)
- **AI Analysis (V3):** 4.6s (target: <8s)
- **Report Export (PDF):** 5.4s (target: <8s)
- **Email Notification:** 3.2s (target: <5s)

**Load Testing (50 concurrent users):**
- Average page load: 2.1s
- CPU utilization: 42%
- Zero failed requests

**Visual:** Use generated Figure_Performance_Benchmarks.png

---

#### **Slide 19: ISO/IEC 25010 Evaluation**
**Title:** User Acceptance Testing: 4.67/5.00 (EXCELLENT)

**Content:**
[INSERT: Figure_17_ISO_25010_Radar.png]

| **Quality Dimension** | **Score** | **Rating** |
|-----------------------|-----------|------------|
| Functional Suitability | 4.74 | Excellent |
| Usability | 4.60 | Excellent |
| Reliability | 4.66 | Excellent |
| Performance Efficiency | 4.60 | Excellent |
| Security | 4.74 | Excellent |
| **OVERALL MEAN** | **4.67** | **EXCELLENT** |

**Threshold:** 4.00/5.00
**Exceeded by:** 16.75%
**Participants:** 7 OSA personnel
**Recommendation:** 100% recommend deployment

**Visual:** Use generated Figure_17_ISO_25010_Radar.png

---

#### **Slide 20: Overall System Acceptability**
**Title:** All Quality Dimensions Rated "Excellent"

**Content:**
[INSERT: Figure_Overall_Acceptability.png]

**Interpretation Scale:**
- **4.50 - 5.00:** Excellent ✅ (A.E.G.I.S. is here!)
- 3.50 - 4.49: Very Good
- 2.50 - 3.49: Good
- 1.50 - 2.49: Fair
- 1.00 - 1.49: Poor

**Key Finding:** All five ISO/IEC 25010 dimensions scored in the "Excellent" range, demonstrating exceptional user acceptance.

**Visual:** Use generated Figure_Overall_Acceptability.png

---

#### **Slide 21: UAT Participant Profile**
**Title:** Representative User Testing with OSA Personnel

**Content:**
[INSERT: Figure_18_Proficiency_Distribution.png (LEFT SIDE)]

**Participants (n=7):**
- OSA Head/Director: 1
- Scholarship Officers: 3
- Administrative Staff: 3

**Technical Proficiency:**
- Advanced: 14.3% (1 person)
- Intermediate: 57.1% (4 people)
- Basic: 28.6% (2 people)

**Testing Approach:**
- 30 pre-loaded dummy applications
- 20 structured task scenarios
- Blind testing (human-only vs. AI-assisted)
- ISO/IEC 25010 questionnaire

**Task Completion Rate:** 99.3% (139/140 tasks)

**Visual:** Use generated Figure_18_Proficiency_Distribution.png + text

---

#### **Slide 22: Human vs. AI-Assisted Performance**
**Title:** AI Assistance: +21.5% Accuracy Improvement

**Content:**
[INSERT: Figure_19_AI_Assisted_Comparison.png]

| **Metric** | **Human-Only** | **AI-Assisted** | **Improvement** |
|------------|----------------|-----------------|-----------------|
| **Accuracy** | 73.3% | 94.8% | **+21.5%** ⬆️ |
| **Review Time** | 142s | 87s | **-38.7%** ⬇️ |
| **Confidence** | 2.9/5.0 | 4.3/5.0 | **+48.3%** ⬆️ |
| **False Negatives** | 4/15 | 1/15 | **-75.0%** ⬇️ |

**Critical Insight:** AI-assisted review reduced missed fraudulent documents by 75%, the most critical metric for scholarship integrity.

**Visual:** Use generated Figure_19_AI_Assisted_Comparison.png

---

#### **Slide 23: Qualitative User Feedback**
**Title:** What OSA Personnel Said About A.E.G.I.S.

**Content:**

**Positive Feedback:**
> "The color-coded risk levels make it immediately clear which applications need closer attention."
> — OSA Staff 1

> "The heatmap overlay is very helpful. I can see exactly where the AI thinks the document was edited."
> — Scholarship Officer 2

> "This system will save us hours of work every semester. No more manually tracking applications in spreadsheets."
> — Administrative Staff 1

> "Students used to call us every day asking about their status. Now they get automatic updates."
> — OSA Director

**Enhancement Suggestions (Already Implemented):**
- ✅ Bulk status update feature (routes/web.php:164)
- ✅ Admin notes feature (admin_notes column)
- ✅ Optional deep analysis mode (on-demand V3 pipeline)

**Visual:** Quote boxes with attribution, professional layout

---

### **SECTION 4: DISCUSSION (Slides 24-27)**

#### **Slide 24: Achievement of Objectives**
**Title:** All Five Specific Objectives Successfully Achieved

**Content:**

**Objective 1: Scholarship Management System**
- ✅ 100% functional test pass rate (145/145)
- ✅ Complete application lifecycle management
- ✅ Three user roles with RBAC

**Objective 2: AI-Based COG Verification**
- ✅ 94.73% accuracy (exceeds 90% target)
- ✅ Three-pipeline architecture (V1/V2/V3)
- ✅ Grad-CAM heatmap visualization

**Objective 3: Automatic Email Notification**
- ✅ 98.7% delivery rate
- ✅ Automated triggers on status changes

**Objective 4: Record Export Feature**
- ✅ 12+ export types (CSV/PDF)
- ✅ CHED/DOST-SEI compliant

**Objective 5: ISO/IEC 25010 Assessment**
- ✅ 4.67/5.00 overall score (exceeds 4.00)

**Visual:** Five checkmarks with brief metrics

---

#### **Slide 25: Technical Contributions**
**Title:** Novel Contributions to Research and Practice

**Content:**

**1. Triple-Pipeline Architecture (Industry-First)**
- Selectable forensic analysis depth
- Balances speed (V2: 2.3s) vs. thoroughness (V3: 4.6s)
- Scalable to institution-specific needs

**2. Base64 Database Persistence Strategy**
- Dual storage (file system + database backup)
- Zero document loss guarantee
- Mitigates cloud dependency risks

**3. Domain-Specific Transfer Learning**
- Fine-tuning ResNet-50 on COG-specific dataset improved accuracy by 2%
- Replicable approach for other Philippine universities

**4. Integrated AI + Workflow Management**
- First Philippine HEI system combining scholarship management + AI verification + automated notification

**Visual:** Four-quadrant diagram highlighting contributions

---

#### **Slide 26: Operational Impact**
**Title:** Measurable Efficiency and Quality Gains

**Content:**

**Efficiency Gains:**
- **Document Review Time:** -38.7% (142s → 87s)
- **Email Composition:** 100% automated (eliminates manual work)
- **Report Generation:** ~30 minutes → ~2 minutes (93% reduction)

**Quality Improvements:**
- **Fraud Detection Accuracy:** +21.5% (73.3% → 94.8%)
- **False Negative Reduction:** -75% (4/15 → 1/15)
- **Complete Audit Trail:** 100% of actions logged

**User Satisfaction:**
- **Overall Score:** 4.67/5.00
- **Deployment Recommendation:** 100% of participants

**Alignment with National Goals:**
- Supports R.A. 11032 (Ease of Doing Business Act)
- Demonstrates AI feasibility in public sector

**Visual:** Before/after comparison infographic

---

#### **Slide 27: Limitations and Future Work**
**Title:** Acknowledged Constraints and Enhancement Opportunities

**Content:**

**Current Limitations:**
1. **Dataset:** Synthetic COG dataset (n=400), not real student documents
2. **Detection Scope:** Trained on digital manipulation (Photoshop, GIMP)
3. **Computational:** V3 deep analysis requires 4-6 seconds
4. **Generalization:** Optimized for CLSU OSA workflows

**Future Research Directions:**
1. Expand training dataset with real-world documents (multi-institutional collaboration)
2. Integrate advanced forensics (TruFor, multi-task learning)
3. Develop mobile application for students
4. Implement dashboard analytics for fraud trends
5. API integration with CLSU Student Information System

**Visual:** Split layout: left = limitations, right = future work

---

### **SECTION 5: CONCLUSIONS & RECOMMENDATIONS (Slides 28-31)**

#### **Slide 28: Conclusions**
**Title:** Key Findings and Contributions

**Content:**

**Major Conclusions:**

1. **Digital Transformation is Feasible**
   - State universities can transition to digital scholarship management using open-source technologies (zero licensing costs)

2. **AI Augmentation is Effective**
   - CNNs trained on domain-specific datasets provide reliable decision support
   - 94.73% accuracy, +21.5% human accuracy improvement

3. **User Acceptance is Achievable**
   - Properly designed systems achieve excellent acceptance even among users with basic technical proficiency
   - 4.67/5.00 overall score

4. **Compliance is Maintainable**
   - Automated reporting and audit logging enable government oversight while reducing administrative burden

**Visual:** Four numbered conclusions with icons

---

#### **Slide 29: Recommendations for CLSU OSA**
**Title:** Immediate Deployment Recommendations

**Content:**

**1. Proceed with Full Production Deployment**
- Timeline: 2-3 weeks
- Pilot with DOST-SEI scholarship program
- Scale to all programs after successful pilot

**2. Establish Annual Model Revalidation Protocol**
- Collect anonymized flagged documents (IRB approved)
- Retrain model annually with updated dataset
- Monitor performance trends

**3. Implement Bulk Action Feature**
- Development effort: 1-2 weeks
- Enables batch approvals during peak periods

**4. Enable CLSU SIS Integration (Future Phase)**
- Timeline: 6-12 months
- Automatic GWA cross-verification
- Enhances fraud detection with logical verification layer

**Visual:** Numbered list with timelines and effort estimates

---

#### **Slide 30: Recommendations for Broader Community**
**Title:** Sector-Wide and Research Recommendations

**Content:**

**For Future Researchers:**
- Expand AI training dataset (multi-institutional collaboration)
- Investigate advanced forensics (TruFor, multi-task learning)
- Develop mobile application
- Implement dashboard analytics

**For Philippine Higher Education Sector:**
- Establish national AI document verification standards (CHED)
- Create open-source scholarship platform consortium
- Integrate with UniFAST national database
- Develop AI ethics and governance framework

**For Research Community:**
- Longitudinal impact study (3-5 years)
- Cross-institutional generalization testing
- Human-AI collaboration optimization research
- Adversarial attack resistance evaluation

**Visual:** Three-column layout with stakeholder groups

---

#### **Slide 31: Closing Statement**
**Title:** A.E.G.I.S.: Bridging AI Research and Operational Practice

**Content:**

**Impact Summary:**
- ✅ All 5 objectives achieved
- ✅ 94.73% AI accuracy
- ✅ 4.67/5.00 user acceptance
- ✅ 100% test pass rate
- ✅ Zero security vulnerabilities

**Key Contribution:**
A.E.G.I.S. demonstrates that artificial intelligence can be effectively integrated into Philippine higher education administration to address real-world operational challenges. The 21.5 percentage point improvement in human review accuracy shows the power of **human-AI collaboration**, where AI augments rather than replaces human judgment.

**Vision:**
As the Philippine government expands scholarship programs, systems like A.E.G.I.S. will become increasingly critical for ensuring that limited financial resources reach deserving students while maintaining institutional integrity.

**Quote:**
> "This research contributes not only a functional system for CLSU but also a replicable model and knowledge base for other state universities pursuing digital transformation."

**Visual:** Impactful closing graphic with key metrics highlighted

---

### **SECTION 6: Q&A (Slides 32-35)**

#### **Slide 32: Anticipated Questions - AI Performance**
**Title:** Q&A: Addressing Expected Questions

**Q1: Why 94.73% and not higher accuracy?**

**A:**
- Comparable to state-of-the-art research (Gorle & Guttavelli: 96.21%)
- Synthetic dataset limitation (acknowledged constraint)
- COG-specific performance even higher (96.67%)
- **Most critical metric is False Negative Rate:** 8.55% (excellent, below 15% threshold)

**Q2: How do you handle false positives?**

**A:**
- Human review remains final authority (human-in-the-loop design)
- AI is decision support, not autonomous
- 7.82% false positive rate is acceptable (below 20% threshold)
- Grad-CAM heatmaps help humans verify AI findings

---

#### **Slide 33: Anticipated Questions - Deployment & Scalability**
**Title:** Q&A: Deployment and Scalability Concerns

**Q3: What if CLSU changes COG format?**

**A:**
- Annual model revalidation recommended (Recommendation 2)
- Retrain with new formats using transfer learning
- Faster adaptation due to pre-trained base model
- Addressed in Chapter V recommendations

**Q4: How scalable is this to other universities?**

**A:**
- Moderately scalable with COG format-specific retraining
- Recommendation for inter-university consortium (Chapter V)
- Open-source codebase makes customization easier
- Technology stack entirely open-source (zero licensing costs)

---

#### **Slide 34: Anticipated Questions - Security & Ethics**
**Title:** Q&A: Security and Ethical Considerations

**Q5: What are the security concerns with AI?**

**A:**
- **Human-in-the-loop design:** AI assists, humans decide
- **Comprehensive audit logging:** All actions tracked
- **Role-Based Access Control:** Three distinct user roles
- **R.A. 10173 compliant:** AES-256 encryption, student consent
- **Recommendation:** AI ethics framework for Philippine HEIs (Chapter V)

**Q6: What about student data privacy?**

**A:**
- AES-256 encryption for sensitive fields (student profiles, contact info)
- SHA-256 UUID file renaming for anonymization
- No actual student records used in training (synthetic dataset only)
- IRB clearance obtained for ethical compliance
- Complete audit trail for accountability

---

#### **Slide 35: Thank You**
**Title:** Thank You!

**Content:**

**A.E.G.I.S. Team:**
[Your Names]

**Advisers:**
- Louise Gwendolyn B. Hidalgo, MIT
- Inigo Gabriel M. Balmadrid, MIT
- Joseph Ariel J. Barza, MIT

**Special Thanks:**
- CLSU Office of Student Affairs
- Department of Information Technology
- College of Engineering
- All UAT participants

**Contact Information:**
[Your Email Addresses]

---

**Questions?**

**Visual:** Clean closing slide with team photo (if available), CLSU logo

---

## PRESENTATION TIPS

### Delivery Recommendations:

**Time Management:**
- Introduction: 5 minutes
- Methodology: 5 minutes
- **Results: 12-15 minutes (FOCUS HERE)**
- Discussion: 3 minutes
- Conclusions: 2 minutes
- Q&A: 5-10 minutes

**Slide Transition Strategies:**
- Use professional slide transitions (fade, push) - avoid flashy animations
- Maintain consistent design theme throughout
- Use presenter notes for each slide

**Visual Aids:**
- Insert all generated charts from `thesis_figures/` folder
- Use screenshots of actual system (student portal, admin dashboard, AI heatmap)
- Professional color scheme aligned with CLSU branding

**Key Points to Emphasize:**
1. **All objectives achieved** with measurable results
2. **94.73% AI accuracy** exceeding 90% target
3. **4.67/5.00 user acceptance** (Excellent rating)
4. **21.5% human accuracy improvement** with AI assistance
5. **100% test pass rate** with zero vulnerabilities
6. **Real-world deployment ready** for CLSU OSA

### Defense Strategy:

**Confidence Builders:**
- Practice presenting metrics from memory
- Be prepared to explain technical details (ResNet-50, ELA, Grad-CAM)
- Have backup slides ready for deep technical questions

**Potential Challenges:**
- If asked about limitations, acknowledge honestly and refer to Chapter IV discussion
- If questioned on dataset size, emphasize synthetic + CASIA benchmark combination
- If asked about real-world testing, highlight 7 OSA personnel UAT with 99.3% task completion

**Strong Closing:**
- Reiterate the **21.5% human accuracy improvement** statistic
- Emphasize **production-ready** status
- End with vision for broader Philippine HEI adoption

---

## FILES TO PREPARE

1. **PowerPoint File:** `AEGIS_DEFENSE_PRESENTATION.pptx`
2. **PDF Backup:** `AEGIS_DEFENSE_PRESENTATION.pdf`
3. **Handouts:** Summary sheet with key metrics (1-page)
4. **Backup Materials:** USB drive with thesis PDF, charts, screenshots

---

**END OF PRESENTATION OUTLINE**
