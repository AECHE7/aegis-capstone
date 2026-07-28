import os
import uuid
import cv2
import numpy as np

try:
    import tensorflow as tf
    TENSORFLOW_AVAILABLE = True
except ImportError:
    TENSORFLOW_AVAILABLE = False
    print("[INIT] Warning: TensorFlow unavailable.")

from flask import Flask, request, jsonify, send_from_directory

# Forensic Pipeline Imports
from pipelines.gwa_ocr import PYTESSERACT_AVAILABLE
from pipelines.image_forensics import run_image_pipeline  # V1 - kept for fallback
from pipelines.image_forensics_v2 import run_image_pipeline_v2  # V2 - enhanced detection
from pipelines.image_forensics_v3_deep import run_deep_analysis_pipeline  # V3 - deep analysis mode
from pipelines.pdf_forensics import run_pdf_pipeline, PDF2IMAGE_AVAILABLE
from forensics.pdf_signals import PIKEPDF_AVAILABLE

# Local / Database Persistent Storage (100% Free - Zero Cloudinary Overhead)
def upload_heatmap_to_cloudinary(local_path: str, public_id: str) -> str | None:
    """Local storage active: heatmaps are saved locally and backed up in PostgreSQL database."""
    return None

app = Flask(__name__)

UPLOAD_FOLDER = 'temp_uploads'
ELA_FOLDER = 'ela_outputs'
HEATMAP_FOLDER = 'heatmap_outputs'
# Model Weights Search (EfficientNet-B4 default, ResNet-50 fallback)
DEFAULT_MODEL = 'aegis_efficientnet_b4.keras' if os.path.exists('aegis_efficientnet_b4.keras') else 'aegis_resnet50_v1.keras'
MODEL_PATH = os.environ.get('MODEL_PATH', DEFAULT_MODEL)
ALLOW_SIMULATION = os.environ.get('ALLOW_SIMULATION', 'false').lower() == 'true' or os.environ.get('AEGIS_AI_ALLOW_SIMULATION', 'false').lower() == 'true'

# V2/V3 Configuration - Enable enhanced detection pipeline
USE_V2_PIPELINE = os.environ.get('USE_V2_PIPELINE', 'true').lower() == 'true'
USE_DEEP_ANALYSIS = os.environ.get('USE_DEEP_ANALYSIS', 'false').lower() == 'true'  # V3 Deep Analysis Mode
FUSION_MODE = os.environ.get('FUSION_MODE', 'balanced')  # strict, balanced, or sensitive

for folder in [UPLOAD_FOLDER, ELA_FOLDER, HEATMAP_FOLDER]:
    os.makedirs(folder, exist_ok=True)

# Load AI Classification Model Globally
model = None
if TENSORFLOW_AVAILABLE:
    if os.path.exists(MODEL_PATH):
        try:
            print(f"[INIT] Loading A.E.G.I.S. Visual AI Model from {MODEL_PATH}...")
            model = tf.keras.models.load_model(MODEL_PATH)
            print("[INIT] Model Loaded Successfully!")
        except Exception as e:
            print(f"[INIT] Error loading model weights from {MODEL_PATH}: {e}")
            model = None
    else:
        print(f"[INIT] Model weights file '{MODEL_PATH}' not found.")
        model = None

@app.route('/', methods=['GET'])
def index():
    """Root homepage endpoint for Hugging Face Space preview."""
    return jsonify({
        "service": "A.E.G.I.S. Dual-Pipeline AI Document Integrity Scanner",
        "status": "running",
        "version": "2.1.0",  # Updated to reflect V2 improvements
        "pipeline_version": "V2" if USE_V2_PIPELINE else "V1",
        "fusion_mode": FUSION_MODE if USE_V2_PIPELINE else "N/A",
        "endpoints": {
            "health": "/health",
            "analyze": "/analyze-document"
        },
        "model_loaded": model is not None,
        "allow_simulation": ALLOW_SIMULATION
    }), 200

@app.route('/health', methods=['GET'])
def health_check():
    """Health check diagnostic endpoint."""
    pipeline_ver = "V3_DEEP" if USE_DEEP_ANALYSIS else ("V2" if USE_V2_PIPELINE else "V1")
    return jsonify({
        "status": "healthy",
        "pipeline_version": pipeline_ver,
        "deep_analysis_enabled": USE_DEEP_ANALYSIS,
        "fusion_mode": FUSION_MODE if (USE_V2_PIPELINE or USE_DEEP_ANALYSIS) else "N/A",
        "model_loaded": model is not None,
        "tensorflow_available": TENSORFLOW_AVAILABLE,
        "pikepdf_available": PIKEPDF_AVAILABLE,
        "pdf2image_available": PDF2IMAGE_AVAILABLE,
        "tesseract_available": PYTESSERACT_AVAILABLE,
        "allow_simulation": ALLOW_SIMULATION
    }), 200

