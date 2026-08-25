"""
Document Syntax & Academic Structure Pre-Forensic Gate.
Determines if an uploaded image exhibits characteristics of an academic document
(tabular layout, text density, university headers) versus a non-document graphic.
"""

import cv2
import numpy as np


def evaluate_document_syntax(image_path: str, extracted_text: str = "") -> dict:
    """
    Evaluates syntactic validity and layout structure of a document image.

    Returns:
        {
            "is_valid_academic_document": bool,
            "document_category": str,
            "syntax_confidence": float,
            "has_tabular_structure": bool,
            "has_academic_keywords": bool,
            "text_line_count": int,
            "diagnostic_message": str
        }
    """
    img = cv2.imread(image_path)
    if img is None:
        return {
            "is_valid_academic_document": False,
            "document_category": "Corrupted / Unreadable",
            "syntax_confidence": 0.0,
            "has_tabular_structure": False,
            "has_academic_keywords": False,
            "text_line_count": 0,
            "diagnostic_message": "Image file could not be decoded."
        }

    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    h, w = gray.shape

    # 1. Edge & Line Density Analysis (Detect document grid / table borders)
    edges = cv2.Canny(gray, 50, 150, apertureSize=3)
    lines = cv2.HoughLinesP(edges, 1, np.pi / 180, threshold=100, minLineLength=int(w * 0.15), maxLineGap=10)
    has_tabular_structure = lines is not None and len(lines) >= 4

    # 2. Textual Keyword Analysis
    text_lower = (extracted_text or "").lower()
    academic_keywords = [
        "grade", "unit", "gwa", "semester", "academic", "course", "subject",
        "clsu", "university", "college", "student", "registrar", "form",
        "curriculum", "passed", "instructor", "section", "enrollment"
    ]
    matched_keywords = [kw for kw in academic_keywords if kw in text_lower]
    has_academic_keywords = len(matched_keywords) >= 2

    # 3. Text Region Distribution (Morphological text line estimation)
    kernel = cv2.getStructuringElement(cv2.MORPH_RECT, (25, 3))
    dilated = cv2.dilate(edges, kernel, iterations=1)
    contours, _ = cv2.findContours(dilated, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    
    text_regions = 0
    for c in contours:
        x, y, cw, ch = cv2.boundingRect(c)
        aspect = cw / max(1, ch)
        if cw > w * 0.08 and aspect > 2.0:
            text_regions += 1

    is_valid = has_academic_keywords or (has_tabular_structure and text_regions >= 5) or text_regions >= 10

    if is_valid:
        category = "Official Academic Record / Document"
        msg = f"Verified academic layout ({len(matched_keywords)} keywords, {text_regions} text blocks)."
        conf = 0.90
    else:
        category = "Non-Academic Graphic / Unrecognized Asset"
        msg = "Uploaded file lacks standard academic document structure (no course tables or grade records found)."
        conf = 0.35

    return {
        "is_valid_academic_document": is_valid,
        "document_category": category,
        "syntax_confidence": conf,
        "has_tabular_structure": has_tabular_structure,
        "has_academic_keywords": has_academic_keywords,
        "text_line_count": text_regions,
        "diagnostic_message": msg
    }
