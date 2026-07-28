import os
import uuid
import cv2
import numpy as np

from forensics.pdf_signals import analyze_pdf_structure
from pipelines.gwa_ocr import extract_gwa_from_text
from pipelines.image_forensics import run_image_pipeline

try:
    from pdf2image import convert_from_path
    PDF2IMAGE_AVAILABLE = True
except ImportError:
    PDF2IMAGE_AVAILABLE = False


def run_pdf_pipeline(
    pdf_path: str,
    ela_folder: str,
    heatmap_folder: str,
    temp_folder: str,
    model=None,
    allow_simulation: bool = False,
    max_pages: int = 1
) -> dict:
    """
    Executes Pipeline B for PDF documents.
    Fuses structural pikepdf audit + page rasterization visual AI + text/OCR GWA extraction.
    """
    # 1. Structural & Metadata Forensic Analysis
    structural_result = analyze_pdf_structure(pdf_path)
    structural_risk = structural_result["risk_score"]
    anomaly_indicators = list(structural_result["indicators"])
    pdf_report = structural_result["report"]

    # 2. Extract Native Text GWA
    extracted_gwa = None
    try:
        import pypdf
        reader = pypdf.PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            text += page.extract_text() or ""
        extracted_gwa = extract_gwa_from_text(text)
    except Exception as e:
        print(f"[PDF_PIPELINE] Text GWA extraction warning: {e}")

    # 3. Page Rasterization & Visual AI Analysis
    page_fraud_probs = []
    highest_risk_heatmap = None
    highest_risk_ela = None

    if PDF2IMAGE_AVAILABLE:
        try:
            # Fast-Path: Scan Page 1 by default; dynamically scan Page 2 if structural risk >= 30.0
            effective_max_pages = max_pages
            if structural_risk >= 30.0 and max_pages == 1:
                effective_max_pages = 2

            images = convert_from_path(pdf_path, first_page=1, last_page=effective_max_pages)
            pdf_report["pages_scanned"] = len(images)

            for i, img in enumerate(images):
                page_uuid = f"{uuid.uuid4()}_p{i+1}"
                temp_png = os.path.join(temp_folder, f"{page_uuid}.png")
                page_ela = os.path.join(ela_folder, f"{page_uuid}_ela.jpg")
                page_heatmap = os.path.join(heatmap_folder, f"{page_uuid}_heatmap.jpg")

                img.save(temp_png, 'PNG')

                # Run Pipeline A on the rasterized page PNG
                img_res = run_image_pipeline(
                    original_path=temp_png,
                    ela_path=page_ela,
                    heatmap_path=page_heatmap,
                    model=model,
                    allow_simulation=allow_simulation
                )

                page_prob = img_res["fraud_probability"]
                page_fraud_probs.append(page_prob)

                # Track visual indicators
                for ind in img_res.get("anomaly_indicators", []):
                    if ind not in anomaly_indicators:
                        anomaly_indicators.append(ind)

                # Keep the heatmap/ELA of the highest-risk page
                if highest_risk_heatmap is None or page_prob > max(page_fraud_probs[:-1], default=0):
                    highest_risk_heatmap = page_heatmap
                    highest_risk_ela = page_ela

                # If text GWA wasn't found in native text layer, check page OCR
                if extracted_gwa is None and img_res.get("extracted_gwa") is not None:
                    extracted_gwa = img_res["extracted_gwa"]

                if os.path.exists(temp_png):
                    os.remove(temp_png)

        except Exception as e:
            print(f"[PDF_PIPELINE] pdf2image rasterization warning: {e}")
            anomaly_indicators.append("pdf_rasterization_warning")
    else:
        print("[PDF_PIPELINE] pdf2image not available. Skipping visual rasterization.")
        anomaly_indicators.append("pdf_rasterization_unavailable")

    max_visual_fraud = max(page_fraud_probs) if page_fraud_probs else 0.0
    pdf_report["max_page_fraud"] = max_visual_fraud

    # 4. Maximum Risk Fusion Calculation (Ensures visual or structural forgeries are never diluted)
    fusion_fraud_prob = round(
        min(100.0, max(structural_risk, max_visual_fraud)),
        2
    )

    classification = "Tampered" if fusion_fraud_prob >= 50.0 else "Authentic"

    import base64
    ela_base64 = None
    if highest_risk_ela and os.path.exists(highest_risk_ela):
        try:
            with open(highest_risk_ela, 'rb') as f:
                ela_base64 = base64.b64encode(f.read()).decode('utf-8')
        except Exception:
            pass

    heatmap_base64 = None
    if highest_risk_heatmap and os.path.exists(highest_risk_heatmap):
        try:
            with open(highest_risk_heatmap, 'rb') as f:
                heatmap_base64 = base64.b64encode(f.read()).decode('utf-8')
        except Exception:
            pass

    original_page_base64 = None
    if PDF2IMAGE_AVAILABLE and 'images' in locals() and len(images) > 0:
        try:
            import io
            buffered = io.BytesIO()
            images[0].save(buffered, format="PNG")
            original_page_base64 = base64.b64encode(buffered.getvalue()).decode('utf-8')
        except Exception:
            pass

    deep_analysis_report = {
        'summary': {
            'total_suspect_regions': len(anomaly_indicators),
            'high_severity_regions': 1 if fusion_fraud_prob >= 75.0 else 0,
            'clustered_regions': 0,
            'detectors_triggered': len(anomaly_indicators)
        },
        'ela_analysis': {
            'high_anomaly_regions': [],
            'mean_anomaly': 0.0,
            'max_anomaly': 0.0,
            'anomaly_map': []
        },
        'noise_analysis': {
            'inconsistent_regions': [],
            'noise_uniformity_score': 1.0,
            'total_outliers': 0
        },
        'edge_analysis': {
            'unusual_edge_regions': [],
            'edge_density_score': 0.0,
            'total_anomalies': 0
        },
        'color_analysis': {
            'suspicious_uniform_regions': [],
            'total_whiteout_candidates': 0,
            'overall_color_uniformity': 1.0
        },
        'suspect_regions': [],
        'region_crops': [],
        'visualizations': {
            'composite_heatmap_base64': heatmap_base64,
            'annotated_image_base64': heatmap_base64,
            'layer_heatmaps': {
                'ela_detailed': ela_base64
            } if ela_base64 else {},
            'original_page_base64': original_page_base64
        }
    }

    return {
        "status": "success",
        "pipeline": "pdf_forensics",
        "fraud_probability": fusion_fraud_prob,
        "classification": classification,
        "extracted_gwa": extracted_gwa,
        "anomaly_indicators": anomaly_indicators,
        "pdf_report": pdf_report,
        "deep_analysis_report": deep_analysis_report,
        "visualizations": {
            'composite_heatmap_base64': heatmap_base64,
            'annotated_image_base64': heatmap_base64,
            'layer_heatmaps': {
                'ela_detailed': ela_base64
            } if ela_base64 else {},
            'original_page_base64': original_page_base64
        },
        "model": {
            "name": "aegis_pdf_fusion_v2",
            "version": "2.0.0",
            "mode": "trained" if model is not None else "forensic_rules"
        },
        "paths": {
            "heatmap_path": highest_risk_heatmap,
            "ela_path": highest_risk_ela
        }
    }
