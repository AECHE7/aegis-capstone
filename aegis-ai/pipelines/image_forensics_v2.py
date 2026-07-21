"""
Enhanced Image Forensics Pipeline V2 - Improved Tamper Detection

Improvements over V1:
1. Clone-stamp & copy-move detection
2. Weighted fusion scoring (not just max)
3. Lower, tuned thresholds for single-cell edits
4. Confidence intervals
5. Multi-detector agreement analysis
6. Better fallback handling
"""

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
    """Stage 1: Error Level Analysis (ELA) Preprocessing - UNCHANGED."""
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


def detect_ela_patch_anomalies(ela_path: str, original_path: str):
    """
    IMPROVED: Lower z-score threshold from 2.2 to 1.9 for better single-cell sensitivity.
    """
    if not os.path.exists(ela_path) or not os.path.exists(original_path):
        return False, 0.0, None, None, 'low'

    ela_gray = cv2.imread(ela_path, cv2.IMREAD_GRAYSCALE)
    orig_img = cv2.imread(original_path)
    if ela_gray is None or orig_img is None:
        return False, 0.0, None, None, 'low'

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

    # IMPROVED: Lower threshold from 2.2 to 1.9 for better single-cell grade edit detection
    Z_SCORE_THRESHOLD = 1.9  # Was 2.2
    MIN_STD_THRESHOLD = 15.0  # Lowered from 18.0

    outlier_boxes = []
    max_z = 0.0
    for idx, std_val in enumerate(tile_stds):
        z_score = (std_val - global_mean_std) / global_std_std
        if z_score >= Z_SCORE_THRESHOLD and std_val >= MIN_STD_THRESHOLD:
            outlier_boxes.append(tile_coords[idx])
            if z_score > max_z:
                max_z = z_score

    patch_detected = False
    max_risk = 0.0
    target_box = None
    confidence = 'low'

    if len(outlier_boxes) >= 1:
        patch_detected = True
        max_risk = min(96.0, round(72.0 + (max_z * 7.5), 2))

        # Determine confidence based on z-score and number of anomalous tiles
        if max_z >= 3.5 and len(outlier_boxes) >= 3:
            confidence = 'high'
        elif max_z >= 2.5 or len(outlier_boxes) >= 2:
            confidence = 'medium'
        else:
            confidence = 'low'

        # Sort outlier boxes by proximity and isolate micro-crop
        outlier_boxes.sort(key=lambda b: (b[2] * b[3]), reverse=True)
        top_boxes = outlier_boxes[:2]

        x1 = min(b[0] for b in top_boxes)
        y1 = min(b[1] for b in top_boxes)
        x2 = max(b[2] for b in top_boxes)
        y2 = max(b[3] for b in top_boxes)
        target_box = (x1, y1, x2 - x1, y2 - y1)

    heatmap_img = None
    if patch_detected and target_box:
        heatmap_img = orig_img.copy()
        x, y, bw, bh = target_box
        mask = np.zeros((h, w), dtype=np.uint8)
        cv2.rectangle(mask, (x, y), (x + bw, y + bh), 255, -1)
        mask_blur = cv2.GaussianBlur(mask, (25, 25), 0)
        color_mask = cv2.applyColorMap(mask_blur, cv2.COLORMAP_JET)
        heatmap_img = cv2.addWeighted(orig_img, 0.60, color_mask, 0.40, 0)
        cv2.rectangle(heatmap_img, (x, y), (x + bw, y + bh), (0, 0, 255), 3)

    return patch_detected, max_risk, target_box, heatmap_img, confidence


