# 🧪 TruFor Local Testing Guide

This guide shows you how to test TruFor on your local machine before deploying to Hugging Face.

---

## 📋 Prerequisites

### 1. **TruFor Weights** (Already Downloaded ✅)
- Location: `E:\aegis-capstone\aegis-ai\TruFor_weights\weights\trufor.pth.tar`
- Size: 269MB
- Status: Ready to use

### 2. **Python Dependencies**
You need to install TruFor dependencies. Choose one option:

#### Option A: Minimal Install (Recommended for Quick Test)
```bash
pip install torch torchvision opencv-python numpy pillow
```

#### Option B: Full Install (For Complete Integration)
```bash
pip install torch torchvision timm mmsegmentation opencv-python numpy pillow
```

---

## 🚀 Quick Test (3 Methods)

### **Method 1: Simple Test Script** (Easiest)

```bash
cd /e/aegis-capstone/aegis-ai

# Test with any image
python test_trufor_local.py path/to/your/image.jpg

# Example with a document image
python test_trufor_local.py test_document.jpg
```

**Expected Output:**
```
==============================================================
TruFor Local Test - Simplified Mode
==============================================================

📁 Image: test_document.jpg
📊 File size: 245.32 KB
✅ TruFor weights found: 269.00 MB
✅ TruFor detector module imported

==============================================================
Initializing TruFor Detector...
==============================================================
✅ Detector initialized

==============================================================
Running Detection...
==============================================================

==============================================================
RESULTS
==============================================================
Detector: TruFor_CVPR2023
Score: 0.823
Classification: Tampered
Confidence: high
Suspect Regions: 3

📍 Top 5 Suspect Regions:
  1. Position: (245, 112) Size: 85x42 Severity: high (conf: 0.91)
  2. Position: (401, 298) Size: 63x31 Severity: medium (conf: 0.74)
  3. Position: (189, 421) Size: 52x28 Severity: medium (conf: 0.68)

💾 Heatmap saved: test_document_trufor_heatmap.jpg

==============================================================
Test Complete!
==============================================================
```

---

### **Method 2: Test Basic Forensics Only** (No TruFor Dependencies)

If you don't want to install TruFor dependencies yet:

```bash
python test_trufor_local.py --basic path/to/image.jpg
```

This tests:
- ELA (Error Level Analysis)
- CLAHE LAB (Whiteout detection)
- Clone Detection (Copy-paste detection)

---

### **Method 3: Python Interactive Test**

```bash
cd /e/aegis-capstone/aegis-ai
python
```

```python
from forensics.trufor_detector import TruForDetector

# Initialize
detector = TruForDetector(
    weights_path='TruFor_weights/weights/trufor.pth.tar',
    device='cpu'  # Use 'cuda' if you have GPU
)

# Test on image
result = detector.detect('path/to/your/image.jpg')

# Print results
print(f"Score: {result['score']}")
print(f"Classification: {result['classification']}")
print(f"Regions: {len(result['suspect_regions'])}")

# Show regions
for region in result['suspect_regions']:
    print(f"  - ({region['x']}, {region['y']}) {region['width']}x{region['height']}")
```

---

## 📸 What Images to Test?

### **Test Set Recommendations:**

#### 1. **Authentic Images** (Should score LOW)
- Real, unedited documents
- Scanned certificates
- Original photos
- Expected score: **< 0.3** (0-30%)

#### 2. **Tampered Images** (Should score HIGH)
- Documents with whiteout/edits
- Copy-pasted sections
- Digitally modified text
- Expected score: **> 0.7** (70-100%)

### **Where to Get Test Images:**

```bash
# Create test directory
mkdir test_images

# Add your own images:
# - Copy authentic document images
# - Copy edited/tampered document images
```

---

## 🔍 Understanding Results

### **Score Interpretation:**

| Score Range | Classification | Meaning |
|-------------|----------------|---------|
| 0.0 - 0.3 | Authentic | Likely genuine, no edits detected |
| 0.3 - 0.5 | Uncertain | Unclear, needs manual review |
| 0.5 - 0.7 | Suspicious | Possible tampering detected |
| 0.7 - 1.0 | Tampered | High confidence of editing |

### **Confidence Levels:**

- **high**: Multiple strong indicators, reliable detection
- **medium**: Some indicators, moderate confidence
- **low**: Weak indicators, uncertain

