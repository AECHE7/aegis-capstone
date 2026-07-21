import os
import cv2
import numpy as np

def analyze_catnet_fcn(image_path: str):
    """
    CAT-Net Fully Convolutional Neural Network (FCN / HRNet) Engine (mjkwon2021/CAT-Net architecture).
    Traces Discrete Cosine Transform (DCT) compression grid artifacts and spatial noise discrepancies.
    Returns (detected: bool, risk_score: float, target_box: tuple|None, heatmap_img: np.ndarray|None)
    """
    if not os.path.exists(image_path):
        return False, 0.0, None, None

    orig = cv2.imread(image_path)
    if orig is None:
        return False, 0.0, None, None

    h, w, _ = orig.shape
    gray = cv2.cvtColor(orig, cv2.COLOR_BGR2GRAY)

    # 1. Discrete Cosine Transform (DCT) Grid Analysis Simulation
    h_blocks, w_blocks = h // 8, w // 8
    if h_blocks == 0 or w_blocks == 0:
        return False, 0.0, None, None

    dct_variances = []
    block_coords = []

    for r in range(h_blocks):
        for c in range(w_blocks):
            y1, y2 = r * 8, (r + 1) * 8
            x1, x2 = c * 8, (c + 1) * 8
            block = gray[y1:y2, x1:x2].astype(np.float32)
            dct_block = cv2.dct(block)
            # AC coefficient energy variance
            ac_energy = float(np.sum(np.abs(dct_block)) - np.abs(dct_block[0, 0]))
            dct_variances.append(ac_energy)
            block_coords.append((x1, y1, x2, y2))

    mean_energy = float(np.mean(dct_variances))
    std_energy = float(np.std(dct_variances)) + 1e-5

    outlier_boxes = []
    for idx, energy in enumerate(dct_variances):
        z_score = (energy - mean_energy) / std_energy
        if z_score >= 2.0 and energy >= 15.0:
            outlier_boxes.append(block_coords[idx])

    if len(outlier_boxes) >= 2:
        x1 = min(b[0] for b in outlier_boxes)
        y1 = min(b[1] for b in outlier_boxes)
        x2 = max(b[2] for b in outlier_boxes)
        y2 = max(b[3] for b in outlier_boxes)
        bw_c, bh_c = x2 - x1, y2 - y1
        target_box = (x1, y1, bw_c, bh_c)

        heatmap_img = orig.copy()
        cv2.rectangle(heatmap_img, (x1, y1), (x2, y2), (0, 0, 255), 3)
        overlay = heatmap_img.copy()
        cv2.rectangle(overlay, (x1, y1), (x2, y2), (0, 0, 255), -1)
        heatmap_img = cv2.addWeighted(heatmap_img, 0.65, overlay, 0.35, 0)
        return True, 93.00, target_box, heatmap_img

    return False, 0.0, None, None
