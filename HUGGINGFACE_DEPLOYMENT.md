# 🤗 Hugging Face Deployment Guide

## Why Deploy to Hugging Face?

### **Current Setup (Local):**
```
Laravel App → http://127.0.0.1:5000/analyze-document
```

**Problems:**
- ❌ Single point of failure (if local server crashes)
- ❌ No auto-scaling (slow during peak application season)
- ❌ Manual updates required
- ❌ No GPU acceleration (slower inference)

### **Proposed Setup (Hugging Face):**
```
Laravel App → https://aegis-forensics.hf.space/analyze-document
```

**Benefits:**
- ✅ Free GPU for faster processing
- ✅ Auto-scaling (handles 1000s of concurrent requests)
- ✅ 99.9% uptime SLA
- ✅ Global CDN (fast worldwide)
- ✅ Git-based deployment (push = auto-deploy)
- ✅ Zero infrastructure costs

---

## 📋 Deployment Checklist

### **Prerequisites:**
- [x] Hugging Face account (free): https://huggingface.co/join
- [x] Git installed
- [ ] aegis-ai code pushed to git
- [ ] Model file available (.keras file)

---

## 🚀 Step-by-Step Deployment

### **Step 1: Create Hugging Face Space**

1. Go to: https://huggingface.co/spaces
2. Click **"Create new Space"**
3. Fill in:
   - **Name:** `aegis-document-forensics` (or your choice)
   - **License:** Apache 2.0
   - **Space SDK:** **Docker** ← IMPORTANT
   - **Space Hardware:** CPU Basic (free) or upgrade to GPU later
4. Click **"Create Space"**

You'll get a URL like: `https://huggingface.co/spaces/YOUR_USERNAME/aegis-document-forensics`

---

### **Step 2: Prepare aegis-ai Directory**

Your `aegis-ai/` folder is already configured! Just verify these files exist:

```bash
cd aegis-capstone/aegis-ai

# Required files for Hugging Face:
ls -la
# Should show:
# - Dockerfile          ← Already configured (port 7860)
# - requirements.txt    ← Python dependencies
# - app.py             ← Flask application
# - README.md          ← Documentation (just created)
# - .gitignore         ← Ignore temp files (just created)
# - forensics/         ← Detection modules
# - pipelines/         ← V1/V2/V3 pipelines
```

---

### **Step 3: Add Model File**

**Option A: Include Model in Repo (if < 10MB)**
```bash
cd aegis-capstone/aegis-ai
# Copy your trained model
cp /path/to/aegis_resnet50_v1.keras .
```

**Option B: Download Model on Startup (Recommended if > 10MB)**

Update `app.py` to download model from Hugging Face Hub:

```python
# Add at top of app.py
import os
from huggingface_hub import hf_hub_download

# Before loading model:
if not os.path.exists(MODEL_PATH):
    print(f"[INIT] Model not found locally. Downloading from Hugging Face Hub...")
    MODEL_PATH = hf_hub_download(
        repo_id="YOUR_USERNAME/aegis-model-weights",
        filename="aegis_resnet50_v1.keras"
    )
```

**Option C: Use Without Model (Deterministic Mode)**
- V2/V3 work without CNN model using forensic rules
- Set `ALLOW_SIMULATION=false` to ensure fail-closed behavior

---

### **Step 4: Initialize Git in aegis-ai**

```bash
cd aegis-capstone/aegis-ai

# Initialize git (if not already)
git init

# Add all files
git add .

# Commit
git commit -m "Initial deployment: AEGIS AI Forensics V3"
```

---

### **Step 5: Push to Hugging Face Space**

```bash
# Add Hugging Face remote
git remote add hf https://huggingface.co/spaces/YOUR_USERNAME/aegis-document-forensics

# Push
git push hf main  # or master, depending on your branch
```