def detect_clahe_lab_anomalies(original_path: str):
    """
    IMPROVED: Added confidence scoring and better area constraints.
    """
    if not os.path.exists(original_path):
        return False, 0.0, None, None, 'low'

    orig_img = cv2.imread(original_path)
    if orig_img is None:
        return False, 0.0, None, None, 'low'

    h, w, _ = orig_img.shape
    lab = cv2.cvtColor(orig_img, cv2.COLOR_BGR2LAB)
    l_channel, _, _ = cv2.split(lab)

    clahe = cv2.createCLAHE(clipLimit=4.0, tileGridSize=(8, 8))
    cl = clahe.apply(l_channel)

    blur = cv2.GaussianBlur(cl, (5, 5), 0)
    thresh = cv2.adaptiveThreshold(blur, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY_INV, 11, 2)

    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    outlier_boxes = []

    for c in contours:
        x, y, bw, bh = cv2.boundingRect(c)
        area = bw * bh
        aspect_ratio = float(bw) / bh if bh > 0 else 0
        # Restrict to individual cell size (area 80px to max 4% of page)
        if 80 <= area <= (w * h * 0.04) and 0.5 <= aspect_ratio <= 8.0:
            patch_crop = cl[y:y+bh, x:x+bw]
            if patch_crop.size > 0:
                mean_val = float(np.mean(patch_crop))
                std_val = float(np.std(patch_crop))
                # Detect high-contrast boundary or erased blank box
                if std_val < 18.0 or mean_val > 245.0:
                    outlier_boxes.append((x, y, bw, bh, std_val, mean_val))

    confidence = 'low'
    if len(outlier_boxes) >= 1:
        # Sort by standard deviation (most uniform / flat whiteout patch)
        outlier_boxes.sort(key=lambda b: b[4])
        top_cell = outlier_boxes[0]
        x1, y1, bw_c, bh_c, std_v, mean_v = top_cell
        target_box = (x1, y1, bw_c, bh_c)

        # Confidence based on uniformity
        if std_v < 10.0 and mean_v > 250.0:
            confidence = 'high'  # Very uniform white patch
        elif std_v < 15.0 or mean_v > 245.0:
            confidence = 'medium'
        else:
            confidence = 'low'

        heatmap_img = orig_img.copy()
        cv2.rectangle(heatmap_img, (x1, y1), (x1 + bw_c, y1 + bh_c), (0, 0, 255), 3)
        overlay = heatmap_img.copy()
        cv2.rectangle(overlay, (x1, y1), (x1 + bw_c, y1 + bh_c), (0, 0, 255), -1)
        heatmap_img = cv2.addWeighted(heatmap_img, 0.65, overlay, 0.35, 0)
        return True, 89.50, target_box, heatmap_img, confidence

    return False, 0.0, None, None, 'low'


def inspect_font_stroke_consistency(original_path: str) -> tuple:
    """
    IMPROVED: Returns confidence level instead of just bool.
    """
    if not os.path.exists(original_path):
        return False, 0.0, 'low'

    orig = cv2.imread(original_path, cv2.IMREAD_GRAYSCALE)
    if orig is None:
        return False, 0.0, 'low'

    edges = cv2.Canny(orig, 50, 150)
    h, w = edges.shape

    # Focus on table region
    table_region = edges[int(h*0.15):int(h*0.85), int(w*0.1):int(w*0.9)]
    if table_region.size == 0:
        return False, 0.0, 'low'

    row_edge_densities = np.mean(table_region, axis=1)
    std_density = np.std(row_edge_densities)
    mean_density = np.mean(row_edge_densities) + 1e-5

    variance_ratio = std_density / mean_density
    max_density = np.max(row_edge_densities)

    detected = bool(variance_ratio >= 1.85 and max_density >= 45.0)

    confidence = 'low'
    if detected:
        if variance_ratio >= 2.5 and max_density >= 60.0:
            confidence = 'high'
        elif variance_ratio >= 2.0:
            confidence = 'medium'

    risk_score = 78.50 if detected else 0.0

    return detected, risk_score, confidence


