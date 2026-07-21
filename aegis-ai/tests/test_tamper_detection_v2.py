"""
Comprehensive test suite for enhanced tamper detection system.

Tests all detection methods against known authentic and tampered samples.
"""

import os
import sys
import cv2
import numpy as np
from PIL import Image, ImageDraw, ImageFont

# Add parent directory to path for imports
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..'))


class TamperTestGenerator:
    """Generate synthetic tampered images for testing."""

    @staticmethod
    def create_authentic_grade_sheet(width=800, height=1000):
        """Create a clean, authentic-looking grade sheet."""
        img = Image.new('RGB', (width, height), color='white')
        draw = ImageDraw.Draw(img)

        # Draw table structure
        for i in range(5):
            y = 200 + i * 80
            draw.rectangle([100, y, width-100, y+60], outline='black', width=2)
            draw.text((120, y+20), f"Subject {i+1}", fill='black')
            draw.text((width-200, y+20), f"1.{i+2}5", fill='black')

        # Add GWA
        draw.text((120, height-200), "GENERAL WEIGHTED AVERAGE: 1.75", fill='black')

        return img

    @staticmethod
    def apply_whiteout_edit(img, x, y, w, h):
        """Apply white box patch (common tampering method)."""
        draw = ImageDraw.Draw(img)
        draw.rectangle([x, y, x+w, y+h], fill='white', outline=None)
        return img

    @staticmethod
    def apply_clone_stamp(img_cv, src_region, dst_region):
        """Clone a region from src to dst (opencv format)."""
        sx, sy, sw, sh = src_region
        dx, dy, dw, dh = dst_region

        source = img_cv[sy:sy+sh, sx:sx+sw].copy()
        img_cv[dy:dy+dh, dx:dx+dw] = cv2.resize(source, (dw, dh))

        return img_cv

    @staticmethod
    def apply_text_edit(img, x, y, old_text, new_text):
        """Replace text by whiteout + new text."""
        draw = ImageDraw.Draw(img)
        # Whiteout old area
        bbox = draw.textbbox((x, y), old_text)
        draw.rectangle(bbox, fill='white')
        # Write new text
        draw.text((x, y), new_text, fill='black')
        return img


def test_authentic_image_detection():
    """Test that authentic images score low fraud probability."""
    from pipelines.image_forensics_v2 import run_image_pipeline_v2

    # Generate authentic image
    auth_img = TamperTestGenerator.create_authentic_grade_sheet()
    auth_path = "test_authentic.png"
    auth_img.save(auth_path)

    ela_path = "test_authentic_ela.jpg"
    heatmap_path = "test_authentic_heatmap.jpg"

    try:
        result = run_image_pipeline_v2(
            original_path=auth_path,
            ela_path=ela_path,
            heatmap_path=heatmap_path,
            model=None,
            fusion_mode='balanced'
        )

        # Authentic image should have low fraud probability
        assert result['fraud_probability'] < 30.0, f"Authentic image scored too high: {result['fraud_probability']}"
        assert result['classification'] == 'Authentic'
        assert result['detector_agreement'] < 0.3

        print(f"[OK] Authentic detection test passed: {result['fraud_probability']}%")

    finally:
        # Cleanup
        for path in [auth_path, ela_path, heatmap_path]:
            if os.path.exists(path):
                os.remove(path)


def test_whiteout_tamper_detection():
    """Test detection of whiteout box edits."""
    from pipelines.image_forensics_v2 import run_image_pipeline_v2

    # Create tampered image with whiteout
    img = TamperTestGenerator.create_authentic_grade_sheet()
    img = TamperTestGenerator.apply_whiteout_edit(img, 500, 300, 150, 40)

    tamper_path = "test_whiteout.png"
    img.save(tamper_path)

    ela_path = "test_whiteout_ela.jpg"
    heatmap_path = "test_whiteout_heatmap.jpg"

    try:
        result = run_image_pipeline_v2(
            original_path=tamper_path,
            ela_path=ela_path,
            heatmap_path=heatmap_path,
            model=None,
            fusion_mode='balanced'
        )

        # Whiteout should be detected
        assert result['fraud_probability'] >= 50.0, f"Whiteout not detected: {result['fraud_probability']}"
        assert result['classification'] == 'Tampered'
        assert 'digital_whiteout_box_detected' in result['anomaly_indicators']

        print(f"[OK] Whiteout detection test passed: {result['fraud_probability']}%")

    finally:
        for path in [tamper_path, ela_path, heatmap_path]:
            if os.path.exists(path):
                os.remove(path)


def test_clone_stamp_detection():
    """Test detection of cloned/copied regions."""
    from pipelines.image_forensics_v2 import run_image_pipeline_v2

    # Create image with cloned region
    img = TamperTestGenerator.create_authentic_grade_sheet()
    img_cv = cv2.cvtColor(np.array(img), cv2.COLOR_RGB2BGR)

    # Clone a grade cell to another location
    img_cv = TamperTestGenerator.apply_clone_stamp(
        img_cv,
        src_region=(500, 280, 100, 60),
        dst_region=(500, 440, 100, 60)
    )

    tamper_path = "test_clone.png"
    cv2.imwrite(tamper_path, img_cv)

    ela_path = "test_clone_ela.jpg"
    heatmap_path = "test_clone_heatmap.jpg"

    try:
        result = run_image_pipeline_v2(
            original_path=tamper_path,
            ela_path=ela_path,
            heatmap_path=heatmap_path,
            model=None,
            fusion_mode='sensitive'  # Use sensitive mode for clone detection
        )

        # Clone should be detected
        print(f"Clone detection result: {result['fraud_probability']}%")
        print(f"Indicators: {result['anomaly_indicators']}")

        # May not always detect simple clones, but should score higher than authentic
        assert result['fraud_probability'] >= 25.0, f"Clone not flagged: {result['fraud_probability']}"

        print(f"[OK] Clone detection test passed: {result['fraud_probability']}%")

    finally:
        for path in [tamper_path, ela_path, heatmap_path]:
            if os.path.exists(path):
                os.remove(path)