**Authentication:**
- Username: Your HF username
- Password: Your HF **Access Token** (get from: https://huggingface.co/settings/tokens)

---

### **Step 6: Wait for Build**

1. Go to your Space URL: `https://huggingface.co/spaces/YOUR_USERNAME/aegis-document-forensics`
2. You'll see **"Building..."** status
3. Check **"Logs"** tab to monitor build progress
4. Build takes ~3-5 minutes

**Expected logs:**
```
Building Docker image...
Installing dependencies...
Starting Flask app...
======================================================================
A.E.G.I.S. Document Integrity Scanner - Starting...
======================================================================
Pipeline Version: V2 (Enhanced Detection)
Fusion Mode: balanced
Model Loaded: False  # OK if using deterministic mode
======================================================================
Running on http://0.0.0.0:7860
```

---

### **Step 7: Test the Deployment**

```bash
# Health check
curl https://YOUR_USERNAME-aegis-document-forensics.hf.space/health

# Expected response:
{
  "status": "healthy",
  "pipeline_version": "V2",
  "model_loaded": false,
  "tensorflow_available": true
}

# Test document analysis
curl -X POST \
  "https://YOUR_USERNAME-aegis-document-forensics.hf.space/analyze-document" \
  -F "file=@test_document.jpg"
```

---

### **Step 8: Update Laravel Configuration**

**File:** `.env` (production)

```bash
# Before (local):
AEGIS_AI_URL=http://127.0.0.1:5000

# After (Hugging Face):
AEGIS_AI_URL=https://YOUR_USERNAME-aegis-document-forensics.hf.space
```

**That's it!** Laravel will now call Hugging Face instead of localhost.

---

## 🔧 Environment Variables on Hugging Face

### **Set in Space Settings:**

1. Go to your Space → **"Settings"** tab
2. Scroll to **"Variables and secrets"**
3. Add:

```bash
USE_V2_PIPELINE=true
USE_DEEP_ANALYSIS=false  # Enable for deep mode
FUSION_MODE=balanced
ALLOW_SIMULATION=false   # CRITICAL: Disable in production
FLASK_PORT=7860          # Already set by Dockerfile
```

---

## 📊 Space Configuration

### **Hardware Options:**

| Tier | CPU | RAM | GPU | Cost | Recommendation |
|------|-----|-----|-----|------|----------------|
| **CPU Basic** | 2 vCPU | 16GB | None | **FREE** | ✅ Start here |
| CPU Upgrade | 8 vCPU | 32GB | None | $5/mo | If slow |
| GPU - T4 | 4 vCPU | 16GB | T4 | $60/mo | Faster inference |

**Recommendation:** Start with **CPU Basic (free)**. Upgrade only if processing is too slow.

---

## 🔄 Updating Your Deployment

### **Method 1: Git Push (Recommended)**

```bash
cd aegis-capstone/aegis-ai

# Make changes to code
nano app.py

# Commit
git add .
git commit -m "feat: improve detection accuracy"

# Push to Hugging Face
git push hf main
```

**Auto-deploys in 2-3 minutes!**

### **Method 2: Web UI Upload**

1. Go to Space → **"Files"** tab
2. Click file to edit
3. Make changes
4. Click **"Commit to main"**

---

## 🐛 Troubleshooting

### **Issue: Space stuck on "Building..."**

**Check logs:**
1. Go to Space → **"Logs"** tab
2. Look for errors

**Common issues:**
- Missing `requirements.txt` dependency
- Wrong port (must be 7860)
- Model file too large (>10GB)

**Fix:** Update code, commit, push again

---

### **Issue: "Module not found" error**

**Cause:** Missing dependency in `requirements.txt`

**Fix:**
```bash
cd aegis-ai

# Add missing package
echo "missing-package==1.0.0" >> requirements.txt

# Commit and push
git add requirements.txt
git commit -m "fix: add missing dependency"
git push hf main
```

---

### **Issue: Space shows "Sleeping"**

**Cause:** Free tier spaces sleep after 48h of inactivity

**Solution:**
- Spaces wake up automatically on first request (takes ~30s)
- Upgrade to persistent hardware ($5/mo) to prevent sleeping

---

### **Issue: Slow processing (>10s per document)**

**Cause:** CPU-only inference is slow

**Solutions:**
1. **Enable V2 pipeline only** (disable deep analysis for regular scans)
2. **Upgrade to GPU hardware** ($60/mo for T4 GPU)
3. **Optimize model:** Use smaller model (MobileNet instead of ResNet)

---

## 📈 Monitoring

### **Track Usage:**

1. Go to Space → **"Analytics"** tab
2. View:
   - Request count
   - Error rate
   - Processing time
   - Uptime

### **Set Alerts:**

1. Space → **"Settings"** → **"Notifications"**
2. Enable alerts for:
   - Space down
   - Error rate > 5%
   - High latency

---

## 💰 Cost Comparison

| Setup | Monthly Cost | Reliability | Scalability |
|-------|--------------|-------------|-------------|
| **Local (127.0.0.1)** | $0 | ⭐⭐ | ❌ None |
| **HF CPU Basic** | **$0** | ⭐⭐⭐⭐⭐ | ✅ Auto |
| **HF GPU T4** | $60 | ⭐⭐⭐⭐⭐ | ✅ Auto |
| **AWS EC2 (t3.medium)** | $30 | ⭐⭐⭐ | Manual |

**Best Choice:** Hugging Face CPU Basic (free + reliable)

---

## 🔒 Security

### **API Access Control:**

By default, your Space is **public** (anyone can call it).

**To restrict access:**

1. Space → **"Settings"** → **"Access Control"**
2. Options:
   - **Public** (default): Anyone can use
   - **Private**: Only you
   - **Gated**: Requires approval

**Recommended for production:**
- Keep **Public** (you want students to upload docs)
- Add rate limiting in Laravel (prevent abuse)
- Monitor analytics for suspicious activity

### **Environment Secrets:**

**Never commit sensitive data!**

Store in Space Settings → **"Variables and secrets"** → **"Secrets"**

```bash
# Example:
API_KEY=your_secret_key
DATABASE_PASSWORD=***
```

Access in code:
```python
import os
api_key = os.environ.get('API_KEY')
```

---

## ✅ Final Checklist

Before going live:

- [ ] Space builds successfully
- [ ] Health check returns 200 OK
- [ ] Test authentic document → fraud < 30%
- [ ] Test tampered document → fraud > 70%
- [ ] Update Laravel `.env` with HF URL
- [ ] Test end-to-end (Laravel → HF → results)
- [ ] Monitor analytics for 24h
- [ ] Set up uptime monitoring (UptimeRobot)

---

## 🎯 Summary

### **Deployment Flow:**

```
1. Create HF Space (Docker SDK)
   ↓
2. git init in aegis-ai/
   ↓
3. git push hf main
   ↓
4. Wait for build (3-5 min)
   ↓
5. Test endpoint
   ↓
6. Update Laravel AEGIS_AI_URL
   ↓
7. ✅ LIVE ON HUGGING FACE
```

### **Benefits You Get:**

✅ **Free hosting** (no infrastructure costs)
✅ **Auto-scaling** (handles peak traffic)
✅ **99.9% uptime** (more reliable than local)
✅ **Git-based deployment** (push = deploy)
✅ **Global CDN** (fast worldwide)
✅ **Easy rollback** (git revert)

---

## 📞 Support

**Hugging Face Docs:** https://huggingface.co/docs/hub/spaces-overview
**Community Forum:** https://discuss.huggingface.co/
**Status Page:** https://status.huggingface.co/

---

**🚀 Deploy your AEGIS AI to Hugging Face and never worry about AI infrastructure again!**
