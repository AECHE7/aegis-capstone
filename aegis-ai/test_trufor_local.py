"""
Simple script to test TruFor model locally before integration.

Usage:
    python test_trufor_local.py path/to/image.jpg
"""

import sys
import os

def test_trufor_simple():
    """Test TruFor without full dependencies - simplified version."""
    print("="*60)
    print("TruFor Local Test - Simplified Mode")
    print("="*60)

    # Check if image path provided
    if len(sys.argv) < 2:
        print("\n❌ ERROR: Please provide an image path")
        print("Usage: python test_trufor_local.py path/to/image.jpg")
        return

    image_path = sys.argv[1]

    # Verify image exists
    if not os.path.exists(image_path):
        print(f"\n❌ ERROR: Image not found: {image_path}")
        return

    print(f"\n📁 Image: {image_path}")
    print(f"📊 File size: {os.path.getsize(image_path) / 1024:.2f} KB")

    # Check if TruFor weights exist
    weights_path = "TruFor_weights/weights/trufor.pth.tar"
    if os.path.exists(weights_path):
        weights_size = os.path.getsize(weights_path) / (1024*1024)
        print(f"✅ TruFor weights found: {weights_size:.2f} MB")
    else:
        print(f"❌ TruFor weights NOT found at: {weights_path}")
        print("   Download from: https://www.grip.unina.it/download/prog/TruFor/TruFor_weights.zip")
        return

    # Try to import TruFor detector
    try:
        from forensics.trufor_detector import TruForDetector
        print("✅ TruFor detector module imported")
    except ImportError as e:
        print(f"❌ Failed to import TruFor detector: {e}")
        print("\nMissing dependencies. Install with:")
        print("  pip install torch torchvision timm opencv-python numpy pillow")
        return

    # Initialize detector
    print("\n" + "="*60)
    print("Initializing TruFor Detector...")
    print("="*60)

    try:
        detector = TruForDetector(weights_path=weights_path, device='cpu')
        print("✅ Detector initialized")
    except Exception as e:
        print(f"❌ Failed to initialize detector: {e}")
        print("\nThis is expected if TruFor dependencies are not fully installed.")
        print("The detector will use fallback mode (no actual detection).")
        return

    # Run detection
    print("\n" + "="*60)
    print("Running Detection...")
    print("="*60)

    try:
        result = detector.detect(image_path)

        print("\n" + "="*60)
        print("RESULTS")
        print("="*60)
        print(f"Detector: {result.get('detector_name', 'Unknown')}")
        print(f"Score: {result['score']:.3f}")
        print(f"Classification: {result['classification']}")
        print(f"Confidence: {result['confidence_level']}")
        print(f"Suspect Regions: {len(result['suspect_regions'])}")

        if 'warning' in result:
            print(f"\n⚠️  {result['warning']}")

        if result['suspect_regions']:
            print("\n📍 Top 5 Suspect Regions:")
            for i, region in enumerate(result['suspect_regions'][:5], 1):
                print(f"  {i}. Position: ({region['x']}, {region['y']}) "
                      f"Size: {region['width']}x{region['height']} "
                      f"Severity: {region['severity']} "
                      f"(conf: {region['confidence']:.2f})")

        # Save heatmap if available
        if result.get('heatmap_base64'):
            import base64
            output_path = image_path.replace('.', '_trufor_heatmap.')
            if not output_path.endswith(('.jpg', '.png')):
                output_path += '.png'

            with open(output_path, 'wb') as f:
                f.write(base64.b64decode(result['heatmap_base64']))
            print(f"\n💾 Heatmap saved: {output_path}")

        print("\n" + "="*60)
        print("Test Complete!")
        print("="*60)

    except Exception as e:
        print(f"\n❌ Detection failed: {e}")
        import traceback
        traceback.print_exc()


def test_basic_forensics():
    """Test basic forensic detectors without TruFor."""
    print("\n" + "="*60)
    print("Testing Basic Forensic Detectors (Without TruFor)")
    print("="*60)

    if len(sys.argv) < 2:
        print("\n❌ ERROR: Please provide an image path")
        return

    image_path = sys.argv[1]

    if not os.path.exists(image_path):
        print(f"\n❌ ERROR: Image not found: {image_path}")
        return

    print(f"\n📁 Testing: {image_path}")

    # Test ELA
    try:
        from pipelines.image_forensics_v2 import generate_ela, detect_ela_patch_anomalies

        ela_path = image_path.replace('.', '_ela.')
        generate_ela(image_path, ela_path)
        print(f"✅ ELA generated: {ela_path}")

        patch_det, patch_risk, _, _, patch_conf = detect_ela_patch_anomalies(ela_path, image_path)
        print(f"   ELA Patch Detection: {patch_det} (risk: {patch_risk:.2f}, conf: {patch_conf})")

    except Exception as e:
        print(f"❌ ELA test failed: {e}")

    # Test CLAHE LAB
    try:
        from pipelines.image_forensics_v2 import detect_clahe_lab_anomalies

        lab_det, lab_risk, _, _, lab_conf = detect_clahe_lab_anomalies(image_path)
        print(f"✅ CLAHE LAB Detection: {lab_det} (risk: {lab_risk:.2f}, conf: {lab_conf})")

    except Exception as e:
        print(f"❌ CLAHE LAB test failed: {e}")

    # Test Clone Detection
    try:
        from forensics.clone_detection import detect_clone_stamp

        clone_det, clone_risk, clone_pairs, _ = detect_clone_stamp(image_path, sensitivity=0.82)
        print(f"✅ Clone Detection: {clone_det} (risk: {clone_risk:.2f}, pairs: {len(clone_pairs) if clone_pairs else 0})")

    except Exception as e:
        print(f"❌ Clone detection test failed: {e}")

    print("\n" + "="*60)


if __name__ == "__main__":
    print("\n" + "="*70)
    print(" "*20 + "TruFor Local Testing")
    print("="*70)

    # Ask user which test to run
    if len(sys.argv) > 1 and sys.argv[1] == "--basic":
        # Shift arguments
        sys.argv.pop(1)
        test_basic_forensics()
    else:
        print("\nTest Mode: Full TruFor Detection")
        print("(Use --basic flag for basic forensics only)")
        print()
        test_trufor_simple()
