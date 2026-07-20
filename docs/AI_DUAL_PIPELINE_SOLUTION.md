# A.E.G.I.S. Dual-Pipeline AI Solution

**Status:** Design proposal (ready to implement)  
**Date:** 2026-07-20  
**Problem owner:** AI document integrity scanning  

---

## 1. The Problem (What Is Broken Today)

### 1.1 Image path (JPG / PNG) is not a reliable “real AI”

| Issue | Evidence in codebase |
|---|---|
| Silent **simulation mode** | `aegis-ai/app.py` returns random scores if TensorFlow or `aegis_resnet50_v1.keras` fails to load |
| Training data is **synthetic only** | `train_model.py` draws fake COGs with PIL — not real CLSU scans |
| Model may not generalize | Synthetic JPEG recompression artifacts ≠ real phone-scanned COGs |
| GWA from images is **null** | Image branch sets `extracted_gwa: null` — no OCR |
| PNG is technically accepted | ELA works on RGB, but training never used PNG / real photos |

Uploads already allow `jpeg,png,pdf` (`StoreApplicationRequest`). The portal is ready; the AI is not.

### 1.2 PDF path is a security hole

```python
# app.py — current PDF handling
if original_ext == 'pdf':
    extracted_gwa = extract_gwa_from_pdf(original_path)
    return jsonify({
        "fraud_probability": 0.0,
        "classification": "Authentic (PDF Bypass)",  # ← always trusted
        ...
    })
```

Any student can upload a forged PDF and get **0% fraud / Authentic**. Text GWA extraction alone does not prove integrity.

### 1.3 What we need

1. A **real trained image model** that scores JPG/PNG (and rasterized PDF pages).
2. A **separate PDF forensic pipeline** (free/open-source) for structure + embedded content.
3. Unified results back into Laravel `AIResult` + auto-approval logic.

---

## 2. Recommended Architecture: Dual Pipeline

```
                    ┌──────────────────────────────┐
                    │  Laravel ScanDocumentJob     │
                    │  POST /analyze-document      │
                    └──────────────┬───────────────┘
                                   │
                    ┌──────────────▼───────────────┐
                    │  A.E.G.I.S. AI Gateway        │
                    │  (Flask) MIME router          │
                    └──────┬───────────────┬───────┘
                           │               │
              image/*      │               │  application/pdf
                           ▼               ▼
              ┌────────────────────┐  ┌─────────────────────────┐
              │ PIPELINE A         │  │ PIPELINE B              │
              │ Image Forensics AI │  │ PDF Forensic + Raster   │
              │ JPG / PNG          │  │                         │
              │                    │  │ B1 Structural (free OS) │
              │ 1. Normalize RGB   │  │ B2 Extract embeds       │
              │ 2. ELA             │  │ B3 Raster pages→PNG     │
              │ 3. Trained CNN     │  │ B4 Run Pipeline A       │
              │ 4. Grad-CAM        │  │ B5 Text/OCR GWA         │
              │ 5. OCR GWA         │  │                         │
              └─────────┬──────────┘  └────────────┬────────────┘
                        │                          │
                        └──────────┬───────────────┘
                                   ▼
                        Unified JSON response
                        → AIResult in Laravel
```

**Principle:** PDF is never auto-trusted. PDF structural signals + visual AI on rendered pages.

---

## 3. Pipeline A — Real Image AI (JPG / PNG)

### 3.1 Goal

Train and serve a model that classifies **authentic vs tampered** academic grade sheets from real-looking images, with:

- Fraud probability (0–100)
- Classification
- Grad-CAM heatmap
- Anomaly indicators
- Extracted GWA (OCR)

### 3.2 Why the current model is insufficient

`train_model.py` only teaches the network: “white PIL document + white rectangle over GWA + different JPEG quality.”  
Real student uploads are phone photos, scanner PDFs-as-images, shadows, skew, different fonts, and multi-page crops. The model will **overfit synthetic patterns** and fail UAT on real COGs.

### 3.3 Training strategy (practical for capstone)

#### Phase A1 — Dataset (hybrid)

| Source | Role | License / notes |
|---|---|---|
| **Real authentic COGs** (20–50+) | Domain anchor | Collect with consent / blur PII; or use de-identified OSA samples |
| **Programmatic forgeries** of those authentic scans | Tampered class | White-box edit GWA, splice grade cells, font paste, clone-stamp |
| **Public image forensics sets** (optional boost) | Transfer learning | CASIA v2, CoMoFoD, Columbia — general splice/copy-move |
| **Synthetic COGs** (existing script) | Bootstrap only | Keep as cold-start, not sole data |

