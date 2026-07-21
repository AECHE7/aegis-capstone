import os
import json
import uuid
import numpy as np
from PIL import Image, ImageDraw, ImageChops, ImageEnhance, ImageFilter

try:
    import tensorflow as tf
    from tensorflow.keras.applications import EfficientNetB4
    from tensorflow.keras.applications.efficientnet import preprocess_input
    from tensorflow.keras.layers import Dense, GlobalAveragePooling2D, Dropout
    from tensorflow.keras.models import Model
    from tensorflow.keras.optimizers import Adam
    TENSORFLOW_AVAILABLE = True
except ImportError:
    TENSORFLOW_AVAILABLE = False
    print("TensorFlow not installed. Run in Python 3.11 environment to train v4 model.")

DATASET_DIR = "dataset_v4_web_screenshots"
MODEL_SAVE_PATH = "aegis_efficientnet_b4_v4.keras"
METRICS_SAVE_PATH = "model_metrics_v4.json"

def generate_web_screenshot_cog(is_tampered=False):
    """Generates a synthetic web screenshot transcript with optional Canva/Paint whiteout grade edits."""
    img = Image.new('RGB', (800, 1000), color=(255, 255, 255))
    draw = ImageDraw.Draw(img)

    # Web browser UI top bar simulation
    draw.rectangle([0, 0, 800, 40], fill=(235, 238, 242))
    draw.ellipse([15, 12, 27, 24], fill=(255, 95, 86))
    draw.ellipse([35, 12, 47, 24], fill=(255, 189, 46))
    draw.ellipse([55, 12, 67, 24], fill=(255, 39, 200))
    draw.rectangle([100, 8, 700, 32], fill=(255, 255, 255), outline=(200, 200, 200))
    draw.text((120, 12), "https://portal.clsu.edu.ph/grades/view", fill=(100, 100, 100))

    # Page Header
    draw.text((250, 70), "CENTRAL LUZON STATE UNIVERSITY", fill=(15, 23, 42))
    draw.text((310, 95), "STUDENT ACADEMIC PORTAL", fill=(71, 85, 105))
    draw.line([50, 130, 750, 130], fill=(226, 232, 240), width=2)

    # Grade Table
    draw.rectangle([80, 160, 720, 550], fill=(255, 255, 255), outline=(203, 213, 225), width=1)
    draw.rectangle([80, 160, 720, 200], fill=(241, 245, 249))
    draw.text((100, 172), "COURSE CODE", fill=(51, 65, 85))
    draw.text((320, 172), "UNITS", fill=(51, 65, 85))
    draw.text((500, 172), "FINAL GRADE", fill=(51, 65, 85))

    subjects = [
        ("INTECH 3200", "3.0", "1.25"),
        ("ITEC 3201", "3.0", "1.50"),
        ("ITCAP 3200", "3.0", "1.25"),
        ("MATH 1100", "3.0", "1.75"),
        ("ENG 1100", "3.0", "1.50")
    ]

    for idx, (sub, unit, gr) in enumerate(subjects):
        y = 220 + idx * 45
        draw.text((100, y), sub, fill=(15, 23, 42))
        draw.text((330, y), unit, fill=(15, 23, 42))
        draw.text((510, y), gr, fill=(15, 23, 42))
        draw.line([80, y + 35, 720, y + 35], fill=(241, 245, 249), width=1)

    if is_tampered:
        # Simulate Digital Whiteout Box forgery artifact (Paint / Canva edit over grade row)
        draw.rectangle([500, 260, 600, 295], fill=(248, 250, 252))
        draw.text((510, 265), "1.00", fill=(0, 0, 0))
        draw.rectangle([300, 600, 720, 640], fill=(255, 255, 255))
        draw.text((100, 610), "CUMULATIVE GWA: 1.00 (FORGED)", fill=(220, 38, 38))
    else:
        draw.text((100, 610), "CUMULATIVE GWA: 1.45", fill=(15, 23, 42))

    return img

def create_clahe_lab_tensor(img):
    """Computes CLAHE L* Luminance tensor representation."""
    cv_img = np.array(img)
    lab = cv2_lab_transform(cv_img)
    return Image.fromarray(lab).resize((380, 380))

def cv2_lab_transform(cv_img):
    import cv2
    lab = cv2.cvtColor(cv_img, cv2.COLOR_RGB2LAB)
    l, a, b = cv2.split(lab)
    clahe = cv2.createCLAHE(clipLimit=4.0, tileGridSize=(8, 8))
    cl = clahe.apply(l)
    return cv2.cvtColor(cv2.merge([cl, a, b]), cv2.COLOR_LAB2RGB)

def build_v4_dataset(samples_per_class=100):
    """Builds v4 web screenshot forgery dataset."""
    print(f"[DATASET_V4] Generating {samples_per_class} web screenshot forgery samples per class in '{DATASET_DIR}'...")
    for split in ['train', 'val']:
        for category in ['authentic', 'tampered']:
            os.makedirs(os.path.join(DATASET_DIR, split, category), exist_ok=True)

    for i in range(samples_per_class):
        split = 'train' if i < int(samples_per_class * 0.8) else 'val'
        
        # Authentic
        auth_img = generate_web_screenshot_cog(is_tampered=False)
        auth_tensor = create_clahe_lab_tensor(auth_img)
        auth_tensor.save(os.path.join(DATASET_DIR, split, 'authentic', f"auth_{i}.jpg"))

        # Tampered
        tamp_img = generate_web_screenshot_cog(is_tampered=True)
        tamp_tensor = create_clahe_lab_tensor(tamp_img)
        tamp_tensor.save(os.path.join(DATASET_DIR, split, 'tampered', f"tamp_{i}.jpg"))

    print("[DATASET_V4] Dataset generation complete.")

if __name__ == '__main__':
    print("[TRAIN_V4] Web screenshot forgery generator initialized.")