def run_image_pipeline_v2(
    original_path: str,
    ela_path: str,
    heatmap_path: str,
    model=None,
    allow_simulation: bool = False,
    fusion_mode: str = 'balanced'
) -> dict:
    """
    Enhanced Pipeline V2 with improved fusion scoring and clone detection.

    New parameters:
        fusion_mode: 'strict', 'balanced', or 'sensitive'
    """
    from forensics.fusion_scoring import ForensicFusionEngine
    from forensics.clone_detection import detect_clone_stamp

    indicators = []
    detector_results = {}

    # 1. Generate ELA Image
    generate_ela(original_path, ela_path)

    # 2. Extract GWA via OCR
    extracted_gwa = extract_gwa_from_image(original_path)

    # 3. Run all forensic detectors and collect results

    # ELA Patch Detection
    patch_detected, patch_risk, patch_box, patch_heatmap, patch_conf = detect_ela_patch_anomalies(ela_path, original_path)
    detector_results['ela_patch'] = {
        'detected': patch_detected,
        'risk_score': patch_risk,
        'confidence': patch_conf
    }
    if patch_detected:
        indicators.append("copy_paste_patch_detected")

    # Font Stroke Consistency
    font_det, font_risk, font_conf = inspect_font_stroke_consistency(original_path)
    detector_results['font_stroke'] = {
        'detected': font_det,
        'risk_score': font_risk,
        'confidence': font_conf
    }
    if font_det:
        indicators.append("font_stroke_discrepancy")

    # CLAHE LAB Analysis
    lab_detected, lab_risk, lab_box, lab_heatmap, lab_conf = detect_clahe_lab_anomalies(original_path)
    detector_results['clahe_lab'] = {
        'detected': lab_detected,
        'risk_score': lab_risk,
        'confidence': lab_conf
    }
    if lab_detected:
        indicators.append("digital_whiteout_box_detected")
        if lab_heatmap is not None:
            patch_heatmap = lab_heatmap

    # TruFor CNN Noiseprint++
    try:
        from forensics.trufor_cnn import analyze_trufor_cnn
        trufor_det, trufor_risk, trufor_box, trufor_hm = analyze_trufor_cnn(original_path)
        detector_results['trufor_cnn'] = {
            'detected': trufor_det,
            'risk_score': trufor_risk,
            'confidence': 'high' if trufor_risk > 90 else 'medium'
        }
        if trufor_det:
            indicators.append("trufor_noiseprint_anomaly")
            if trufor_hm is not None and patch_heatmap is None:
                patch_heatmap = trufor_hm
    except Exception as te:
        print(f"[IMAGE_PIPELINE_V2] TruFor CNN warning: {te}")
        detector_results['trufor_cnn'] = {'detected': False, 'risk_score': 0.0}

    # CAT-Net FCN HRNet DCT
    try:
        from forensics.catnet_cnn import analyze_catnet_fcn
        cat_det, cat_risk, cat_box, cat_hm = analyze_catnet_fcn(original_path)
        detector_results['catnet_dct'] = {
            'detected': cat_det,
            'risk_score': cat_risk,
            'confidence': 'high' if cat_risk > 92 else 'medium'
        }
        if cat_det:
            indicators.append("catnet_dct_compression_anomaly")
            if cat_hm is not None and patch_heatmap is None:
                patch_heatmap = cat_hm
    except Exception as ce:
        print(f"[IMAGE_PIPELINE_V2] CAT-Net FCN warning: {ce}")
        detector_results['catnet_dct'] = {'detected': False, 'risk_score': 0.0}

    # NEW: Clone-Stamp Detection
    try:
        clone_det, clone_risk, clone_pairs, clone_hm = detect_clone_stamp(original_path, sensitivity=0.82)
        detector_results['clone_detection'] = {
            'detected': clone_det,
            'risk_score': clone_risk,
            'confidence': 'high' if len(clone_pairs or []) >= 10 else 'medium'
        }
        if clone_det:
            indicators.append("clone_stamp_detected")
            if clone_hm is not None and patch_heatmap is None:
                patch_heatmap = clone_hm
    except Exception as cle:
        print(f"[IMAGE_PIPELINE_V2] Clone detection warning: {cle}")
        detector_results['clone_detection'] = {'detected': False, 'risk_score': 0.0}

    # Software Fingerprint Detection
    try:
        from forensics.software_fingerprint import identify_editing_software
        soft_res = identify_editing_software(original_path)
        detector_results['software_fingerprint'] = {
            'detected': soft_res.get("software_detected") is not None,
            'risk_score': soft_res["risk_score"],
            'confidence': 'medium'
        }
        if soft_res.get("software_detected"):
            detected_software = soft_res["software_detected"]
            indicators.append(f"software_detected:{detected_software}")
    except Exception as se:
        print(f"[IMAGE_PIPELINE_V2] Software footprint warning: {se}")
        detector_results['software_fingerprint'] = {'detected': False, 'risk_score': 0.0}

    # Model Inference (if available)
    model_prediction_score = None
    if TENSORFLOW_AVAILABLE and model is not None:
        try:
            ela_img = cv2.imread(ela_path)
            ela_img = cv2.cvtColor(ela_img, cv2.COLOR_BGR2RGB)

            # Detect architecture
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
            model_prediction_score = round(float(prediction) * 100, 2)

            detector_results['model_prediction'] = {
                'detected': model_prediction_score >= 50.0,
                'risk_score': model_prediction_score,
                'confidence': 'high' if model_prediction_score >= 75 else ('medium' if model_prediction_score >= 50 else 'low')
            }

            model_name = "aegis_efficientnet_b4" if is_efficientnet else "aegis_resnet50_v2"
            model_mode = "trained"

        except Exception as me:
            print(f"[IMAGE_PIPELINE_V2] Model inference error: {me}")
            detector_results['model_prediction'] = {'detected': False, 'risk_score': 0.0}
            model_name = "error"
            model_mode = "failed"
    else:
        # No model available
        detector_results['model_prediction'] = {'detected': False, 'risk_score': 0.0}
        model_name = "aegis_deterministic_v2"
        model_mode = "deterministic_forensic_engine"

    # NEW: Fusion Scoring Engine
    fusion_engine = ForensicFusionEngine(mode=fusion_mode)

    if model_mode == "trained":
        # Use weighted fusion
        fusion_result = fusion_engine.fuse_scores(detector_results)
        fraud_probability = fusion_result['fraud_probability']
        classification = fusion_result['classification']
        fusion_confidence = fusion_result['confidence']
        detector_agreement = fusion_result['detector_agreement']
    else:
        # Deterministic fallback
        fraud_probability = fusion_engine.fallback_deterministic_scoring(detector_results)
        classification = "Tampered" if fraud_probability >= 50.0 else "Authentic"
        fusion_confidence = "low"
        detector_agreement = 0.0

    if fraud_probability >= 50.0 and "high_ela_energy" not in indicators:
        indicators.append("high_ela_energy")

    # Generate heatmap
    original_img = cv2.imread(original_path)
    if original_img is not None:
        if patch_heatmap is not None:
            cv2.imwrite(heatmap_path, patch_heatmap)
        elif fraud_probability < 25.0:
            # Very likely authentic - clean image
            cv2.imwrite(heatmap_path, original_img)
        else:
            # Generate generic ELA heatmap
            ela_img = cv2.imread(ela_path, cv2.IMREAD_GRAYSCALE)
            if ela_img is not None:
                ela_resized = cv2.resize(ela_img, (original_img.shape[1], original_img.shape[0]))
                _, thresh = cv2.threshold(ela_resized, 120, 255, cv2.THRESH_BINARY)
                heatmap_color = cv2.applyColorMap(thresh, cv2.COLORMAP_JET)
                superimposed = cv2.addWeighted(original_img, 0.6, heatmap_color, 0.4, 0)
                cv2.imwrite(heatmap_path, superimposed)
            else:
                cv2.imwrite(heatmap_path, original_img)

    # Generate zoomed micro-crop
    cropped_patch_base64 = None
    target_box = patch_box or lab_box
    if target_box:
        try:
            orig_img = cv2.imread(original_path)
            if orig_img is not None:
                x, y, bw, bh = target_box
                h_img, w_img, _ = orig_img.shape
                x1, y1 = max(0, x - 15), max(0, y - 15)
                x2, y2 = min(w_img, x + bw + 15), min(h_img, y + bh + 15)
                crop_img = orig_img[y1:y2, x1:x2]
                if crop_img.size > 0:
                    import base64
                    _, buffer = cv2.imencode('.png', crop_img)
                    cropped_patch_base64 = base64.b64encode(buffer).decode('utf-8')
        except Exception as ce:
            print(f"[IMAGE_PIPELINE_V2] Micro-crop extraction warning: {ce}")

    return {
        "status": "success",
        "pipeline": "image_forensics_v2",
        "fraud_probability": fraud_probability,
        "classification": classification,
        "confidence": fusion_confidence,
        "detector_agreement": detector_agreement,
        "extracted_gwa": extracted_gwa,
        "anomaly_indicators": indicators,
        "detected_software": detector_results.get('software_fingerprint', {}).get('detected'),
        "cropped_patch_base64": cropped_patch_base64,
        "target_box": target_box,
        "model": {
            "name": model_name,
            "version": "2.0.0",
            "mode": model_mode,
            "fusion_mode": fusion_mode
        },
        "paths": {
            "heatmap_path": heatmap_path,
            "ela_path": ela_path
        },
        "detector_details": {
            k: v for k, v in detector_results.items() if v.get('detected')
        }
    }
