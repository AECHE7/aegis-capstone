# A.E.G.I.S. Capstone Diagram Set

28 diagrams supporting the capstone paper / technical documentation, generated from a direct analysis of the current codebase (not the older thesis drafts). Each figure exists as both a `.svg` (source, editable, infinitely scalable) and a `.png` (2x-resolution raster, ready to paste directly into Word).

**Design system:** Primary Blue `#1F4E79` (core/primary elements), Accent Amber `#C77C22` (AI/automated/processing elements), Accent Red `#A6303E` (risk/security/critical elements), plus Ink `#1A1A1A` / White / Gray `#595959`–`#D9D9D9` for structure — consistent across all 28 figures. Sans-serif (Arial) labels, Times New Roman reserved for the surrounding paper body text, per your institution's existing thesis convention.

All facts (table names, thresholds, algorithm steps, route names, class names) are traced to the actual source code and to the previously generated `AEGIS_SYSTEM_DOCUMENTATION.docx` — nothing was invented.

| # | Figure | File |
|---|--------|------|
| 1 | Conceptual Framework (IPO Model) | `01_conceptual_framework` |
| 2 | Agile Development Process | `02_agile_development_process` |
| 3 | System Context Diagram (Level 0 DFD) | `03_context_diagram` |
| 4 | Data Flow Diagram — Level 1 | `04_dfd_level1` |
| 5 | Data Flow Diagram — Level 2 (AI Forensic Verification) | `05_dfd_level2_ai_forensics` |
| 6 | System-Wide Use Case Diagram | `06_use_case_system_wide` |
| 7 | Detailed Use Case Diagram by Actor Role | `07_use_case_per_actor` |
| 8 | System Architecture Diagram | `08_system_architecture` |
| 9 | Deployment Diagram | `09_deployment_diagram` |
| 10 | Layered / Component Architecture Diagram | `10_component_layered_architecture` |
| 11 | Class Diagram (Core Domain Models) | `11_class_diagram` |
| 12 | Master ERD (System Overview) | `12_erd_master_overview` |
| 13 | ERD — Identity & Access Module | `13_erd_identity_access` |
| 14 | ERD — Scholarship Program Module | `14_erd_scholarship_program` |
| 15 | ERD — Application Lifecycle Module | `15_erd_application_lifecycle` |
| 16 | ERD — Audit & Compliance Module | `16_erd_audit_compliance` |
| 17 | Sequence — Registration and MFA Login | `17_seq_registration_mfa_login` |
| 18 | Sequence — Application Submission and Document Upload | `18_seq_application_submission_upload` |
| 19 | Sequence — AI-Powered Document Forensic Scan | `19_seq_ai_forensic_scan` |
| 20 | Sequence — Staff Review and Application Decision | `20_seq_staff_review_decision` |
| 21 | Sequence — Announcement Broadcast and Notification Delivery | `21_seq_announcement_notification` |
| 22 | Activity — End-to-End Application Lifecycle | `22_activity_application_lifecycle` |
| 23 | Activity — Document Forensic Verification Process | `23_activity_forensic_verification_process` |
| 24 | State Transition — Application Status Lifecycle | `24_state_application_status_lifecycle` |
| 25 | Flowchart — EFDF Weighted Fusion Scoring Algorithm | `25_flowchart_efdf_fusion_scoring` |
| 26 | Flowchart — Application Assignment Algorithm | `26_flowchart_application_assignment` |
| 27 | Defense-in-Depth Security Architecture | `27_security_defense_in_depth` |
| 28 | ISO/IEC 25010 Software Quality Model Mapping | `28_iso25010_quality_model` |

## Notes for the paper

- **ERD split**: The full ~23-table schema doesn't fit legibly on one page, so Figure 12 is a simplified map and Figures 13–16 give full attribute-level detail per module. Use 12 as the Chapter 3/4 overview figure and 13–16 as supporting detail figures (or appendix).
- **Printing in black & white**: every diagram uses shape/line-style conventions (open vs. filled arrowheads, dashed vs. solid, distinct notation per diagram type) on top of color, so they stay legible if printed in grayscale — color is a convenience layer, not the only signal.
- **Traceability findings**: while drawing these, the underlying analysis surfaced a few real inconsistencies in the codebase worth flagging to your panel or fixing before defense — see the accompanying discussion (dead `routes/auth.php`, unwired `stipend_amount` column, `render.yaml` health-check path mismatch, mismatched upload size limits). None of that was fabricated for the diagrams; it came from directly reading the code.