Folder layout (keep current structure):

```
aegis-ai/dataset_real/
  train/{authentic,tampered}/
  val/{authentic,tampered}/
  test/{authentic,tampered}/
```

Rules:

- Train on **ELA images**, not raw RGB (matches inference).
- Include **JPG and PNG** variants (re-save authentic as both).
- Include mild augmentation: rotation, brightness, slight blur, JPEG re-encode (not horizontal flip on text docs — flips hurt document semantics).

#### Phase A2 — Model

Keep **ResNet-50 + ELA** (already integrated) but improve training:

1. Freeze ResNet backbone for 3–5 epochs (head only), then fine-tune last blocks at low LR.
2. Binary cross-entropy + class weights if unbalanced.
3. Early stopping on `val_recall` (prefer catching forgeries over perfect accuracy).
4. Save metrics JSON next to model: accuracy, precision, recall, F1, confusion matrix.
5. **Fail closed in production** if model missing — no random simulation.

Optional upgrade later (if GPU time allows): EfficientNet-B0 (lighter for free-tier hosts).

#### Phase A3 — OCR for GWA (free)

| Tool | Why |
|---|---|
| **Tesseract OCR** (`pytesseract`) | Free, open source, offline |
| Preprocess | Grayscale, adaptive threshold, deskew optional |

Patterns (reuse PDF regex family):

- `GWA: 1.75`
- `GENERAL WEIGHTED AVERAGE 1.50`

Laravel already forces fraud to **99%** when declared GWA ≠ extracted GWA (`ScanDocumentJob`). OCR closes that loop for images.

#### Phase A4 — Inference contract (unchanged shape, richer fields)

```json
{
  "status": "success",
  "pipeline": "image_forensics",
  "fraud_probability": 72.4,
  "classification": "Tampered",
  "extracted_gwa": 1.25,
  "anomaly_indicators": [
    "high_ela_energy",
    "gwa_region_activation",
    "ocr_low_confidence"
  ],
  "model": {
    "name": "aegis_resnet50_v2",
    "version": "2.0.0",
    "mode": "trained"
  },
  "paths": {
    "heatmap_path": "...",
    "ela_path": "..."
  }
}
```

**Hard rule:** `mode` must be `"trained"`. If not, return HTTP 503 so Laravel marks scan `failed` instead of fake Authentic.

---

## 4. Pipeline B — PDF Forensics (Free / Open Source)

There is **no single free “PDF fraud AI SaaS”** that is both production-ready and specialized for grade sheets. The correct free approach is a **multi-signal open-source stack**, then reuse Pipeline A on visuals.

### 4.1 Recommended open-source stack

| Layer | Tool | License | What it detects |
|---|---|---|---|
| **B1 Structural** | `pikepdf` (preferred) or `pypdf` | MPL / BSD | Incremental updates, object count anomalies, encryption flags, producer/creator |
| **B1 Metadata** | `pikepdf` document info + optional **ExifTool** | Free | Create vs modify date mismatch, editor software (Photoshop, Preview, LibreOffice) |
| **B2 Embedded images** | **Poppler** `pdfimages` or `pikepdf` XObject extract | GPL / MPL | Student embedded a photo of a COG inside a PDF wrapper |
| **B3 Rasterize pages** | **pdf2image** + **Poppler** | GPL | Turns each page into PNG for Pipeline A |
| **B4 Visual AI** | Pipeline A (ResNet+ELA) | Project | Same model as JPG/PNG |
| **B5 Text layer** | `pypdf` / `pdfminer.six` | Open | Native text GWA |
| **B5 OCR fallback** | Tesseract on raster page | Apache 2.0 | Scanned PDFs with no text layer |
| **B6 Heuristics** | Custom Python rules | Project | Suspicious JS, launch actions, file attachment, huge image over text |

### 4.2 Why not “just one PDF AI model”?

- PDF forgery is often **structural** (incremental save, image overlay), not a single pixel class.
- Training a PDF-native deep model needs huge labeled PDF corpora (rare for COGs).
- Capstone-feasible: **rules + metadata + visual CNN on rendered pages** is defensible and free.

### 4.3 PDF risk scoring (example formula)

