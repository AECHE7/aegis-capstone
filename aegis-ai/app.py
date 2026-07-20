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
from pipelines.image_forensics import run_image_pipeline
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
MODEL_PATH = os.environ.get('MODEL_PATH', 'aegis_resnet50_v1.keras')
ALLOW_SIMULATION = os.environ.get('ALLOW_SIMULATION', 'false').lower() == 'true' or os.environ.get('AEGIS_AI_ALLOW_SIMULATION', 'false').lower() == 'true'

for folder in [UPLOAD_FOLDER, ELA_FOLDER, HEATMAP_FOLDER]:
    os.makedirs(folder, exist_ok=True)

# Load ResNet-50 Model Globally
model = None
if TENSORFLOW_AVAILABLE:
    if os.path.exists(MODEL_PATH):
        try:
            print(f"[INIT] Loading A.E.G.I.S. ResNet-50 Model from {MODEL_PATH}...")
            model = tf.keras.models.load_model(MODEL_PATH)
            print("[INIT] Model Loaded Successfully!")
        except Exception as e:
            print(f"[INIT] Error loading model weights: {e}")
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
        "version": "2.0.0",
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
    return jsonify({
        "status": "healthy",
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
    original_path = os.path.join(UPLOAD_FOLDER, f"{file_uuid}.{original_ext}")
    ela_path = os.path.join(ELA_FOLDER, f"{file_uuid}_ela.jpg")
    heatmap_path = os.path.join(HEATMAP_FOLDER, f"{file_uuid}_heatmap.jpg")
    
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
    debug_mode = os.environ.get('FLASK_DEBUG', 'false').lower() == 'true'
    app.run(host='0.0.0.0', port=int(os.environ.get('FLASK_PORT', 5000)), debug=debug_mode)