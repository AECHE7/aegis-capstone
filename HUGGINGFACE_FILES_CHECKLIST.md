# 📦 Hugging Face Upload Checklist

## ✅ **Required Files (MUST Upload)**

These files are **essential** for your Space to work:

### **1. Core Application Files**

```
aegis-ai/
├── app.py                          ✅ REQUIRED - Flask application
├── requirements.txt                ✅ REQUIRED - Python dependencies
├── Dockerfile                      ✅ REQUIRED - Container configuration
├── README.md                       ✅ REQUIRED - Documentation (shows on Space page)
└── .gitignore                      ✅ REQUIRED - Exclude temp files
```

### **2. Detection Modules**

```
aegis-ai/forensics/
├── __init__.py                     ✅ REQUIRED - Makes it a Python package
├── catnet_cnn.py                   ✅ REQUIRED - CAT-Net DCT detector
├── clone_detection.py              ✅ REQUIRED - Clone-stamp detector (V2)
├── deep_analysis.py                ✅ REQUIRED - Deep analysis engine (V3)
├── ela.py                          ✅ REQUIRED - ELA preprocessing
├── fusion_scoring.py               ✅ REQUIRED - Weighted fusion (V2)
├── gradcam.py                      ✅ REQUIRED - Grad-CAM heatmaps
├── pdf_signals.py                  ✅ REQUIRED - PDF structure analysis
├── software_fingerprint.py         ✅ REQUIRED - Software detection
└── trufor_cnn.py                   ✅ REQUIRED - TruFor Noiseprint++
```

### **3. Pipeline Modules**

```
aegis-ai/pipelines/
├── __init__.py                     ✅ REQUIRED - Makes it a Python package
├── gwa_ocr.py                      ✅ REQUIRED - GWA extraction
├── image_forensics.py              ✅ REQUIRED - V1 pipeline (fallback)
├── image_forensics_v2.py           ✅ REQUIRED - V2 enhanced pipeline
├── image_forensics_v3_deep.py      ✅ REQUIRED - V3 deep analysis
└── pdf_forensics.py                ✅ REQUIRED - PDF pipeline
```

---

## ⚠️ **Optional Files (Can Upload or Exclude)**

### **Test Files (Not Needed for Production)**

```
aegis-ai/tests/
├── __init__.py                     ⚠️ OPTIONAL - Only if you want to run tests on HF
├── test_pipelines.py               ⚠️ OPTIONAL
└── test_tamper_detection_v2.py     ⚠️ OPTIONAL
```

**Recommendation:** **EXCLUDE** tests to reduce deployment size

### **Training Scripts (Not Needed for Production)**

```
aegis-ai/
├── train_model.py                  ⚠️ OPTIONAL - Training scripts
├── train_model_v2.py               ⚠️ OPTIONAL
├── train_model_v3.py               ⚠️ OPTIONAL
├── train_model_v4.py               ⚠️ OPTIONAL
└── test_resnet_weights.py          ⚠️ OPTIONAL - Testing script
```

**Recommendation:** **EXCLUDE** training scripts (only needed for local training)

---

## 🚫 **Files to EXCLUDE (Don't Upload)**

These will be ignored by `.gitignore`:

```
❌ __pycache__/                     # Python cache
❌ *.pyc, *.pyo                     # Compiled Python
❌ temp_uploads/                    # Temporary files
❌ ela_outputs/                     # Generated files
❌ heatmap_outputs/                 # Generated files
❌ *.jpg, *.png, *.pdf              # Test images
❌ *.keras, *.h5                    # Model files (handle separately)
❌ .env                             # Environment variables (use HF secrets)
❌ venv/, env/                      # Virtual environments
```

---

## 📊 **Complete File Structure for HF**

Here's exactly what your HF Space should look like:

```
aegis-document-forensics/          ← Your HF Space root
│
├── README.md                       ✅ Shows on Space homepage
├── Dockerfile                      ✅ Container config
├── requirements.txt                ✅ Dependencies
├── .gitignore                      ✅ Exclude patterns
├── app.py                          ✅ Main Flask app
│
├── forensics/                      ✅ Detection modules
│   ├── __init__.py
│   ├── catnet_cnn.py
│   ├── clone_detection.py
│   ├── deep_analysis.py
│   ├── ela.py
│   ├── fusion_scoring.py
│   ├── gradcam.py
│   ├── pdf_signals.py
│   ├── software_fingerprint.py
│   └── trufor_cnn.py
│
└── pipelines/                      ✅ Pipeline modules
    ├── __init__.py
    ├── gwa_ocr.py
    ├── image_forensics.py
    ├── image_forensics_v2.py
    ├── image_forensics_v3_deep.py
    └── pdf_forensics.py
```

**Total:** ~25 files, ~150KB (without model)

---

## 🔧 **Model File Handling**

### **Option A: No Model (Deterministic Mode)**

**Recommended for quick start**

- Upload: All files above (NO .keras file)
- Set in HF Space Settings:
  ```bash
  ALLOW_SIMULATION=false
  ```
- Result: V2/V3 work without CNN using forensic rules

