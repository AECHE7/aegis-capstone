"""
Clone-stamp and copy-move forgery detection using feature matching.
Detects when students copy-paste grade cells or duplicate regions.
"""

import os
import cv2
import numpy as np
from typing import Tuple, List, Optional


def detect_clone_stamp(image_path: str, sensitivity: float = 0.85) -> Tuple[bool, float, Optional[List], Optional[np.ndarray]]:
    """
    Detects copy-move/clone-stamp forgeries using ORB feature matching.

    This catches:
    - Copied grade cells pasted over other grades
    - Duplicated text regions
    - Cloned signatures or stamps

    Args:
        image_path: Path to the image file
        sensitivity: Match threshold (0.0-1.0). Lower = more sensitive

    Returns:
        (detected: bool, risk_score: float, clone_pairs: list, heatmap: np.ndarray|None)
    """
    if not os.path.exists(image_path):
        return False, 0.0, None, None

    img = cv2.imread(image_path)
    if img is None:
        return False, 0.0, None, None

    h, w, _ = img.shape
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

    # 1. Extract ORB features (fast, rotation-invariant)
    orb = cv2.ORB_create(nfeatures=2000, scaleFactor=1.2, nlevels=8)
    keypoints, descriptors = orb.detectAndCompute(gray, None)

    if descriptors is None or len(keypoints) < 10:
        return False, 0.0, None, None

    # 2. Match features against themselves to find duplicated regions
    bf = cv2.BFMatcher(cv2.NORM_HAMMING, crossCheck=False)
    matches = bf.knnMatch(descriptors, descriptors, k=2)

    # 3. Filter good matches using Lowe's ratio test
    suspicious_matches = []
    clone_pairs = []

    for match_pair in matches:
        if len(match_pair) < 2:
            continue

        m, n = match_pair

        # Skip self-matches
        if m.queryIdx == m.trainIdx:
            continue

        # Lowe's ratio test (lower threshold = more strict)
        if m.distance < sensitivity * n.distance:
            pt1 = keypoints[m.queryIdx].pt
            pt2 = keypoints[m.trainIdx].pt

            # Calculate spatial distance between matched features
            spatial_dist = np.sqrt((pt1[0] - pt2[0])**2 + (pt1[1] - pt2[1])**2)

            # Only consider matches that are spatially separated (not adjacent pixels)
            # Typical clone-stamp: 20-300 pixels apart
            if 20 <= spatial_dist <= min(w, h) * 0.3:
                suspicious_matches.append(m)
                clone_pairs.append((pt1, pt2, spatial_dist))

    # 4. Cluster clone pairs to identify distinct tampered regions
    clone_clusters = []
    if len(clone_pairs) >= 3:
        # Group matches by proximity
        for pt1, pt2, dist in clone_pairs:
            added = False
            for cluster in clone_clusters:
                # Check if this match is near existing cluster
                cluster_center = np.mean([p[0] for p in cluster], axis=0)
                if np.linalg.norm(np.array(pt1) - cluster_center) < 50:
                    cluster.append((pt1, pt2, dist))
                    added = True
                    break

            if not added:
                clone_clusters.append([(pt1, pt2, dist)])

    # 5. Determine if cloning is detected
    detected = False
    risk_score = 0.0
    heatmap = None

    # Criteria: At least 3 clustered matches OR 8+ total suspicious matches
    significant_clusters = [c for c in clone_clusters if len(c) >= 3]

    if len(significant_clusters) >= 1 or len(suspicious_matches) >= 8:
        detected = True

        # Risk scoring based on match count and cluster quality
        base_risk = min(60.0, len(suspicious_matches) * 5.0)
        cluster_bonus = len(significant_clusters) * 10.0
        risk_score = min(94.0, base_risk + cluster_bonus)

        # 6. Generate heatmap visualization
        heatmap = img.copy()

        # Draw all clone pairs
        for pt1, pt2, _ in clone_pairs[:20]:  # Limit to top 20 for clarity
            x1, y1 = int(pt1[0]), int(pt1[1])
            x2, y2 = int(pt2[0]), int(pt2[1])

            # Draw circles at match locations
            cv2.circle(heatmap, (x1, y1), 8, (0, 0, 255), 2)
            cv2.circle(heatmap, (x2, y2), 8, (0, 0, 255), 2)

            # Draw line connecting cloned regions
            cv2.line(heatmap, (x1, y1), (x2, y2), (255, 0, 0), 1)

        # Highlight clustered regions with bounding boxes
        for cluster in significant_clusters:
            points = np.array([p[0] for p in cluster])
            x, y, bw, bh = cv2.boundingRect(points.astype(np.int32))
            cv2.rectangle(heatmap, (x-10, y-10), (x+bw+10, y+bh+10), (0, 255, 255), 3)

    return detected, risk_score, clone_pairs if detected else None, heatmap


