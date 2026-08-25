<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>A.E.G.I.S. Document Forensics Audit Certificate</title>
    <style>
        @page { margin: 24px 30px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.45;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #00754A;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #00754A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9px;
            color: #64748b;
            font-weight: 500;
        }
        .audit-badge {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 10px;
            font-weight: bold;
            color: #334155;
            text-align: right;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .info-grid td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            width: 25%;
        }
        .info-value {
            color: #0f172a;
            font-weight: 500;
        }
        .score-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
            margin-bottom: 14px;
        }
        .score-number {
            font-size: 26px;
            font-weight: bold;
            font-family: monospace;
        }
        .pillar-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
        }
        .pillar-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }
        .pillar-table td {
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .status-pill {
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
        }
        .status-verified { background: #dcfce7; color: #15803d; }
        .status-anomalous { background: #fee2e2; color: #b91c1c; }
        .status-neutral { background: #f1f5f9; color: #475569; }
        .footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="title">A.E.G.I.S. Forensic Audit Certificate</div>
                <div class="subtitle">Central Luzon State University • Office of Student Affairs Forensic AI Engine</div>
            </td>
            <td style="text-align: right;">
                <div class="audit-badge">AUDIT: {{ $auditId }}</div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 4px;">{{ $generatedAt }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Applicant & Record Summary</div>
    <table class="info-grid">
        <tr>
            <td class="info-label">Application Reference:</td>
            <td class="info-value"><strong>APP-{{ $application->id }}</strong></td>
            <td class="info-label">Scholarship Program:</td>
            <td class="info-value">{{ $application->program_name }}</td>
        </tr>
        <tr>
            <td class="info-label">Student Name:</td>
            <td class="info-value">{{ $application->user->name ?? 'Unknown' }}</td>
            <td class="info-label">CLSU Student ID:</td>
            <td class="info-value" style="font-family: monospace;">{{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Degree & Year:</td>
            <td class="info-value">{{ $application->user->profile?->course ?? 'N/A' }} — {{ $application->user->profile?->year_level ?? 'N/A' }}</td>
            <td class="info-label">Declared GWA:</td>
            <td class="info-value"><strong>{{ $application->gwa !== null ? number_format((float)$application->gwa, 2) : 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Target Document:</td>
            <td class="info-value">{{ $document->document_type }} ({{ $document->original_name }})</td>
            <td class="info-label">Current Status:</td>
            <td class="info-value"><strong>{{ $application->status }}</strong></td>
        </tr>
    </table>

    <div class="section-title">2. AI Forensic Multi-Spectrum Evaluation</div>
    @php
        $fraud = $aiResult->fraud_probability;
        $color = $fraud >= 70 ? '#dc2626' : ($fraud >= 35 ? '#d97706' : '#16a34a');
        $label = $fraud >= 70 ? 'HIGH TAMPERING RISK' : ($fraud >= 35 ? 'REVIEW RECOMMENDED' : 'AUTHENTIC / LOW RISK');
    @endphp

    <div class="score-box">
        <div style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold;">Evaluated Fraud Probability</div>
        <div class="score-number" style="color: {{ $color }};">{{ number_format($fraud, 1) }}%</div>
        <div style="font-weight: bold; font-size: 11px; color: {{ $color }}; margin-top: 2px;">{{ $label }}</div>
    </div>

    <div class="section-title">3. Explainable 4-Pillar Evidence Matrix</div>
    <table class="pillar-table">
        <thead>
            <tr>
                <th style="width: 30%;">Forensic Pillar</th>
                <th style="width: 15%;">Model Weight</th>
                <th style="width: 25%;">Evaluation Status</th>
                <th style="width: 30%;">Diagnostic Signal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Text & Grade OCR Consistency</strong></td>
                <td>35%</td>
                <td>
                    @php
                        $gwaMismatch = false;
                        $extracted = $aiResult->deep_analysis_report['extracted_gwa'] ?? null;
                        if ($extracted !== null && !empty($application->gwa)) {
                            $gwaMismatch = abs((float)$application->gwa - (float)$extracted) > 0.01;
                        }
                    @endphp
                    @if($extracted === null)
                        <span class="status-pill status-neutral">No OCR Data</span>
                    @elseif($gwaMismatch)
                        <span class="status-pill status-anomalous">Mismatch ({{ $extracted }})</span>
                    @else
                        <span class="status-pill status-verified">Verified ({{ $extracted }})</span>
                    @endif
                </td>
                <td>Cross-referenced student declared GWA with scanned grade sheet cells.</td>
            </tr>
            <tr>
                <td><strong>2. Pixel Compression & Frequency</strong></td>
                <td>25%</td>
                <td>
                    @if(in_array('resampling_traces_detected', $aiResult->anomaly_indicators ?? []) || in_array('catnet_dct_compression_anomaly', $aiResult->anomaly_indicators ?? []))
                        <span class="status-pill status-anomalous">Resampled Traces</span>
                    @else
                        <span class="status-pill status-verified">Uniform Compression</span>
                    @endif
                </td>
                <td>Error Level Analysis (ELA) and CAT-Net Discrete Cosine Transform.</td>
            </tr>
            <tr>
                <td><strong>3. Sensor Continuity & Clone Stamp</strong></td>
                <td>25%</td>
                <td>
                    @if(in_array('trufor_noiseprint_anomaly', $aiResult->anomaly_indicators ?? []) || in_array('clone_stamp_detected', $aiResult->anomaly_indicators ?? []))
                        <span class="status-pill status-anomalous">Noise Discontinuity</span>
                    @else
                        <span class="status-pill status-verified">Natural Sensor Grain</span>
                    @endif
                </td>
                <td>TruFor Noiseprint sensor fingerprint & SIFT block-matching.</td>
            </tr>
            <tr>
                <td><strong>4. Metadata & File Provenance</strong></td>
                <td>15%</td>
                <td>
                    @if(!empty($aiResult->detected_software))
                        <span class="status-pill status-anomalous">{{ $aiResult->detected_software }}</span>
                    @else
                        <span class="status-pill status-verified">Clean Provenance</span>
                    @endif
                </td>
                <td>EXIF software headers and timestamp integrity checks.</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">4. Institutional Verification & Evaluator Remarks</div>
    <table class="info-grid" style="margin-top: 8px;">
        <tr>
            <td class="info-label">Evaluator Remarks:</td>
            <td class="info-value" colspan="3">{{ $application->remarks ?: 'No additional manual evaluator remarks logged.' }}</td>
        </tr>
        <tr>
            <td class="info-label">Verified By:</td>
            <td class="info-value">{{ $verifier }}</td>
            <td class="info-label">Audit Timestamp:</td>
            <td class="info-value">{{ $generatedAt }}</td>
        </tr>
    </table>

    <div class="footer">
        This document is an automated forensic verification certificate generated by the A.E.G.I.S. Decision Support System.<br>
        Central Luzon State University • Office of Student Affairs • All cryptographic evidence hashes recorded in immutable system ledger.
    </div>

</body>
</html>