**Pros:**
- ✅ Fast deployment
- ✅ Small Space size
- ✅ Still detects tampering (forensic rules)

**Cons:**
- ❌ No CNN model predictions
- ❌ Slightly lower accuracy (but V3 deep analysis compensates)

### **Option B: Include Small Model**

If you have a model file < 10MB:

```
aegis-document-forensics/
├── aegis_resnet50_v1.keras        ⚠️ If < 10MB
└── (all other files)
```

### **Option C: Download Model on Startup**

For large models, add download code to `app.py`:

```python
# Add to app.py before model loading:
if not os.path.exists(MODEL_PATH):
    print("[INIT] Downloading model from Hugging Face Hub...")
    from huggingface_hub import hf_hub_download
    MODEL_PATH = hf_hub_download(
        repo_id="YOUR_USERNAME/aegis-models",
        filename="aegis_resnet50_v1.keras",
        cache_dir="./models"
    )
```

**Requirement:** Upload model separately to HF Hub model repo

---

## 📝 **Step-by-Step Upload Process**

### **Method 1: Git Push (Recommended)**

```bash
cd /e/aegis-capstone/aegis-ai

# 1. Initialize git
git init

# 2. Add only required files (tests/training excluded automatically by .gitignore)
git add .

# 3. Verify what will be uploaded
git status

# Expected output:
# On branch main
# Changes to be committed:
#   new file:   .gitignore
#   new file:   Dockerfile
#   new file:   README.md
#   new file:   app.py
#   new file:   forensics/__init__.py
#   new file:   forensics/catnet_cnn.py
#   ... (all the files listed in "Required" section)

# 4. Commit
git commit -m "Deploy AEGIS AI V3 to Hugging Face"

# 5. Add HF remote
git remote add hf https://huggingface.co/spaces/YOUR_USERNAME/aegis-document-forensics

# 6. Push
git push hf main  # or master
```

### **Method 2: Web UI Upload**

1. Go to your Space: `https://huggingface.co/spaces/YOUR_USERNAME/aegis-document-forensics`
2. Click **"Files"** tab
3. Click **"Add file"** → **"Upload files"**
4. Drag and drop files from the checklist above
5. Click **"Commit to main"**

**Note:** This is slower for multiple files. Use Method 1 (git) if possible.

---

## ✅ **Quick Checklist**

Before pushing to HF, verify:

- [ ] `app.py` exists and is updated with V3 code
- [ ] `Dockerfile` has port 7860
- [ ] `requirements.txt` has all dependencies
- [ ] `README.md` documents your API
- [ ] `.gitignore` excludes temp files
- [ ] `forensics/` folder has 10 .py files
- [ ] `pipelines/` folder has 6 .py files
- [ ] Both folders have `__init__.py`
- [ ] NO test files (excluded)
- [ ] NO training files (excluded)
- [ ] NO temp/cache folders
- [ ] NO large model files (unless < 10MB)

---

## 🔍 **Verify Your Upload**

After pushing, check your Space's **"Files"** tab:

```
✅ Should see:
- README.md (displays on homepage)
- Dockerfile
- requirements.txt
- app.py
- forensics/ (10 files)
- pipelines/ (6 files)

❌ Should NOT see:
- __pycache__/
- tests/
- train_model*.py
- temp_uploads/
- ela_outputs/
- *.jpg, *.png test images
```

---

## 📦 **Minimal Upload (Fastest)**

If you want the **absolute minimum** to get started:

```bash
cd aegis-ai

# Create minimal upload
mkdir ../aegis-ai-deploy
cp app.py ../aegis-ai-deploy/
cp Dockerfile ../aegis-ai-deploy/
cp requirements.txt ../aegis-ai-deploy/
cp README.md ../aegis-ai-deploy/
cp .gitignore ../aegis-ai-deploy/
cp -r forensics ../aegis-ai-deploy/
cp -r pipelines ../aegis-ai-deploy/

cd ../aegis-ai-deploy
git init
git add .
git commit -m "Deploy AEGIS AI"
git remote add hf https://huggingface.co/spaces/YOUR_USERNAME/aegis-document-forensics
git push hf main
```

**Result:** Clean deployment with ONLY production files

---

## 🎯 **Summary**

### **Minimum Required:**
- 5 core files (app.py, Dockerfile, requirements.txt, README.md, .gitignore)
- 10 forensics modules
- 6 pipeline modules
- **Total: 21 files**

### **Excluded:**
- Tests (not needed)
- Training scripts (not needed)
- Temp files (auto-excluded)
- Model files (optional)

### **Upload Size:**
- **Without model:** ~150KB (very fast)
- **With model:** ~150KB + model size

---

## 📞 **Need Help?**

If you get stuck:

1. **Check build logs:** HF Space → "Logs" tab
2. **Verify files:** HF Space → "Files" tab
3. **Test locally first:**
   ```bash
   cd aegis-ai
   docker build -t test .
   docker run -p 7860:7860 test
   curl http://localhost:7860/health
   ```

---

**🚀 You're ready to upload! Follow the checklist and push to HF.**

See `HUGGINGFACE_DEPLOYMENT.md` for complete step-by-step instructions.