def detect_block_matching_forgery(image_path: str, block_size: int = 16) -> Tuple[bool, float, Optional[np.ndarray]]:
    """
    Alternative clone detection using block-matching (faster, less accurate).

    Divides image into blocks and finds duplicated blocks using DCT coefficients.
    Good for detecting large copy-paste regions in grade tables.

    Args:
        image_path: Path to image file
        block_size: Size of blocks to compare (16x16 typical)

    Returns:
        (detected: bool, risk_score: float, heatmap: np.ndarray|None)
    """
    if not os.path.exists(image_path):
        return False, 0.0, None

    img = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    if img is None:
        return False, 0.0, None

    h, w = img.shape

    # Ensure image dimensions are divisible by block_size
    h_blocks = h // block_size
    w_blocks = w // block_size

    if h_blocks < 2 or w_blocks < 2:
        return False, 0.0, None

    # Extract block DCT features
    block_features = {}
    block_positions = {}

    for i in range(h_blocks):
        for j in range(w_blocks):
            y1, y2 = i * block_size, (i + 1) * block_size
            x1, x2 = j * block_size, (j + 1) * block_size

            block = img[y1:y2, x1:x2].astype(np.float32)

            # Compute DCT coefficients (frequency domain representation)
            dct_block = cv2.dct(block)

            # Use low-frequency coefficients as feature vector
            feature = dct_block[:8, :8].flatten()
            feature_key = tuple(np.round(feature, 1))

            if feature_key in block_features:
                block_features[feature_key].append((i, j))
            else:
                block_features[feature_key] = [(i, j)]

            block_positions[(i, j)] = feature_key

    # Find duplicated blocks (same DCT signature)
    duplicates = {k: v for k, v in block_features.items() if len(v) >= 2}

    if len(duplicates) == 0:
        return False, 0.0, None

    # Filter: Remove adjacent blocks (legitimate repetitive patterns like table lines)
    significant_duplicates = []

    for feature, positions in duplicates.items():
        for i in range(len(positions)):
            for j in range(i + 1, len(positions)):
                pos1 = positions[i]
                pos2 = positions[j]

                # Calculate block distance
                dist = np.sqrt((pos1[0] - pos2[0])**2 + (pos1[1] - pos2[1])**2)

                # Only flag if blocks are separated (not adjacent)
                if dist >= 2.0:
                    significant_duplicates.append((pos1, pos2, dist))

    if len(significant_duplicates) < 2:
        return False, 0.0, None

    # Generate risk score
    risk_score = min(88.0, 50.0 + len(significant_duplicates) * 6.0)

    # Create heatmap
    heatmap = cv2.cvtColor(img, cv2.COLOR_GRAY2BGR)

    for pos1, pos2, _ in significant_duplicates[:15]:
        y1, x1 = pos1[0] * block_size, pos1[1] * block_size
        y2, x2 = pos2[0] * block_size, pos2[1] * block_size

        cv2.rectangle(heatmap, (x1, y1), (x1 + block_size, y1 + block_size), (0, 0, 255), 2)
        cv2.rectangle(heatmap, (x2, y2), (x2 + block_size, y2 + block_size), (0, 0, 255), 2)

        # Draw connecting line
        center1 = (x1 + block_size//2, y1 + block_size//2)
        center2 = (x2 + block_size//2, y2 + block_size//2)
        cv2.line(heatmap, center1, center2, (255, 0, 255), 1)

    return True, risk_score, heatmap
