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

def validate_grade_table_math(text: str) -> dict:
    """
    Parses individual subject grades and units from grade report text,
    recalculating the exact mathematical Weighted Average.
    Returns: {"math_valid": bool, "calculated_gwa": float|None, "extracted_grades": list}
    """
    if not text:
        return {"math_valid": True, "calculated_gwa": None, "extracted_grades": []}

    lines = text.split('\n')
    extracted_grades = []
    total_units = 0.0
    weighted_sum = 0.0

    # Pattern for row entries e.g. "INTECH 3200 3 1.25 PASSED" or "ITEC 1100 3 1.50"
    row_pattern = re.compile(r'([A-Z]{3,6}\s*\d{3,4})\s+([1-6])\s+([1-5]\.[0-9]{1,2})', re.IGNORECASE)

    for line in lines:
        match = row_pattern.search(line)
        if match:
            try:
                subject = match.group(1).upper()
                units = float(match.group(2))
                grade = float(match.group(3))
                if 1.00 <= grade <= 5.00 and 1.0 <= units <= 6.0:
                    extracted_grades.append({"subject": subject, "units": units, "grade": grade})
                    weighted_sum += (units * grade)
                    total_units += units
            except ValueError:
                continue

    if total_units > 0 and len(extracted_grades) >= 2:
        calc_gwa = round(weighted_sum / total_units, 2)
        return {
            "math_valid": True,
            "calculated_gwa": calc_gwa,
            "total_units": total_units,
            "extracted_grades": extracted_grades
        }

    return {"math_valid": True, "calculated_gwa": None, "extracted_grades": []}


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
