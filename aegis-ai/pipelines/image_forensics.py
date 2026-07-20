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


def detect_ela_patch_anomalies(ela_path: str, original_path: str):
    """
    Scans ELA difference matrix for localized copy-paste / whiteout box patches using contiguous tile variance analysis.
    Returns (patch_detected: bool, max_risk_score: float, patch_box: tuple|None, heatmap_img: np.ndarray|None)
    """
    if not os.path.exists(ela_path) or not os.path.exists(original_path):
        return False, 0.0, None, None

    ela_gray = cv2.imread(ela_path, cv2.IMREAD_GRAYSCALE)
    orig_img = cv2.imread(original_path)
    if ela_gray is None or orig_img is None:
        return False, 0.0, None, None

    h, w = ela_gray.shape
    # Divide into 16x16 grid tiles
    grid_rows, grid_cols = 16, 16
    tile_h, tile_w = h // grid_rows, w // grid_cols
    
    tile_stds = []
    tile_coords = []
    
    for r in range(grid_rows):
        for c in range(grid_cols):
            y1, y2 = r * tile_h, (r + 1) * tile_h
            x1, x2 = c * tile_w, (c + 1) * tile_w
            tile = ela_gray[y1:y2, x1:x2]
            std_val = float(np.std(tile))
            tile_stds.append(std_val)
            tile_coords.append((x1, y1, x2, y2))
            
    global_mean_std = float(np.mean(tile_stds))
    global_std_std = float(np.std(tile_stds)) + 1e-5
    
    # Identify localized cluster tiles exceeding 4.5 standard deviations (sharp localized manipulation boundary)
    outlier_boxes = []
    max_z = 0.0
    for idx, std_val in enumerate(tile_stds):
        z_score = (std_val - global_mean_std) / global_std_std
        if z_score >= 4.5 and std_val >= 35.0:
            outlier_boxes.append(tile_coords[idx])
            if z_score > max_z:
                max_z = z_score

    patch_detected = False
    max_risk = 0.0
    target_box = None

    # Require at least 3 contiguous outlier tiles to confirm a localized copy-paste / whiteout patch
    if len(outlier_boxes) >= 3:
        patch_detected = True
        max_risk = min(96.0, round(65.0 + (max_z * 5.0), 2))
        x1 = min(b[0] for b in outlier_boxes)
        y1 = min(b[1] for b in outlier_boxes)
        x2 = max(b[2] for b in outlier_boxes)
        y2 = max(b[3] for b in outlier_boxes)
        target_box = (x1, y1, x2 - x1, y2 - y1)

    heatmap_img = None
    if patch_detected and target_box:
        heatmap_img = orig_img.copy()
        x, y, bw, bh = target_box
        mask = np.zeros((h, w), dtype=np.uint8)
        cv2.rectangle(mask, (x, y), (x + bw, y + bh), 255, -1)
        mask_blur = cv2.GaussianBlur(mask, (35, 35), 0)
        color_mask = cv2.applyColorMap(mask_blur, cv2.COLORMAP_JET)
        heatmap_img = cv2.addWeighted(orig_img, 0.55, color_mask, 0.45, 0)
        cv2.rectangle(heatmap_img, (x, y), (x + bw, y + bh), (0, 0, 255), 2)

    return patch_detected, max_risk, target_box, heatmap_img


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

    # 3. Perform Localized ELA Patch & Whiteout Box Detection
    patch_detected, patch_risk, patch_box, patch_heatmap = detect_ela_patch_anomalies(ela_path, original_path)
    if patch_detected:
        indicators.append("copy_paste_patch_detected")

    # 4. Model Inference or Fallback Check
    if not TENSORFLOW_AVAILABLE or model is None:
        if not allow_simulation:
            raise RuntimeError("ResNet-50 AI model weights are not loaded. Fail-closed security active.")
        
        # Simulation Mode (dev fallback)
        filename_lower = os.path.basename(original_path).lower()
        if patch_detected or 'forged' in filename_lower or 'tamp' in filename_lower or 'fake' in filename_lower:
            fraud_probability = max(patch_risk, round(float(np.random.uniform(75.0, 98.0)), 2))
            classification = "Tampered"
            if "high_ela_energy" not in indicators:
                indicators.append("high_ela_energy")
        else:
            fraud_probability = round(float(np.random.uniform(1.0, 25.0)), 2)
            classification = "Authentic"

        original_img = cv2.imread(original_path)
        if original_img is not None:
            if classification == "Tampered":
                if patch_heatmap is not None:
                    cv2.imwrite(heatmap_path, patch_heatmap)
                else:
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

    # Detect architecture (EfficientNet-B4 vs ResNet-50)
    is_efficientnet = False
    last_layer = "conv5_block3_out"
    if hasattr(model, 'layers'):
        layer_names = [l.name for l in model.layers]
        if "top_activation" in layer_names or "block7a_project_conv" in layer_names:
            is_efficientnet = True
            last_layer = "top_activation" if "top_activation" in layer_names else "block7a_project_conv"

    input_size = (380, 380) if is_efficientnet else (224, 224)
    ela_resized = cv2.resize(ela_img, input_size)

    if is_efficientnet:
        try:
            from tensorflow.keras.applications.efficientnet import preprocess_input as eff_prep
            img_array = eff_prep(np.expand_dims(ela_resized, axis=0).astype(np.float32))
        except ImportError:
            from tensorflow.keras.applications.resnet50 import preprocess_input as res_prep
            img_array = res_prep(np.expand_dims(ela_resized, axis=0).astype(np.float32))
    else:
        from tensorflow.keras.applications.resnet50 import preprocess_input as res_prep
        img_array = res_prep(np.expand_dims(ela_resized, axis=0).astype(np.float32))

    prediction = model.predict(img_array, verbose=0)[0][0]
    base_fraud_prob = round(float(prediction) * 100, 2)
    
    # Fuse model prediction with ELA patch anomaly detector score
    if patch_detected:
        fraud_probability = max(base_fraud_prob, patch_risk)
    else:
        fraud_probability = base_fraud_prob

    classification = "Tampered" if fraud_probability >= 50.0 else "Authentic"

    if fraud_probability >= 50.0 and "high_ela_energy" not in indicators:
        indicators.append("high_ela_energy")

    # Heatmap generation
    try:
        if patch_detected and patch_heatmap is not None:
            cv2.imwrite(heatmap_path, patch_heatmap)
        else:
            heatmap = get_gradcam_heatmap(img_array, model, last_conv_layer_name=last_layer)
            weighted_heatmap = heatmap * (fraud_probability / 100.0)
            
            original_img = cv2.imread(original_path)
            heatmap_resized = cv2.resize(weighted_heatmap, (original_img.shape[1], original_img.shape[0]))
            heatmap_resized = np.uint8(255 * heatmap_resized)
            
            if fraud_probability < 35.0:
                cv2.imwrite(heatmap_path, original_img)
            else:
                jet_heatmap = cv2.applyColorMap(heatmap_resized, cv2.COLORMAP_JET)
                superimposed_img = cv2.addWeighted(original_img, 0.6, jet_heatmap, 0.4, 0)
                cv2.imwrite(heatmap_path, superimposed_img)
    except Exception as e:
        print(f"[IMAGE_PIPELINE] Grad-CAM generation warning: {e}")

    model_name = "aegis_efficientnet_b4" if is_efficientnet else "aegis_resnet50_v2"
    return {
        "status": "success",
        "pipeline": "image_forensics",
        "fraud_probability": fraud_probability,
        "classification": classification,
        "extracted_gwa": extracted_gwa,
        "anomaly_indicators": indicators,
        "model": {
            "name": model_name,
            "version": "3.0.0" if is_efficientnet else "2.0.0",
            "mode": "trained"
        },
        "paths": {
            "heatmap_path": heatmap_path,
            "ela_path": ela_path
        }
    }
