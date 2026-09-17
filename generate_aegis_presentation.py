"""
A.E.G.I.S. CAPSTONE 2 DEFENSE PRESENTATION GENERATOR
Generates a complete 27-slide PowerPoint presentation
Run: python generate_aegis_presentation.py
"""

from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.enum.text import PP_ALIGN
from pptx.dml.color import RGBColor
import os

# COLOR PALETTE
NAVY   = RGBColor(0x0D, 0x2B, 0x5A)
GOLD   = RGBColor(0xC8, 0x96, 0x22)
WHITE  = RGBColor(0xFF, 0xFF, 0xFF)
LIGHT  = RGBColor(0xF4, 0xF6, 0xFA)
GREEN  = RGBColor(0x16, 0xA3, 0x4A)
RED    = RGBColor(0xDC, 0x26, 0x26)
GRAY   = RGBColor(0x6B, 0x72, 0x80)
ACCENT = RGBColor(0x06, 0x89, 0xD4)
PURPLE = RGBColor(0x7C, 0x3A, 0xED)

FIGURES_DIR     = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'thesis_figures')
SCREENSHOTS_DIR = os.path.join(FIGURES_DIR, 'screenshots')


def get_fig(name):
    for d in [FIGURES_DIR, SCREENSHOTS_DIR]:
        p = os.path.join(d, name)
        if os.path.exists(p):
            return p
    return None


def new_slide(prs, bg=LIGHT):
    layout = prs.slide_layouts[6]
    s = prs.slides.add_slide(layout)
    fill = s.background.fill
    fill.solid()
    fill.fore_color.rgb = bg
    return s


def tb(slide, text, l, t, w, h, size=12, bold=False, color=WHITE,
       align=PP_ALIGN.LEFT, italic=False, wrap=True):
    box = slide.shapes.add_textbox(Inches(l), Inches(t), Inches(w), Inches(h))
    tf = box.text_frame
    tf.word_wrap = wrap
    p = tf.paragraphs[0]
    p.alignment = align
    r = p.add_run()
    r.text = text
    r.font.size = Pt(size)
    r.font.bold = bold
    r.font.italic = italic
    r.font.color.rgb = color
    r.font.name = "Calibri"
    return box


def rect(slide, l, t, w, h, fill=NAVY, line=None):
    sh = slide.shapes.add_shape(1, Inches(l), Inches(t), Inches(w), Inches(h))
    sh.fill.solid()
    sh.fill.fore_color.rgb = fill
    if line is None:
        sh.line.fill.background()
    else:
        sh.line.color.rgb = line
    return sh


def header(slide, title, sub=None):
    rect(slide, 0, 0, 10, 0.95, NAVY)
    tb(slide, title, 0.2, 0.08, 9.2, 0.65, size=22, bold=True, color=GOLD)
    if sub:
        tb(slide, sub, 0.2, 0.68, 9.2, 0.28, size=9, color=RGBColor(0xBB, 0xCC, 0xDD))


def footer(slide):
    rect(slide, 0, 7.2, 10, 0.3, RGBColor(0x06, 0x2A, 0x58))
    tb(slide, "A.E.G.I.S.  |  BSIT Capstone 2  |  CLSU Department of Information Technology  |  2026",
       0.2, 7.22, 9.6, 0.26, size=8, color=RGBColor(0x99, 0xAA, 0xBB), align=PP_ALIGN.CENTER)


def kpi(slide, l, t, value, label, bg=GOLD, tc=NAVY):
    rect(slide, l, t, 2.1, 1.1, bg)
    tb(slide, value, l, t+0.05, 2.1, 0.62, size=26, bold=True, color=tc, align=PP_ALIGN.CENTER)
    tb(slide, label, l, t+0.65, 2.1, 0.38, size=9,  bold=False, color=tc, align=PP_ALIGN.CENTER)


def try_img(slide, name, l, t, w, h):
    p = get_fig(name)
    if p:
        try:
            slide.shapes.add_picture(p, Inches(l), Inches(t), Inches(w), Inches(h))
            return True
        except Exception:
            pass
    return False


def img_placeholder(slide, l, t, w, h, label):
    rect(slide, l, t, w, h, RGBColor(0xE5, 0xE7, 0xEB))
    tb(slide, f"[ {label} ]", l, t + h/2 - 0.25, w, 0.5,
       size=12, color=GRAY, align=PP_ALIGN.CENTER)


def add_img_or_placeholder(slide, name, l, t, w, h, label):
    if not try_img(slide, name, l, t, w, h):
        img_placeholder(slide, l, t, w, h, label)


# ══════════════════════════════════════════════════
prs = Presentation()
prs.slide_width  = Inches(10)
prs.slide_height = Inches(7.5)

# ── SLIDE 1: TITLE ───────────────────────────────
s = new_slide(prs, NAVY)
rect(s, 0, 2.55, 10, 0.06, GOLD)
tb(s, "A.E.G.I.S.", 0.5, 0.35, 9, 1.1, size=54, bold=True, color=GOLD, align=PP_ALIGN.CENTER)
tb(s, "AI-Enhanced Grant Information System", 0.5, 1.38, 9, 0.55, size=19, color=WHITE, align=PP_ALIGN.CENTER)
tb(s, "A Web-Based Scholarship Management Platform with Integrated Deep Learning\nDocument Fraud Detection for Central Luzon State University",
   0.5, 1.9, 9, 0.7, size=12, color=RGBColor(0xCC,0xD5,0xE0), align=PP_ALIGN.CENTER, italic=True)
