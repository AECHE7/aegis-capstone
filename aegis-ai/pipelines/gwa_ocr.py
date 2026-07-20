import re
import os
from PIL import Image

try:
    import pytesseract
    PYTESSERACT_AVAILABLE = True
except ImportError:
    PYTESSERACT_AVAILABLE = False

# Common regex patterns for extracting GWA from text or OCR outputs
GWA_PATTERNS = [
    r'GWA\s*[:\-=]?\s*([1-5]\.[0-9]{1,2})',
    r'GENERAL\s+WEIGHTED\s+AVERAGE\s*[:\-=]?\s*([1-5]\.[0-9]{1,2})',
    r'WEIGHTED\s+AVERAGE\s*[:\-=]?\s*([1-5]\.[0-9]{1,2})',
    r'GRADE\s+POINT\s+AVERAGE\s*[:\-=]?\s*([1-5]\.[0-9]{1,2})',
    r'GPA\s*[:\-=]?\s*([1-5]\.[0-9]{1,2})',
]

def extract_gwa_from_text(text: str) -> float | None:
    """Extract a valid GWA float from raw text string using regular expressions."""
    if not text:
        return None
    
    text_upper = text.upper()
    for pattern in GWA_PATTERNS:
        match = re.search(pattern, text_upper)
        if match:
            try:
                val = float(match.group(1))
                if 1.00 <= val <= 5.00:
                    return val
            except ValueError:
                continue
    return None

def extract_gwa_from_image(image_path: str) -> float | None:
    """Extract GWA from an image file using Tesseract OCR if available."""
    if not PYTESSERACT_AVAILABLE:
        return None
    
    try:
        img = Image.open(image_path).convert('L')
        # Perform Tesseract OCR
        ocr_text = pytesseract.image_to_string(img)
        return extract_gwa_from_text(ocr_text)
    except Exception as e:
        print(f"[GWA_OCR] Image OCR extraction failed: {e}")
        return None
