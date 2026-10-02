<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CLSU | {{ $application->program_name ?? 'Scholarship Grant' }} Application & Evaluation Form</title>
    <style>
        @page {
            margin: 6px 12px;
            size: letter portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.2px;
            color: #0f172a;
            line-height: 1.12;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .title-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.2px solid #0f5934;
            margin-bottom: 2.5px;
            background-color: #f8fafc;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #0f5934;
            margin-bottom: 2.5px;
            background-color: #ffffff;
            page-break-inside: avoid;
        }
        .grid-table td, .grid-table th {
            border: 1px solid #0f5934;
            padding: 1.6px 3.5px;
            vertical-align: top;
        }
        .section-header {
            background-color: #0f5934;
            color: #ffffff;
            font-size: 6.8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 1.8px 5px;
            letter-spacing: 0.4px;
        }
        .label-text {
            font-size: 5.6px;
            color: #475569;
            font-weight: bold;
            display: block;
            text-transform: uppercase;
            margin-bottom: 0.5px;
        }
        .value-text {
            font-size: 7.2px;
            font-weight: bold;
            color: #0f172a;
            display: block;
        }
        .checkbox-box {
            display: inline-block;
            width: 7px;
            height: 7px;
            border: 1px solid #475569;
            text-align: center;
            line-height: 6px;
            font-size: 5.8px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            margin-right: 2px;
            vertical-align: middle;
            background-color: #ffffff;
        }
        .checkbox-checked {
            background-color: #0f5934;
            color: #ffffff;
            border-color: #0f5934;
        }
        .attestation-box {
            font-size: 6.2px;
            line-height: 1.15;
            text-align: justify;
            border: 1px solid #cbd5e1;
            padding: 2.5px 4px;
            background-color: #f8fafc;
            margin-bottom: 2.5px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2.5px;
            margin-bottom: 2px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-bottom: 1px solid #334155;
            width: 85%;
            margin: 0 auto 1.5px;
            font-weight: bold;
            font-size: 7.2px;
            text-align: center;
            color: #0f172a;
        }
        .signature-title {
            font-size: 5.8px;
            color: #475569;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .aegis-badge-card {
            border: 1px solid #d97706;
            background-color: #fffbeb;
            padding: 2.5px 4px;
            border-radius: 3px;
            margin-top: 2px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    @php
        $application->loadMissing([
            'scholarship.fields',
            'customFields',
            'user.profile',
            'evaluator',
            'document.aiResult',
            'academicTerm'
        ]);

        // 1. Zero-Failure Base64 Logo Encoding
        $clsuSealPath = public_path('images/clsu-seal.png');
        if (!file_exists($clsuSealPath)) {
            $clsuSealPath = public_path('logo.png');
        }
        $clsuLogoBase64 = file_exists($clsuSealPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($clsuSealPath))
            : null;

        $osaSealPath = public_path('images/osa-seal.png');
        if (!file_exists($osaSealPath)) {
            $osaSealPath = $clsuSealPath;
        }
        $osaLogoBase64 = file_exists($osaSealPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($osaSealPath))
            : null;

        // 2. Helper to query custom application fields (fuzzy matching)
        $getField = function($label) use ($application) {
            if (!$application->customFields) {
                return null;
            }
            $field = $application->customFields->first(function($f) use ($label) {
                $cleanedFieldName = strtolower(trim($f->field_name));
                $cleanedLabel = strtolower(trim($label));
                return $cleanedFieldName === $cleanedLabel || 
                       str_contains($cleanedFieldName, $cleanedLabel) || 
                       str_contains($cleanedLabel, $cleanedFieldName);
            });
            return $field ? trim($field->field_value) : null;
        };

        // 3. Name splitter logic
        $name = $application->user->name ?? '';
        $lastName = '';
        $firstName = '';
        $middleName = '';

        if (str_contains($name, ',')) {
            $nameParts = explode(',', $name);
            $lastName = trim($nameParts[0]);
            $rest = trim($nameParts[1] ?? '');
            $restParts = explode(' ', $rest);
            if (count($restParts) >= 2) {
                $middleName = array_pop($restParts);
                $firstName = implode(' ', $restParts);
            } else {
                $firstName = $rest;
            }
        } else {
            $parts = explode(' ', trim($name));
            if (count($parts) >= 3) {
                $lastName = array_pop($parts);
                $middleName = array_pop($parts);
                $firstName = implode(' ', $parts);
            } elseif (count($parts) == 2) {
                $firstName = $parts[0];
                $lastName = $parts[1];
            } else {
                $firstName = $name;
            }
        }

        // 4. Dynamic Term, Program, and Renewal detection
        $programName = $application->program_name ?? $application->scholarship?->name ?? 'Official Scholarship Grant';
        $isRenewal = (bool)($application->is_renewal || str_contains(strtolower($getField('type') ?? ''), 'renewal'));
        $isNew = !$isRenewal;
        $termLabel = $application->academicTerm 
            ? ($application->academicTerm->semester . ', A.Y. ' . $application->academicTerm->academic_year)
            : ($application->created_at ? $application->created_at->format('F Y') : 'Active Academic Term');
        $controlNo = 'APP-' . str_pad((string)$application->id, 5, '0', STR_PAD_LEFT);
        $directorName = \App\Models\User::where('role', 'superadmin')->first()?->name ?? 'Director, Office of Student Affairs';
        $evaluatorName = $application->evaluator->name ?? 'OSA Scholarship Evaluator';

        $normStatus = strtolower($application->status ?? 'submitted');
        $statusColor = match($normStatus) {
            'approved' => '#15803d',
            'rejected', 'disapproved' => '#b91c1c',
            'incomplete', 'returned' => '#b45309',
            default => '#0369a1',
        };
        $statusBg = match($normStatus) {
            'approved' => '#dcfce7',
            'rejected', 'disapproved' => '#fee2e2',
            'incomplete', 'returned' => '#fef3c7',
            default => '#e0f2fe',
        };
    @endphp

    <!-- Institutional Header with Authentic CLSU & OSA Logos -->
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                @if($clsuLogoBase64)
                    <img src="{{ $clsuLogoBase64 }}" style="width: 44px; height: 44px; object-fit: contain;" alt="CLSU Seal">
                @else
                    <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #0f5934; text-align: center; line-height: 38px; color: #0f5934; font-weight: bold; font-size: 11px; margin: 0 auto;">CLSU</div>
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <div style="font-size: 7.5px; font-weight: normal; color: #334155; margin-bottom: 1px;">Republic of the Philippines</div>
                <div style="font-size: 11px; font-weight: bold; color: #0f5934; letter-spacing: 0.5px; margin-bottom: 1px;">CENTRAL LUZON STATE UNIVERSITY</div>
                <div style="font-size: 7px; color: #475569; margin-bottom: 1.5px;">Science City of Muñoz, Nueva Ecija, Philippines</div>
                <div style="font-size: 9px; font-weight: bold; color: #0f5934; text-transform: uppercase; margin-bottom: 1px;">OFFICE OF STUDENT AFFAIRS</div>
                <div style="font-size: 7px; font-weight: normal; color: #475569;">Student Welfare and Scholarship Services Division</div>
            </td>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                @if($osaLogoBase64)
                    <img src="{{ $osaLogoBase64 }}" style="width: 44px; height: 44px; object-fit: contain;" alt="OSA Seal">
                @else
                    <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px dashed #f2a900; text-align: center; line-height: 38px; color: #f2a900; font-weight: bold; font-size: 11px; margin: 0 auto;">OSA</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Title, Type & 2x2 Picture Block -->
    <table class="title-table">
        <tr>
            <td style="width: 80%; padding: 5px 8px; vertical-align: middle; border-right: 1.5px solid #0f5934;">
                <div style="font-size: 10px; font-weight: bold; color: #0f5934; text-transform: uppercase; margin-bottom: 3px; line-height: 1.25;">
                    {{ strtoupper($programName) }} APPLICATION & EVALUATION FORM
                </div>
                <div style="font-size: 7.2px; color: #475569; margin-bottom: 3px;">
                    <strong>Academic Term:</strong> {{ $termLabel }}
                    &nbsp;&bull;&nbsp;
                    <strong>Control No:</strong> {{ $controlNo }}
                    &nbsp;&bull;&nbsp;
                    <strong>Official Status:</strong> <span style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; border-radius: 3px; padding: 1px 5px; font-weight: bold; font-size: 7px;">{{ strtoupper($application->status) }}</span>
                </div>
                <div style="margin-top: 2px;">
                    <span class="checkbox-box @if($isNew) checkbox-checked @endif">@if($isNew) X @endif</span>
                    <span style="font-size: 7.5px; font-weight: bold; margin-right: 14px; vertical-align: middle;">NEW APPLICANT</span>
                    
                    <span class="checkbox-box @if($isRenewal) checkbox-checked @endif">@if($isRenewal) X @endif</span>
                    <span style="font-size: 7.5px; font-weight: bold; vertical-align: middle;">RENEWAL APPLICANT</span>
                </div>
            </td>
            <td style="width: 20%; text-align: center; vertical-align: middle; padding: 3px; height: 65px;">
                <div style="width: 60px; height: 60px; border: 1px dashed #64748b; margin: 0 auto; background-color: #ffffff; text-align: center;">
                    <div style="font-size: 6.2px; color: #64748b; padding-top: 20px; font-weight: bold; line-height: 1.15;">
                        2X2 PHOTO<br>Passport / ID
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Section 1: Personal & Academic Profile -->
    <table class="grid-table">
        <tr>
            <th colspan="3" class="section-header" style="text-align: left;">I. Personal & Academic Information</th>
        </tr>
        <tr>
            <td style="width: 33.3%;">
                <span class="label-text">SURNAME</span>
                <span class="value-text">{{ $lastName ?: '—' }}</span>
            </td>
            <td style="width: 33.3%;">
                <span class="label-text">FIRST NAME</span>
                <span class="value-text">{{ $firstName ?: '—' }}</span>
            </td>
            <td style="width: 33.3%;">
                <span class="label-text">MIDDLE NAME</span>
                <span class="value-text">{{ $middleName ?: '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">CLSU STUDENT ID</span>
                <span class="value-text" style="color: #0f5934;">
                    {{ $application->user->profile?->clsu_id_number ?? $getField('clsu id number') ?? $getField('student id') ?? '—' }}
                </span>
            </td>
            <td>
                <span class="label-text">COLLEGE</span>
                <span class="value-text">
                    {{ $application->user->profile?->college ?? $getField('college') ?? '—' }}
                </span>
            </td>
            <td>
                <span class="label-text">COURSE & YEAR LEVEL</span>
                <span class="value-text">
                    {{ trim(($application->user->profile?->course ?? $getField('course') ?? '') . ' - ' . ($application->user->profile?->year_level ?? $getField('year level') ?? '')) ?: '—' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">SEX</span>
                @php
                    $sex = strtolower($application->user->profile?->gender ?? $getField('sex') ?? $getField('gender') ?? '');
                    $isMale = str_contains($sex, 'male') && !str_contains($sex, 'female');
                    $isFemale = str_contains($sex, 'female');
                @endphp
                <span class="value-text">
                    @if($isMale) Male @elseif($isFemale) Female @else {{ $sex ? ucfirst($sex) : '—' }} @endif
                </span>
            </td>
            <td>
                <span class="label-text">CIVIL STATUS</span>
                @php
                    $civil = strtolower($application->user->profile?->civil_status ?? $getField('civil status') ?? $getField('status') ?? '');
                @endphp
                <span class="value-text">{{ $civil ? ucfirst($civil) : 'Single' }}</span>
            </td>
            <td>
                <span class="label-text">GENERAL WEIGHTED AVERAGE (GWA)</span>
                <span class="value-text" style="color: #0f5934;">
                    {{ $application->gwa !== null ? number_format((float)$application->gwa, 2) . ' (Verified)' : 'Pending Evaluation' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">CONTACT NUMBER</span>
                <span class="value-text">
                    {{ $application->user->profile?->contact_number ?? $getField('contact no') ?? $getField('contact number') ?? '—' }}
                </span>
            </td>
            <td colspan="2">
                <span class="label-text">EMAIL ADDRESS</span>
                <span class="value-text">{{ $application->user->email ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="label-text">PERMANENT HOME ADDRESS</span>
                <span class="value-text">
                    {{ $getField('home address') ?? $getField('permanent address') ?? $getField('address') ?? $application->user->profile?->full_address ?? $application->user->profile?->address ?? '—' }}
                </span>
            </td>
        </tr>
    </table>

    <!-- Section 2: Family Background & Emergency Contact -->
    <table class="grid-table">
        <tr>
            <th colspan="2" class="section-header" style="text-align: left;">II. Family Background & Emergency Contact</th>
        </tr>
        <tr>
            <td style="width: 50%;">
                <span class="label-text">FATHER / GUARDIAN NAME</span>
                <span class="value-text">{{ $application->user->profile?->guardian_name ?? $getField('name of father') ?? $getField('father name') ?? $getField('guardian') ?? '—' }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label-text">OCCUPATION / MONTHLY INCOME</span>
                <span class="value-text">{{ $getField('father occupation') ?? $getField('occupation of father') ?? $getField('family income') ?? '—' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">MOTHER'S MAIDEN NAME</span>
                <span class="value-text">{{ $getField('name of mother') ?? $getField('mother name') ?? '—' }}</span>
            </td>
            <td>
                <span class="label-text">EMERGENCY CONTACT NUMBER</span>
                <span class="value-text">{{ $application->user->profile?->emergency_contact_number ?? $getField('emergency contact') ?? '—' }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 3: Documentary Requirements & Verification Checklist -->
    @php
        $hasCog = (bool)$application->document;
        $hasId = !empty($application->user->profile?->clsu_id_number);
        $hasItr = (bool)($getField('itr') || $getField('income') || $getField('indigency') || $getField('tax'));
    @endphp
    <table class="grid-table">
        <tr>
            <th colspan="2" class="section-header" style="text-align: left;">III. Documentary Requirements & Verification Checklist</th>
        </tr>
        <tr>
            <td style="width: 50%; padding: 3px 5px;">
                <span class="checkbox-box checkbox-checked">X</span>
                <span style="font-weight: bold; font-size: 7.5px;">1. Duly accomplished Application Record</span>
                <span style="color: #0f5934; font-size: 7px; font-weight: bold;">(AEGIS Verified)</span>
            </td>
            <td style="width: 50%; padding: 3px 5px;">
                <span class="checkbox-box @if($hasCog) checkbox-checked @endif">@if($hasCog) X @endif</span>
                <span style="font-weight: bold; font-size: 7.5px;">2. Official Certificate of Grades (COG)</span>
                <span style="color: {{ $hasCog ? '#0f5934' : '#b91c1c' }}; font-size: 7px; font-weight: bold;">{{ $hasCog ? '(Submitted)' : '(Pending)' }}</span>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding: 3px 5px;">
                <span class="checkbox-box @if($hasId) checkbox-checked @endif">@if($hasId) X @endif</span>
                <span style="font-weight: bold; font-size: 7.5px;">3. Valid CLSU Student Identification</span>
                <span style="color: {{ $hasId ? '#0f5934' : '#b91c1c' }}; font-size: 7px; font-weight: bold;">{{ $hasId ? '(Verified)' : '(Pending)' }}</span>
            </td>
            <td style="width: 50%; padding: 3px 5px;">
                <span class="checkbox-box @if($hasItr) checkbox-checked @endif">@if($hasItr) X @endif</span>
                <span style="font-weight: bold; font-size: 7.5px;">4. Proof of Income / Indigency</span>
                <span style="color: #475569; font-size: 7px;">{{ $hasItr ? '(Attached)' : '(If applicable)' }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 4: Dynamic Program Credentials & Custom Answers -->
    @if($application->customFields && $application->customFields->count() > 0)
    <table class="grid-table">
        <tr>
            <th colspan="2" class="section-header" style="text-align: left;">IV. Program-Specific Application Parameters & Question Responses</th>
        </tr>
        <tr style="background-color: #f8fafc;">
            <td style="width: 45%; font-weight: bold; color: #475569; font-size: 7px; text-transform: uppercase;">Parameter / Question</td>
            <td style="width: 55%; font-weight: bold; color: #475569; font-size: 7px; text-transform: uppercase;">Applicant Response</td>
        </tr>
        @foreach($application->customFields as $field)
        <tr>
            <td style="width: 45%; font-weight: bold; color: #334155; font-size: 7.2px;">
                {{ $field->field_label ?? ucwords(str_replace(['_', '-'], ' ', $field->field_name)) }}
            </td>
            <td style="width: 55%; font-weight: bold; color: #0f172a; font-size: 7.2px; word-break: break-all;">
                {{ $field->field_value ?? '—' }}
            </td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- Legal Attestation Box -->
    <div class="attestation-box">
        <strong>OATH OF VERACITY & COMPLIANCE:</strong> I hereby certify on my honor that all entries, statements, and supporting documents attached to this application are true, correct, and authentic. I understand that any false declaration or fraudulent modification discovered in this submission shall warrant automatic disqualification, forfeiture of scholarship benefits, restitution of funds received, and administrative sanctions under the Central Luzon State University Student Handbook and the Philippine Data Privacy Act (R.A. 10173).
    </div>

    <!-- Official Signatures (3 Columns Matching On-Screen Modal) -->
    <table class="signature-table">
        <tr>
            <td style="width: 33.3%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $application->user->name ?? 'Student Applicant' }}</div>
                <div class="signature-title">Signature of Student Grantee</div>
            </td>
            <td style="width: 33.3%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $evaluatorName }}</div>
                <div class="signature-title">Verified by OSA Evaluator / Staff</div>
            </td>
            <td style="width: 33.3%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $directorName }}</div>
                <div class="signature-title">
                    @if(strtolower($application->status) === 'approved')
                        Approved by: Director, OSA
                    @elseif(in_array(strtolower($application->status), ['rejected', 'disapproved']))
                        Disapproved by: Director, OSA
                    @else
                        Endorsed by: Director, OSA
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- A.E.G.I.S. Digital Forensics Clearance Badge -->
    <div class="aegis-badge-card">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
            <tr>
                <td style="font-weight: bold; color: #b45309; font-size: 7.2px; text-transform: uppercase;">
                    🛡 A.E.G.I.S. Institutional Security & AI Verification Clearance
                </td>
                <td style="text-align: right; font-weight: bold; color: #b45309; font-size: 6.5px;">
                    Tamper-Evident
                </td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse; font-size: 6.8px;">
            <tr>
                <td style="width: 25%; color: #475569; font-weight: bold;">
                    Verification Hash:<br>
                    <span style="color: #0f172a;">AEGIS-{{ strtoupper(substr(md5($application->id . $application->created_at), 0, 10)) }}</span>
                </td>
                <td style="width: 25%; color: #475569; font-weight: bold;">
                    Document Forensic Status:<br>
                    @php
                        $aiResult = $application->document?->aiResult;
                        $class = $aiResult ? strtolower($aiResult->classification) : 'authentic';
                    @endphp
                    @if($class === 'authentic')
                        <span style="color: #15803d;">✔ AUTHENTIC (CLEARED)</span>
                    @else
                        <span style="color: #b91c1c;">⚠ FLAGGED (REVIEWED)</span>
                    @endif
                </td>
                <td style="width: 25%; color: #475569; font-weight: bold;">
                    GWA Integrity:<br>
                    <span style="color: #0f5934;">{{ $application->gwa !== null ? 'GWA ' . number_format((float)$application->gwa, 2) . ' (Passed)' : 'Pending' }}</span>
                </td>
                <td style="width: 25%; color: #475569; font-weight: bold;">
                    Date of Clearance:<br>
                    <span style="color: #0f172a;">{{ $application->updated_at ? $application->updated_at->format('F d, Y') : date('F d, Y') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footnote Revision -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 3px; font-size: 6.2px; color: #64748b; border-top: 1px solid #cbd5e1; padding-top: 2px;">
        <tr>
            <td style="width: 60%; text-align: left;">
                CLSU-OSA-AEGIS-FORM-001 (Rev. 2 • ISO/IEC 25010 & R.A. 10173 Compliant)
            </td>
            <td style="width: 40%; text-align: right;">
                A.E.G.I.S. Secure Educational Record Verification
            </td>
        </tr>
    </table>

</body>
</html>