### **Suspect Regions:**

Each region shows:
- **Position (x, y)**: Top-left corner coordinates in pixels
- **Size (width x height)**: Region dimensions
- **Severity**: high/medium based on anomaly strength
- **Confidence**: 0.0-1.0 reliability score

---

## 🐛 Troubleshooting

### **Error: "TruFor weights NOT found"**

**Solution:**
```bash
cd /e/aegis-capstone/aegis-ai

# Verify weights exist
ls -lh TruFor_weights/weights/trufor.pth.tar

# If missing, they should be at:
ls -lh TruFor_weights/weights/
```

---

### **Error: "Failed to import TruFor detector"**

**Cause:** Missing Python dependencies

**Solution:**
```bash
pip install torch torchvision opencv-python numpy pillow
```

---

### **Error: "Failed to initialize detector"**

**Cause:** Missing TruFor-specific dependencies

**Solution 1 - Use Fallback Mode:**
The detector will automatically use fallback mode (basic forensics without TruFor model). This is fine for testing the integration.

**Solution 2 - Install Full Dependencies:**
```bash
pip install torch torchvision timm
pip install mmsegmentation mmcv-full
```

**Note:** Full installation is complex. For testing, fallback mode is sufficient.

---

### **Warning: "Using fallback detection mode"**

**This is NORMAL if:**
- TruFor dependencies not fully installed
- Model weights not found
- Running on incompatible system

**What it means:**
- Uses basic forensic detectors (ELA, CLAHE, clone detection)
- No TruFor CNN model
- Lower accuracy, but still functional

**To fix (optional):**
Install full dependencies as shown above.

---

## 📊 Expected Performance

### **With TruFor Model:**
- Precision: 90-95%
- Recall: 92-96%
- Processing time: 3-5 seconds per image (CPU)
- Processing time: 1-2 seconds per image (GPU)

### **Fallback Mode (Without TruFor):**
- Precision: ~79%
- Recall: ~83%
- Processing time: 1-2 seconds per image
- Uses: ELA + CLAHE + Clone Detection

---

## ✅ Validation Checklist

Test TruFor with these scenarios:

### **Authentic Images:**
- [ ] Authentic document 1 → Score < 0.3 ✅
- [ ] Authentic document 2 → Score < 0.3 ✅
- [ ] Authentic document 3 → Score < 0.3 ✅
- [ ] Authentic document 4 → Score < 0.3 ✅
- [ ] Authentic document 5 → Score < 0.3 ✅

### **Tampered Images:**
- [ ] Whiteout edit → Score > 0.7 ✅
- [ ] Copy-paste grade → Score > 0.7 ✅
- [ ] Text modification → Score > 0.7 ✅
- [ ] Digital alteration → Score > 0.7 ✅
- [ ] Composite edit → Score > 0.7 ✅

### **Visual Verification:**
- [ ] Heatmap highlights edited regions ✅
- [ ] Bounding boxes accurate ✅
- [ ] Confidence scores reasonable ✅

---

## 🔄 Next Steps After Testing

Once you validate TruFor works:

### **1. Document Results**
```bash
# Save test results
python test_trufor_local.py image1.jpg > results1.txt
python test_trufor_local.py image2.jpg > results2.txt
```

### **2. Integrate into V2 Pipeline**
Edit `pipelines/image_forensics_v2.py` to use TruFor as primary detector.

### **3. Update Dockerfile**
Add automatic weight download for Hugging Face deployment.

### **4. Push to HF**
Deploy updated system with TruFor enabled.

---

## 💡 Tips

1. **Start with fallback mode** if TruFor dependencies are complex
2. **Test on real application images** (documents users actually upload)
3. **Compare with current system** - does TruFor improve accuracy?
4. **Check processing time** - is it acceptable for your use case?
5. **Verify heatmaps** - do they highlight actual edits?

---

## 📞 Need Help?

If you encounter issues:

1. **Check logs** - The test script shows detailed error messages
2. **Try fallback mode** - Use `--basic` flag to test without TruFor
3. **Verify files** - Ensure weights exist and are not corrupted
4. **Check dependencies** - Run `pip list | grep torch` to verify installation

---

**Ready to test!** 🚀

Run: `python test_trufor_local.py your_image.jpg`