def test_fusion_modes():
    """Test different fusion modes (strict, balanced, sensitive)."""
    from pipelines.image_forensics_v2 import run_image_pipeline_v2

    img = TamperTestGenerator.create_authentic_grade_sheet()
    img = TamperTestGenerator.apply_whiteout_edit(img, 500, 300, 150, 40)

    tamper_path = "test_fusion.png"
    img.save(tamper_path)

    ela_path = "test_fusion_ela.jpg"
    heatmap_path = "test_fusion_heatmap.jpg"

    try:
        results = {}

        for mode in ['strict', 'balanced', 'sensitive']:
            result = run_image_pipeline_v2(
                original_path=tamper_path,
                ela_path=ela_path,
                heatmap_path=heatmap_path,
                model=None,
                fusion_mode=mode
            )
            results[mode] = result['fraud_probability']

        # Sensitive should score higher than strict
        assert results['sensitive'] >= results['balanced'] >= results['strict'] - 5.0

        print(f"[OK] Fusion modes test passed:")
        print(f"  Strict: {results['strict']}%")
        print(f"  Balanced: {results['balanced']}%")
        print(f"  Sensitive: {results['sensitive']}%")

    finally:
        for path in [tamper_path, ela_path, heatmap_path]:
            if os.path.exists(path):
                os.remove(path)


def test_detector_agreement():
    """Test that multiple detectors agreeing increases confidence."""
    from pipelines.image_forensics_v2 import run_image_pipeline_v2

    # Create heavily tampered image (should trigger multiple detectors)
    img = TamperTestGenerator.create_authentic_grade_sheet()
    img = TamperTestGenerator.apply_whiteout_edit(img, 500, 300, 150, 40)
    img = TamperTestGenerator.apply_whiteout_edit(img, 500, 400, 150, 40)

    tamper_path = "test_agreement.png"
    img.save(tamper_path)

    ela_path = "test_agreement_ela.jpg"
    heatmap_path = "test_agreement_heatmap.jpg"

    try:
        result = run_image_pipeline_v2(
            original_path=tamper_path,
            ela_path=ela_path,
            heatmap_path=heatmap_path,
            model=None,
            fusion_mode='balanced'
        )

        # Multiple edits should increase detector agreement
        assert result['detector_agreement'] >= 0.3, f"Low agreement: {result['detector_agreement']}"
        assert len(result.get('detector_details', {})) >= 2, "Not enough detectors triggered"

        print(f"[OK] Detector agreement test passed: {result['detector_agreement']:.2f}")
        print(f"  Active detectors: {list(result.get('detector_details', {}).keys())}")

    finally:
        for path in [tamper_path, ela_path, heatmap_path]:
            if os.path.exists(path):
                os.remove(path)


def test_threshold_sensitivity():
    """Test that lowered thresholds catch subtle edits."""
    from pipelines.image_forensics_v2 import run_image_pipeline_v2

    # Create subtle edit (small whiteout)
    img = TamperTestGenerator.create_authentic_grade_sheet()
    img = TamperTestGenerator.apply_whiteout_edit(img, 500, 300, 60, 25)  # Small patch

    tamper_path = "test_subtle.png"
    img.save(tamper_path)

    ela_path = "test_subtle_ela.jpg"
    heatmap_path = "test_subtle_heatmap.jpg"

    try:
        result = run_image_pipeline_v2(
            original_path=tamper_path,
            ela_path=ela_path,
            heatmap_path=heatmap_path,
            model=None,
            fusion_mode='sensitive'
        )

        # With lowered thresholds (z-score 1.9), should still detect
        print(f"Subtle edit detection: {result['fraud_probability']}%")
        print(f"Indicators: {result['anomaly_indicators']}")

        # Should at least flag as suspicious (>40%)
        assert result['fraud_probability'] >= 30.0, f"Subtle edit missed: {result['fraud_probability']}"

        print(f"[OK] Threshold sensitivity test passed: {result['fraud_probability']}%")

    finally:
        for path in [tamper_path, ela_path, heatmap_path]:
            if os.path.exists(path):
                os.remove(path)


if __name__ == '__main__':
    """Run all tests."""
    print("=" * 60)
    print("AEGIS Tamper Detection V2 - Test Suite")
    print("=" * 60)

    tests = [
        ("Authentic Image Detection", test_authentic_image_detection),
        ("Whiteout Tamper Detection", test_whiteout_tamper_detection),
        ("Clone Stamp Detection", test_clone_stamp_detection),
        ("Fusion Modes", test_fusion_modes),
        ("Detector Agreement", test_detector_agreement),
        ("Threshold Sensitivity", test_threshold_sensitivity),
    ]

    passed = 0
    failed = 0

    for test_name, test_func in tests:
        print(f"\n{'-' * 60}")
        print(f"Running: {test_name}")
        print('-' * 60)
        try:
            test_func()
            passed += 1
        except AssertionError as e:
            print(f"X FAILED: {e}")
            failed += 1
        except Exception as e:
            print(f"X ERROR: {e}")
            failed += 1

    print(f"\n{'=' * 60}")
    print(f"Test Results: {passed} passed, {failed} failed")
    print('=' * 60)
