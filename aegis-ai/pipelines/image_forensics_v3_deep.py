"""
Image Forensics Pipeline V3 - Deep Analysis Mode

Critical Improvements for False Positive/Negative Issues:
1. Deep multi-pass inspection
2. Precise edit location mapping
3. Multi-layer heatmap visualization
4. Region-by-region detailed analysis
5. Exact pixel coordinates of edits

Use this pipeline when:
- V2 gives unexpected results (FP or FN)
- High-value scholarship requires maximum scrutiny
- Admin needs to see exact edit locations
- Dispute resolution
"""

import os
import uuid
import cv2
import numpy as np
from PIL import Image

from pipelines.gwa_ocr import extract_gwa_from_image
from pipelines.image_forensics_v2 import (
    generate_ela,
    detect_ela_patch_anomalies,
    detect_clahe_lab_anomalies,
    inspect_font_stroke_consistency
)

try:
    import tensorflow as tf
    TENSORFLOW_AVAILABLE = True
except ImportError:
    TENSORFLOW_AVAILABLE = False


def run_deep_analysis_pipeline(
    original_path: str,
    ela_path: str,
    heatmap_path: str,
    model=None,
    fusion_mode: str = 'balanced'
) -> dict:
    """
    V3 Deep Analysis Pipeline with comprehensive forensic inspection.

    Returns enriched response with:
    - Detailed forensic report
    - Multi-layer heatmaps
    - Exact edit coordinates
    - Region-by-region analysis
    """
    from forensics.deep_analysis import DeepForensicAnalyzer
    from forensics.fusion_scoring import ForensicFusionEngine
    from forensics.clone_detection import detect_clone_stamp

    print("[DEEP_ANALYSIS_V3] Starting comprehensive forensic analysis...")

    indicators = []
    detector_results = {}

    # 1. Generate ELA
    generate_ela(original_path, ela_path)

    # 2. Extract GWA
    extracted_gwa = extract_gwa_from_image(original_path)

    # 3. Run all V2 detectors
    print("[DEEP_ANALYSIS_V3] Running standard detectors...")

    # ELA Patch Detection
    patch_detected, patch_risk, patch_box, patch_heatmap, patch_conf = detect_ela_patch_anomalies(ela_path, original_path)
    detector_results['ela_patch'] = {
        'detected': patch_detected,
        'risk_score': patch_risk,
        'confidence': patch_conf,
        'details': {'box': patch_box}
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
        'confidence': lab_conf,
        'details': {'box': lab_box}
    }
    if lab_detected:
        indicators.append("digital_whiteout_box_detected")

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
    except Exception as te:
        print(f"[DEEP_ANALYSIS_V3] TruFor warning: {te}")
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
    except Exception as ce:
        print(f"[DEEP_ANALYSIS_V3] CAT-Net warning: {ce}")
        detector_results['catnet_dct'] = {'detected': False, 'risk_score': 0.0}

    # Clone-Stamp Detection
    try:
        clone_det, clone_risk, clone_pairs, clone_hm = detect_clone_stamp(original_path, sensitivity=0.80)
        detector_results['clone_detection'] = {
            'detected': clone_det,
            'risk_score': clone_risk,
            'confidence': 'high' if len(clone_pairs or []) >= 10 else 'medium',
            'details': {'clone_pairs_count': len(clone_pairs or [])}
        }
        if clone_det:
            indicators.append("clone_stamp_detected")
    except Exception as cle:
        print(f"[DEEP_ANALYSIS_V3] Clone detection warning: {cle}")
        detector_results['clone_detection'] = {'detected': False, 'risk_score': 0.0}

    # Software Fingerprint
    try:
        from forensics.software_fingerprint import identify_editing_software
        soft_res = identify_editing_software(original_path)
        detector_results['software_fingerprint'] = {
            'detected': soft_res.get("software_detected") is not None,
            'risk_score': soft_res["risk_score"],
            'confidence': 'medium',
            'details': {'software': soft_res.get("software_detected")}
        }
        if soft_res.get("software_detected"):
            indicators.append(f"software_detected:{soft_res['software_detected']}")
    except Exception as se:
        print(f"[DEEP_ANALYSIS_V3] Software footprint warning: {se}")
        detector_results['software_fingerprint'] = {'detected': False, 'risk_score': 0.0}

    # 4. DEEP ANALYSIS - NEW IN V3
    print("[DEEP_ANALYSIS_V3] Performing deep forensic inspection...")

    deep_analyzer = DeepForensicAnalyzer(original_path)
    deep_report = deep_analyzer.generate_detailed_report()

    # Add deep analysis findings to indicators
    if deep_report['summary']['high_severity_regions'] >= 2:
        indicators.append("deep_analysis_multiple_high_severity_regions")

    if deep_report['summary']['detectors_triggered'] >= 3:
        indicators.append("deep_analysis_multi_detector_agreement")

    # Create deep analysis detector entry
    deep_risk = min(95.0, 50.0 + (deep_report['summary']['high_severity_regions'] * 15.0))
    detector_results['deep_analysis'] = {
        'detected': deep_report['summary']['total_suspect_regions'] > 0,
        'risk_score': deep_risk,
        'confidence': 'high' if deep_report['summary']['high_severity_regions'] >= 2 else 'medium',
        'details': deep_report['summary']
    }

    # 5. Model Inference (if available)
    model_prediction_score = None
    if TENSORFLOW_AVAILABLE and model is not None:
        try:
            ela_img = cv2.imread(ela_path)
            ela_img = cv2.cvtColor(ela_img, cv2.COLOR_BGR2RGB)

            # Detect architecture
            is_efficientnet = False
            if hasattr(model, 'layers'):
                layer_names = [l.name for l in model.layers]
                if "top_activation" in layer_names or "block7a_project_conv" in layer_names:
                    is_efficientnet = True

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
                'confidence': 'high' if model_prediction_score >= 75 else 'medium'
            }

            model_name = "aegis_efficientnet_b4" if is_efficientnet else "aegis_resnet50_v2"
            model_mode = "trained"

        except Exception as me:
            print(f"[DEEP_ANALYSIS_V3] Model inference error: {me}")
            detector_results['model_prediction'] = {'detected': False, 'risk_score': 0.0}
            model_name = "error"
            model_mode = "failed"
    else:
        detector_results['model_prediction'] = {'detected': False, 'risk_score': 0.0}
        model_name = "aegis_deep_forensic_v3"
        model_mode = "deep_analysis_only"

    # 6. Fusion Scoring
    fusion_engine = ForensicFusionEngine(mode=fusion_mode)

    if model_mode == "trained":
        fusion_result = fusion_engine.fuse_scores(detector_results)
        fraud_probability = fusion_result['fraud_probability']
        classification = fusion_result['classification']
        fusion_confidence = fusion_result['confidence']
        detector_agreement = fusion_result['detector_agreement']
    else:
        fraud_probability = fusion_engine.fallback_deterministic_scoring(detector_results)
        classification = "Tampered" if fraud_probability >= 50.0 else "Authentic"
        fusion_confidence = "medium"
        detector_agreement = len([d for d in detector_results.values() if d.get('detected')]) / len(detector_results)

    if fraud_probability >= 50.0 and "high_ela_energy" not in indicators:
        indicators.append("high_ela_energy")

    # 7. Save composite heatmap
    composite_heatmap = deep_report['visualizations']['annotated_image_base64']

    # Decode and save annotated image
    import base64
    annotated_bytes = base64.b64decode(composite_heatmap)
    with open(heatmap_path, 'wb') as f:
        f.write(annotated_bytes)

    # 8. Generate region crops for each suspect area
    region_crops = []
    for region in deep_report['suspect_regions'][:5]:  # Top 5 most suspicious
        try:
            orig_img = cv2.imread(original_path)
            x, y, w, h = region['x'], region['y'], region['w'], region['h']

            # Add padding
            pad = 20
            h_img, w_img, _ = orig_img.shape
            x1, y1 = max(0, x - pad), max(0, y - pad)
            x2, y2 = min(w_img, x + w + pad), min(h_img, y + h + pad)

            crop_img = orig_img[y1:y2, x1:x2]

            if crop_img.size > 0:
                _, buffer = cv2.imencode('.png', crop_img)
                crop_base64 = base64.b64encode(buffer).decode('utf-8')

                region_crops.append({
                    'x': x, 'y': y, 'w': w, 'h': h,
                    'detector': region['detector'],
                    'severity': region['severity'],
                    'crop_base64': crop_base64
                })
        except Exception as ce:
            print(f"[DEEP_ANALYSIS_V3] Crop extraction warning: {ce}")

    return {
        "status": "success",
        "pipeline": "image_forensics_v3_deep",
        "fraud_probability": fraud_probability,
        "classification": classification,
        "confidence": fusion_confidence,
        "detector_agreement": detector_agreement,
        "extracted_gwa": extracted_gwa,
        "anomaly_indicators": indicators,
        "detector_details": {
            k: v for k, v in detector_results.items() if v.get('detected')
        },
        "deep_analysis_report": {
            'summary': deep_report['summary'],
            'suspect_regions': deep_report['suspect_regions'],
            'region_crops': region_crops,  # NEW: Zoomed views of suspect areas
            'ela_analysis': deep_report['ela_analysis'],
            'noise_analysis': deep_report['noise_analysis'],
            'edge_analysis': deep_report['edge_analysis'],
            'color_analysis': deep_report['color_analysis']
        },
        "visualizations": {
            'composite_heatmap_base64': composite_heatmap,
            'annotated_image_base64': deep_report['visualizations']['annotated_image_base64'],
            'layer_heatmaps': deep_report['visualizations']['individual_layers']
        },
        "model": {
            "name": model_name,
            "version": "3.0.0",
            "mode": model_mode,
            "fusion_mode": fusion_mode
        },
        "paths": {
            "heatmap_path": heatmap_path,
            "ela_path": ela_path
        }
    }
