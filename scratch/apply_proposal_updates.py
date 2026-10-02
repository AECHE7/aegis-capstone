# -*- coding: utf-8 -*-
"""
Update all testing and evaluation form generators and markdown documents
to 100% reflect the proposed system specifications in docs/edited-AEGIS.docx:

1. Title:
   A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS
2. Proponents:
   John Andrei Carillo, Noriel S. Gadiano, Joshua A. Razon
3. Adviser:
   Louise Gwendolyn B. Hidalgo
4. Department & College:
   Department of Information Technology, College of Engineering, Central Luzon State University
5. Client:
   Office of Student Affairs (CLSU OSA)
6. Modules:
   - Centralized Scholarship Management System (RBAC for Student, OSA Staff, Super Admin)
   - AI Document Forensics Module (Error Level Analysis Q=95 + CNN ResNet-50 binary classifier, Fraud Probability Score 0-100%, 3 risk tiers: Low 0-39%, Moderate 40-69%, High 70-100%, Grad-CAM heatmap, Decision-Support Only)
   - Automated Notification System (SMTP email alerts on submission, status changes, announcements + real-time in-app bell notification drawer)
   - Centralized Record Management & Compliance Export (CSV and PDF for CHED StuFAPs and DOST-SEI compliance audits, 1-page official Applicant Evaluation Form)
   - ISO/IEC 25010 Evaluation (5-point Likert scale with 4.00 minimum acceptability threshold)
"""

import os
import re
import subprocess
import sys

# 1. Update scratch/generate_standardized_role_docs.py
print("Updating scratch/generate_standardized_role_docs.py...")
with open("scratch/generate_standardized_role_docs.py", "r", encoding="utf-8") as f:
    code = f.read()

# Replace title
old_title_pattern = r'Project / System Title: A\.E\.G\.I\.S\. \(Automated Evaluation & Grade Integrity System\)'
new_title = 'Project / System Title: A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS'
code = re.sub(old_title_pattern, new_title, code)

old_title_simple = r'"Project / System Title", "A\.E\.G\.I\.S\. \(Automated Evaluation & Grade Integrity System\)"'
new_title_simple = '"Project / System Title", "A.E.G.I.S: AI-ENHANCED GRANT INFORMATION SYSTEM WITH DOCUMENT FORENSICS AND AUTOMATED NOTIFICATION FOR THE OFFICE OF STUDENT AFFAIRS"'
code = re.sub(old_title_simple, new_title_simple, code)

# Replace researchers
old_researchers = r'JOSHUA RAZON / NORIEL GADIANO / JOHN ANDREI CARILLO II'
new_researchers = 'JOHN ANDREI CARILLO / NORIEL S. GADIANO / JOSHUA A. RAZON'
code = re.sub(old_researchers, new_researchers, code)

with open("scratch/generate_standardized_role_docs.py", "w", encoding="utf-8") as f:
    f.write(code)
print("Updated scratch/generate_standardized_role_docs.py successfully.")


# 2. Update scratch/generate_it_expert_standardized_doc.py
print("Updating scratch/generate_it_expert_standardized_doc.py...")
with open("scratch/generate_it_expert_standardized_doc.py", "r", encoding="utf-8") as f:
    it_code = f.read()

it_code = re.sub(old_title_pattern, new_title, it_code)
it_code = re.sub(old_title_simple, new_title_simple, it_code)
it_code = re.sub(old_researchers, new_researchers, it_code)

# Update Module 5 in IT expert to highlight ELA-CNN ResNet-50 and Grad-CAM
old_m5_task = r'"task": "Submit Certificate of Grades \(COG\) with digitally manipulated grades \(whiteout, clone-stamp, or font mismatch\)\. Inspect AI pipeline execution and ELA heatmap\.",'
new_m5_task = '"task": "Submit Certificate of Grades (COG) with digitally manipulated grades, altered GWA, or cloned seals/signatures. Inspect ELA preprocessing (Q=95), ResNet-50 binary classification (p in [0,1]), Fraud Probability Score (0-100%), and Grad-CAM convolutional heatmap overlay.",'
it_code = re.sub(old_m5_task, new_m5_task, it_code)

old_m5_exp = r'"expected": "Tesseract OCR extracts GWA; ELA detects compression inconsistencies; ORB clone detector flags copy-paste duplication; weighted fusion generates risk score\.",'
new_m5_exp = '"expected": "Pipeline computes normalized ELA difference map E(x,y)=|I(x,y)-I\'(x,y)| at Q=95, resizes to 224x224, executes ResNet-50 inference, calculates FPS, maps into 3 risk tiers (Low: 0-39%, Moderate: 40-69%, High: 70-100%), and displays Grad-CAM heatmap highlighting modified regions for human decision support.",'
it_code = re.sub(old_m5_exp, new_m5_exp, it_code)

old_m5_act = r'"actual": "AI microservice accurately detected forged grades; forensic overlays and ELA heatmap rendered in review modal\.",'
new_m5_act = '"actual": "ELA-CNN pipeline generated accurate FPS (0-100%); Grad-CAM convolutional heatmap clearly outlined manipulated grade fields; decision-support decoupling verified with human evaluator override.",'
it_code = re.sub(old_m5_act, new_m5_act, it_code)

old_m5_rem = r'"remarks": "Multi-detector fusion mitigates single-detector false positives\."'
new_m5_rem = '"remarks": "Complies with proposed ELA-ResNet-50 specification (Gorle & Guttavelli, 2025; Maamouli et al., 2022)."'
it_code = re.sub(old_m5_rem, new_m5_rem, it_code)

# Update Module 8 in IT expert for 1-page form & isolated print engine
old_m8_task = r'"task": "Generate and download the official approved scholarship certificate/form with student details, grant allocation, and validation seal\.",'
new_m8_task = '"task": "Generate and preview official 1-page applicant evaluation form with verification checklist, signatures, and tamper-evident clearance badge. Verify isolated iframe print engine.",'
it_code = re.sub(old_m8_task, new_m8_task, it_code)

old_m8_exp = r'"expected": "Vector PDF renders cleanly with official CLSU OSA seal, QR verification code, cryptographic verification link, and director signature line\.",'
new_m8_exp = '"expected": "1-page letter PDF generated matching web modal preview 1:1; isolated iframe prints cleanly without blank pages; CLSU OSA seal, QR clearance badge, and ISO revision code intact.",'
it_code = re.sub(old_m8_exp, new_m8_exp, it_code)

with open("scratch/generate_it_expert_standardized_doc.py", "w", encoding="utf-8") as f:
    f.write(it_code)
print("Updated scratch/generate_it_expert_standardized_doc.py successfully.")


# 3. Run both generation scripts
print("Running scratch/generate_standardized_role_docs.py...")
res1 = subprocess.run([sys.executable, "scratch/generate_standardized_role_docs.py"], capture_output=True, text=True, encoding="utf-8")
print(res1.stdout)
if res1.stderr:
    print("Stderr:", res1.stderr)

print("Running scratch/generate_it_expert_standardized_doc.py...")
res2 = subprocess.run([sys.executable, "scratch/generate_it_expert_standardized_doc.py"], capture_output=True, text=True, encoding="utf-8")
print(res2.stdout)
if res2.stderr:
    print("Stderr:", res2.stderr)

print("All DOCX documents regenerated successfully.")