```
pdf_risk = clamp(
    0.35 * structural_score +
    0.25 * metadata_score +
    0.40 * max(page_image_fraud_probs)
  , 0, 100)
```

| Signal | Example risk contribution |
|---|---|
| Incremental updates / multiple revisions | +25 |
| Producer contains “Photoshop”, “GIMP”, “Photopea” | +20 |
| Creation date ≪ Mod date (large gap) | +10 |
| Embedded image covers text region (heuristic) | +25 |
| Page visual AI ≥ threshold | up to +40 (scaled) |
| No text layer + high ELA on raster | +15 |
| JavaScript / Launch / EmbeddedFile | +30 |

Classification:

- `pdf_risk >= ai_fraud_threshold` → `Tampered`
- else → `Authentic` (with indicators still shown to admin)

### 4.4 PDF response shape

```json
{
  "status": "success",
  "pipeline": "pdf_forensics",
  "fraud_probability": 81.0,
  "classification": "Tampered",
  "extracted_gwa": 1.50,
  "anomaly_indicators": [
    "pdf_incremental_update",
    "pdf_producer_image_editor",
    "page1_high_fraud_probability",
    "create_modify_date_mismatch"
  ],
  "pdf_report": {
    "page_count": 1,
    "producer": "Adobe Photoshop",
    "has_javascript": false,
    "has_incremental_updates": true,
    "pages_scanned": 1,
    "max_page_fraud": 88.2
  },
  "paths": {
    "heatmap_path": "heatmap of highest-risk page",
    "ela_path": "..."
  }
}
```

### 4.5 Free alternatives considered

| Option | Verdict |
|---|---|
| **Didier Stevens pdfid / pdf-parser** | Excellent forensic CLI; harder to embed than pikepdf |
| **qpdf --check** | Good structure integrity; weak for “edited grade” |
| **peepdf** | Aging / Python 2 era |
| **Commercial PDF fraud APIs** | Paid; avoid for free-tier capstone |
| **Only text extraction (current)** | Reject — not integrity |
| **Only rasterize + image AI** | Good MVP; add structural layer for thesis strength |

**Recommended MVP:** `pikepdf` + `pdf2image`/`poppler` + Pipeline A + Tesseract.  
**Recommended full:** MVP + ExifTool metadata + JS/attachment rules.

---

## 5. System Integration (Laravel side)

Minimal Laravel changes — keep one endpoint:

| Component | Change |
|---|---|
| `ScanDocumentJob` | Already posts file; store new `anomaly_indicators` + optional `pdf_report` JSON |
| `AIResult` model | Ensure `anomaly_indicators` cast to array (already); optional `pipeline` / `raw_response` column later |
| Auto-approval | Unchanged — already blocks non-authentic / high fraud / anomalies |
| Upload validation | Already `mimes:jpeg,png,pdf` — keep |
| Admin review UI | Show pipeline name + PDF report chips + heatmap |

Optional later:

- `documents.scan_pipeline` column (`image` | `pdf`)
- Admin toggle: “Require visual scan for PDFs” (default on)

---

## 6. Implementation Phases

### Phase 0 — Safety (1 day) — **do first**

- [ ] Disable simulation mode when `APP_ENV=production` or `AEGIS_AI_ALLOW_SIMULATION=false`
- [ ] PDF path: **stop** returning Authentic with 0% fraud
- [ ] Health endpoint: `GET /health` reports `model_loaded: true/false`

### Phase 1 — Image model that actually works (2–4 days)

- [ ] Build `dataset_real/` with authentic + forged samples
- [ ] Update `train_model.py` → `train_model_v2.py` (no horizontal flip on docs; freeze/unfreeze; metrics dump)
- [ ] Train → save `aegis_resnet50_v2.keras`
- [ ] Wire `app.py` to load v2; reject if missing
- [ ] Add Tesseract GWA OCR for images
- [ ] Unit tests: authentic low score, forged high score, PNG accepted

### Phase 2 — PDF dual pipeline MVP (2–3 days)

- [ ] Install: `pikepdf`, `pdf2image`, `pytesseract`, system Poppler + Tesseract
- [ ] Implement `pipelines/pdf_forensics.py`
- [ ] Rasterize first N pages (default 2) → Pipeline A
- [ ] Structural scoring + fuse with visual max
- [ ] Return unified JSON

### Phase 3 — Hardening (1–2 days)

