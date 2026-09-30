<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CLSU | {{ $application->program_name ?? 'Scholarship Grant' }} Application & Evaluation Form</title>
    <style>
        @page {
            margin: 14px 20px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 8.5px;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .title-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #0f5934;
            margin-bottom: 5px;
            background-color: #ffffff;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #0f5934;
            margin-bottom: 5px;
            background-color: #ffffff;
        }
        .grid-table td, .grid-table th {
            border: 1px solid #0f5934;
            padding: 3px 5px;
            vertical-align: top;
        }
        .section-header {
            background-color: #0f5934;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 2.5px 6px;
            letter-spacing: 0.5px;
        }
        .label-text {
            font-size: 6.8px;
            color: #475569;
            font-weight: bold;
            display: block;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .value-text {
            font-size: 8.5px;
            font-weight: bold;
            color: #0f172a;
            display: block;
        }
        .checkbox-box {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1px solid #475569;
            text-align: center;
            line-height: 7px;
            font-size: 7px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            margin-right: 3px;
            vertical-align: middle;
            background-color: #ffffff;
        }
        .checkbox-checked {
            background-color: #0f5934;
            color: #ffffff;
            border-color: #0f5934;
        }
        .attestation-box {
            font-size: 7.5px;
            line-height: 1.25;
            text-align: justify;
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            background-color: #f8fafc;
            margin-bottom: 6px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 4px;
        }
        .signature-line {
            border-bottom: 1px solid #334155;
            width: 85%;
            margin: 0 auto 2px;
            font-weight: bold;
            font-size: 8.5px;
            text-align: center;
            color: #0f172a;
        }
        .signature-title {
            font-size: 7px;
            color: #475569;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .aegis-badge-card {
            border: 1px solid #d97706;
            background-color: #fffbeb;
            padding: 4px 6px;
            border-radius: 4px;
            margin-top: 6px;
        }
        .custom-fields-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5px;
        }
        .custom-fields-table td {
            padding: 2px 4px;
            border-bottom: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body>

    @php
        // 1. Zero-Failure Base64 Logo Encoding
        $clsuSealPath = public_path('images/clsu-seal.png');
        if (!file_exists($clsuSealPath)) {
            $clsuSealPath = public_path('logo.png');
        }
        $clsuLogoBase64 = file_exists($clsuSealPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($clsuSealPath)) 
            : null;

        $customOsaLogo = \App\Models\Setting::get('osa_logo');
        $osaSealPath = ($customOsaLogo && file_exists(storage_path('app/public/' . $customOsaLogo)))
            ? storage_path('app/public/' . $customOsaLogo)
            : public_path('images/osa-seal.png');
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
                    <img src="{{ $clsuLogoBase64 }}" style="width: 50px; height: 50px; object-fit: contain;" alt="CLSU Seal">
                @else
                    <div style="width: 46px; height: 46px; border-radius: 50%; border: 2px solid #0f5934; text-align: center; line-height: 44px; color: #0f5934; font-weight: bold; font-size: 12px; margin: 0 auto;">CLSU</div>
                @endif
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <div style="font-size: 8px; font-weight: normal; color: #334155; margin-bottom: 1px;">Republic of the Philippines</div>
                <div style="font-size: 12px; font-weight: bold; color: #0f5934; letter-spacing: 0.5px; margin-bottom: 1px;">CENTRAL LUZON STATE UNIVERSITY</div>
                <div style="font-size: 7.5px; color: #475569; margin-bottom: 2px;">Science City of Muñoz, Nueva Ecija, Philippines</div>
                <div style="font-size: 9.5px; font-weight: bold; color: #0f5934; text-transform: uppercase; margin-bottom: 1px;">OFFICE OF STUDENT AFFAIRS</div>
                <div style="font-size: 7.5px; font-weight: normal; color: #475569;">Student Welfare and Scholarship Services Division</div>
            </td>
            <td style="width: 15%; text-align: center; vertical-align: middle;">
                @if($osaLogoBase64)
                    <img src="{{ $osaLogoBase64 }}" style="width: 50px; height: 50px; object-fit: contain;" alt="OSA Seal">
                @else
                    <div style="width: 46px; height: 46px; border-radius: 50%; border: 2px dashed #f2a900; text-align: center; line-height: 44px; color: #f2a900; font-weight: bold; font-size: 12px; margin: 0 auto;">OSA</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- Title, Type & 2x2 Picture Block -->
    <table class="title-table">
        <tr>
            <td style="width: 78%; padding: 6px 8px; vertical-align: middle; border-right: 1.5px solid #0f5934;">
                <div style="font-size: 11px; font-weight: bold; color: #0f5934; text-transform: uppercase; margin-bottom: 3px; line-height: 1.25;">
                    {{ strtoupper($programName) }} APPLICATION & EVALUATION FORM
                </div>
                <div style="font-size: 7.5px; color: #475569; margin-bottom: 4px;">
                    <strong>Academic Term:</strong> {{ $termLabel }}
                    &nbsp;&bull;&nbsp;
                    <strong>Control No:</strong> {{ $controlNo }}
                    &nbsp;&bull;&nbsp;
                    <strong>Official Status:</strong> <span style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; border-radius: 3px; padding: 1.5px 6px; font-weight: bold; font-size: 7.5px;">{{ strtoupper($application->status) }}</span>
                </div>
                <div style="margin-top: 3px;">
                    <span class="checkbox-box @if($isNew) checkbox-checked @endif">@if($isNew) X @endif</span>
                    <span style="font-size: 8px; font-weight: bold; margin-right: 16px; vertical-align: middle;">NEW APPLICANT</span>
                    
                    <span class="checkbox-box @if($isRenewal) checkbox-checked @endif">@if($isRenewal) X @endif</span>
                    <span style="font-size: 8px; font-weight: bold; vertical-align: middle;">RENEWAL APPLICANT</span>
                </div>
            </td>
            <td style="width: 22%; text-align: center; vertical-align: middle; padding: 4px; height: 75px;">
                <div style="width: 65px; height: 65px; border: 1px dashed #64748b; margin: 0 auto; background-color: #f8fafc; text-align: center;">
                    <div style="font-size: 6.5px; color: #64748b; padding-top: 22px; font-weight: bold; line-height: 1.2;">
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
                <span class="value-text">{{ $lastName ?: '____________________' }}</span>
            </td>
            <td style="width: 33.3%;">
                <span class="label-text">FIRST NAME</span>
                <span class="value-text">{{ $firstName ?: '____________________' }}</span>
            </td>
            <td style="width: 33.3%;">
                <span class="label-text">MIDDLE NAME</span>
                <span class="value-text">{{ $middleName ?: '____________________' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">CLSU STUDENT ID</span>
                <span class="value-text" style="color: #0f5934;">
                    {{ $application->user->profile?->clsu_id_number ?? $getField('clsu id number') ?? $getField('student id') ?? '________' }}
                </span>
            </td>
            <td>
                <span class="label-text">COLLEGE</span>
                <span class="value-text">
                    {{ $application->user->profile?->college ?? $getField('college') ?? '________' }}
                </span>
            </td>
            <td>
                <span class="label-text">COURSE & YEAR LEVEL</span>
                <span class="value-text">
                    {{ trim(($application->user->profile?->course ?? $getField('course') ?? '') . ' - ' . ($application->user->profile?->year_level ?? $getField('year level') ?? '')) ?: '________' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">SEX</span>
                <div style="margin-top: 1px;">
                    @php
                        $sex = strtolower($application->user->profile?->gender ?? $getField('sex') ?? $getField('gender') ?? '');
                        $isMale = str_contains($sex, 'male') && !str_contains($sex, 'female');
                        $isFemale = str_contains($sex, 'female');
                    @endphp
                    <span class="checkbox-box @if($isMale) checkbox-checked @endif">@if($isMale) X @endif</span>
                    <span style="vertical-align: middle; margin-right: 8px;">Male</span>
                    <span class="checkbox-box @if($isFemale) checkbox-checked @endif">@if($isFemale) X @endif</span>
                    <span style="vertical-align: middle;">Female</span>
                </div>
            </td>
            <td>
                <span class="label-text">CIVIL STATUS</span>
                <div style="margin-top: 1px;">
                    @php
                        $civil = strtolower($application->user->profile?->civil_status ?? $getField('civil status') ?? $getField('status') ?? '');
                        $isMarried = str_contains($civil, 'married');
                        $isSingle = !$isMarried;
                    @endphp
                    <span class="checkbox-box @if($isSingle) checkbox-checked @endif">@if($isSingle) X @endif</span>
                    <span style="vertical-align: middle; margin-right: 8px;">Single</span>
                    <span class="checkbox-box @if($isMarried) checkbox-checked @endif">@if($isMarried) X @endif</span>
                    <span style="vertical-align: middle;">Married</span>
                </div>
            </td>
            <td>
                <span class="label-text">GENERAL WEIGHTED AVERAGE (GWA)</span>
                <span class="value-text" style="color: #0f5934;">
                    {{ $application->gwa !== null ? number_format((float)$application->gwa, 2) . ' (Verified by OSA)' : 'Pending Evaluation' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">CONTACT NO.</span>
                <span class="value-text">
                    {{ $application->user->profile?->contact_number ?? $getField('contact no') ?? $getField('contact number') ?? '________' }}
                </span>
            </td>
            <td colspan="2">
                <span class="label-text">EMAIL ADDRESS</span>
                <span class="value-text">{{ $application->user->email ?? '________' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <span class="label-text">PERMANENT HOME ADDRESS</span>
                <span class="value-text">
                    {{ $getField('home address') ?? $getField('permanent address') ?? $getField('address') ?? '____________________________________________________________' }}
                </span>
            </td>
        </tr>
    </table>

    <!-- Section 2: Family & Guardian Information -->
    <table class="grid-table">
        <tr>
            <th colspan="2" class="section-header" style="text-align: left;">II. Family Background & Emergency Contact</th>
        </tr>
        <tr>
            <td style="width: 50%;">
                <span class="label-text">NAME OF FATHER / GUARDIAN</span>
                <span class="value-text">{{ $application->user->profile?->guardian_name ?? $getField('name of father') ?? $getField('father name') ?? $getField('guardian') ?? '________' }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label-text">FATHER OCCUPATION / MONTHLY INCOME</span>
                <span class="value-text">{{ $getField('father occupation') ?? $getField('occupation of father') ?? $getField('family income') ?? '________' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">NAME OF MOTHER</span>
                <span class="value-text">{{ $getField('name of mother') ?? $getField('mother name') ?? '________' }}</span>
            </td>
            <td>
                <span class="label-text">EMERGENCY CONTACT NUMBER</span>
                <span class="value-text">{{ $application->user->profile?->emergency_contact_number ?? $getField('emergency contact') ?? $getField('mother contact') ?? '________' }}</span>
            </td>
        </tr>
    </table>

    <!-- Section 3: Documentary Requirements Checklist (Dynamic) -->
    <table class="grid-table" style="font-size: 7.5px;">
        <thead>
            <tr>
                <th class="section-header" style="text-align: left;">
                    III. Documentary Requirements & Verification Checklist
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 4px 6px; line-height: 1.3;">
                    @php
                        // Verification states
                        $hasForm = true;
                        $hasCog = (bool)$application->document;
                        $hasId = !empty($application->user->profile?->clsu_id_number);
                        $hasItr = (bool)($getField('itr') || $getField('income') || $getField('indigency') || $getField('tax'));
                    @endphp
                    
                    <div style="margin-bottom: 2px;">
                        <span class="checkbox-box @if($hasForm) checkbox-checked @endif">@if($hasForm) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">1. Duly accomplished {{ $programName }} Application & Evaluation Record (A.E.G.I.S. Verified)</span>
                    </div>

                    <div style="margin-bottom: 2px;">
                        <span class="checkbox-box @if($hasCog) checkbox-checked @endif">@if($hasCog) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">2. Certificate of Grades (COG / Form 6 / Transcript of Records)</span>
                        @if($application->gwa)
                            <span style="color: #0f5934; font-weight: bold; margin-left: 6px;">[Certified GWA: {{ number_format((float)$application->gwa, 2) }}]</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 2px;">
                        <span class="checkbox-box @if($hasId) checkbox-checked @endif">@if($hasId) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">3. Photocopy of Valid CLSU Student Identification Card</span>
                        @if($application->user->profile?->clsu_id_number)
                            <span style="color: #475569; margin-left: 6px;">(ID No: {{ $application->user->profile->clsu_id_number }})</span>
                        @endif
                    </div>

                    <div style="margin-bottom: 2px;">
                        <span class="checkbox-box @if($hasItr) checkbox-checked @endif">@if($hasItr) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">4. Certificate of Indigency / Parents' Latest Income Tax Return (ITR) / BIR Tax Exemption</span>
                    </div>

                    @if($application->documents && $application->documents->count() > 1)
                        @foreach($application->documents as $doc)
                            @if($doc->document_type !== 'COG')
                                <div style="margin-bottom: 2px;">
                                    <span class="checkbox-box checkbox-checked">X</span>
                                    <span style="font-weight: bold; vertical-align: middle;">5. Program Attachment: {{ $doc->document_type }}</span>
                                    <span style="color: #475569; margin-left: 4px;">({{ $doc->original_name }})</span>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Section 4: Dynamic Program Credentials & Custom Answers -->
    @if($application->customFields && $application->customFields->count() > 0)
    <table class="grid-table">
        <tr>
            <th colspan="2" class="section-header" style="text-align: left;">IV. Program-Specific Details & Applicant Disclosures</th>
        </tr>
        <tr>
            <td colspan="2" style="padding: 3px 5px;">
                <table class="custom-fields-table">
                    @php
                        $validFields = $application->customFields->filter(function($f) {
                            return !str_starts_with($f->field_value, 'uploads/');
                        });
                        $fieldChunks = $validFields->chunk(2);
                    @endphp
                    @forelse($fieldChunks as $chunk)
                        <tr>
                            @foreach($chunk as $f)
                                <td style="width: 50%;">
                                    <span style="color: #475569; font-weight: bold; text-transform: uppercase;">{{ $f->field_name }}:</span>
                                    <span style="color: #0f172a; font-weight: bold; margin-left: 4px;">{{ $f->field_value }}</span>
                                </td>
                            @endforeach
                            @if($chunk->count() === 1)
                                <td style="width: 50%;"></td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td style="color: #64748b; font-style: italic;">No additional program questions recorded.</td>
                        </tr>
                    @endforelse
                </table>
            </td>
        </tr>
    </table>
    @endif

    <!-- Legal Attestation Box -->
    <div class="attestation-box">
        <strong>OATH OF VERACITY & COMPLIANCE:</strong> I hereby certify on my honor that all entries, statements, and supporting documents attached to this application are true, correct, and authentic. I understand that any false declaration or fraudulent modification discovered in this submission shall warrant automatic disqualification, forfeiture of scholarship benefits, restitution of funds received, and administrative sanctions under the Central Luzon State University Student Handbook and the Philippine Data Privacy Act (R.A. 10173).
    </div>

    <!-- Official Signatures -->
    <table class="signature-table">
        <tr>
            <td style="width: 45%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $application->user->name ?? 'Student Applicant' }}</div>
                <div class="signature-title">Signature of Student Grantee</div>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $evaluatorName }}</div>
                <div class="signature-title">Verified by OSA Evaluator / Staff</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 10px;"></td>
        </tr>
        <tr>
            <td style="width: 25%;"></td>
            <td style="width: 50%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $directorName }}</div>
                <div class="signature-title">
                    @if(strtolower($application->status) === 'approved')
                        Approved by: Director, Office of Student Affairs
                    @elseif(in_array(strtolower($application->status), ['rejected', 'disapproved']))
                        Disapproved by: Director, Office of Student Affairs
                    @else
                        Endorsed for Review: Office of Student Affairs
                    @endif
                </div>
            </td>
            <td style="width: 25%;"></td>
        </tr>
    </table>

    <!-- A.E.G.I.S. Digital Forensics Clearance Badge -->
    <div class="aegis-badge-card">
        <div style="font-weight: bold; color: #b45309; font-size: 7.5px; text-transform: uppercase; margin-bottom: 2px;">
            🛡 A.E.G.I.S. Institutional Security & AI Verification Clearance
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 7px;">
            <tr>
                <td style="width: 25%; font-weight: bold; color: #475569;">Verification Hash:</td>
                <td style="width: 25%; font-weight: bold; color: #0f172a;">AEGIS-{{ strtoupper(substr(md5($application->id . $application->created_at), 0, 10)) }}</td>
                <td style="width: 25%; font-weight: bold; color: #475569;">Document Forensic Status:</td>
                <td style="width: 25%; font-weight: bold;">
                    @php
                        $aiResult = $application->document?->aiResult;
                        $class = $aiResult ? strtolower($aiResult->classification) : 'authentic';
                        $fraudScore = $aiResult ? $aiResult->fraud_probability : 0.0;
                    @endphp
                    @if($class === 'authentic')
                        <span style="color: #16a34a;">AUTHENTIC (CLEARED)</span>
                    @else
                        <span style="color: #dc2626;">FLAGGED (REVIEWED)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #475569;">GWA Integrity:</td>
                <td style="font-weight: bold; color: #0f5934;">{{ $application->gwa !== null ? 'GWA ' . number_format((float)$application->gwa, 2) . ' (Passed)' : 'Pending' }}</td>
                <td style="font-weight: bold; color: #475569;">Date of Clearance:</td>
                <td style="font-weight: bold; color: #0f172a;">{{ $application->updated_at ? $application->updated_at->format('F d, Y') : date('F d, Y') }}</td>
            </tr>
        </table>
    </div>

    <!-- Form Revision Code -->
    <div style="width: 100%; text-align: left; font-size: 6px; color: #94a3b8; margin-top: 4px; font-weight: bold;">
        CLSU-OSA-AEGIS-FORM-001 (Rev. 2 • ISO/IEC 25010 & R.A. 10173 Compliant)
    </div>

</body>
</html>
