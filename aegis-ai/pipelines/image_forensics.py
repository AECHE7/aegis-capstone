import os
import uuid
import cv2
import numpy as np
from PIL import Image, ImageChops, ImageEnhance

from pipelines.gwa_ocr import extract_gwa_from_image

try:
    import tensorflow as tf
    from tensorflow.keras.applications.resnet50 import preprocess_input
    TENSORFLOW_AVAILABLE = True
except ImportError:
    TENSORFLOW_AVAILABLE = False


def generate_ela(img_path: str, output_path: str, quality: int = 95) -> str:
    """Stage 1: Error Level Analysis (ELA) Preprocessing."""
    original = Image.open(img_path).convert('RGB')
    
    # Pre-scale high-res images (>1280px) for fast sub-second matrix computation
    max_dim = 1280
    if original.width > max_dim or original.height > max_dim:
        original.thumbnail((max_dim, max_dim), Image.Resampling.LANCZOS)

    temp_filename = f'temp_{uuid.uuid4()}.jpg'
    original.save(temp_filename, 'JPEG', quality=quality)
    compressed = Image.open(temp_filename)
    
    ela_image = ImageChops.difference(original, compressed)
    extrema = ela_image.getextrema()
    max_diff = max([ex[1] for ex in extrema]) if extrema else 1
    if max_diff == 0:
        max_diff = 1
    
    # Cap scale multiplier to 12.0x max to prevent amplifying minor 1-pixel noise on authentic files
    scale = min(255.0 / max_diff, 12.0)
    ela_image = ImageEnhance.Brightness(ela_image).enhance(scale)
    ela_image.save(output_path)
    if os.path.exists(temp_filename):
        os.remove(temp_filename)
    return output_path


def get_gradcam_heatmap(img_array, model, last_conv_layer_name="conv5_block3_out"):
    """Generates a Grad-CAM heatmap highlighting tampered regions."""
    grad_model = tf.keras.models.Model(
        model.inputs, [model.get_layer(last_conv_layer_name).output, model.output]
    )

    with tf.GradientTape() as tape:
        last_conv_layer_output, preds = grad_model(img_array)
        class_channel = preds[:, 0]

    grads = tape.gradient(class_channel, last_conv_layer_output)
    pooled_grads = tf.reduce_mean(grads, axis=(0, 1, 2))
    
    last_conv_layer_output = last_conv_layer_output[0]
    heatmap = last_conv_layer_output @ pooled_grads[..., tf.newaxis]
    heatmap = tf.squeeze(heatmap)
    
    heatmap = tf.maximum(heatmap, 0) / (tf.math.reduce_max(heatmap) + 1e-10)
    return heatmap.numpy()


def run_image_pipeline(
    original_path: str,
    ela_path: str,
    heatmap_path: str,
    model=None,
    allow_simulation: bool = False
) -> dict:
    """
    Executes Pipeline A for image files (JPG/PNG).
    Returns a unified dictionary with fraud_probability, classification, extracted_gwa, indicators, and model metadata.
    """
    indicators = []
    
    # 1. Generate ELA Image
    generate_ela(original_path, ela_path)

    # 2. Extract GWA via OCR
    extracted_gwa = extract_gwa_from_image(original_path)

    # 3. Model Inference or Fallback Check
    if not TENSORFLOW_AVAILABLE or model is None:
        if not allow_simulation:
            raise RuntimeError("ResNet-50 AI model weights are not loaded. Fail-closed security active.")
        
        # Simulation Mode (dev fallback)
        filename_lower = os.path.basename(original_path).lower()
        if 'forged' in filename_lower or 'tamp' in filename_lower or 'fake' in filename_lower:
            fraud_probability = round(float(np.random.uniform(70.0, 98.0)), 2)
            classification = "Tampered"
            indicators.append("high_ela_energy")
        else:
            fraud_probability = round(float(np.random.uniform(1.0, 25.0)), 2)
            classification = "Authentic"

        original_img = cv2.imread(original_path)
        if original_img is not None:
            if classification == "Tampered":
                # Highlight actual high-energy ELA difference pixels
                ela_img = cv2.imread(ela_path, cv2.IMREAD_GRAYSCALE)
                if ela_img is not None:
                    ela_resized = cv2.resize(ela_img, (original_img.shape[1], original_img.shape[0]))
                    _, thresh = cv2.threshold(ela_resized, 120, 255, cv2.THRESH_BINARY)
                    heatmap_color = cv2.applyColorMap(thresh, cv2.COLORMAP_JET)
                    superimposed = cv2.addWeighted(original_img, 0.6, heatmap_color, 0.4, 0)
                    cv2.imwrite(heatmap_path, superimposed)
                else:
                    cv2.imwrite(heatmap_path, original_img)
            else:
                # Authentic document: Clean image with zero red blobs
                cv2.imwrite(heatmap_path, original_img)

        return {
            "status": "success",
            "pipeline": "image_forensics",
            "fraud_probability": fraud_probability,
            "classification": classification,
            "extracted_gwa": extracted_gwa,
            "anomaly_indicators": indicators,
            "model": {
                "name": "aegis_resnet50",
                "version": "1.0.0",
                "mode": "simulation"
            },
            "paths": {
                "heatmap_path": heatmap_path,
                "ela_path": ela_path
            }
        }

    # Real Trained Model Inference
    ela_img = cv2.imread(ela_path)
    ela_img = cv2.cvtColor(ela_img, cv2.COLOR_BGR2RGB)
    ela_resized = cv2.resize(ela_img, (224, 224))
    img_array = preprocess_input(np.expand_dims(ela_resized, axis=0).astype(np.float32))

    prediction = model.predict(img_array, verbose=0)[0][0]
    fraud_probability = round(float(prediction) * 100, 2)
    classification = "Tampered" if fraud_probability >= 50.0 else "Authentic"

    if fraud_probability >= 50.0:
        indicators.append("high_ela_energy")

    # Grad-CAM heatmap generation weighted by predicted fraud probability
    try:
        heatmap = get_gradcam_heatmap(img_array, model)
        # Scale heatmap maximum intensity by (fraud_probability / 100.0)
        # If low fraud (<30%), intensity is capped so NO red/yellow blobs appear
        weighted_heatmap = heatmap * (fraud_probability / 100.0)
        
        original_img = cv2.imread(original_path)
        heatmap_resized = cv2.resize(weighted_heatmap, (original_img.shape[1], original_img.shape[0]))
        heatmap_resized = np.uint8(255 * heatmap_resized)
        
        if fraud_probability < 35.0:
            # Low risk: clean image without red/yellow distortion
            cv2.imwrite(heatmap_path, original_img)
        else:
            jet_heatmap = cv2.applyColorMap(heatmap_resized, cv2.COLORMAP_JET)
            superimposed_img = cv2.addWeighted(original_img, 0.6, jet_heatmap, 0.4, 0)
            cv2.imwrite(heatmap_path, superimposed_img)
    except Exception as e:
        print(f"[IMAGE_PIPELINE] Grad-CAM generation warning: {e}")

    return {
        "status": "success",
        "pipeline": "image_forensics",
        "fraud_probability": fraud_probability,
        "classification": classification,
        "extracted_gwa": extracted_gwa,
        "anomaly_indicators": indicators,
        "model": {
            "name": "aegis_resnet50_v2",
            "version": "2.0.0",
            "mode": "trained"
        },
        "paths": {
            "heatmap_path": heatmap_path,
            "ela_path": ela_path
        }
    }
