import os
from datetime import datetime, timezone

try:
    import pikepdf
    PIKEPDF_AVAILABLE = True
except ImportError:
    PIKEPDF_AVAILABLE = False

SUSPICIOUS_PRODUCERS = [
    'photoshop', 'gimp', 'photopea', 'canva', 'libreoffice',
    'pdf2image', 'pdf-editor', 'foxit phantom', 'pdfscape', 'ilovepdf'
]

def analyze_pdf_structure(pdf_path: str) -> dict:
    """
    Performs structural and metadata forensic analysis on a PDF document.
    Returns a dictionary containing risk_score (0-100), anomaly_indicators (list), and pdf_report (dict).
    """
    indicators = []
    structural_risk = 0.0
    report = {
        "producer": "Unknown",
        "creator": "Unknown",
        "has_incremental_updates": False,
        "has_javascript": False,
        "has_attachments": False,
        "page_count": 0,
    }

    if not PIKEPDF_AVAILABLE:
        # Graceful fallback using pypdf if pikepdf is not installed
        try:
            import pypdf
            reader = pypdf.PdfReader(pdf_path)
            report["page_count"] = len(reader.pages)
            meta = reader.metadata or {}
            report["producer"] = str(meta.get('/Producer', ''))
            report["creator"] = str(meta.get('/Creator', ''))

            prod_lower = report["producer"].lower()
            if any(s in prod_lower for s in SUSPICIOUS_PRODUCERS):
                indicators.append("pdf_producer_image_editor")
                structural_risk += 30.0
        except Exception as e:
            print(f"[PDF_SIGNALS] Fallback analysis failed: {e}")
        
        return {
            "risk_score": min(100.0, structural_risk),
            "indicators": indicators,
            "report": report
        }

    # Full structural analysis using pikepdf
    try:
        pdf = pikepdf.open(pdf_path)
        report["page_count"] = len(pdf.pages)

        # 1. Producer / Creator inspection
        docinfo = pdf.docinfo
        producer = str(docinfo.get('/Producer', '')).strip()
        creator = str(docinfo.get('/Creator', '')).strip()
        report["producer"] = producer
        report["creator"] = creator

        prod_lower = producer.lower() + " " + creator.lower()
        if any(s in prod_lower for s in SUSPICIOUS_PRODUCERS):
            indicators.append("pdf_producer_image_editor")
            structural_risk += 35.0

        # 2. Check for Incremental Updates (Multiple Save Revisions)
        # pikepdf allows checking if the file has multiple revisions
        try:
            with open(pdf_path, 'rb') as f:
                content = f.read()
                # Count EOF markers - multiple %%EOF indicates incremental updates / edits
                eof_count = content.count(b'%%EOF')
                if eof_count > 1:
                    report["has_incremental_updates"] = True
                    indicators.append("pdf_incremental_update")
                    structural_risk += 25.0
        except Exception:
            pass

        # 3. Check for embedded JavaScript & Attachments
        if '/JS' in pdf.Root or '/JavaScript' in pdf.Root:
            report["has_javascript"] = True
            indicators.append("pdf_contains_javascript")
            structural_risk += 30.0

        if '/EmbeddedFiles' in pdf.Root or '/Names' in pdf.Root and '/EmbeddedFiles' in pdf.Root.Names:
            report["has_attachments"] = True
            indicators.append("pdf_contains_attachments")
            structural_risk += 20.0

        # 4. Check CreationDate vs ModDate gap
        creation_date_str = str(docinfo.get('/CreationDate', ''))
        mod_date_str = str(docinfo.get('/ModDate', ''))

        if creation_date_str and mod_date_str and creation_date_str != mod_date_str:
            # Significant modification after creation
            indicators.append("create_modify_date_mismatch")
            structural_risk += 15.0

        pdf.close()

    except Exception as e:
        print(f"[PDF_SIGNALS] pikepdf analysis error: {e}")
        indicators.append("pdf_structure_parse_warning")
        structural_risk += 10.0

    return {
        "risk_score": min(100.0, structural_risk),
        "indicators": indicators,
        "report": report
    }
