<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>A.E.G.I.S. | Student Scholarship Application Form</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; padding: 10px; }
        .border-container { border: 2px solid #0f5934; padding: 20px; border-radius: 8px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f2a900; padding-bottom: 12px; }
        .header h3, .header h4, .header p { margin: 2px 0; }
        .text-green { color: #0f5934; font-weight: bold; }
        .form-title { font-size: 14px; font-weight: bold; margin-top: 10px; text-transform: uppercase; color: #0f5934; letter-spacing: 0.5px; }
        
        .section-title { font-size: 10px; font-weight: bold; color: #0f5934; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; margin-top: 15px; margin-bottom: 8px; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 4px 2px; vertical-align: top; }
        .info-label { font-weight: bold; color: #475569; width: 28%; }
        .info-value { color: #0f172a; border-bottom: 1px dashed #cbd5e1; }
        
        .clearance-box { background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; border-radius: 6px; margin: 15px 0; }
        .clearance-title { font-weight: bold; color: #15803d; margin-bottom: 3px; }
        .clearance-text { font-size: 10px; color: #166534; line-height: 1.3; }
        
        .instruction-box { background-color: #fefbeb; border: 1px solid #fef3c7; padding: 10px; border-radius: 6px; margin: 15px 0; font-size: 10px; color: #78350f; line-height: 1.3; }
        
        .signature-section { width: 100%; margin-top: 35px; }
        .signature-col { width: 45%; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #475569; text-align: center; padding-top: 4px; margin-top: 30px; font-size: 10px; }
    </style>
</head>
<body>

    <div class="border-container">
        <div class="header">
            <h4>Republic of the Philippines</h4>
            <h3 class="text-green">CENTRAL LUZON STATE UNIVERSITY</h3>
            <p>Science City of Muñoz, Nueva Ecija</p>
            <p style="font-size: 9px; color: #64748b;">Office of Student Affairs | Scholarship & Financial Assistance Division</p>
            <div class="form-title">Official Scholarship Application Form</div>
        </div>

        <div class="section-title">Application Details</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Application Ref ID:</td>
                <td class="info-value"><strong>APP-{{ $application->id }}</strong></td>
                <td class="info-label">Date Submitted:</td>
                <td class="info-value">{{ $application->created_at->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Scholarship Program:</td>
                <td class="info-value" colspan="3"><strong>{{ $application->program_name }}</strong></td>
            </tr>
        </table>

        <div class="section-title">Student Background Information</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Full Name:</td>
                <td class="info-value" colspan="3">{{ $application->user->name ?? 'Unknown' }}</td>
            </tr>
            <tr>
                <td class="info-label">CLSU ID Number:</td>
                <td class="info-value">{{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</td>
                <td class="info-label">Email Address:</td>
                <td class="info-value">{{ $application->user->email ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Degree Course:</td>
                <td class="info-value">{{ $application->user->profile?->course ?? 'N/A' }}</td>
                <td class="info-label">Year Level:</td>
                <td class="info-value">{{ $application->user->profile?->year_level ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">General Weighted Average:</td>
                <td class="info-value"><strong>{{ $application->gwa }}</strong></td>
                <td class="info-label">Verification Status:</td>
                <td class="info-value">Verified & Approved</td>
            </tr>
        </table>

        <div class="section-title">Forensic Audit & System Clearance</div>
        <div class="clearance-box">
            <div class="clearance-title">✔ A.E.G.I.S. Digital Forensics Cleared</div>
            <div class="clearance-text">
                The uploaded Certificate of Grades (COG) has been audited for structural anomalies.
                AI classification: <strong>{{ $application->document && $application->document->aiResult ? $application->document->aiResult->classification : 'Authentic' }}</strong> | 
                Tampering score: <strong>{{ $application->document && $application->document->aiResult ? $application->document->aiResult->fraud_probability : 0.00 }}%</strong> probability. 
                Remarks: {{ $application->remarks ?? 'Cleared and qualified for scholarship grant.' }}
            </div>
        </div>

        <div class="instruction-box">
            <strong>IMPORTANT INSTRUCTIONS:</strong> Please print a copy of this system-generated application form. Write your signature below and submit this document to the Office of Student Affairs (OSA) office together with your physical Certificate of Grades (COG) for final physical validation.
        </div>

        <table class="signature-section">
            <tr>
                <td class="signature-col">
                    <div class="signature-line">
                        <strong>{{ $application->user->name ?? 'Student Applicant' }}</strong><br>
                        Signature of Student
                    </div>
                </td>
                <td style="width: 10%;"></td>
                <td class="signature-col">
                    <div class="signature-line">
                        <strong>{{ $application->evaluator->name ?? 'OSA Evaluator' }}</strong><br>
                        Signature of OSA Officer
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