tb(s, "[Your Names Here]", 0.5, 2.75, 9, 0.45, size=14, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
tb(s, "Advisers: L.G.B. Hidalgo, MIT  |  I.G.M. Balmadrid, MIT  |  J.A.J. Barza, MIT",
   0.5, 3.18, 9, 0.32, size=10, color=RGBColor(0xAA,0xBB,0xCC), align=PP_ALIGN.CENTER)
tb(s, "Department of Information Technology, College of Engineering\nCentral Luzon State University  •  January 2026",
   0.5, 3.52, 9, 0.55, size=11, color=RGBColor(0xAA,0xBB,0xCC), align=PP_ALIGN.CENTER)
kpi(s, 0.5, 4.9, "94.73%", "AI Accuracy",         GOLD,   NAVY)
kpi(s, 2.7, 4.9, "4.67/5", "UAT Score",            GREEN,  WHITE)
kpi(s, 4.9, 4.9, "198",    "Tests Passed",         ACCENT, WHITE)
kpi(s, 7.1, 4.9, "+21.5%", "Accuracy Boost",       PURPLE, WHITE)
footer(s)

# ── SLIDE 2: PROBLEM STATEMENT ────────────────────
s = new_slide(prs)
header(s, "Problem Statement", "Five persistent operational challenges at CLSU OSA")
problems = [
    ("📁 Disorganized Records",     "Physical storage only — no central digital repository for documents"),
    ("⏱  Slow Processing",          "Manual paper-based workflow causes delays in scholarship disbursement"),
    ("⚠️  Fraud Vulnerability",      "Tampered COGs submitted — no automated verification system existed"),
    ("📧 Unreliable Communication", "Manual email drafting — inconsistent and delayed applicant notifications"),
    ("📊 No Compliance Reports",    "No automated generation of CHED/DOST-SEI compliance reports"),
]
for i, (title, desc) in enumerate(problems):
    t = 1.05 + i * 1.18
    rect(s, 0.3, t, 9.4, 1.05, WHITE)
    rect(s, 0.3, t, 0.08, 1.05, RED)
    tb(s, title, 0.5, t+0.05, 5.0, 0.45, size=12, bold=True, color=NAVY)
    tb(s, desc,  0.5, t+0.5,  8.8, 0.45, size=10, color=GRAY)
footer(s)

# ── SLIDE 3: RESEARCH OBJECTIVES ──────────────────
s = new_slide(prs)
header(s, "Research Objectives", "Five specific objectives — all achieved ✅")
objs = [
    ("SO1", "Centralized Scholarship Management System",
     "Full application lifecycle: apply → scan → review → approve/reject → notify"),
    ("SO2", "AI-Based COG Verification Module",
     "Fraud probability score with Grad-CAM heatmap visualization"),
    ("SO3", "Automatic Email Notification System",
     "Status-triggered emails via Brevo SMTP — 98.7% delivery rate"),
    ("SO4", "Record Export Feature",
     "CSV and PDF export for CHED/DOST-SEI compliance reporting"),
    ("SO5", "ISO/IEC 25010 User Acceptance Testing",
     "UAT with 7 OSA personnel — overall 4.67/5.00 (Excellent)"),
]
for i, (code, obj, desc) in enumerate(objs):
    t = 1.05 + i * 1.18
    rect(s, 0.3, t, 9.4, 1.05, WHITE)
    rect(s, 0.3, t, 0.08, 1.05, GREEN)
    rect(s, 0.42, t+0.2, 0.55, 0.55, GREEN)
    tb(s, code, 0.43, t+0.22, 0.52, 0.5, size=10, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    tb(s, "✅ "+obj, 1.08, t+0.05, 8.4, 0.42, size=12, bold=True, color=NAVY)
    tb(s, desc,      1.08, t+0.52, 8.4, 0.45, size=10, color=GRAY)
footer(s)

# ── SLIDE 4: METHODOLOGY ──────────────────────────
s = new_slide(prs)
header(s, "Research Design & Methodology", "Applied developmental research — SDLC waterfall")
phases = [
    ("1", "Requirements Analysis", "OSA staff interviews, process mapping, gap identification",           ACCENT),
    ("2", "System Design",         "ER diagrams, wireframes, three-tier architecture design",             NAVY),
    ("3", "Implementation",        "Laravel 12 + Flask + ResNet-50 CNN — full stack development",         PURPLE),
    ("4", "Testing",               "198 PHPUnit assertions + 48 Python AI tests — 100% pass rate",        GREEN),
    ("5", "UAT",                   "ISO/IEC 25010 with 7 OSA personnel, 20 tasks each, 30-day period",    GOLD),
    ("6", "Evaluation",            "Statistical analysis of UAT scores, AI metrics, performance results", GREEN),
]
for i, (num, phase, desc, color) in enumerate(phases):
    t = 1.05 + i * 1.02
    rect(s, 0.3, t, 9.4, 0.92, WHITE)
    rect(s, 0.3, t, 0.07, 0.92, color)
    rect(s, 0.42, t+0.18, 0.38, 0.38, color)
    tb(s, num,   0.43, t+0.18, 0.36, 0.38, size=12, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    tb(s, phase, 0.9,  t+0.05, 2.8,  0.4,  size=12, bold=True, color=NAVY)
    tb(s, desc,  0.9,  t+0.5,  8.6,  0.35, size=10, color=GRAY)
footer(s)

# ── SLIDE 5: SYSTEM ARCHITECTURE ──────────────────
s = new_slide(prs)
header(s, "System Architecture", "Three-tier web application with AI microservice")
tiers = [
    ("PRESENTATION\nLAYER",  "Blade + Alpine.js\nTailwind CSS\nPWA + Offline",      ACCENT),
    ("APPLICATION\nLAYER",   "Laravel 12 (PHP 8.2)\nMVC + Services\nJob Queue",      NAVY),
    ("DATA\nLAYER",          "PostgreSQL (Supabase)\nCloudflare R2 Storage\nRedis",  GREEN),
]
for i, (label, tech, color) in enumerate(tiers):
    l = 0.3 + i * 3.15
    rect(s, l, 1.05, 2.95, 2.8, color)
    tb(s, label, l+0.1, 1.12, 2.75, 0.75, size=11, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    rect(s, l+0.1, 1.85, 2.75, 0.04, WHITE)
    tb(s, tech, l+0.1, 1.95, 2.75, 1.75, size=10, color=WHITE, align=PP_ALIGN.CENTER)
tb(s, "⟺", 3.2, 2.15, 0.55, 0.55, size=20, color=GOLD, align=PP_ALIGN.CENTER)
tb(s, "⟺", 6.35,2.15, 0.55, 0.55, size=20, color=GOLD, align=PP_ALIGN.CENTER)
rect(s, 2.0, 4.15, 6.0, 1.8, PURPLE)
tb(s, "🤖  AI MICROSERVICE", 2.1, 4.22, 5.8, 0.55, size=14, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
tb(s, "Flask + Python 3.11  •  ResNet-50 CNN  •  V1/V2/V3 Pipeline\nELA  •  Grad-CAM  •  Clone-Stamp Detection  •  7,891 training images",
   2.1, 4.75, 5.8, 0.9, size=10, color=WHITE, align=PP_ALIGN.CENTER)
tb(s, "⟺ HTTP/JSON", 4.5, 3.82, 1.3, 0.32, size=9, color=GRAY, align=PP_ALIGN.CENTER)
footer(s)

# ── SLIDE 6: AI PIPELINE ──────────────────────────
s = new_slide(prs)
header(s, "AI Fraud Detection Pipeline", "Novel triple-pipeline architecture")
pipelines = [
    ("V1  Baseline",       ACCENT,  ["Error Level Analysis (ELA)", "ResNet-50 Extraction", "Grad-CAM Heatmap", "Fraud Score"]),
    ("V2  Enhanced",       NAVY,    ["V1 components +", "Clone-Stamp Detection", "Weighted Fusion Scoring", "Multi-factor Class."]),
    ("V3  Deep Forensic",  PURPLE,  ["V2 components +", "Multi-layer Heatmaps", "Pixel Anomaly Mapping", "Deep Report"]),
]
for i, (title, color, steps) in enumerate(pipelines):
    l = 0.3 + i * 3.15
    rect(s, l, 1.05, 2.95, 4.35, color)
    tb(s, title, l+0.1, 1.1, 2.75, 0.55, size=13, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    for j, step in enumerate(steps):
        t = 1.8 + j * 0.85
        bg = RGBColor(0xFF,0xFF,0xFF) if i==0 else (RGBColor(0x1A,0x3A,0x6E) if i==1 else RGBColor(0x5B,0x21,0xB6))
        tc = NAVY if i==0 else WHITE
        rect(s, l+0.12, t, 2.72, 0.72, bg)
        tb(s, f"  {j+1}. {step}", l+0.15, t+0.12, 2.65, 0.5, size=10, color=tc)
rect(s, 0.3, 5.6, 9.4, 0.8, NAVY)
tb(s, "OUTPUT: Fraud Probability (0–100%)  •  Classification (Authentic / Tampered)  •  Heatmap  •  Anomaly Indicators",
   0.5, 5.72, 9.0, 0.55, size=11, color=GOLD, align=PP_ALIGN.CENTER)
footer(s)

# ── SLIDE 7: RESULTS OVERVIEW (section break) ─────
s = new_slide(prs, NAVY)
tb(s, "SECTION 3", 0.3, 0.5, 9.4, 0.5, size=14, bold=True, color=GOLD, align=PP_ALIGN.CENTER)
tb(s, "Results & Discussion", 0.3, 1.0, 9.4, 1.0, size=40, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
rect(s, 2.5, 2.05, 5.0, 0.06, GOLD)
tb(s, "All five objectives achieved — all metrics exceed pre-defined targets",
   0.3, 2.2, 9.4, 0.5, size=15, color=RGBColor(0xCC,0xD5,0xE0), align=PP_ALIGN.CENTER)
kpi_data = [
    ("94.73%","AI Accuracy",  GOLD,   NAVY,  0.5, 3.2),
    ("4.67/5","UAT Score",    GREEN,  WHITE, 2.7, 3.2),
    ("+21.5%","Accuracy Boost",ACCENT,WHITE, 4.9, 3.2),
    ("−38.7%","Time Saved",   PURPLE, WHITE, 7.1, 3.2),
    ("198/198","Tests Passed",GREEN,  WHITE, 0.5, 4.55),
    ("99.2%", "Uptime",       ACCENT, WHITE, 2.7, 4.55),
    ("98.7%", "Email Delivery",GOLD,  NAVY,  4.9, 4.55),
    ("−75%",  "False Negs.",  RED,    WHITE, 7.1, 4.55),
]
for v, l2, bg, tc, x, y in kpi_data:
    kpi(s, x, y, v, l2, bg, tc)
footer(s)

# ── SLIDE 8: AI METRICS TABLE ─────────────────────
s = new_slide(prs)
header(s, "AI Module Performance Metrics", "All metrics exceed pre-defined targets")
metrics = [
    ("Accuracy",           "94.73%", "≥ 90.00%", "+4.73%",   GREEN),
    ("Precision",          "92.18%", "≥ 85.00%", "+7.18%",   GREEN),
    ("Recall",             "91.45%", "≥ 85.00%", "+6.45%",   GREEN),
    ("F1-Score",           "91.81%", "≥ 85.00%", "+6.81%",   GREEN),
    ("False Negative Rate","8.55%",  "≤ 15.00%", "−6.45%",   GREEN),
    ("False Positive Rate","7.82%",  "≤ 20.00%", "−12.18%",  GREEN),
    ("COG-Specific Acc.",  "96.67%", "N/A",       "🏆 Best",  GOLD),
]
hdrs = ["Metric", "Achieved", "Target", "Δ vs Target"]
cls  = [0.3, 3.4, 5.3, 7.2]
wds  = [2.95, 1.75, 1.75, 2.4]
for j, (h, c, w) in enumerate(zip(hdrs, cls, wds)):
    rect(s, c, 1.08, w-0.05, 0.48, NAVY)
    tb(s, h, c+0.08, 1.13, w-0.1, 0.38, size=11, bold=True, color=WHITE)
for i, (m, ach, tgt, delta, col) in enumerate(metrics):
    bg = WHITE if i%2==0 else LIGHT
    t = 1.6 + i * 0.7
    for j, (val, c, w) in enumerate(zip([m, ach, tgt, delta], cls, wds)):
        rect(s, c, t, w-0.05, 0.64, bg)
        tc2 = GRAY if j==0 else (col if j in (1,3) else GRAY)
        tb(s, val, c+0.08, t+0.1, w-0.15, 0.45, size=11, bold=(j==1), color=tc2)
footer(s)

# ── SLIDE 9: CONFUSION MATRIX ─────────────────────
s = new_slide(prs)
header(s, "Confusion Matrix — AI Classification Results", "1,184 test documents evaluated")
add_img_or_placeholder(s, "Figure_14_Confusion_Matrix.png", 0.3, 1.1, 5.5, 5.3, "Confusion Matrix")
cells = [
    ("True Negative",  "547", "Correctly identified AUTHENTIC",  GREEN),
    ("True Positive",  "541", "Correctly identified TAMPERED",   GREEN),
    ("False Positive", "45",  "Authentic → wrongly flagged",     RGBColor(0xF5,0x9E,0x0B)),
    ("False Negative", "51",  "Tampered → MISSED (critical)",    RED),
]
for i, (lbl, cnt, desc, col) in enumerate(cells):
    t = 1.15 + i * 1.4
    rect(s, 6.0, t, 3.7, 1.25, WHITE)
    rect(s, 6.0, t, 0.07, 1.25, col)
    tb(s, cnt,  6.12, t+0.05, 1.0, 0.62, size=28, bold=True, color=col)
    tb(s, lbl,  6.12, t+0.62, 3.4, 0.35, size=10, bold=True, color=NAVY)
    tb(s, desc, 6.12, t+0.92, 3.4, 0.28, size=9,  color=GRAY)
footer(s)

# ── SLIDE 10: ROC CURVE ───────────────────────────
s = new_slide(prs)
header(s, "ROC Curve Analysis", "AUC = 0.968 — Excellent discrimination ability")
add_img_or_placeholder(s, "Figure_15_ROC_Curve.png", 0.3, 1.1, 5.5, 5.3, "ROC Curve")
rect(s, 6.0, 1.2, 3.7, 1.45, NAVY)
tb(s, "AUC = 0.968", 6.05, 1.28, 3.6, 0.72, size=30, bold=True, color=GOLD, align=PP_ALIGN.CENTER)
tb(s, "EXCELLENT", 6.05, 1.95, 3.6, 0.45, size=13, bold=True, color=GREEN, align=PP_ALIGN.CENTER)
notes = [
    "• AUC > 0.9 = Excellent discrimination",
    "• Random classifier AUC = 0.5",
    "• Perfect classifier AUC = 1.0",
    "• Our model at 0.968 is near-perfect",
    "• Robust across all decision thresholds",
    "• No single threshold dependency",
]
for i, note in enumerate(notes):
    t = 2.85 + i * 0.72
    tb(s, note, 6.0, t, 3.7, 0.65, size=11, color=NAVY)
footer(s)

# ── SLIDE 11: ACCURACY COMPARISON ────────────────
s = new_slide(prs)
header(s, "Accuracy Comparison with Related Studies", "A.E.G.I.S. competitive with state-of-the-art")
add_img_or_placeholder(s, "Figure_16_Accuracy_Comparison.png", 0.3, 1.1, 9.4, 5.3, "Accuracy Comparison Chart")
footer(s)

# ── SLIDE 12: UAT / ISO 25010 ─────────────────────
s = new_slide(prs)
header(s, "User Acceptance Testing — ISO/IEC 25010", "7 OSA personnel, 20 tasks, 5 quality dimensions")
add_img_or_placeholder(s, "Figure_17_ISO_25010_Radar.png", 0.3, 1.1, 4.8, 5.2, "ISO 25010 Radar Chart")
dims = [
    ("Functional Suitability","4.74"), ("Usability","4.60"),
    ("Reliability","4.66"), ("Performance Efficiency","4.60"), ("Security","4.74"),
]
rect(s, 5.3, 1.2, 4.4, 0.52, NAVY)
tb(s, "Quality Dimension Scores", 5.35, 1.26, 4.3, 0.4, size=12, bold=True, color=WHITE)
for i, (dim, score) in enumerate(dims):
    t = 1.76 + i * 0.9
    rect(s, 5.3, t, 4.4, 0.82, WHITE if i%2==0 else LIGHT)
    tb(s, dim,   5.4, t+0.1,  2.6, 0.38, size=11, color=NAVY)
    tb(s, score, 8.0, t+0.06, 1.5, 0.48, size=20, bold=True, color=GREEN, align=PP_ALIGN.CENTER)
rect(s, 5.3, 6.28, 4.4, 0.88, GOLD)
tb(s, "OVERALL:  4.67 / 5.00  (EXCELLENT)", 5.35, 6.38, 4.3, 0.45, size=13, bold=True, color=NAVY, align=PP_ALIGN.CENTER)
tb(s, "Exceeded threshold by 16.75%  •  100% recommend deployment",
   5.35, 6.76, 4.3, 0.35, size=9, color=NAVY, align=PP_ALIGN.CENTER)
footer(s)

# ── SLIDE 13: HUMAN vs AI ────────────────────────
s = new_slide(prs)
header(s, "Human Performance Enhancement via AI Assistance", "Measurable improvements across all reviewer metrics")
add_img_or_placeholder(s, "Figure_19_AI_Assisted_Comparison.png", 0.3, 1.1, 5.5, 5.2, "AI Assisted Comparison")
enhancements = [
    ("Review Accuracy",     "73.3%", "94.8%", "+21.5 pp", GREEN),
    ("Review Time / Doc",   "142 s", "87 s",  "−38.7%",   GREEN),
    ("Reviewer Confidence", "2.9/5", "4.3/5", "+48.3%",   GREEN),
    ("False Negatives",     "4/15",  "1/15",  "−75.0%",   GREEN),
]
for i, (metric, bef, aft, change, col) in enumerate(enhancements):
    t = 1.15 + i * 1.42
    rect(s, 6.0, t, 3.7, 1.28, WHITE)
    tb(s, metric, 6.1, t+0.07, 3.5, 0.4, size=12, bold=True, color=NAVY)
    tb(s, f"Before: {bef}  →  After: {aft}", 6.1, t+0.48, 3.5, 0.34, size=10, color=GRAY)
    rect(s, 6.1, t+0.85, 2.1, 0.38, col)
    tb(s, change, 6.15, t+0.88, 2.0, 0.3, size=14, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
footer(s)

# ── SLIDE 14: SYSTEM TESTING ──────────────────────
s = new_slide(prs)
header(s, "System Testing Results", "100% pass rate — zero defects detected")
add_img_or_placeholder(s, "Figure_Test_Suite_Results.png", 0.3, 1.1, 5.5, 4.5, "Test Suite Results")
test_data = [
    ("PHPUnit — Laravel",      "198/198", "100%", GREEN),
    ("Python AI Tests",        "48/48",   "100%", GREEN),
    ("Security Vulnerabilities","0 found","None",  GREEN),
    ("UAT Task Completion",    "139/140", "99.3%", GREEN),
]
for i, (cat, detail, rate, col) in enumerate(test_data):
    t = 1.15 + i * 1.5
    rect(s, 5.9, t, 3.8, 1.35, WHITE if i%2==0 else LIGHT)
    tb(s, cat,    5.98, t+0.08, 3.6, 0.4,  size=11, bold=True, color=NAVY)
    tb(s, rate,   5.98, t+0.52, 3.6, 0.55, size=26, bold=True, color=col, align=PP_ALIGN.CENTER)
    tb(s, detail, 5.98, t+1.05, 3.6, 0.25, size=9,  color=GRAY, align=PP_ALIGN.CENTER)
footer(s)

# ── SLIDE 15: PERFORMANCE BENCHMARKS ─────────────
s = new_slide(prs)
header(s, "System Performance Benchmarks", "All operations exceed performance targets")
add_img_or_placeholder(s, "Figure_Performance_Benchmarks.png", 0.3, 1.1, 5.5, 5.2, "Performance Benchmarks")
benchmarks = [
    ("AI Analysis V2",      "< 5.0 s", "2.3 s",  GREEN),
    ("AI Analysis V3",      "< 8.0 s", "4.6 s",  GREEN),
    ("Page Load (avg)",     "< 2.0 s", "0.94 s", GREEN),
    ("PDF Export",          "< 10.0 s","3.2 s",  GREEN),
    ("Email Delivery Rate", "≥ 95%",   "98.7%",  GREEN),
    ("System Uptime",       "≥ 99%",   "99.2%",  GREEN),
    ("False Neg. Rate",     "≤ 15%",   "8.55%",  GREEN),
]
for i, (op, target, actual, col) in enumerate(benchmarks):
    t = 1.15 + i * 0.88
    rect(s, 5.9, t, 3.8, 0.8, WHITE if i%2==0 else LIGHT)
    tb(s, op,     5.98, t+0.1,  2.2, 0.35, size=10, color=NAVY)
    tb(s, actual, 8.15, t+0.06, 1.4, 0.46, size=17, bold=True, color=col, align=PP_ALIGN.CENTER)
    tb(s, f"target: {target}", 5.98, t+0.5, 3.6, 0.25, size=8, color=GRAY)
footer(s)

# ── SLIDE 16: ACHIEVEMENT OF OBJECTIVES ──────────
s = new_slide(prs)
header(s, "Achievement of Research Objectives", "All 5 specific objectives fully achieved")
achievements = [
    ("SO1","Centralized System",   "✅ Full lifecycle: apply → AI scan → review → approve → notify → export"),
    ("SO2","AI Fraud Detection",   "✅ 94.73% accuracy; 96.67% COG-specific; Grad-CAM heatmaps operational"),
    ("SO3","Email Notifications",  "✅ 98.7% delivery; status-triggered; Brevo SMTP integrated"),
    ("SO4","Record Export",        "✅ CSV + PDF for all 10 audit log types; CHED/DOST-SEI compliant"),
    ("SO5","ISO/IEC 25010 UAT",    "✅ 4.67/5.00 overall; all 5 dimensions Excellent; 100% recommend deployment"),
]
for i, (code, title, evidence) in enumerate(achievements):
    t = 1.08 + i * 1.2
    rect(s, 0.3, t, 9.4, 1.08, WHITE)
    rect(s, 0.3, t, 0.07, 1.08, GREEN)
    rect(s, 0.42, t+0.18, 0.58, 0.58, GREEN)
    tb(s, code,     0.43, t+0.2,  0.56, 0.5,  size=10, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
    tb(s, title,    1.1,  t+0.05, 2.5,  0.42, size=13, bold=True, color=NAVY)
    tb(s, evidence, 1.1,  t+0.55, 8.3,  0.45, size=10, color=GRAY)
footer(s)

# ── SLIDE 17: TECHNICAL CONTRIBUTIONS ────────────
s = new_slide(prs)
header(s, "Technical Contributions", "Novel innovations for Philippine HEI digital transformation")
contribs = [
    ("🔬", "Triple-Pipeline AI Architecture",
     "First V1/V2/V3 fraud detection pipeline with escalating forensic depth in a Philippine HEI context",  NAVY),
    ("🎓", "Domain-Adapted COG Dataset",
     "400 synthetic CLSU COGs + CASIA v2.0 (7,891 total) — first Philippine academic image forgery dataset", RGBColor(0x1A,0x3A,0x6E)),
    ("🏛️", "First Integrated HEI Platform",
     "First Philippine system combining scholarship management + AI doc verification + automated notification", NAVY),
    ("🔒", "Enterprise Security Stack",
     "MFA + AES-256 + RBAC + rate limiting + IDOR prevention + full audit trail — zero vulnerabilities",       RGBColor(0x1A,0x3A,0x6E)),
    ("🌏", "Replicable Open-Source Model",
     "Zero-licensing-cost stack replicable by any Philippine SUC — aligns with R.A. 11032",                   NAVY),
]
for i, (icon, title, desc, bg) in enumerate(contribs):
    t = 1.08 + i * 1.22
    rect(s, 0.3, t, 9.4, 1.1, bg)
    tb(s, icon,  0.45, t+0.22, 0.65, 0.72, size=26, color=GOLD)
    tb(s, title, 1.15, t+0.05, 8.4,  0.45, size=13, bold=True, color=GOLD)
    tb(s, desc,  1.15, t+0.55, 8.4,  0.45, size=10, color=WHITE)
footer(s)

# ── SLIDE 18: LIMITATIONS ─────────────────────────
s = new_slide(prs)
header(s, "Limitations of the Study", "Acknowledged constraints and boundary conditions")
limitations = [
    ("Synthetic Training Data",
     "400 CLSU COG documents were synthetically generated. Performance on real-world documents may vary slightly. Mitigated by 96.67% COG-specific validation accuracy."),
    ("Small UAT Sample (N=7)",
     "Convenience sample from 1 institution. Findings demonstrate strong acceptability but may not generalize to all Philippine HEI contexts without replication."),
    ("AI Black-Box Transparency",
     "Grad-CAM provides visual explanations but internal CNN weights are not interpretable by non-technical reviewers without additional training."),
    ("Single-Institution Scope",
     "Workflows are tailored to CLSU OSA procedures. Adaptation and re-training would be required for institutions with different scholarship processes."),
]
for i, (lim, desc) in enumerate(limitations):
    t = 1.08 + i * 1.52
    rect(s, 0.3, t, 9.4, 1.4, WHITE)
    rect(s, 0.3, t, 0.07, 1.4, RGBColor(0xF5,0x9E,0x0B))
    tb(s, f"L{i+1}. {lim}", 0.5, t+0.05, 8.8, 0.45, size=13, bold=True, color=NAVY)
    tb(s, desc,              0.5, t+0.55, 8.8, 0.75, size=10, color=GRAY)
footer(s)

# ── SLIDE 19: CONCLUSIONS ─────────────────────────
s = new_slide(prs, NAVY)
tb(s, "SECTION 5", 0.3, 0.35, 9.4, 0.45, size=14, bold=True, color=GOLD, align=PP_ALIGN.CENTER)
tb(s, "Conclusions", 0.3, 0.82, 9.4, 0.88, size=40, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
rect(s, 2.5, 1.72, 5.0, 0.06, GOLD)
conclusions = [
    "✦ A.E.G.I.S. successfully resolves all 5 identified OSA operational challenges",
    "✦ AI fraud detection: 94.73% accuracy — exceeds 90% target by 4.73 percentage points",
    "✦ Automated workflow: 38.7% time reduction, 21.5% human accuracy improvement",
    "✦ ISO/IEC 25010 UAT: 4.67/5.00 overall — all 5 quality dimensions 'Excellent'",
    "✦ 100% of OSA personnel recommend immediate production deployment",
    "✦ Zero security vulnerabilities; 99.2% uptime over 30-day testing period",
    "✦ Demonstrates AI-augmented public service delivery is both efficient and secure",
    "✦ Provides replicable model for Philippine SUC digital transformation (R.A. 11032)",
]
for i, c in enumerate(conclusions):
    tc2 = WHITE if i%2==0 else RGBColor(0xCC,0xD5,0xE0)
    tb(s, c, 0.4, 1.9 + i*0.68, 9.2, 0.62, size=11, color=tc2)
footer(s)

# ── SLIDE 20: RECOMMENDATIONS ────────────────────
s = new_slide(prs)
header(s, "Recommendations", "Short-term through long-term action items")
recs = [
    ("🚀", "IMMEDIATE",   "Deploy to CLSU OSA production; provide onboarding training for 7 OSA staff",       GREEN),
    ("📱", "SHORT-TERM",  "Add bulk approval features and SMS/push notifications for students",                ACCENT),
    ("🔗", "MEDIUM-TERM", "Integrate with CLSU Student Information System for real-time grade verification",  GOLD),
    ("📊", "ANNUAL",      "Retrain AI model with real COG documents collected from annual OSA operations",     RGBColor(0xF5,0x9E,0x0B)),
    ("🌏", "LONG-TERM",   "Open-source framework for adoption by other Philippine State Universities (SUCs)",  PURPLE),
]
for i, (icon, timeframe, rec, col) in enumerate(recs):
    t = 1.08 + i * 1.25
    rect(s, 0.3, t, 9.4, 1.12, col)
    tb(s, icon,      0.45, t+0.2,  0.62, 0.72, size=26, color=WHITE)
    tb(s, timeframe, 1.12, t+0.06, 1.8,  0.42, size=11, bold=True, color=WHITE)
    tb(s, rec,       1.12, t+0.58, 8.4,  0.45, size=10, color=WHITE)
footer(s)

# ── SLIDE 21: Q&A — AI ───────────────────────────
s = new_slide(prs)
header(s, "Panel Q&A Preparation — AI Module", "Anticipated questions and prepared answers")
qa = [
    ("Q: Why 94.73%? Is that sufficient for fraud detection?",
     "A: Yes — it exceeds our 90% target by 4.73 pp and is competitive with Agarwal et al. (93.2%). COG-specific accuracy is 96.67%. False negative rate is only 8.55% (target ≤15%), meaning very few tampered COGs are missed — the most critical metric for fraud detection."),
    ("Q: The training data was synthetic. How valid are your results?",
     "A: Synthetic data is standard in fraud detection research (see: CASIA v2.0 benchmark used globally). Our 400 synthetic COGs mirror real CLSU document formatting precisely. Validation used a held-out test set of 1,184 images — separate from training. The 96.67% COG-specific result confirms domain adaptation succeeded."),
    ("Q: Can fraudsters deliberately bypass the AI system?",
     "A: The triple-pipeline creates multiple overlapping detection layers. Even if one signal is bypassed, others (clone-stamp, pixel anomaly, ELA, grade discrepancy check) can still flag tampering. V3 operates at pixel-level depth, making bypass extremely difficult without detectable artifacts."),
]
for i, (q, a) in enumerate(qa):
    t = 1.08 + i * 2.1
    rect(s, 0.3, t, 9.4, 0.56, NAVY)
    tb(s, q, 0.45, t+0.09, 9.1, 0.42, size=11, bold=True, color=GOLD)
    rect(s, 0.3, t+0.56, 9.4, 1.42, WHITE)
    rect(s, 0.3, t+0.56, 0.07, 1.42, ACCENT)
    tb(s, a, 0.5, t+0.66, 9.1, 1.25, size=10, color=GRAY, wrap=True)
footer(s)

# ── SLIDE 22: Q&A — DEPLOYMENT ───────────────────
s = new_slide(prs)
header(s, "Panel Q&A Preparation — Deployment & Security", "Technical and operational readiness")
qa2 = [
    ("Q: Is the system truly production-ready?",
     "A: Yes. 198/198 passing tests, zero security vulnerabilities, 99.2% uptime over 30-day testing, and all 7 OSA personnel recommend deployment. The zero-cost Render + Supabase + Cloudflare R2 stack is production-grade and fully configured in the render.yaml deployment manifest."),
    ("Q: What happens when the AI microservice goes down?",
     "A: Graceful degradation is built in. Applications remain accessible for manual review with a clear 'AI scan pending' indicator. The Laravel Job Queue automatically retries failed scans with exponential backoff. This was formally tested in EmergencyRecoveryTest.php."),
    ("Q: How is sensitive student data protected?",
     "A: AES-256 document encryption, mandatory MFA for all users, RBAC with 3 permission tiers, rate limiting on all endpoints, IDOR prevention, immutable audit trail for every action, and HTTPS-only deployment. Zero vulnerabilities were detected in automated security testing."),
]
for i, (q, a) in enumerate(qa2):
    t = 1.08 + i * 2.1
    rect(s, 0.3, t, 9.4, 0.56, NAVY)
    tb(s, q, 0.45, t+0.09, 9.1, 0.42, size=11, bold=True, color=GOLD)
    rect(s, 0.3, t+0.56, 9.4, 1.42, WHITE)
    rect(s, 0.3, t+0.56, 0.07, 1.42, GREEN)
    tb(s, a, 0.5, t+0.66, 9.1, 1.25, size=10, color=GRAY, wrap=True)
footer(s)

# ── SLIDE 23: OVERALL ACCEPTABILITY ──────────────
s = new_slide(prs)
header(s, "Overall System Acceptability Chart", "4.67/5.00 — all dimensions rated Excellent")
add_img_or_placeholder(s, "Figure_Overall_Acceptability.png", 0.3, 1.1, 9.4, 5.2, "Overall Acceptability Chart")
footer(s)

# ── SLIDE 24: SCREENSHOTS PORTAL ──────────────────
s = new_slide(prs)
header(s, "System Interface — Student Portal", "Clean, responsive interface for scholarship applicants")
scr_files = ["Figure_13_Student_Dashboard.png", "Figure_14_Student_Apply_Form.png"]
for i, fname in enumerate(scr_files):
    l = 0.3 + i * 4.9
    add_img_or_placeholder(s, fname, l, 1.1, 4.55, 5.5, fname.replace("_", " ").replace(".png",""))
footer(s)

# ── SLIDE 25: SCREENSHOTS ADMIN ───────────────────
s = new_slide(prs)
header(s, "System Interface — Admin Review & AI Scan", "Fraud detection panel with heatmap visualization")
scr_files = ["Figure_07_Admin_Application_Queue.png", "Figure_09_Application_Review_Detail.png"]
for i, fname in enumerate(scr_files):
    l = 0.3 + i * 4.9
    add_img_or_placeholder(s, fname, l, 1.1, 4.55, 5.5, fname.replace("_", " ").replace(".png",""))
footer(s)

# ── SLIDE 26: SCREENSHOTS ANALYTICS ───────────────
s = new_slide(prs)
header(s, "System Interface — Analytics & Settings", "Superadmin analytics dashboard and configuration")
scr_files = ["Figure_03_Analytics_Dashboard.png", "Figure_04_System_Settings.png"]
for i, fname in enumerate(scr_files):
    l = 0.3 + i * 4.9
    add_img_or_placeholder(s, fname, l, 1.1, 4.55, 5.5, fname.replace("_", " ").replace(".png",""))
footer(s)

# ── SLIDE 27: THANK YOU ───────────────────────────
s = new_slide(prs, NAVY)
rect(s, 0, 0, 10, 0.09, GOLD)
rect(s, 0, 7.41, 10, 0.09, GOLD)
tb(s, "Thank You", 0.5, 0.5, 9, 1.2, size=54, bold=True, color=GOLD, align=PP_ALIGN.CENTER)
rect(s, 2.5, 1.72, 5.0, 0.06, GOLD)
tb(s, "A.E.G.I.S. — AI-Enhanced Grant Information System",
   0.5, 1.85, 9, 0.48, size=14, color=WHITE, align=PP_ALIGN.CENTER, italic=True)
rect(s, 0.5, 2.55, 9.0, 2.45, RGBColor(0x0A, 0x22, 0x4A))
tb(s, '"A.E.G.I.S. demonstrates that AI-augmented digital services can improve\n'
       'public sector efficiency while maintaining accountability and security.\n'
       'This research contributes a replicable model for Philippine state universities\n'
       'pursuing digital transformation aligned with Republic Act 11032."',
   0.7, 2.68, 8.6, 2.22, size=13, color=RGBColor(0xCC,0xD5,0xE0), italic=True, align=PP_ALIGN.CENTER)
tb(s, "[Your Names Here]", 0.5, 5.25, 9, 0.48, size=15, bold=True, color=WHITE, align=PP_ALIGN.CENTER)
tb(s, "Department of Information Technology  •  College of Engineering\n"
       "Central Luzon State University  •  Science City of Muñoz, Nueva Ecija  •  January 2026",
   0.5, 5.72, 9, 0.62, size=11, color=RGBColor(0xAA,0xBB,0xCC), align=PP_ALIGN.CENTER)
kpi(s, 0.5, 6.55, "94.73%", "AI Accuracy",    GOLD,   NAVY)
kpi(s, 2.7, 6.55, "4.67/5", "UAT Score",      GREEN,  WHITE)
kpi(s, 4.9, 6.55, "198",    "Tests Passed",   ACCENT, WHITE)
kpi(s, 7.1, 6.55, "100%",   "Recommend ✅",   PURPLE, WHITE)

# ── SAVE ──────────────────────────────────────────
out = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'AEGIS_DEFENSE_PRESENTATION.pptx')
prs.save(out)
size_kb = os.path.getsize(out) // 1024
print(f"\n[OK] Saved: {out}")
print(f"   Slides : {len(prs.slides)}")
print(f"   Size   : {size_kb:,} KB\n")
