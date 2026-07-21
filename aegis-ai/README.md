# A.E.G.I.S. Document Integrity Scanner - AI Microservice

**Version:** 3.0.0 (Deep Analysis V3)
**Purpose:** Forensic analysis and tamper detection for academic documents

---

## 🎯 Features

### V3 - Deep Analysis Mode (Latest)
- Multi-layer heatmap visualization (ELA, Noise, Edges, Color)
- Pixel-perfect edit localization with bounding boxes
- Zoomed previews of suspect regions
- Region clustering and severity classification
- Comprehensive forensic reporting

### V2 - Enhanced Detection
- Clone-stamp & copy-move detection (ORB feature matching)
- Weighted fusion scoring (reduces false positives by 14%)
- Confidence levels (high/medium/low)
- Detector agreement analysis

### V1 - Standard Detection (Legacy)
- ELA analysis
- CLAHE LAB whiteout detection
- TruFor CNN Noiseprint++
- CAT-Net DCT compression analysis

---

## 🔧 Configuration

### Environment Variables

```bash
# Pipeline Selection
USE_V2_PIPELINE=true          # Enable V2 enhanced detection
USE_DEEP_ANALYSIS=false       # Enable V3 deep analysis (for critical cases)
FUSION_MODE=balanced          # strict | balanced | sensitive

# Model Configuration
MODEL_PATH=aegis_resnet50_v1.keras
ALLOW_SIMULATION=false        # Disable in production

# Flask Settings
FLASK_PORT=7860              # Hugging Face Spaces default
FLASK_DEBUG=false
```

---

## 📡 API Endpoints

### `GET /`
Health check with service information

**Response:**
```json
{
  "service": "A.E.G.I.S. Dual-Pipeline AI Document Integrity Scanner",
  "status": "running",
  "version": "2.1.0",
  "pipeline_version": "V3_DEEP",
  "model_loaded": true
}
```

### `POST /analyze-document`
Analyze document for tampering

**Parameters:**
- `file` (required): Image file (JPG/PNG/PDF)
- `mode` (optional): `standard` or `deep`

**Example:**
```bash
curl -X POST "https://your-space.hf.space/analyze-document?mode=deep" \
  -F "file=@document.jpg"
```

**Response:**
```json
{
  "fraud_probability": 78.3,
  "classification": "Tampered",
  "confidence": "high",
  "detector_agreement": 0.625,
  "anomaly_indicators": ["clone_stamp_detected", "digital_whiteout_box_detected"],
  "deep_analysis_report": { /* detailed forensics */ },
  "visualizations": { /* base64 heatmaps */ }
}
```

### `GET /health`
Service health diagnostics

---

## 🏃 Quick Start

### Local Development

```bash
# Install dependencies
pip install -r requirements.txt

# Run server
python app.py
```

### Docker

```bash
# Build
docker build -t aegis-ai .

# Run
docker run -p 7860:7860 aegis-ai
```

---

## 📊 Performance

| Mode | Processing Time | Use Case |
|------|----------------|----------|
| V1 Standard | 1.8s | Legacy (deprecated) |
| V2 Enhanced | 2.1s | Regular scanning |
| V3 Deep | 4-6s | High-value, disputes |

---

## 🔒 Security

- No data persistence (stateless)
- Temporary files auto-cleaned
- Base64-encoded outputs (no file storage)
- Fail-closed detection (errors = suspicious)

---

## 📚 Documentation

- **Deep Analysis Guide:** `docs/DEEP_ANALYSIS_V3_GUIDE.md`
- **V2 Improvements:** `docs/TAMPER_DETECTION_IMPROVEMENTS_V2.md`
- **API Integration:** See Laravel `ScanDocumentJob.php`

---

## 🤝 Integration

### Laravel Example

```php
$response = Http::timeout(180)
    ->attach('file', $fileContents, $filename)
    ->post(env('AEGIS_AI_URL') . '/analyze-document?mode=deep');

$result = $response->json();
```

---

## 📝 License

Apache 2.0

---

## 🙏 Credits

Built with:
- TensorFlow / Keras
- OpenCV
- TruFor CNN (grip-unina)
- CAT-Net (mjkwon2021)
- Tesseract OCR

---

**Deployed on Hugging Face Spaces for reliable, scalable document forensics.**
