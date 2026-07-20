"""Grad-CAM heatmap generation for ResNet-50 fraud classifier."""

from __future__ import annotations

import cv2
import numpy as np

try:
    import tensorflow as tf

    TENSORFLOW_AVAILABLE = True
except ImportError:
    TENSORFLOW_AVAILABLE = False


LAST_CONV_LAYER = "conv5_block3_out"


def get_gradcam_heatmap(img_array, model, last_conv_layer_name: str = LAST_CONV_LAYER):
    """Generate a Grad-CAM heatmap highlighting regions driving the prediction."""
    if not TENSORFLOW_AVAILABLE:
        raise RuntimeError("TensorFlow is required for Grad-CAM")

    grad_model = tf.keras.models.Model(
        model.inputs,
        [model.get_layer(last_conv_layer_name).output, model.output],
    )

    with tf.GradientTape() as tape:
        last_conv_layer_output, preds = grad_model(img_array)
        class_channel = preds[:, 0]

    grads = tape.gradient(class_channel, last_conv_layer_output)
    pooled_grads = tf.reduce_mean(grads, axis=(0, 1, 2))

    last_conv_layer_output = last_conv_layer_output[0]
    heatmap = last_conv_layer_output @ pooled_grads[..., tf.newaxis]
    heatmap = tf.squeeze(heatmap)
    heatmap = tf.maximum(heatmap, 0)

    max_val = tf.math.reduce_max(heatmap)
    if float(max_val) > 0:
        heatmap = heatmap / max_val
    return heatmap.numpy()


def superimpose_heatmap(original_path: str, heatmap: np.ndarray, output_path: str) -> str:
    """Overlay a jet colormap Grad-CAM heatmap on the original image."""
    original_img = cv2.imread(original_path)
    if original_img is None:
        raise ValueError(f"Cannot read image for heatmap: {original_path}")

    heatmap_resized = cv2.resize(heatmap, (original_img.shape[1], original_img.shape[0]))
    heatmap_resized = np.uint8(255 * heatmap_resized)
    jet_heatmap = cv2.applyColorMap(heatmap_resized, cv2.COLORMAP_JET)
    superimposed = cv2.addWeighted(original_img, 0.6, jet_heatmap, 0.4, 0)
    cv2.imwrite(output_path, superimposed)
    return output_path


def write_simulated_heatmap(original_path: str, output_path: str, is_tampered: bool) -> str:
    """Dev-only fallback heatmap when the trained model is unavailable."""
    original_img = cv2.imread(original_path)
    if original_img is None:
        # Write a blank placeholder so callers still get a path
        blank = np.zeros((224, 224, 3), dtype=np.uint8)
        cv2.imwrite(output_path, blank)
        return output_path

    h, w, _ = original_img.shape
    overlay = original_img.copy()
    if is_tampered:
        cv2.circle(
            overlay,
            (int(w * 0.75), int(h * 0.85)),
            int(min(h, w) * 0.15),
            (0, 0, 255),
            -1,
        )
        cv2.addWeighted(overlay, 0.4, original_img, 0.6, 0, original_img)
    else:
        cv2.circle(
            overlay,
            (int(w / 2), int(h / 2)),
            int(min(h, w) * 0.1),
            (0, 255, 0),
            -1,
        )
        cv2.addWeighted(overlay, 0.1, original_img, 0.9, 0, original_img)

    cv2.imwrite(output_path, original_img)
    return output_path
