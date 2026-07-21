"""
TruFor Image Forgery Detection Wrapper

Integrates TruFor (CVPR 2023) pretrained model for state-of-the-art
image forgery detection and localization.

Official Repo: https://github.com/grip-unina/TruFor
Paper: https://arxiv.org/pdf/2212.10957

Usage:
    detector = TruForDetector()
    result = detector.detect('image.jpg')
    # Returns: {score, localization_map, confidence_map, suspect_regions}
"""

import os
import sys
import numpy as np
import cv2
from typing import Dict, List, Tuple, Optional
import base64

# Add TruFor to path
TRUFOR_PATH = os.path.join(os.path.dirname(os.path.dirname(__file__)), 'TruFor', 'TruFor_train_test')
if os.path.exists(TRUFOR_PATH):
    sys.path.insert(0, TRUFOR_PATH)

class TruForDetector:
    """
    Wrapper for TruFor image forgery detection model.

    Provides pixel-level localization maps, confidence scores, and
    automated region extraction for suspected forgeries.
    """

    def __init__(self, weights_path: Optional[str] = None, device: str = 'cpu'):
        """
        Initialize TruFor detector.

        Args:
            weights_path: Path to trufor.pth.tar weights file
            device: 'cpu' or 'cuda' for GPU acceleration
        """
        self.device = device
        self.model = None
        self.weights_path = weights_path

        # Default weights path
        if weights_path is None:
            self.weights_path = os.path.join(
                os.path.dirname(os.path.dirname(__file__)),
                'TruFor_weights',
                'trufor.pth.tar'
            )

        # Try to load model
        try:
            self._load_model()
            print(f"[TruFor] Model loaded successfully from {self.weights_path}")
        except Exception as e:
            print(f"[TruFor] Warning: Could not load model: {e}")
            print(f"[TruFor] Will use fallback detection mode")
            self.model = None

    def _load_model(self):
        """Load TruFor model from weights file."""
        if not os.path.exists(self.weights_path):
            raise FileNotFoundError(f"Weights not found at {self.weights_path}")

        try:
            # Import TruFor dependencies
            from lib.config import config, update_config
            from lib.core.function import test_one_image
            from lib.models import get_model

            # Load config for trufor_ph3 (phase 3 - full model)
            config_file = os.path.join(TRUFOR_PATH, 'lib', 'config', 'trufor_ph3.yaml')
            update_config(config, config_file)

            # Override model file path
            config.TEST.MODEL_FILE = self.weights_path

            # Create model
            self.model = get_model(config)
            self.config = config
            self.test_function = test_one_image

        except ImportError as e:
            raise ImportError(
                f"TruFor dependencies not installed. "
                f"Please install: pip install torch torchvision timm mmsegmentation. "
                f"Error: {e}"
            )

    def detect(self, image_path: str) -> Dict:
        """
        Run TruFor inference on an image.

        Args:
            image_path: Path to input image

        Returns:
            {
                'score': float (0-1),  # Overall forgery score
                'localization_map': np.ndarray (H x W),  # Pixel-level forgery map
                'confidence_map': np.ndarray (H x W),  # Per-pixel confidence
                'classification': str ('Authentic' | 'Tampered'),
                'confidence_level': str ('high' | 'medium' | 'low'),
                'suspect_regions': List[dict],  # Bounding boxes of forgeries
                'heatmap_base64': str  # Base64-encoded visualization
            }
        """
        if not os.path.exists(image_path):
            raise FileNotFoundError(f"Image not found: {image_path}")

        # If model not loaded, use fallback
        if self.model is None:
            return self._fallback_detection(image_path)

        try:
            # Run TruFor inference
            result_npz = self._run_inference(image_path)

            # Parse results
            score = float(result_npz['score'])
            loc_map = result_npz['map']  # H x W float array
            conf_map = result_npz['conf']  # H x W float array

            # Extract suspect regions with bounding boxes
            suspect_regions = self._extract_regions(loc_map, conf_map)

            # Generate visualization
            heatmap = self._generate_heatmap(image_path, loc_map, suspect_regions)

            # Determine classification and confidence
            classification = 'Tampered' if score > 0.5 else 'Authentic'

            if score > 0.7 or score < 0.3:
                confidence_level = 'high'
            elif score > 0.6 or score < 0.4:
                confidence_level = 'medium'
            else:
                confidence_level = 'low'

            return {
                'score': score,
                'localization_map': loc_map,
                'confidence_map': conf_map,
                'classification': classification,
                'confidence_level': confidence_level,
                'suspect_regions': suspect_regions,
                'heatmap_base64': heatmap,
                'detector_name': 'TruFor_CVPR2023'
            }

        except Exception as e:
            print(f"[TruFor] Error during inference: {e}")
            return self._fallback_detection(image_path)

    def _run_inference(self, image_path: str) -> Dict:
        """
        Run TruFor model inference.

        Returns:
            npz-like dict with 'score', 'map', 'conf' keys
        """
        # Use TruFor's test_one_image function
        output = self.test_function(
            model=self.model,
            image_path=image_path,
            config=self.config
        )

        return output

    def _extract_regions(self, loc_map: np.ndarray, conf_map: np.ndarray,
                        threshold: float = 0.5, min_area: int = 100) -> List[Dict]:
        """
        Extract bounding boxes of suspect regions from localization map.

        Args:
            loc_map: Localization map (H x W)
            conf_map: Confidence map (H x W)
            threshold: Detection threshold (0-1)
            min_area: Minimum region area in pixels

        Returns:
            List of regions with format:
            [
                {
                    'x': int, 'y': int, 'width': int, 'height': int,
                    'confidence': float,
                    'severity': 'high' | 'medium'
                },
                ...
            ]
        """
        # Threshold localization map
        mask = (loc_map > threshold).astype(np.uint8) * 255

        # Find contours
        contours, _ = cv2.findContours(mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

        regions = []
        for cnt in contours:
            x, y, w, h = cv2.boundingRect(cnt)
            area = w * h

            if area < min_area:
                continue

            # Calculate average confidence in this region
            roi_conf = conf_map[y:y+h, x:x+w]
            avg_conf = float(np.mean(roi_conf))

            # Calculate severity based on localization intensity
            roi_loc = loc_map[y:y+h, x:x+w]
            avg_intensity = float(np.mean(roi_loc))

            severity = 'high' if avg_intensity > 0.7 else 'medium'

            regions.append({
                'x': int(x),
                'y': int(y),
                'width': int(w),
                'height': int(h),
                'confidence': avg_conf,
                'severity': severity,
                'area': area
            })

        # Sort by confidence (highest first)
        regions.sort(key=lambda r: r['confidence'], reverse=True)

        return regions

    def _generate_heatmap(self, image_path: str, loc_map: np.ndarray,
                         regions: List[Dict]) -> str:
        """
        Generate annotated heatmap visualization.

        Args:
            image_path: Original image path
            loc_map: Localization map
            regions: Suspect regions with bounding boxes

        Returns:
            Base64-encoded PNG of annotated heatmap
        """
        # Load original image
        img = cv2.imread(image_path)
        if img is None:
            return ""

        # Resize localization map to match image
        h, w = img.shape[:2]
        loc_resized = cv2.resize(loc_map, (w, h), interpolation=cv2.INTER_LINEAR)

        # Create heatmap
        heatmap = cv2.applyColorMap((loc_resized * 255).astype(np.uint8), cv2.COLORMAP_JET)

        # Overlay heatmap on image (50% transparency)
        overlay = cv2.addWeighted(img, 0.5, heatmap, 0.5, 0)

        # Draw bounding boxes
        for region in regions[:10]:  # Top 10 regions
            x, y, w_box, h_box = region['x'], region['y'], region['width'], region['height']

            # Color based on severity
            color = (0, 0, 255) if region['severity'] == 'high' else (0, 165, 255)  # Red or orange

            # Draw rectangle
            cv2.rectangle(overlay, (x, y), (x + w_box, y + h_box), color, 2)

            # Add label
            label = f"{region['severity'].upper()} ({region['confidence']:.2f})"
            cv2.putText(overlay, label, (x, y - 5),
                       cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 1)

        # Encode to base64
        _, buffer = cv2.imencode('.png', overlay)
        base64_str = base64.b64encode(buffer).decode('utf-8')

        return base64_str

    def _fallback_detection(self, image_path: str) -> Dict:
        """
        Fallback detection when TruFor model not available.

        Uses basic forensic indicators as placeholder.
        """
        print(f"[TruFor] Using fallback detection mode (model not loaded)")

        # Load image
        img = cv2.imread(image_path)
        if img is None:
            raise ValueError(f"Could not load image: {image_path}")

        h, w = img.shape[:2]

        # Create placeholder maps
        loc_map = np.zeros((h, w), dtype=np.float32)
        conf_map = np.zeros((h, w), dtype=np.float32)

        return {
            'score': 0.5,  # Neutral score
            'localization_map': loc_map,
            'confidence_map': conf_map,
            'classification': 'Unknown',
            'confidence_level': 'low',
            'suspect_regions': [],
            'heatmap_base64': '',
            'detector_name': 'TruFor_Fallback',
            'warning': 'TruFor model not loaded - using fallback mode'
        }


# Standalone test function
if __name__ == "__main__":
    import argparse

    parser = argparse.ArgumentParser(description='Test TruFor detector')
    parser.add_argument('image', help='Path to image file')
    parser.add_argument('--weights', help='Path to TruFor weights', default=None)
    parser.add_argument('--device', help='cpu or cuda', default='cpu')

    args = parser.parse_args()

    # Initialize detector
    detector = TruForDetector(weights_path=args.weights, device=args.device)

    # Run detection
    result = detector.detect(args.image)

    # Print results
    print("\n" + "="*60)
    print("TruFor Detection Results")
    print("="*60)
    print(f"Image: {args.image}")
    print(f"Score: {result['score']:.3f}")
    print(f"Classification: {result['classification']}")
    print(f"Confidence: {result['confidence_level']}")
    print(f"Suspect Regions: {len(result['suspect_regions'])}")

    if result['suspect_regions']:
        print("\nTop 5 Suspect Regions:")
        for i, region in enumerate(result['suspect_regions'][:5], 1):
            print(f"  {i}. ({region['x']}, {region['y']}) "
                  f"{region['width']}x{region['height']} - "
                  f"{region['severity']} severity (conf: {region['confidence']:.2f})")

    print("="*60 + "\n")
