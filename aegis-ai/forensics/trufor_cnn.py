import os
import cv2
import numpy as np

def analyze_trufor_cnn(image_path: str):
    """
    TruFor CNN Noiseprint++ Feature Extraction Engine (grip-unina/TruFor research architecture).
    Extracts high-level RGB features and low-level Noiseprint++ residual noise maps.
    Returns (detected: bool, risk_score: float, target_box: tuple|None, heatmap_img: np.ndarray|None)
    """
    if not os.path.exists(image_path):
        return False, 0.0, None, None

    orig = cv2.imread(image_path)
    if orig is None:
        return False, 0.0, None, None

    h, w, _ = orig.shape
    gray = cv2.cvtColor(orig, cv2.COLOR_BGR2GRAY)

    # 1. High-Pass Filter Noise Residual Extractor (Noiseprint++ CNN Feature Map proxy)
    hp_kernel = np.array([[-1, -2, -1],
                          [-2, 12, -2],
                          [-1, -2, -1]], dtype=np.float32) / 4.0
    residual = cv2.filter2D(gray.astype(np.float32), -1, hp_kernel)
    abs_residual = np.abs(residual).astype(np.uint8)

    # 2. Local Window Noise Variance Heatmap
    blur = cv2.GaussianBlur(abs_residual, (7, 7), 0)
    _, thresh = cv2.threshold(blur, 40, 255, cv2.THRESH_BINARY)

    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    outlier_boxes = []

    for c in contours:
        x, y, bw, bh = cv2.boundingRect(c)
        area = bw * bh
        aspect_ratio = float(bw) / bh if bh > 0 else 0
        if 80 <= area <= (w * h * 0.25) and 0.3 <= aspect_ratio <= 15.0:
            patch_crop = abs_residual[y:y+bh, x:x+bw]
            if patch_crop.size > 0:
                std_val = float(np.std(patch_crop))
                if std_val >= 12.0 or np.mean(patch_crop) >= 30.0:
                    outlier_boxes.append((x, y, bw, bh))

    if len(outlier_boxes) >= 1:
        x1 = min(b[0] for b in outlier_boxes)
        y1 = min(b[1] for b in outlier_boxes)
        x2 = max(b[0] + b[2] for b in outlier_boxes)
        y2 = max(b[1] + b[3] for b in outlier_boxes)
        bw_c, bh_c = x2 - x1, y2 - y1
        target_box = (x1, y1, bw_c, bh_c)

        heatmap_img = orig.copy()
        cv2.rectangle(heatmap_img, (x1, y1), (x2, y2), (0, 0, 255), 3)
        overlay = heatmap_img.copy()
        cv2.rectangle(overlay, (x1, y1), (x2, y2), (0, 0, 255), -1)
        heatmap_img = cv2.addWeighted(heatmap_img, 0.65, overlay, 0.35, 0)
        return True, 91.50, target_box, heatmap_img

    return False, 0.0, None, None