@app.route('/analyze-document', methods=['POST'])
def analyze_document():
    if 'file' not in request.files:
        return jsonify({"error": "No file part in request"}), 400
        
    file = request.files['file']
    if file.filename == '':
        return jsonify({"error": "No file selected"}), 400
        
    file_uuid = str(uuid.uuid4())
    filename_parts = file.filename.rsplit('.', 1)
    if len(filename_parts) < 2:
        return jsonify({"error": "File lacks valid extension"}), 400
        
    original_ext = filename_parts[1].lower()
    heatmap_ext = 'png' if original_ext == 'png' else 'jpg'
    original_path = os.path.join(UPLOAD_FOLDER, f"{file_uuid}.{original_ext}")
    ela_path = os.path.join(ELA_FOLDER, f"{file_uuid}_ela.jpg")
    heatmap_path = os.path.join(HEATMAP_FOLDER, f"{file_uuid}_heatmap.{heatmap_ext}")
    
    file.save(original_path)
    
    try:
        if original_ext == 'pdf':
            # Execute Pipeline B (PDF Forensics)
            result = run_pdf_pipeline(
                pdf_path=original_path,
                ela_folder=ELA_FOLDER,
                heatmap_folder=HEATMAP_FOLDER,
                temp_folder=UPLOAD_FOLDER,
                model=model,
                allow_simulation=ALLOW_SIMULATION
            )
        elif original_ext in ['jpg', 'jpeg', 'png']:
            # Execute Pipeline A (Image Forensics)
            req_mode = request.args.get('mode', request.form.get('mode', '')).lower()
            run_deep = False
            if req_mode == 'deep':
                run_deep = True
            elif req_mode in ['standard', 'enhanced']:
                run_deep = False
            else:
                run_deep = USE_DEEP_ANALYSIS

            if run_deep:
                # V3 - DEEP ANALYSIS MODE for maximum accuracy and detailed inspection
                print(f"[ANALYZE] Running DEEP ANALYSIS V3 (fusion_mode={FUSION_MODE})")
                print("[ANALYZE] Deep mode: Multi-layer heatmaps, precise edit localization, comprehensive forensics")
                result = run_deep_analysis_pipeline(
                    original_path=original_path,
                    ela_path=ela_path,
                    heatmap_path=heatmap_path,
                    model=model,
                    fusion_mode=FUSION_MODE
                )
            elif USE_V2_PIPELINE:
                # V2 - Enhanced detection with clone-stamp, weighted fusion, confidence scoring
                print(f"[ANALYZE] Running Image Forensics V2 (fusion_mode={FUSION_MODE})")
                result = run_image_pipeline_v2(
                    original_path=original_path,
                    ela_path=ela_path,
                    heatmap_path=heatmap_path,
                    model=model,
                    allow_simulation=ALLOW_SIMULATION,
                    fusion_mode=FUSION_MODE
                )
            else:
                # V1 - Legacy pipeline (fallback)
                print("[ANALYZE] Running Image Forensics V1 (legacy)")
                result = run_image_pipeline(
                    original_path=original_path,
                    ela_path=ela_path,
                    heatmap_path=heatmap_path,
                    model=model,
                    allow_simulation=ALLOW_SIMULATION
                )
        else:
            return jsonify({"error": f"Unsupported file extension: {original_ext}"}), 400

        # Base64-encode generated heatmap image for 100% free database persistence in PostgreSQL
        h_path = result.get('paths', {}).get('heatmap_path')
        if h_path and os.path.exists(h_path):
            try:
                import base64
                with open(h_path, 'rb') as hf:
                    result['heatmap_base64'] = base64.b64encode(hf.read()).decode('utf-8')
            except Exception as he:
                print(f"[ANALYZE] Heatmap base64 encoding warning: {he}")

        # Clean up uploaded raw file to free disk space
        if os.path.exists(original_path):
            os.remove(original_path)

        return jsonify(result), 200

    except RuntimeError as re:
        print(f"[ANALYZE_ERROR] Security Fail-Closed: {re}")
        return jsonify({
            "error": "AI service unavailable (Fail-Closed Security)",
            "details": str(re)
        }), 503
    except Exception as e:
        print(f"[ANALYZE_ERROR] Internal Exception: {e}")
        return jsonify({"error": str(e)}), 500

@app.route('/heatmap/<filename>', methods=['GET'])
def serve_heatmap(filename):
    """Serve local heatmap images."""
    return send_from_directory(os.path.abspath(HEATMAP_FOLDER), filename)

if __name__ == '__main__':
    print("\n" + "=" * 70)
    print("A.E.G.I.S. Document Integrity Scanner - Starting...")
    print("=" * 70)

    if USE_DEEP_ANALYSIS:
        print("Pipeline Version: V3 (DEEP ANALYSIS MODE)")
        print(f"Fusion Mode: {FUSION_MODE}")
        print("V3 Deep Analysis Features:")
        print("  * Multi-layer heatmap visualization")
        print("  * Precise pixel-level edit localization")
        print("  * Region-by-region forensic analysis")
        print("  * ELA detailed inspection (32x32 grid)")
        print("  * Noise consistency mapping")
        print("  * Edge consistency analysis")
        print("  * Color uniformity detection")
        print("  * Automated region clustering")
        print("  * Zoomed suspect area previews")
        print("  + All V2 detectors (clone-stamp, fusion scoring, etc.)")
    elif USE_V2_PIPELINE:
        print("Pipeline Version: V2 (Enhanced Detection)")
        print(f"Fusion Mode: {FUSION_MODE}")
        print("V2 Features:")
        print("  - Clone-Stamp Detection")
        print("  - Weighted Fusion Scoring")
        print("  - Confidence Levels")
        print("  - Detector Agreement Analysis")
    else:
        print("Pipeline Version: V1 (Legacy)")

    print(f"Model Loaded: {model is not None}")
    print(f"TensorFlow Available: {TENSORFLOW_AVAILABLE}")
    print(f"Simulation Mode: {ALLOW_SIMULATION}")
    print("=" * 70 + "\n")

    debug_mode = os.environ.get('FLASK_DEBUG', 'false').lower() == 'true'
    app.run(host='0.0.0.0', port=int(os.environ.get('FLASK_PORT', 5000)), debug=debug_mode)