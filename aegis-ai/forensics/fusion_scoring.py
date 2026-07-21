"""
Advanced fusion scoring system for combining multiple forensic detector outputs.
Uses weighted averaging, confidence scoring, and detector agreement analysis.
"""

import numpy as np
from typing import Dict, List, Tuple


class ForensicFusionEngine:
    """
    Intelligently combines outputs from multiple tamper detection algorithms.

    Detectors:
    - ELA patch analysis
    - CLAHE LAB whiteout detection
    - Font stroke consistency
    - TruFor CNN Noiseprint++
    - CAT-Net DCT analysis
    - Software fingerprint
    - Clone-stamp detection
    - CNN model prediction
    """

    # Detector weights based on empirical reliability
    DETECTOR_WEIGHTS = {
        'model_prediction': 0.30,      # Highest weight - trained on real data
        'clahe_lab': 0.20,             # Strong for whiteout/erased cells
        'ela_patch': 0.15,             # Good for localized edits
        'trufor_cnn': 0.12,            # Noiseprint analysis
        'catnet_dct': 0.10,            # DCT compression artifacts
        'clone_detection': 0.08,       # Copy-paste detection
        'font_stroke': 0.03,           # Lower - can have false positives
        'software_fingerprint': 0.02   # Informative but not deterministic
    }

    # Confidence thresholds for each detector
    CONFIDENCE_THRESHOLDS = {
        'high': 85.0,      # Very confident detection
        'medium': 60.0,    # Moderate confidence
        'low': 35.0        # Low confidence / borderline
    }

    def __init__(self, mode: str = 'balanced'):
        """
        Initialize fusion engine.

        Args:
            mode: 'strict' (fewer false positives), 'balanced', or 'sensitive' (fewer false negatives)
        """
        self.mode = mode
        self.weights = self._adjust_weights_for_mode(mode)

    def _adjust_weights_for_mode(self, mode: str) -> Dict[str, float]:
        """Adjust detector weights based on operation mode."""
        weights = self.DETECTOR_WEIGHTS.copy()

        if mode == 'strict':
            # Increase model weight, decrease heuristic detectors
            weights['model_prediction'] = 0.40
            weights['clahe_lab'] = 0.18
            weights['font_stroke'] = 0.01
            weights['software_fingerprint'] = 0.01

        elif mode == 'sensitive':
            # Increase sensitivity - boost heuristic detectors
            weights['clahe_lab'] = 0.25
            weights['ela_patch'] = 0.18
            weights['clone_detection'] = 0.12

        # Normalize to sum to 1.0
        total = sum(weights.values())
        return {k: v/total for k, v in weights.items()}

    def fuse_scores(self, detector_results: Dict[str, Dict]) -> Dict:
        """
        Fuse multiple detector outputs into unified fraud assessment.

        Args:
            detector_results: Dict mapping detector name to result dict with keys:
                - 'detected': bool
                - 'risk_score': float (0-100)
                - 'confidence': str (optional: 'high', 'medium', 'low')

        Returns:
            {
                'fraud_probability': float,
                'classification': str,
                'confidence': str,
                'detector_agreement': float,
                'active_detectors': list,
                'fusion_method': str
            }
        """
        active_detectors = []
        weighted_sum = 0.0
        total_weight = 0.0

        # Collect active detector scores
        for detector_name, result in detector_results.items():
            if result.get('detected', False):
                risk = result.get('risk_score', 0.0)
                weight = self.weights.get(detector_name, 0.01)

                # Adjust weight based on confidence level
                confidence = result.get('confidence', 'medium')
                confidence_multiplier = {
                    'high': 1.2,
                    'medium': 1.0,
                    'low': 0.7
                }.get(confidence, 1.0)

                adjusted_weight = weight * confidence_multiplier

                weighted_sum += risk * adjusted_weight
                total_weight += adjusted_weight
                active_detectors.append({
                    'name': detector_name,
                    'risk': risk,
                    'weight': adjusted_weight
                })

        # Calculate weighted average fraud probability
        if total_weight > 0:
            fraud_probability = weighted_sum / total_weight
        else:
            # No detectors fired - likely authentic
            fraud_probability = 12.5

        # Calculate detector agreement (how many detectors agree)
        total_detectors = len([r for r in detector_results.values() if 'detected' in r])
        num_detected = len(active_detectors)

        detector_agreement = num_detected / total_detectors if total_detectors > 0 else 0.0

        # Agreement boost: If many detectors agree, increase confidence
        if detector_agreement >= 0.5 and num_detected >= 3:
            # Multiple detectors agree - boost score slightly
            fraud_probability = min(98.0, fraud_probability * 1.15)

        # Agreement penalty: If only one weak detector fires, reduce confidence
        if num_detected == 1 and detector_agreement < 0.3:
            fraud_probability = fraud_probability * 0.85

        # Determine classification
        classification = "Tampered" if fraud_probability >= 50.0 else "Authentic"

        # Determine overall confidence level
        if fraud_probability >= 85.0 or (fraud_probability >= 70.0 and detector_agreement >= 0.6):
            confidence = "high"
        elif fraud_probability >= 60.0 or detector_agreement >= 0.4:
            confidence = "medium"
        else:
            confidence = "low"

        return {
            'fraud_probability': round(fraud_probability, 2),
            'classification': classification,
            'confidence': confidence,
            'detector_agreement': round(detector_agreement, 3),
            'active_detectors': [d['name'] for d in active_detectors],
            'detector_details': active_detectors,
            'fusion_method': f'weighted_average_{self.mode}'
        }

    def fallback_deterministic_scoring(self, detector_results: Dict[str, Dict]) -> float:
        """
        Fallback when CNN model unavailable - use deterministic rules.

        Returns fraud probability based on heuristic detectors only.
        """
        # High-priority detectors for deterministic mode
        priority_scores = []

        if detector_results.get('clahe_lab', {}).get('detected'):
            priority_scores.append(detector_results['clahe_lab']['risk_score'])

        if detector_results.get('ela_patch', {}).get('detected'):
            priority_scores.append(detector_results['ela_patch']['risk_score'])

        if detector_results.get('clone_detection', {}).get('detected'):
            priority_scores.append(detector_results['clone_detection']['risk_score'])

        if detector_results.get('trufor_cnn', {}).get('detected'):
            priority_scores.append(detector_results['trufor_cnn']['risk_score'])

        if len(priority_scores) >= 2:
            # Multiple strong signals - take average of top 2
            top_scores = sorted(priority_scores, reverse=True)[:2]
            return round(np.mean(top_scores), 2)

        elif len(priority_scores) == 1:
            # Single detector - use it but cap at 85%
            return min(85.0, priority_scores[0])

        else:
            # No strong signals - likely authentic
            return 12.5


def compute_detector_correlation(history: List[Dict[str, Dict]]) -> np.ndarray:
    """
    Analyze correlation between detectors across multiple scans.
    Helps identify redundant or complementary detectors.

    Args:
        history: List of detector_results dicts from multiple document scans

    Returns:
        Correlation matrix (numpy array)
    """
    if len(history) < 5:
        return None

    detector_names = list(history[0].keys())
    n_detectors = len(detector_names)

    # Build detection matrix: rows = scans, cols = detectors
    detection_matrix = np.zeros((len(history), n_detectors))

    for i, scan_results in enumerate(history):
        for j, detector_name in enumerate(detector_names):
            detection_matrix[i, j] = 1.0 if scan_results[detector_name].get('detected') else 0.0

    # Compute correlation matrix
    correlation = np.corrcoef(detection_matrix, rowvar=False)

    return correlation
