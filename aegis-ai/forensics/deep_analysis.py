"""
Deep Analysis Engine for Tamper Detection

Provides thorough, multi-pass inspection with detailed forensic analysis
and precise edit location identification.

Use this when:
- Standard detection gives unexpected results
- Need to identify exact pixel locations of edits
- Admin wants detailed forensic report
- High-value scholarship requires maximum scrutiny
"""

import os
import cv2
import numpy as np
from typing import Dict, List, Tuple, Optional
import base64


class DeepForensicAnalyzer:
    """
    Performs comprehensive deep analysis of document images.

    Returns detailed forensic report with:
    - Pixel-level anomaly maps
    - Region-by-region analysis
    - Multi-layer visualization
    - Exact edit coordinates
    """

    def __init__(self, image_path: str):
        self.image_path = image_path
        self.image = cv2.imread(image_path)
        self.gray = cv2.cvtColor(self.image, cv2.COLOR_BGR2GRAY)
        self.h, self.w = self.gray.shape

        # Storage for all analysis layers
        self.anomaly_maps = {}
        self.suspect_regions = []
        self.detailed_metrics = {}

    def analyze_ela_detailed(self, quality: int = 95) -> Dict:
        """
        Deep ELA analysis with granular tile inspection.

        Returns heatmap showing compression artifact intensity per region.
        """
        from PIL import Image, ImageChops
        import uuid

        # Generate ELA
        pil_img = Image.open(self.image_path).convert('RGB')
        temp_path = f'temp_ela_{uuid.uuid4()}.jpg'
        pil_img.save(temp_path, 'JPEG', quality=quality)
        compressed = Image.open(temp_path)

        ela_img = ImageChops.difference(pil_img, compressed)
        ela_cv = cv2.cvtColor(np.array(ela_img), cv2.COLOR_RGB2GRAY)

        os.remove(temp_path)

        # Create high-resolution anomaly map (32x32 grid)
        grid_size = 32
        tile_h, tile_w = self.h // grid_size, self.w // grid_size

        anomaly_map = np.zeros((grid_size, grid_size), dtype=np.float32)

        for r in range(grid_size):
            for c in range(grid_size):
                y1, y2 = r * tile_h, (r + 1) * tile_h
                x1, x2 = c * tile_w, (c + 1) * tile_w

                tile = ela_cv[y1:y2, x1:x2]

                # Calculate multiple metrics
                mean_intensity = float(np.mean(tile))
                std_intensity = float(np.std(tile))
                max_intensity = float(np.max(tile))

                # Composite anomaly score
                anomaly_score = (mean_intensity * 0.4 + std_intensity * 0.4 + max_intensity * 0.2)
                anomaly_map[r, c] = anomaly_score

        # Identify high-anomaly tiles
        threshold = np.percentile(anomaly_map, 85)  # Top 15%
        high_anomaly_tiles = []

        for r in range(grid_size):
            for c in range(grid_size):
                if anomaly_map[r, c] >= threshold:
                    y1, y2 = r * tile_h, (r + 1) * tile_h
                    x1, x2 = c * tile_w, (c + 1) * tile_w
                    high_anomaly_tiles.append({
                        'x': x1, 'y': y1, 'w': x2 - x1, 'h': y2 - y1,
                        'score': float(anomaly_map[r, c]),
                        'percentile': float((anomaly_map[r, c] / np.max(anomaly_map)) * 100)
                    })

        # Generate visualization
        heatmap_vis = cv2.resize(anomaly_map, (self.w, self.h))
        heatmap_vis = np.uint8(255 * (heatmap_vis / np.max(heatmap_vis)))
        heatmap_colored = cv2.applyColorMap(heatmap_vis, cv2.COLORMAP_JET)

        self.anomaly_maps['ela_detailed'] = heatmap_colored

        return {
            'high_anomaly_regions': high_anomaly_tiles,
            'mean_anomaly': float(np.mean(anomaly_map)),
            'max_anomaly': float(np.max(anomaly_map)),
            'anomaly_map': anomaly_map.tolist()
        }

    def analyze_noise_consistency(self) -> Dict:
        """
        Analyzes noise pattern consistency across image.

        Edited regions often have different noise characteristics
        than authentic camera noise.
        """
        # High-pass filter to extract noise
        kernel = np.array([[-1, -1, -1],
                          [-1,  8, -1],
                          [-1, -1, -1]], dtype=np.float32)

        noise = cv2.filter2D(self.gray.astype(np.float32), -1, kernel)
        noise = np.abs(noise).astype(np.uint8)

        # Divide into regions and analyze noise variance
        grid_size = 16
        tile_h, tile_w = self.h // grid_size, self.w // grid_size

        noise_variances = []
        noise_map = np.zeros((grid_size, grid_size), dtype=np.float32)

        for r in range(grid_size):
            for c in range(grid_size):
                y1, y2 = r * tile_h, (r + 1) * tile_h
                x1, x2 = c * tile_w, (c + 1) * tile_w

                tile_noise = noise[y1:y2, x1:x2]
                variance = float(np.var(tile_noise))
                noise_variances.append(variance)
                noise_map[r, c] = variance

        # Identify outliers (inconsistent noise)
        mean_var = np.mean(noise_variances)
        std_var = np.std(noise_variances)

        inconsistent_regions = []
        for r in range(grid_size):
            for c in range(grid_size):
                z_score = (noise_map[r, c] - mean_var) / (std_var + 1e-5)

                if abs(z_score) >= 2.0:  # Outlier
                    y1, y2 = r * tile_h, (r + 1) * tile_h
                    x1, x2 = c * tile_w, (c + 1) * tile_w
                    inconsistent_regions.append({
                        'x': x1, 'y': y1, 'w': x2 - x1, 'h': y2 - y1,
                        'z_score': float(z_score),
                        'variance': float(noise_map[r, c]),
                        'type': 'low_noise' if z_score < 0 else 'high_noise'
                    })

        # Visualization
        noise_vis = cv2.resize(noise_map, (self.w, self.h))
        noise_vis = np.uint8(255 * (noise_vis / np.max(noise_vis)))
        noise_colored = cv2.applyColorMap(noise_vis, cv2.COLORMAP_VIRIDIS)

        self.anomaly_maps['noise_consistency'] = noise_colored

        return {
            'inconsistent_regions': inconsistent_regions,
            'noise_uniformity_score': float(1.0 - (std_var / (mean_var + 1e-5))),
            'total_outliers': len(inconsistent_regions)
        }

    def analyze_edge_consistency(self) -> Dict:
        """
        Analyzes edge sharpness and anti-aliasing consistency.

        Pasted text often has different edge characteristics.
        """
        # Multi-scale edge detection
        edges_fine = cv2.Canny(self.gray, 50, 150)
        edges_coarse = cv2.Canny(self.gray, 100, 200)

        # Analyze edge density in grid
        grid_size = 20
        tile_h, tile_w = self.h // grid_size, self.w // grid_size

        edge_density_map = np.zeros((grid_size, grid_size), dtype=np.float32)
        edge_sharpness_map = np.zeros((grid_size, grid_size), dtype=np.float32)

        for r in range(grid_size):
            for c in range(grid_size):
                y1, y2 = r * tile_h, (r + 1) * tile_h
                x1, x2 = c * tile_w, (c + 1) * tile_w

                tile_fine = edges_fine[y1:y2, x1:x2]
                tile_coarse = edges_coarse[y1:y2, x1:x2]

                density = float(np.sum(tile_fine > 0) / tile_fine.size)
                sharpness = float(np.sum(tile_coarse > 0) / (np.sum(tile_fine > 0) + 1e-5))

                edge_density_map[r, c] = density
                edge_sharpness_map[r, c] = sharpness

        # Find regions with unusual edge characteristics
        mean_density = np.mean(edge_density_map)
        std_density = np.std(edge_density_map)

        unusual_edge_regions = []
        for r in range(grid_size):
            for c in range(grid_size):
                density_z = (edge_density_map[r, c] - mean_density) / (std_density + 1e-5)

                if abs(density_z) >= 2.2:
                    y1, y2 = r * tile_h, (r + 1) * tile_h
                    x1, x2 = c * tile_w, (c + 1) * tile_w
                    unusual_edge_regions.append({
                        'x': x1, 'y': y1, 'w': x2 - x1, 'h': y2 - y1,
                        'density': float(edge_density_map[r, c]),
                        'sharpness': float(edge_sharpness_map[r, c]),
                        'z_score': float(density_z)
                    })

        # Visualization
        edge_vis = cv2.resize(edge_density_map, (self.w, self.h))
        edge_vis = np.uint8(255 * (edge_vis / np.max(edge_vis)))
        edge_colored = cv2.applyColorMap(edge_vis, cv2.COLORMAP_HOT)

        self.anomaly_maps['edge_consistency'] = edge_colored

        return {
            'unusual_edge_regions': unusual_edge_regions,
            'edge_uniformity_score': float(1.0 - (std_density / (mean_density + 1e-5))),
            'total_edge_outliers': len(unusual_edge_regions)
        }

    def analyze_color_consistency(self) -> Dict:
        """
        Analyzes color distribution consistency.

        Whiteout edits create unnaturally uniform white regions.
        """
        # Convert to LAB color space
        lab = cv2.cvtColor(self.image, cv2.COLOR_BGR2LAB)
        l_channel, a_channel, b_channel = cv2.split(lab)

        # Find uniform regions (low variance)
        grid_size = 24
        tile_h, tile_w = self.h // grid_size, self.w // grid_size

        uniformity_map = np.zeros((grid_size, grid_size), dtype=np.float32)
        brightness_map = np.zeros((grid_size, grid_size), dtype=np.float32)

        for r in range(grid_size):
            for c in range(grid_size):
                y1, y2 = r * tile_h, (r + 1) * tile_h
                x1, x2 = c * tile_w, (c + 1) * tile_w

                tile_l = l_channel[y1:y2, x1:x2]

                mean_l = float(np.mean(tile_l))
                std_l = float(np.std(tile_l))

                uniformity_map[r, c] = std_l
                brightness_map[r, c] = mean_l

        # Find suspicious uniform white regions
        suspicious_regions = []
        for r in range(grid_size):
            for c in range(grid_size):
                # Very uniform (std < 12) AND very bright (L > 240)
                if uniformity_map[r, c] < 12.0 and brightness_map[r, c] > 240.0:
                    y1, y2 = r * tile_h, (r + 1) * tile_h
                    x1, x2 = c * tile_w, (c + 1) * tile_w
                    suspicious_regions.append({
                        'x': x1, 'y': y1, 'w': x2 - x1, 'h': y2 - y1,
                        'uniformity': float(uniformity_map[r, c]),
                        'brightness': float(brightness_map[r, c]),
                        'suspicion': 'whiteout_candidate'
                    })

        # Visualization
        uniformity_vis = cv2.resize(uniformity_map, (self.w, self.h))
        uniformity_vis = np.uint8(255 * (uniformity_vis / np.max(uniformity_vis)))
        uniformity_colored = cv2.applyColorMap(255 - uniformity_vis, cv2.COLORMAP_PLASMA)

        self.anomaly_maps['color_consistency'] = uniformity_colored

        return {
            'suspicious_uniform_regions': suspicious_regions,
            'total_whiteout_candidates': len(suspicious_regions),
            'overall_color_uniformity': float(np.mean(uniformity_map))
        }

    def generate_composite_heatmap(self, layers: List[str] = None) -> np.ndarray:
        """
        Generate multi-layer composite heatmap showing all detections.

        Args:
            layers: List of layer names to combine. If None, uses all.

        Returns:
            Composite heatmap image
        """
        if layers is None:
            layers = list(self.anomaly_maps.keys())

        if not layers:
            return self.image.copy()

        # Start with original image
        composite = self.image.copy().astype(np.float32)

        # Blend all layers
        for layer_name in layers:
            if layer_name in self.anomaly_maps:
                layer = self.anomaly_maps[layer_name].astype(np.float32)
                composite = cv2.addWeighted(composite, 0.6, layer, 0.4, 0)

        return composite.astype(np.uint8)

    def generate_detailed_report(self) -> Dict:
        """
        Generate comprehensive forensic report with all analysis results.

        Returns complete diagnostic information for admin review.
        """
        # Run all analyses
        print("[DEEP_ANALYSIS] Running detailed ELA analysis...")
        ela_results = self.analyze_ela_detailed()

        print("[DEEP_ANALYSIS] Running noise consistency analysis...")
        noise_results = self.analyze_noise_consistency()

        print("[DEEP_ANALYSIS] Running edge consistency analysis...")
        edge_results = self.analyze_edge_consistency()

        print("[DEEP_ANALYSIS] Running color consistency analysis...")
        color_results = self.analyze_color_consistency()

        # Aggregate all suspect regions
        all_regions = []

        for region in ela_results['high_anomaly_regions']:
            all_regions.append({**region, 'detector': 'ELA', 'severity': 'high' if region['percentile'] > 90 else 'medium'})

        for region in noise_results['inconsistent_regions']:
            all_regions.append({**region, 'detector': 'Noise', 'severity': 'high' if abs(region['z_score']) > 3.0 else 'medium'})

        for region in edge_results['unusual_edge_regions']:
            all_regions.append({**region, 'detector': 'Edge', 'severity': 'medium'})

        for region in color_results['suspicious_uniform_regions']:
            all_regions.append({**region, 'detector': 'Color', 'severity': 'high'})

        # Cluster overlapping regions
        clustered_regions = self._cluster_regions(all_regions)

        # Generate composite visualizations
        composite_all = self.generate_composite_heatmap()
        composite_high = self.generate_composite_heatmap(['ela_detailed', 'color_consistency'])

        # Generate annotated image with bounding boxes
        annotated = self.image.copy()
        for cluster in clustered_regions:
            x, y, w, h = cluster['x'], cluster['y'], cluster['w'], cluster['h']
            severity = cluster['severity']

            color = (0, 0, 255) if severity == 'high' else (0, 165, 255)  # Red for high, orange for medium
            cv2.rectangle(annotated, (x, y), (x + w, y + h), color, 3)

            # Add label
            label = f"{cluster['detector']} ({cluster['count']})"
            cv2.putText(annotated, label, (x, y - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

        # Encode images to base64
        _, buffer_composite = cv2.imencode('.png', composite_all)
        composite_base64 = base64.b64encode(buffer_composite).decode('utf-8')

        _, buffer_annotated = cv2.imencode('.png', annotated)
        annotated_base64 = base64.b64encode(buffer_annotated).decode('utf-8')

        return {
            'summary': {
                'total_suspect_regions': len(all_regions),
                'high_severity_regions': len([r for r in all_regions if r.get('severity') == 'high']),
                'clustered_regions': len(clustered_regions),
                'detectors_triggered': len(set(r['detector'] for r in all_regions))
            },
            'ela_analysis': ela_results,
            'noise_analysis': noise_results,
            'edge_analysis': edge_results,
            'color_analysis': color_results,
            'suspect_regions': clustered_regions,
            'visualizations': {
                'composite_heatmap_base64': composite_base64,
                'annotated_image_base64': annotated_base64,
                'individual_layers': {
                    name: base64.b64encode(cv2.imencode('.png', img)[1]).decode('utf-8')
                    for name, img in self.anomaly_maps.items()
                }
            }
        }

    def _cluster_regions(self, regions: List[Dict], distance_threshold: int = 50) -> List[Dict]:
        """
        Cluster overlapping or nearby suspect regions.

        Combines regions from different detectors that point to same edit.
        """
        if not regions:
            return []

        clusters = []

        for region in regions:
            merged = False
            for cluster in clusters:
                # Check if regions overlap or are close
                if self._regions_overlap(region, cluster, distance_threshold):
                    # Merge into existing cluster
                    cluster['x'] = min(cluster['x'], region['x'])
                    cluster['y'] = min(cluster['y'], region['y'])
                    cluster['w'] = max(cluster['x'] + cluster['w'], region['x'] + region['w']) - cluster['x']
                    cluster['h'] = max(cluster['y'] + cluster['h'], region['y'] + region['h']) - cluster['y']
                    cluster['detectors'].add(region['detector'])
                    cluster['count'] += 1
                    cluster['severity'] = 'high' if cluster['count'] >= 2 or cluster['severity'] == 'high' else 'medium'
                    merged = True
                    break

            if not merged:
                clusters.append({
                    'x': region['x'],
                    'y': region['y'],
                    'w': region['w'],
                    'h': region['h'],
                    'detectors': {region['detector']},
                    'detector': region['detector'],
                    'count': 1,
                    'severity': region.get('severity', 'medium')
                })

        # Convert sets to lists for JSON serialization
        for cluster in clusters:
            cluster['detectors'] = list(cluster['detectors'])

        return clusters

    def _regions_overlap(self, r1: Dict, r2: Dict, threshold: int) -> bool:
        """Check if two regions overlap or are within threshold distance."""
        x1_min, y1_min = r1['x'], r1['y']
        x1_max, y1_max = r1['x'] + r1['w'], r1['y'] + r1['h']

        x2_min, y2_min = r2['x'], r2['y']
        x2_max, y2_max = r2['x'] + r2['w'], r2['y'] + r2['h']

        # Check overlap with threshold padding
        overlap_x = (x1_min - threshold <= x2_max) and (x2_min - threshold <= x1_max)
        overlap_y = (y1_min - threshold <= y2_max) and (y2_min - threshold <= y1_max)

        return overlap_x and overlap_y
