import os
import unittest
from PIL import Image

from pipelines.gwa_ocr import extract_gwa_from_text
from forensics.pdf_signals import analyze_pdf_structure
from pipelines.image_forensics import run_image_pipeline
from app import app


class TestAegisForensicsPipelines(unittest.TestCase):

    def setUp(self):
        self.app = app.test_client()
        self.app.testing = True

    def test_gwa_regex_extraction(self):
        sample_text_1 = "OFFICIAL REPORT - GWA: 1.75 - PASSED"
        self.assertEqual(extract_gwa_from_text(sample_text_1), 1.75)

        sample_text_2 = "GENERAL WEIGHTED AVERAGE: 1.25"
        self.assertEqual(extract_gwa_from_text(sample_text_2), 1.25)

        sample_text_3 = "NO GWA HERE - 99.00"
        self.assertIsNone(extract_gwa_from_text(sample_text_3))

    def test_health_endpoint(self):
        response = self.app.get('/health')
        self.assertEqual(response.status_code, 200)
        data = response.get_json()
        self.assertIn("model_loaded", data)
        self.assertIn("tensorflow_available", data)
        self.assertIn("pikepdf_available", data)

    def test_image_pipeline_simulation_mode(self):
        temp_img_path = "temp_test_img.jpg"
        temp_ela_path = "temp_test_ela.jpg"
        temp_heatmap_path = "temp_test_heatmap.jpg"

        img = Image.new('RGB', (224, 224), color=(255, 255, 255))
        img.save(temp_img_path)

        res = run_image_pipeline(
            original_path=temp_img_path,
            ela_path=temp_ela_path,
            heatmap_path=temp_heatmap_path,
            model=None,
            allow_simulation=True
        )

        self.assertEqual(res["status"], "success")
        self.assertIn(res["classification"], ["Authentic", "Tampered"])
        self.assertIn("fraud_probability", res)

        for p in [temp_img_path, temp_ela_path, temp_heatmap_path]:
            if os.path.exists(p):
                os.remove(p)


if __name__ == '__main__':
    unittest.main()
