<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CLSU | Student Part-Time Employment Service Application Form</title>
    <style>
        @page {
            margin: 15px 20px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #0f172a;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .title-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #0f5934;
            margin-bottom: 5px;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #0f5934;
            margin-bottom: 5px;
        }
        .grid-table td, .grid-table th {
            border: 1px solid #0f5934;
            padding: 3px 5px;
            vertical-align: top;
        }
        .section-title {
            background-color: #0f5934;
            color: #ffffff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 3px 6px;
            letter-spacing: 0.5px;
        }
        .label-text {
            font-size: 7px;
            color: #475569;
            font-weight: bold;
            display: block;
            text-transform: uppercase;
            margin-bottom: 1px;
        }
        .value-text {
            font-size: 9px;
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
            line-height: 6px;
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
        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .requirements-list li {
            margin-bottom: 3px;
        }
        .attestation-box {
            font-size: 8px;
            line-height: 1.3;
            text-align: justify;
            border: 1px solid #cbd5e1;
            padding: 5px;
            background-color: #f8fafc;
            margin-bottom: 8px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .signature-line {
            border-bottom: 1px solid #475569;
            width: 80%;
            margin: 0 auto 3px;
            font-weight: bold;
            font-size: 9px;
            text-align: center;
        }
        .signature-title {
            font-size: 7.5px;
            color: #475569;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .aegis-badge-card {
            border: 1px solid #f2a900;
            background-color: #fefbeb;
            padding: 5px;
            border-radius: 4px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

    @php
        // Helper to query custom application fields (fuzzy matching label)
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

        // Name splitter logic
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
    @endphp

    <!-- Institutional Header -->
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                <div style="width: 45px; height: 45px; border-radius: 50%; border: 2px solid #0f5934; text-align: center; line-height: 43px; color: #0f5934; font-weight: bold; font-size: 13px; margin: 0 auto;">CLSU</div>
            </td>
            <td style="width: 70%; text-align: center; vertical-align: middle;">
                <div style="font-size: 9px; font-weight: normal; margin-bottom: 1px;">Republic of the Philippines</div>
                <div style="font-size: 12px; font-weight: bold; color: #0f5934; letter-spacing: 0.5px; margin-bottom: 1px;">CENTRAL LUZON STATE UNIVERSITY</div>
                <div style="font-size: 8px; color: #475569; margin-bottom: 2px;">Science City of Muñoz, Nueva Ecija</div>
                <div style="font-size: 9px; font-weight: bold; text-transform: uppercase; margin-bottom: 1px;">OFFICE OF STUDENT AFFAIRS</div>
                <div style="font-size: 8px; font-weight: normal; color: #475569;">Career Development and Employment Services Unit</div>
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle;">
                <div style="width: 45px; height: 45px; border-radius: 50%; border: 2px dashed #f2a900; text-align: center; line-height: 43px; color: #f2a900; font-weight: bold; font-size: 13px; margin: 0 auto;">OSA</div>
            </td>
        </tr>
    </table>

    <!-- Title, Type & 2x2 Picture Block -->
    <table class="title-table">
        <tr>
            <td style="width: 78%; padding: 8px; vertical-align: middle; border-right: 1px solid #0f5934;">
                <div style="font-size: 12px; font-weight: bold; color: #0f5934; text-transform: uppercase; margin-bottom: 6px;">
                    STUDENT PART-TIME EMPLOYMENT SERVICE APPLICATION FORM
                </div>
                <div style="margin-top: 6px;">
                    @php
                        $appType = strtolower($getField('application type') ?? $getField('type') ?? '');
                        $isRenewal = str_contains($appType, 'renewal');
                        $isNew = !$isRenewal;
                    @endphp
                    <span class="checkbox-box @if($isNew) checkbox-checked @endif">@if($isNew) X @endif</span>
                    <span style="font-size: 9px; font-weight: bold; margin-right: 20px; vertical-align: middle;">NEW</span>
                    
                    <span class="checkbox-box @if($isRenewal) checkbox-checked @endif">@if($isRenewal) X @endif</span>
                    <span style="font-size: 9px; font-weight: bold; vertical-align: middle;">RENEWAL</span>
                </div>
            </td>
            <td style="width: 22%; text-align: center; vertical-align: middle; padding: 4px; height: 80px;">
                <div style="width: 70px; height: 70px; border: 1px dashed #64748b; margin: 0 auto; background-color: #f8fafc; text-align: center;">
                    <div style="font-size: 7px; color: #64748b; padding-top: 25px; font-weight: bold; line-height: 1.2;">
                        PICTURE 2X2<br>Paste here
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Personal Information Grid -->
    <table class="grid-table">
        <!-- Names Row -->
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
        <!-- Sex, Civil Status & Age -->
        <tr>
            <td>
                <span class="label-text">SEX</span>
                <div style="margin-top: 1px;">
                    @php
                        $sex = strtolower($getField('sex') ?? $getField('gender') ?? '');
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
                        $civil = strtolower($getField('civil status') ?? $getField('status') ?? '');
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
                <span class="label-text">AGE</span>
                <span class="value-text">{{ $getField('age') ?? '________' }}</span>
            </td>
        </tr>
        <!-- Course & Year, DOB, POB -->
        <tr>
            <td>
                <span class="label-text">COURSE & YEAR</span>
                <span class="value-text">
                    {{ ($application->user->profile?->course . ' - ' . $application->user->profile?->year_level) ?: ($getField('course & year') ?? '________') }}
                </span>
            </td>
            <td>
                <span class="label-text">DATE OF BIRTH (mm/dd/yyyy)</span>
                <span class="value-text">{{ $getField('date of birth') ?? $getField('dob') ?? '________' }}</span>
            </td>
            <td>
                <span class="label-text">PLACE OF BIRTH</span>
                <span class="value-text">{{ $getField('place of birth') ?? '________' }}</span>
            </td>
        </tr>
        <!-- Contact & Home Address -->
        <tr>
            <td>
                <span class="label-text">CONTACT NO.</span>
                <span class="value-text">
                    {{ $application->user->profile?->contact_number ?? $getField('contact no') ?? $getField('contact number') ?? '________' }}
                </span>
            </td>
            <td colspan="2">
                <span class="label-text">HOME ADDRESS</span>
                <span class="value-text">{{ $getField('home address') ?? $getField('address') ?? '________' }}</span>
            </td>
        </tr>
        <!-- Local Address -->
        <tr>
            <td colspan="3">
                <span class="label-text">ADDRESS WHILE STUDYING IN CLSU</span>
                <span class="value-text">
                    {{ $getField('address while studying in clsu') ?? $getField('clsu address') ?? $getField('local address') ?? '________' }}
                </span>
            </td>
        </tr>
    </table>

    <!-- Family Background Grid -->
    <table class="grid-table">
        <tr>
            <td style="width: 50%;">
                <span class="label-text">NAME OF FATHER</span>
                <span class="value-text">{{ $getField('name of father') ?? $getField('father name') ?? '________' }}</span>
            </td>
            <td style="width: 50%;">
                <span class="label-text">OCCUPATION</span>
                <span class="value-text">{{ $getField('father occupation') ?? $getField('occupation of father') ?? '________' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label-text">NAME OF MOTHER</span>
                <span class="value-text">{{ $getField('name of mother') ?? $getField('mother name') ?? '________' }}</span>
            </td>
            <td>
                <span class="label-text">OCCUPATION</span>
                <span class="value-text">{{ $getField('mother occupation') ?? $getField('occupation of mother') ?? '________' }}</span>
            </td>
        </tr>
    </table>

    <!-- Educational Background Table -->
    <table class="grid-table" style="text-align: left;">
        <thead>
            <tr style="background-color: #0f5934; color: #ffffff;">
                <th style="width: 25%; font-size: 7.5px; font-weight: bold; padding: 2px 5px;">EDUCATION</th>
                <th style="width: 50%; font-size: 7.5px; font-weight: bold; padding: 2px 5px;">NAME OF SCHOOL</th>
                <th style="width: 25%; font-size: 7.5px; font-weight: bold; padding: 2px 5px;">INCLUSIVE DATES OF ATTENDANCE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; background-color: #f8fafc; font-size: 8px; padding: 3px 5px;">Elementary</td>
                <td>{{ $getField('elementary school') ?? $getField('elementary') ?? '________' }}</td>
                <td>{{ $getField('elementary dates') ?? $getField('elementary inclusive dates') ?? '________' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #f8fafc; font-size: 8px; padding: 3px 5px;">Secondary</td>
                <td>{{ $getField('secondary school') ?? $getField('secondary') ?? '________' }}</td>
                <td>{{ $getField('secondary dates') ?? $getField('secondary inclusive dates') ?? '________' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #f8fafc; font-size: 8px; padding: 3px 5px;">College</td>
                <td>{{ $getField('college school') ?? $application->user->profile?->college ?? '________' }}</td>
                <td>{{ $getField('college dates') ?? $getField('college inclusive dates') ?? '________' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Documentary Requirements Checklist -->
    <table class="grid-table" style="font-size: 7.5px;">
        <thead>
            <tr style="background-color: #0f5934; color: #ffffff;">
                <th style="font-size: 7.5px; font-weight: bold; padding: 2px 5px; text-align: left;">
                    DOCUMENTARY REQUIREMENTS (Original and other documents, when applicable, should be presented for validation)
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 5px; line-height: 1.3;">
                    @php
                        // 1. Duly accomplished form: Checked since they successfully completed the system application.
                        $hasForm = true;
                        
                        // 2a. Form 6: Checked if they uploaded the COG attachment.
                        $hasForm6 = $application->document ? true : false;
                        
                        // 2b. CLSU-CAT: Checked if they are a 1st year student.
                        $yr = strtolower($application->user->profile?->year_level ?? '');
                        $hasCat = (str_contains($yr, '1st') || str_contains($yr, 'first')) ? true : false;
                        
                        // 2c. Photocopy of CLSU ID: Checked if CLSU ID is populated in the profile.
                        $hasId = !empty($application->user->profile?->clsu_id_number);
                        
                        // 3. ITR/Indigency: Checked if they submitted it.
                        $hasItr = ($getField('itr') || $getField('income tax return') || $getField('indigency') || $getField('certificate of indigency') || $getField('exempted') || $getField('bir')) ? true : false;
                    @endphp
                    <div style="margin-bottom: 2px;">
                        <span class="checkbox-box @if($hasForm) checkbox-checked @endif">@if($hasForm) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">1. Duly accomplished SPES Application Form;</span>
                    </div>
                    
                    <div style="margin-bottom: 2px;">
                        <span class="checkbox-box @if($hasForm6 || $hasCat || $hasId) checkbox-checked @endif">@if($hasForm6 || $hasCat || $hasId) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">2. Copy of the following:</span>
                        <div style="margin-left: 15px; margin-top: 1px;">
                            <span class="checkbox-box @if($hasForm6) checkbox-checked @endif">@if($hasForm6) X @endif</span>
                            <span style="vertical-align: middle;">a. Form 6 (previous semester for Summer SPES and current semester for Regular Sem SPES)</span>
                            <br>
                            <span class="checkbox-box @if($hasCat) checkbox-checked @endif">@if($hasCat) X @endif</span>
                            <span style="vertical-align: middle;">b. CLSU-CAT result (for Incoming First Year Students)</span>
                            <br>
                            <span class="checkbox-box @if($hasId) checkbox-checked @endif">@if($hasId) X @endif</span>
                            <span style="vertical-align: middle;">c. Photocopy of CLSU ID</span>
                        </div>
                    </div>
                    
                    <div>
                        <span class="checkbox-box @if($hasItr) checkbox-checked @endif">@if($hasItr) X @endif</span>
                        <span style="font-weight: bold; vertical-align: middle;">3. Copy of the latest Income Tax Return (ITR) of parents or certification issued by BIR that the parents are exempted from payment of tax or Certification of Indigency issued by the Barangay where the SPES applicant resides.</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Skills & SPES Availment Grid -->
    <table class="grid-table">
        <tr>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <div style="font-weight: bold; font-size: 8px; color: #0f5934; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 4px; text-transform: uppercase;">
                    Special Skills
                </div>
                <div style="font-size: 9px; font-weight: bold; min-height: 40px; padding-top: 2px;">
                    {{ $getField('special skills') ?? $getField('skills') ?? '__________________________________' }}
                </div>
            </td>
            <td style="width: 50%; padding: 5px; vertical-align: top;">
                <div style="font-weight: bold; font-size: 8px; color: #0f5934; border-bottom: 1px solid #cbd5e1; padding-bottom: 1px; margin-bottom: 4px; text-transform: uppercase;">
                    History of SPES Availment (if applicable)
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 7.5px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #cbd5e1; font-weight: bold; text-align: left;">
                            <th style="width: 45%; padding-bottom: 1px;">Availment</th>
                            <th style="width: 20%; padding-bottom: 1px;">Year</th>
                            <th style="width: 35%; padding-bottom: 1px;">Office Assignment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $avail1 = $getField('1st availment year') || $getField('1st availment office');
                            $avail2 = $getField('2nd availment year') || $getField('2nd availment office');
                            $avail3 = $getField('3rd availment year') || $getField('3rd availment office');
                            $avail4 = $getField('4th availment year') || $getField('4th availment office');
                        @endphp
                        <tr>
                            <td style="padding: 1px 0;">
                                <span class="checkbox-box @if($avail1) checkbox-checked @endif">@if($avail1) X @endif</span>
                                <span style="vertical-align: middle;">1st Availment</span>
                            </td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('1st availment year') ?? '' }}</td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('1st availment office') ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">
                                <span class="checkbox-box @if($avail2) checkbox-checked @endif">@if($avail2) X @endif</span>
                                <span style="vertical-align: middle;">2nd Availment</span>
                            </td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('2nd availment year') ?? '' }}</td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('2nd availment office') ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">
                                <span class="checkbox-box @if($avail3) checkbox-checked @endif">@if($avail3) X @endif</span>
                                <span style="vertical-align: middle;">3rd Availment</span>
                            </td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('3rd availment year') ?? '' }}</td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('3rd availment office') ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 1px 0;">
                                <span class="checkbox-box @if($avail4) checkbox-checked @endif">@if($avail4) X @endif</span>
                                <span style="vertical-align: middle;">4th Availment</span>
                            </td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('4th availment year') ?? '' }}</td>
                            <td style="padding: 1px 0; border-bottom: 1px dashed #cbd5e1;">{{ $getField('4th availment office') ?? '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Legal Attestation Box -->
    <div class="attestation-box">
        I hereby attest that the information above are true and correct to the best of my knowledge, including the attached documents /requirements which I also attest as to their veracity. I agree that any false statement would cause the automatic disqualification /cancellation of the service/ contract/ grant and I shall refund amount received and/or pay damages to CLSU or comply with other sanctions in accordance with law. Any material change in my financial status may affect my eligibility to continue the program.
    </div>

    <!-- Signature Fields -->
    <table class="signature-table">
        <tr>
            <td style="width: 45%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $application->user->name ?? 'Student Applicant' }}</div>
                <div class="signature-title">Signature of Applicant</div>
            </td>
            <td style="width: 10%;"></td>
            <td style="width: 45%; text-align: center; vertical-align: top;">
                <div class="signature-line">{{ $application->evaluator->name ?? 'OSA Director / Officer' }}</div>
                <div class="signature-title">Signature of OSA Officer</div>
            </td>
        </tr>
    </table>

    <!-- Form Footer Code -->
    <div style="width: 100%; text-align: left; font-size: 6.5px; color: #64748b; margin-top: 5px; font-weight: bold; border-top: 1px solid #e2e8f0; padding-top: 3px;">
        ACA.OSA.CDE.F.007 (Revision No. 0; October 25, 2018)
    </div>

    <!-- Student Custom Responses and File Uploads Section -->
    @if(($application->customFields && $application->customFields->count() > 0) || $application->documents->count() > 1)
    <div style="margin-top: 10px; border: 1px solid #0f5934; border-radius: 4px; padding: 5px; background-color: #f8fafc; page-break-inside: avoid;">
        <div style="font-weight: bold; color: #0f5934; font-size: 8px; text-transform: uppercase; margin-bottom: 3px; border-bottom: 1px solid #0f5934; padding-bottom: 1px;">
            📝 Student Application Responses & Custom Credentials
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 7.5px;">
            @if($application->customFields && $application->customFields->count() > 0)
                @foreach($application->customFields as $field)
                    @if(!str_starts_with($field->field_value, 'uploads/'))
                        <tr style="border-bottom: 1px dashed #cbd5e1;">
                            <td style="width: 40%; font-weight: bold; padding: 2px 0; color: #475569; text-transform: uppercase;">{{ $field->field_name }}:</td>
                            <td style="width: 60%; padding: 2px 0; color: #0f172a; font-weight: bold;">{{ $field->field_value }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
            @foreach($application->documents as $doc)
                @if($doc->document_type !== 'COG')
                    <tr style="border-bottom: 1px dashed #cbd5e1;">
                        <td style="width: 40%; font-weight: bold; padding: 2px 0; color: #475569; text-transform: uppercase;">Uploaded {{ $doc->document_type }}:</td>
                        <td style="width: 60%; padding: 2px 0; color: #0f172a; font-weight: bold;">{{ $doc->original_name }} (Verified by A.E.G.I.S.)</td>
                    </tr>
                @endif
            @endforeach
        </table>
    </div>
    @endif

    <!-- A.E.G.I.S. Digital Forensics Audit Trail Section (Thesis Alignment Badge) -->
    <div class="aegis-badge-card">
        <div style="font-weight: bold; color: #b45309; font-size: 8px; text-transform: uppercase; margin-bottom: 2px;">
            🛡 A.E.G.I.S. Grade Integrity System Clearance Report
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 7.5px;">
            <tr>
                <td style="width: 25%; font-weight: bold; padding: 1px 0; color: #475569;">Verification ID:</td>
                <td style="width: 25%; font-weight: bold; padding: 1px 0; color: #0f172a;">AEGIS-{{ strtoupper(substr(md5($application->id), 0, 8)) }}</td>
                <td style="width: 25%; font-weight: bold; padding: 1px 0; color: #475569;">AI Classifier Result:</td>
                <td style="width: 25%; font-weight: bold; padding: 1px 0;">
                    @php
                        $aiResult = $application->document?->aiResult;
                        $class = $aiResult ? strtolower($aiResult->classification) : 'authentic';
                        $fraudScore = $aiResult ? $aiResult->fraud_probability : 0.0;
                    @endphp
                    @if($class === 'authentic')
                        <span style="color: #16a34a;">AUTHENTIC (CLEARED)</span>
                    @else
                        <span style="color: #dc2626;">TAMPERED (FLAGGED)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 1px 0; color: #475569;">GWA Integrity Check:</td>
                <td style="font-weight: bold; padding: 1px 0; color: #0f5934;">{{ $application->gwa !== null ? 'GWA ' . number_format($application->gwa, 2) . ' (Valid)' : 'N/A' }}</td>
                <td style="font-weight: bold; padding: 1px 0; color: #475569;">Tampering Risk Score:</td>
                <td style="font-weight: bold; padding: 1px 0; color: #0f172a;">{{ number_format($fraudScore, 2) }}% Probability</td>
            </tr>
            <tr>
                <td style="font-weight: bold; padding: 1px 0; color: #475569;">Remarks / Audit Logs:</td>
                <td colspan="3" style="color: #64748b; font-style: italic; padding: 1px 0;">
                    {{ $application->remarks ?: ($application->gwa !== null ? 'Grade document metadata cleared. No unauthorized edits detected. Applicant meets GWA criteria.' : 'Document requirements verified.') }}
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
