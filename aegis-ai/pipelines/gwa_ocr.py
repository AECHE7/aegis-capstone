import re
import os
from PIL import Image

try:
    import pytesseract
    PYTESSERACT_AVAILABLE = True
except ImportError:
    PYTESSERACT_AVAILABLE = False

# ─────────────────────────────────────────────────────────────────────────────
# GWA Extraction Patterns — Philippine University Registrar Format Support
#
# Supports:
#   - Standard English: GWA, GPA, WEIGHTED AVERAGE, GENERAL WEIGHTED AVERAGE
#   - Philippine registrar formats: GEN. WT. AVE., G.W.A., SEM. AVERAGE,
#     TERM GWA, GENERAL WEIGHTED AVE.
#   - 3 decimal places (e.g. 1.750 → normalized to 1.75)
#   - Comma-decimal notation (e.g. 1,75 → treated as 1.75)
#   - Surrounding parentheses or brackets: (1.75) → 1.75
# ─────────────────────────────────────────────────────────────────────────────
GWA_PATTERNS = [
    # Standard full phrase
    r'GENERAL\s+WEIGHTED\s+AVERAGE\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    r'GENERAL\s+WEIGHTED\s+AVE\.?\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    # Philippine registrar abbreviations
    r'G\.?W\.?A\.?\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    r'GWA\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    # Semester / Term average labels
    r'SEM(?:ESTER)?\.?\s+AVE(?:RAGE)?\.?\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    r'TERM\s+(?:GWA|AVERAGE)\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    r'GEN\.?\s+WT\.?\s+AVE\.?\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    # General fallback phrases
    r'WEIGHTED\s+AVERAGE\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    r'GRADE\s+POINT\s+AVERAGE\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
    r'GPA\s*[:\-=]?\s*\(?([1-5][.,][0-9]{1,3})\)?',
]


def _normalize_gwa(raw: str) -> float | None:
    """
    Normalizes raw extracted GWA string to a 2-decimal float.
    Handles:
      - Comma as decimal separator: '1,75' → 1.75
      - 3 decimal places: '1.750' → 1.75 (rounds to 2)
      - Out-of-range values: returns None
    """
    try:
        normalized = raw.replace(',', '.')
        val = round(float(normalized), 2)
        if 1.00 <= val <= 5.00:
            return val
    except ValueError:
        pass
    return None


def extract_gwa_from_text(text: str) -> float | None:
    """Extract a valid GWA float from raw text string using regular expressions."""
    if not text:
        return None

    text_upper = text.upper()
    for pattern in GWA_PATTERNS:
        match = re.search(pattern, text_upper)
        if match:
            val = _normalize_gwa(match.group(1))
            if val is not None:
                return val
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
    # Supports 1-3 decimal places in grade cell
    row_pattern = re.compile(r'([A-Z]{3,6}\s*\d{3,4})\s+([1-6])\s+([1-5][.,][0-9]{1,3})', re.IGNORECASE)

    for line in lines:
        match = row_pattern.search(line)
        if match:
            try:
                subject = match.group(1).upper()
                units = float(match.group(2))
                grade = _normalize_gwa(match.group(3))
                if grade is not None and 1.0 <= units <= 6.0:
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
