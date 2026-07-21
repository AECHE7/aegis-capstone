import os
import cv2
import numpy as np

img_path = r"E:\aegis-capstone\aegis-ai\real_test.jpg"
if not os.path.exists(img_path):
    img_path = r"E:\aegis-capstone\aegis-ai\test.jpg"

print(f"Testing on image: {img_path}")
img = cv2.imread(img_path)
if img is None:
    print("Image not loaded.")
    exit(1)

h, w, _ = img.shape
print(f"Image shape: {w}x{h}")

# 1. LAB Color Space + CLAHE Luminance Gradient Analysis
lab = cv2.cvtColor(img, cv2.COLOR_BGR2LAB)
l_channel, a_channel, b_channel = cv2.split(lab)

clahe = cv2.createCLAHE(clipLimit=4.0, tileGridSize=(8, 8))
cl = clahe.apply(l_channel)

# High-frequency gradient map
grad_x = cv2.Sobel(cl, cv2.CV_64F, 1, 0, ksize=3)
grad_y = cv2.Sobel(cl, cv2.CV_64F, 0, 1, ksize=3)
grad_mag = cv2.magnitude(grad_x, grad_y)
grad_mag = cv2.normalize(grad_mag, None, 0, 255, cv2.NORM_MINMAX).astype(np.uint8)

# 2. Adaptive Binarization + Rectangular Patch Contour Audit
blur = cv2.GaussianBlur(cl, (5, 5), 0)
thresh = cv2.adaptiveThreshold(blur, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY_INV, 11, 2)

contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
detected_patches = []

for c in contours:
    x, y, bw, bh = cv2.boundingRect(c)
    area = bw * bh
    aspect_ratio = float(bw) / bh if bh > 0 else 0
    # Search for rectangular patch boxes (typical whiteout or pasted text box over grade rows)
    if 100 <= area <= (w * h * 0.15) and 0.5 <= aspect_ratio <= 10.0:
        # Check standard deviation inside bounding box in original image vs surrounding area
        patch_crop = cl[y:y+bh, x:x+bw]
        if patch_crop.size > 0:
            std_val = float(np.std(patch_crop))
            if std_val < 15.0: # Very uniform / flat whiteout patch
                detected_patches.append((x, y, bw, bh, std_val))

print(f"Detected potential whiteout/tampered patch boxes: {len(detected_patches)}")
for p in detected_patches[:5]:
    print(f"  Box at x={p[0]}, y={p[1]}, w={p[2]}, h={p[3]} (Uniformity std={p[4]:.2f})")