- [ ] Anomaly indicator catalog documented
- [ ] Laravel tests for PDF + image scan job mocks
- [ ] Docker: bake Poppler + Tesseract into `aegis-ai/Dockerfile`
- [ ] Document how to retrain for thesis appendix

---

## 7. Proposed New Module Layout

```
aegis-ai/
├── app.py                      # Flask gateway + routing only
├── train_model_v2.py           # Real training entrypoint
├── requirements.txt
├── aegis_resnet50_v2.keras     # Trained weights (git-lfs or release artifact)
├── pipelines/
│   ├── image_forensics.py      # ELA + CNN + Grad-CAM + OCR
│   ├── pdf_forensics.py        # Structural + raster + fuse
│   └── gwa_ocr.py              # Shared OCR/regex
├── forensics/
│   ├── ela.py
│   ├── gradcam.py
│   └── pdf_signals.py          # pikepdf heuristics
├── dataset_real/               # Real + forged COGs (not public git if PII)
└── tests/
    ├── test_image_pipeline.py
    └── test_pdf_pipeline.py
```

---

## 8. Dependencies (free)

### Python (`requirements.txt` additions)

```
pikepdf>=9.0.0
pdf2image>=1.17.0
pytesseract>=0.3.10
pdfminer.six>=20231228
```

Keep: `tensorflow`, `keras`, `opencv-python-headless`, `Pillow`, `numpy`, `flask`, `pypdf`.

### System packages (Docker / server)

```
# Debian/Ubuntu
apt-get install -y poppler-utils tesseract-ocr tesseract-ocr-eng
```

Windows (dev): install Poppler binaries + Tesseract installer; add to PATH.

---

## 9. Acceptance Criteria (definition of done)

| # | Criterion |
|---|---|
| 1 | JPG authentic sample → fraud &lt; threshold, classification Authentic, `mode=trained` |
| 2 | JPG forged GWA edit → fraud ≥ threshold, heatmap highlights edit region |
| 3 | PNG same behavior as JPG |
| 4 | PDF no longer returns `Authentic (PDF Bypass)` |
| 5 | PDF with Photoshop producer / incremental update raises indicators |
| 6 | Scanned PDF (image-only) still gets visual AI + OCR GWA |
| 7 | Declared GWA vs OCR GWA mismatch still forces 99% in Laravel |
| 8 | Model file missing → scan **fails** (not fake Authentic) |
| 9 | Auto-approval never fires on failed / tampered / high anomaly |

---

## 10. Risks & Mitigations

| Risk | Mitigation |
|---|---|
| Too few real COG samples | Start with 30+ authentic; heavy synthetic forgeries of those; document limitation in thesis |
| Poppler heavy on free hosting | Cap pages to 2; downscale long edge to 1600px before CNN |
| TensorFlow large for Render free | Keep single worker; or host AI on separate free Hugging Face Space / Railway |
| False positives on clean scans | Tune threshold via SuperAdmin `ai_fraud_threshold`; require staff review when medium risk |
| Privacy of training COGs | Strip names/IDs; keep `dataset_real` out of public git |

---

## 11. Thesis / panel talking points

1. **Dual-pipeline design** separates *image pixel forensics* from *PDF container forensics*.
2. **ELA + CNN** is a documented digital-image forensics approach; Grad-CAM provides explainability for OSA staff.
3. **Open-source PDF stack** (pikepdf + Poppler + Tesseract) avoids paid black-box APIs.
4. **Fail-closed** scanning is ethically safer than simulation mode for scholarship decisions.
5. **Logical integrity** (declared GWA vs OCR) complements visual forensics.

---

## 12. Immediate decision needed

| Choice | Recommendation |
|---|---|
| Image model | Retrain ResNet-50 v2 on hybrid real+forged COGs (not synthetic-only) |
| PDF approach | Dual: structural (pikepdf) + raster→image AI (pdf2image) |
| Simulation mode | Off in staging/production |
| Hosting | Keep Flask microservice; ensure model artifact is deployed with the service |

---

## 13. Next step

Implement in this order:

1. Phase 0 safety fixes in `app.py` (block PDF bypass + simulation)
2. Pipeline module split + PDF raster path
3. Training v2 + OCR
4. Docker system deps + Laravel response mapping polish

---

*This document is the solution blueprint for replacing the current simulation/PDF-bypass AI with a real dual-pipeline integrity system.*
