# A.E.G.I.S. Ground Truth Testing Fixtures

This directory contains real sample Certificate of Grades (COG) documents generated from the university dataset for evaluating the multi-spectrum digital forensics engine (TruFor & ResNet-50 ELA/DCT pipeline).

## Available Test Documents:

1. **`authentic_clsu_cog.jpg`**
   - **Type:** Ground Truth Authentic Certificate of Grades
   - **Header:** Central Luzon State University - Certificate of Grades
   - **Declared GWA:** 2.75
   - **Forensic Characteristics:** Consistent pixel noise floor, uniform discrete cosine transform (DCT) quantisation table, unaltered course grade text blocks.
   - **Expected Result:** Authentic verdict (< 35% tampering probability), Low Risk.

2. **`tampered_clsu_cog.jpg`**
   - **Type:** Ground Truth Tampered / Manipulated Certificate of Grades
   - **Header:** Central Luzon State University - Certificate of Grades
   - **Manipulated GWA:** Edited from 2.75 to 1.00 (Latin Honors forgery)
   - **Forensic Characteristics:** Discontinuous error level analysis (ELA) high-frequency residuals around modified grade numbers, localized copy-move and splicing artifacts detected by TruFor dense feature extraction.
   - **Expected Result:** High Tampering Risk (> 70% tampering probability), High Risk verdict.
