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
    temp_filename = f'temp_{uuid.uuid4()}.jpg'
    original.save(temp_filename, 'JPEG', quality=quality)
    compressed = Image.open(temp_filename)
    
    ela_image = ImageChops.difference(original, compressed)
    extrema = ela_image.getextrema()
    max_diff = max([ex[1] for ex in extrema]) if extrema else 1
    if max_diff == 0:
        max_diff = 1
    
    scale = 255.0 / max_diff
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
        
        # Simulation Mode (dev only)
        filename_lower = os.path.basename(original_path).lower()
        if 'forged' in filename_lower or 'tamp' in filename_lower:
            fraud_probability = round(float(np.random.uniform(70.0, 98.0)), 2)
            classification = "Tampered"
            indicators.append("high_ela_energy")
        else:
            fraud_probability = round(float(np.random.uniform(1.0, 25.0)), 2)
            classification = "Authentic"

        original_img = cv2.imread(original_path)
        if original_img is not None:
            h, w, c = original_img.shape
            overlay = original_img.copy()
            if classification == "Tampered":
                cv2.circle(overlay, (int(w * 0.75), int(h * 0.85)), int(min(h, w) * 0.15), (0, 0, 255), -1)
                cv2.addWeighted(overlay, 0.4, original_img, 0.6, 0, original_img)
            else:
                cv2.circle(overlay, (int(w / 2), int(h / 2)), int(min(h, w) * 0.1), (0, 255, 0), -1)
                cv2.addWeighted(overlay, 0.1, original_img, 0.9, 0, original_img)
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

    # Grad-CAM heatmap generation
    try:
        heatmap = get_gradcam_heatmap(img_array, model)
        original_img = cv2.imread(original_path)
        heatmap_resized = cv2.resize(heatmap, (original_img.shape[1], original_img.shape[0]))
        heatmap_resized = np.uint8(255 * heatmap_resized)
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
