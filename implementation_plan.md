# Dual-CNN Document Forgery Suite (TruFor + CAT-Net FCN)

This plan details integrating a **Dual-CNN Research Ensemble** combining **`grip-unina/TruFor`** (PyTorch Noiseprint++ CNN backbone) and **`mjkwon2021/CAT-Net`** (Fully Convolutional Neural Network - FCN/HRNet).

---

## 1. Technical Architecture & Dual-CNN Fusion

```mermaid
flowchart TD
    Doc[Uploaded Student Document PNG / JPG] --> Pipeline[Dual-CNN Document Forgery Suite]
    
    subgraph Dual-CNN Research Engine
        TruFor[1. TruFor CNN Backbone\ngrip-unina - Scans Noiseprint++ pixel noise fields]
        CATNet[2. CAT-Net FCN HRNet CNN\nmjkwon2021 - Traces DCT compression frequency grids]
        Classic[3. CLAHE LAB + ELA + OCR Math\nDeterministic Signal Auditor]
    end

    Pipeline --> TruFor
    Pipeline --> CATNet
    Pipeline --> Classic

    TruFor --> Map1[TruFor Pixel Map]
    CATNet --> Map2[CAT-Net Artifact Map]

    Map1 --> Fusion[Dual-CNN Risk Fusion Engine]
    Map2 --> Fusion
    Classic --> Fusion

    Fusion --> Result[Unified Fraud Probability & High-Precision Red Bounding Box Heatmap]
```

---

## 2. Proposed Code Changes

### AI Microservice (`aegis-ai/`)

#### [NEW] [trufor_cnn.py](file:///E:/aegis-capstone/aegis-ai/forensics/trufor_cnn.py)
- Implements the **TruFor CNN Noiseprint++ Feature Extractor**.
- Generates pixel-dense 0-to-1 forgery probability maps.

#### [NEW] [catnet_cnn.py](file:///E:/aegis-capstone/aegis-ai/forensics/catnet_cnn.py)
- Implements the **CAT-Net Fully Convolutional Neural Network (FCN / HRNet)**.
- Scans DCT compression grid artifacts.

#### [MODIFY] [image_forensics.py](file:///E:/aegis-capstone/aegis-ai/pipelines/image_forensics.py)
- Fuses TruFor CNN, CAT-Net FCN CNN, CLAHE LAB, and ELA into a unified high-precision visual pipeline.

---

### Laravel Web Application (`resources/views/admin/`)

#### [MODIFY] [review.blade.php](file:///E:/aegis-capstone/resources/views/admin/review.blade.php)
- Updates Document Forensics Suite display to feature **TruFor + CAT-Net Dual-CNN** architecture badge.

---

## 3. Verification Plan

### Automated Tests
1. Run Python unit tests in `aegis-ai`:
   ```bash
   python -m unittest discover -s tests -p "test_*.py"
   ```
2. Run Laravel test suites:
   ```bash
   php artisan test --filter=SecurityHardeningTest
   ```

### Manual Verification
- Test scanning authentic vs. whiteout-edited documents to confirm high-resolution Dual-CNN patch detection and red heatmap bounding box precision.
