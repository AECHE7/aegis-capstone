"""Error Level Analysis (ELA) preprocessing for image forgery detection."""

from __future__ import annotations

import os
import uuid

from PIL import Image, ImageChops, ImageEnhance


def generate_ela(img_path: str, output_path: str, quality: int = 95) -> str:
    """
    Stage 1: Error Level Analysis preprocessing.

    Re-compresses the image as JPEG and amplifies pixel-level differences
    that often appear after localized digital edits.
    """
    original = Image.open(img_path).convert("RGB")
    temp_filename = f"temp_{uuid.uuid4()}.jpg"
    try:
        original.save(temp_filename, "JPEG", quality=quality)
        compressed = Image.open(temp_filename)

        ela_image = ImageChops.difference(original, compressed)
        extrema = ela_image.getextrema()
        max_diff = max(ex[1] for ex in extrema) if extrema else 1
        if max_diff == 0:
            max_diff = 1

        scale = 255.0 / max_diff
        ela_image = ImageEnhance.Brightness(ela_image).enhance(scale)
        ela_image.save(output_path)
    finally:
        if os.path.exists(temp_filename):
            os.remove(temp_filename)

    return output_path


def ela_mean_energy(ela_path: str) -> float:
    """Return mean ELA intensity (0-255). Higher values suggest more edits."""
    try:
        img = Image.open(ela_path).convert("L")
        pixels = list(img.getdata())
        if not pixels:
            return 0.0
        return float(sum(pixels) / len(pixels))
    except Exception:
        return 0.0
