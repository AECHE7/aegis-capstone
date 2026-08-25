"""
Explainable Multi-Pillar Forensic Fusion Engine.
Calibrated scoring engine with 4-Pillar Evidence Breakdown and Document Syntax Gate.
"""

import numpy as np
from typing import Dict, List, Optional


class ForensicFusionEngine:
    """
    Combines outputs across 4 explainable pillars:
    - Pillar 1: Structural & Textual Integrity (OCR GWA vs Declared)
    - Pillar 2: Pixel Compression & Frequency (ELA + CAT-Net DCT)
    - Pillar 3: Sensor & Spatial Continuity (TruFor Noiseprint + Clone-Stamp)
    - Pillar 4: Metadata Provenance (EXIF software/camera headers)
    """

    DETECTOR_WEIGHTS = {
        'model_prediction': 0.35,      # Primary learned model representation
        'clone_detection': 0.20,       # Spliced text / duplicate grade cells
        'catnet_dct': 0.15,            # DCT compression artifacts
        'trufor_cnn': 0.12,            # Sensor noiseprint continuity
        'ela_patch': 0.08,             # Localized ELA variance
        'clahe_lab': 0.06,             # Whiteout / erased rectangular blocks
        'font_stroke': 0.02,           # Font stroke irregularity
        'software_fingerprint': 0.02   # Metadata tool signature
    }

    def __init__(self, mode: str = 'balanced'):
        self.mode = mode
        total = sum(self.DETECTOR_WEIGHTS.values())
        self.weights = {k: v / total for k, v in self.DETECTOR_WEIGHTS.items()}

    def fuse_scores(self, detector_results: Dict[str, Dict], ocr_matched: bool = False, syntax_gate: Optional[Dict] = None) -> Dict:
        active_detectors = []
        weighted_risk_sum = 0.0

        for detector_name, result in detector_results.items():
            if result.get('detected', False):
                risk = float(result.get('risk_score', 0.0))
                weight = self.weights.get(detector_name, 0.02)
                confidence = result.get('confidence', 'medium')
                conf_mult = {'high': 1.1, 'medium': 1.0, 'low': 0.7}.get(confidence, 1.0)
                
                effective_weight = weight * conf_mult
                weighted_risk_sum += (risk * effective_weight)

                active_detectors.append({
                    'name': detector_name,
                    'risk': round(risk, 2),
                    'weight': round(effective_weight, 3),
                    'confidence': confidence
                })

        num_detected = len(active_detectors)
        baseline_score = 6.0

        # Syntax check: If uploaded asset is not an academic document (e.g. 3D shirt, arbitrary graphic)
        is_academic_doc = syntax_gate.get('is_valid_academic_document', True) if syntax_gate else True

        if not is_academic_doc:
            # Unrecognized format: Center score around moderate review range (35% - 48%) rather than 98%
            fraud_probability = round(38.0 + min(12.0, weighted_risk_sum * 0.3), 1)
            classification = "Review Needed"
            confidence = "medium"
        else:
            if num_detected == 0:
                fraud_probability = baseline_score
            elif num_detected == 1:
                fraud_probability = baseline_score + min(24.0, weighted_risk_sum * 0.8)
            elif num_detected == 2:
                fraud_probability = baseline_score + min(46.0, weighted_risk_sum * 0.9)
            else:
                fraud_probability = baseline_score + min(65.0, weighted_risk_sum * 1.0)

            # OCR Credit: Valid extracted grade table dampens innocent camera noise
            if ocr_matched and fraud_probability > 15.0:
                fraud_probability = max(6.0, fraud_probability - 15.0)

            fraud_probability = round(max(5.0, min(95.0, fraud_probability)), 1)

            if fraud_probability >= 70.0:
                classification = "Tampered"
                confidence = "high"
            elif fraud_probability >= 35.0:
                classification = "Review Needed"
                confidence = "medium"
            else:
                classification = "Authentic"
                confidence = "high" if num_detected == 0 else "medium"

        # 4-Pillar Evidence Breakdown
        evidence_pillars = {
            "pillar_1_structural_ocr": {
                "title": "Text & Grade Consistency",
                "weight": "35%",
                "status": "Verified" if ocr_matched else ("No Academic OCR" if not is_academic_doc else "Unchecked"),
                "risk_contribution": 0.0 if ocr_matched else 10.0
            },
            "pillar_2_compression_frequency": {
                "title": "Pixel Compression & Frequency (ELA/DCT)",
                "weight": "25%",
                "status": "Anomalous" if any(d['name'] in ['ela_patch', 'catnet_dct'] for d in active_detectors) else "Clean",
                "risk_contribution": round(sum(d['risk'] * d['weight'] for d in active_detectors if d['name'] in ['ela_patch', 'catnet_dct']), 1)
            },
            "pillar_3_sensor_spatial": {
                "title": "Sensor & Spatial Continuity (TruFor/Clone)",
                "weight": "25%",
                "status": "Anomalous" if any(d['name'] in ['trufor_cnn', 'clone_detection', 'clahe_lab'] for d in active_detectors) else "Uniform",
                "risk_contribution": round(sum(d['risk'] * d['weight'] for d in active_detectors if d['name'] in ['trufor_cnn', 'clone_detection', 'clahe_lab']), 1)
            },
            "pillar_4_provenance_metadata": {
                "title": "Metadata & Provenance",
                "weight": "15%",
                "status": "Software Trace" if any(d['name'] == 'software_fingerprint' for d in active_detectors) else "Verifiable",
                "risk_contribution": round(sum(d['risk'] * d['weight'] for d in active_detectors if d['name'] == 'software_fingerprint'), 1)
            }
        }

        total_detectors = len([r for r in detector_results.values() if 'detected' in r])
        agreement_ratio = round(num_detected / max(1, total_detectors), 3)

        return {
            'fraud_probability': fraud_probability,
            'classification': classification,
            'confidence': confidence,
            'detector_agreement': agreement_ratio,
            'document_syntax': syntax_gate or {"is_valid_academic_document": True, "document_category": "Standard Document"},
            'evidence_pillars': evidence_pillars,
            'active_detectors': [d['name'] for d in active_detectors],
            'detector_details': active_detectors,
            'fusion_method': f'explainable_framework_{self.mode}'
        }

    def fallback_deterministic_scoring(self, detector_results: Dict[str, Dict], syntax_gate: Optional[Dict] = None) -> float:
        fused = self.fuse_scores(detector_results, syntax_gate=syntax_gate)
        return fused['fraud_probability']
