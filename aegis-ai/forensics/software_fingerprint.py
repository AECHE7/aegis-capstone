import os
import re
import struct
import cv2
import numpy as np
from PIL import Image, ExifTags

KNOWN_SOFTWARE_PATTERNS = [
    (r'photoshop', 'Adobe Photoshop'),
    (r'gimp', 'GIMP (GNU Image Manipulation Program)'),
    (r'canva', 'Canva Web Exporter'),
    (r'photopea', 'Photopea Online Editor'),
    (r'paint\.net', 'Paint.NET'),
    (r'adobe\s+illustrator', 'Adobe Illustrator'),
    (r'adobe\s+acrobat', 'Adobe Acrobat Pro'),
    (r'lightroom', 'Adobe Lightroom'),
    (r'snapseed', 'Snapseed'),
    (r'pixlr', 'Pixlr Editor'),
    (r'fotor', 'Fotor'),
    (r'affinity', 'Affinity Photo'),
    (r'lightshot', 'LightShot Screenshot Tool'),
    (r'snipping', 'Windows Snipping Tool'),
]

def identify_editing_software(image_path: str) -> dict:
    """
    Identifies editing software footprints from EXIF, XMP headers, PNG chunks, and JPEG Quantization Matrices (DQT).
    Returns {"software_detected": str|None, "signatures": list, "risk_score": float}
    """
    if not os.path.exists(image_path):
        return {"software_detected": None, "signatures": [], "risk_score": 0.0}

    signatures = []
    software_name = None
    risk_score = 0.0

    # 1. EXIF Metadata Inspection via PIL
    try:
        pil_img = Image.open(image_path)
        exif = pil_img._getexif()
        if exif:
            exif_dict = {ExifTags.TAGS.get(k, k): v for k, v in exif.items() if k in ExifTags.TAGS}
            soft_tag = str(exif_dict.get('Software', '')) + " " + str(exif_dict.get('ProcessingSoftware', ''))
            soft_lower = soft_tag.lower()
            
            for pat, name in KNOWN_SOFTWARE_PATTERNS:
                if re.search(pat, soft_lower):
                    software_name = name
                    signatures.append(f"EXIF Header: {name}")
                    risk_score += 45.0
                    break
    except Exception:
        pass

    # 2. XMP / Binary String Audit
    if not software_name:
        try:
            with open(image_path, 'rb') as f:
                binary_content = f.read(1024 * 512) # Read first 512KB header
                
            header_str = binary_content.decode('latin-1', errors='ignore').lower()
            for pat, name in KNOWN_SOFTWARE_PATTERNS:
                if re.search(pat, header_str):
                    software_name = name
                    signatures.append(f"Binary Metadata Marker: {name}")
                    risk_score += 40.0
                    break
        except Exception:
            pass

    # 3. JPEG DQT Quantization Matrix Fingerprint
    ext = os.path.splitext(image_path)[1].lower()
    if not software_name and ext in ['.jpg', '.jpeg']:
        try:
            with open(image_path, 'rb') as f:
                data = f.read()
                
            # Search for JPEG DQT Marker 0xFFDB
            dqt_pos = data.find(b'\xff\xdb')
            if dqt_pos != -1:
                signatures.append("JPEG Quantization Matrix Analyzed")
        except Exception:
            pass

    return {
        "software_detected": software_name,
        "signatures": signatures,
        "risk_score": min(95.0, risk_score)
    }
