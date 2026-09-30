@php
    // Helper to query custom application fields (fuzzy matching)
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

<div class="applicant-form-sheet bg-white p-4 p-md-5 mx-auto" style="max-width: 860px; font-family: 'Arial', sans-serif; color: #0f172a; line-height: 1.3; font-size: 0.88rem; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-radius: 8px; border: 1px solid #e2e8f0;">
    
    {{-- Header with Institutional Details and Logos --}}
    <div class="row align-items-center pb-3 mb-3 border-bottom" style="border-bottom-color: #cbd5e1 !important;">
        <div class="col-2 text-center">
            @if(file_exists(public_path('images/clsu-seal.png')))
                <img src="{{ asset('images/clsu-seal.png') }}" style="max-height: 68px; width: auto;" alt="CLSU Seal">
            @elseif(file_exists(public_path('logo.png')))
                <img src="{{ asset('logo.png') }}" style="max-height: 68px; width: auto;" alt="CLSU Logo">
            @else
                <div class="rounded-circle border border-2 border-success d-flex align-items-center justify-content-center text-success fw-bold mx-auto" style="width: 58px; height: 58px; font-size: 0.9rem;">CLSU</div>
            @endif
        </div>
        <div class="col-8 text-center">
            <div class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">Republic of the Philippines</div>
            <div class="fw-bold text-uppercase" style="font-size: 1.05rem; color: #0f5934; letter-spacing: 0.5px;">Central Luzon State University</div>
            <div class="text-muted" style="font-size: 0.72rem;">Science City of Muñoz, Nueva Ecija, Philippines</div>
            <div class="fw-bold text-uppercase mt-1" style="font-size: 0.88rem; color: #0f5934;">Office of Student Affairs</div>
            <div class="text-muted" style="font-size: 0.72rem;">Student Welfare and Scholarship Services Division</div>
        </div>
        <div class="col-2 text-center">
            @if(file_exists(public_path('images/osa-seal.png')))
                <img src="{{ asset('images/osa-seal.png') }}" style="max-height: 68px; width: auto;" alt="OSA Seal">
            @else
                <div class="rounded-circle border border-2 border-warning d-flex align-items-center justify-content-center text-warning fw-bold mx-auto" style="width: 58px; height: 58px; font-size: 0.9rem;">OSA</div>
            @endif
        </div>
    </div>

    {{-- Title Table & 2x2 Photo --}}
    <div class="border border-2 border-success mb-3 p-3 rounded" style="background-color: #f8fafc; border-color: #0f5934 !important;">
        <div class="row align-items-center">
            <div class="col-9 pe-3 border-end">
                <h5 class="fw-bold text-uppercase mb-1" style="color: #0f5934; font-size: 1.05rem; letter-spacing: 0.5px;">
                    {{ $programName }} APPLICATION & EVALUATION FORM
                </h5>
                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small my-2" style="font-size: 0.76rem;">
                    <div><strong>Academic Term:</strong> {{ $termLabel }}</div>
                    <div>&bull;</div>
                    <div><strong>Control No:</strong> <span class="badge bg-dark">{{ $controlNo }}</span></div>
                    <div>&bull;</div>
                    <div>
                        <strong>Status:</strong>
                        <span class="px-2 py-0.5 rounded fw-bold text-uppercase" style="background-color: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }}; font-size: 0.75rem;">
                            {{ $application->status }}
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4 mt-2">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" disabled @if($isNew) checked @endif id="chkNew">
                        <label class="form-check-label fw-bold text-dark small" for="chkNew">NEW APPLICANT</label>
                    </div>
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" disabled @if($isRenewal) checked @endif id="chkRenewal">
                        <label class="form-check-label fw-bold text-dark small" for="chkRenewal">RENEWAL APPLICANT</label>
                    </div>
                </div>
            </div>
            <div class="col-3 text-center">
                <div class="border border-secondary border-dashed rounded d-flex flex-column align-items-center justify-content-center mx-auto bg-white" style="width: 85px; height: 85px;">
                    <i class="fa-solid fa-user text-muted opacity-50 mb-1" style="font-size: 1.5rem;"></i>
                    <span class="text-muted fw-bold" style="font-size: 0.65rem;">2X2 PHOTO<br>Passport / ID</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Section I: Personal & Academic Information --}}
    <div class="mb-3">
        <div class="text-white fw-bold px-2 py-1 text-uppercase mb-0 rounded-top" style="background-color: #0f5934; font-size: 0.78rem; letter-spacing: 0.5px;">
            I. Personal & Academic Information
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="border-color: #0f5934; font-size: 0.8rem;">
                <tbody>
                    <tr>
                        <td style="width: 33.3%;">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Surname</span>
                            <span class="fw-bold text-dark">{{ $lastName ?: '—' }}</span>
                        </td>
                        <td style="width: 33.3%;">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">First Name</span>
                            <span class="fw-bold text-dark">{{ $firstName ?: '—' }}</span>
                        </td>
                        <td style="width: 33.3%;">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Middle Name</span>
                            <span class="fw-bold text-dark">{{ $middleName ?: '—' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">CLSU Student ID</span>
                            <span class="fw-bold" style="color: #0f5934;">
                                {{ $application->user->profile?->clsu_id_number ?? $getField('clsu id number') ?? $getField('student id') ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">College</span>
                            <span class="fw-bold text-dark">
                                {{ $application->user->profile?->college ?? $getField('college') ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Course & Year Level</span>
                            <span class="fw-bold text-dark">
                                {{ trim(($application->user->profile?->course ?? $getField('course') ?? '') . ' - ' . ($application->user->profile?->year_level ?? $getField('year level') ?? '')) ?: '—' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Sex</span>
                            @php
                                $sex = strtolower($application->user->profile?->gender ?? $getField('sex') ?? $getField('gender') ?? '');
                                $isMale = str_contains($sex, 'male') && !str_contains($sex, 'female');
                                $isFemale = str_contains($sex, 'female');
                            @endphp
                            <span class="fw-bold text-dark">
                                @if($isMale) Male @elseif($isFemale) Female @else {{ $sex ? ucfirst($sex) : '—' }} @endif
                            </span>
                        </td>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Civil Status</span>
                            @php
                                $civil = strtolower($application->user->profile?->civil_status ?? $getField('civil status') ?? $getField('status') ?? '');
                            @endphp
                            <span class="fw-bold text-dark">{{ $civil ? ucfirst($civil) : 'Single' }}</span>
                        </td>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">General Weighted Average (GWA)</span>
                            <span class="fw-bold" style="color: #0f5934;">
                                {{ $application->gwa !== null ? number_format((float)$application->gwa, 2) . ' (Verified)' : 'Pending Evaluation' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Contact Number</span>
                            <span class="fw-bold text-dark">{{ $application->user->profile?->contact_number ?? $getField('contact no') ?? '—' }}</span>
                        </td>
                        <td colspan="2">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Email Address</span>
                            <span class="fw-bold text-dark">{{ $application->user->email ?? '—' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Permanent Home Address</span>
                            <span class="fw-bold text-dark">
                                {{ $getField('home address') ?? $getField('permanent address') ?? $getField('address') ?? $application->user->profile?->address ?? '—' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section II: Family Background & Emergency Contact --}}
    <div class="mb-3">
        <div class="text-white fw-bold px-2 py-1 text-uppercase mb-0 rounded-top" style="background-color: #0f5934; font-size: 0.78rem; letter-spacing: 0.5px;">
            II. Family Background & Emergency Contact
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="border-color: #0f5934; font-size: 0.8rem;">
                <tbody>
                    <tr>
                        <td style="width: 50%;">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Father / Guardian Name</span>
                            <span class="fw-bold text-dark">{{ $application->user->profile?->guardian_name ?? $getField('name of father') ?? $getField('father name') ?? $getField('guardian') ?? '—' }}</span>
                        </td>
                        <td style="width: 50%;">
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Occupation / Monthly Income</span>
                            <span class="fw-bold text-dark">{{ $getField('father occupation') ?? $getField('occupation of father') ?? $getField('family income') ?? '—' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Mother's Maiden Name</span>
                            <span class="fw-bold text-dark">{{ $getField('name of mother') ?? $getField('mother name') ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="text-muted text-uppercase d-block" style="font-size: 0.68rem; font-weight: 700;">Emergency Contact Number</span>
                            <span class="fw-bold text-dark">{{ $application->user->profile?->emergency_contact_number ?? $getField('emergency contact') ?? '—' }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Section III: Documentary Requirements Checklist --}}
    <div class="mb-3">
        <div class="text-white fw-bold px-2 py-1 text-uppercase mb-0 rounded-top" style="background-color: #0f5934; font-size: 0.78rem; letter-spacing: 0.5px;">
            III. Documentary Requirements & Verification Checklist
        </div>
        <div class="border border-success p-3 rounded-bottom bg-white" style="border-color: #0f5934 !important; font-size: 0.8rem;">
            @php
                $hasCog = (bool)$application->document;
                $hasId = !empty($application->user->profile?->clsu_id_number);
                $hasItr = (bool)($getField('itr') || $getField('income') || $getField('indigency') || $getField('tax'));
            @endphp
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-check-circle text-success"></i>
                        <span><strong>1. Duly accomplished Application Record</strong> (AEGIS Verified)</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid {{ $hasCog ? 'fa-check-circle text-success' : 'fa-circle-xmark text-danger' }}"></i>
                        <span><strong>2. Official Certificate of Grades (COG)</strong> {{ $hasCog ? '(Submitted)' : '(Pending)' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid {{ $hasId ? 'fa-check-circle text-success' : 'fa-circle-xmark text-danger' }}"></i>
                        <span><strong>3. Valid CLSU Student Identification</strong> {{ $hasId ? '(Verified)' : '(Pending)' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid {{ $hasItr ? 'fa-check-circle text-success' : 'fa-circle-notch text-muted' }}"></i>
                        <span><strong>4. Proof of Income / Indigency</strong> {{ $hasItr ? '(Attached)' : '(If applicable)' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section IV: Custom Scholarship Program Parameters & Custom Fields --}}
    @if($application->customFields && $application->customFields->count() > 0)
    <div class="mb-3">
        <div class="text-white fw-bold px-2 py-1 text-uppercase mb-0 rounded-top" style="background-color: #0f5934; font-size: 0.78rem; letter-spacing: 0.5px;">
            IV. Program-Specific Application Parameters & Question Responses
        </div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0" style="border-color: #0f5934; font-size: 0.78rem;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 45%;">Parameter / Question</th>
                        <th style="width: 55%;">Applicant Response</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($application->customFields as $field)
                        <tr>
                            <td class="fw-semibold text-secondary">{{ $field->field_label ?? ucwords(str_replace(['_', '-'], ' ', $field->field_name)) }}</td>
                            <td class="fw-bold text-dark">{{ $field->field_value ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Legal Attestation Box --}}
    <div class="p-3 mb-3 border rounded text-muted bg-light" style="font-size: 0.74rem; line-height: 1.35; text-align: justify;">
        <strong class="text-dark">OATH OF VERACITY & COMPLIANCE:</strong> I hereby certify on my honor that all entries, statements, and supporting documents attached to this application are true, correct, and authentic. I understand that any false declaration or fraudulent modification discovered in this submission shall warrant automatic disqualification, forfeiture of scholarship benefits, restitution of funds received, and administrative sanctions under the Central Luzon State University Student Handbook and the Philippine Data Privacy Act (R.A. 10173).
    </div>

    {{-- Signatures Block --}}
    <div class="row pt-2 pb-3 text-center align-items-end">
        <div class="col-4">
            <div class="border-bottom border-dark pb-1 fw-bold text-dark" style="font-size: 0.85rem;">
                {{ $application->user->name ?? 'Student Applicant' }}
            </div>
            <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.68rem;">Signature of Student Grantee</small>
        </div>
        <div class="col-4">
            <div class="border-bottom border-dark pb-1 fw-bold text-dark" style="font-size: 0.85rem;">
                {{ $evaluatorName }}
            </div>
            <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.68rem;">Verified by OSA Evaluator / Staff</small>
        </div>
        <div class="col-4">
            <div class="border-bottom border-dark pb-1 fw-bold text-dark" style="font-size: 0.85rem;">
                {{ $directorName }}
            </div>
            <small class="text-muted text-uppercase fw-semibold d-block" style="font-size: 0.68rem;">
                @if(strtolower($application->status) === 'approved')
                    Approved by: Director, OSA
                @elseif(in_array(strtolower($application->status), ['rejected', 'disapproved']))
                    Disapproved by: Director, OSA
                @else
                    Endorsed by: Director, OSA
                @endif
            </small>
        </div>
    </div>

    {{-- A.E.G.I.S. Institutional Security & AI Verification Clearance Card --}}
    <div class="p-3 rounded border border-warning" style="background-color: #fffbeb;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="fw-bold text-uppercase small" style="color: #b45309; font-size: 0.76rem;">
                <i class="fa-solid fa-shield-halved me-1"></i> A.E.G.I.S. Institutional Security & AI Verification Clearance
            </div>
            <span class="badge bg-warning text-dark" style="font-size: 0.68rem;">Tamper-Evident</span>
        </div>
        <div class="row g-2 text-dark" style="font-size: 0.74rem;">
            <div class="col-md-3">
                <span class="text-muted d-block" style="font-size: 0.66rem;">Verification Hash</span>
                <strong>AEGIS-{{ strtoupper(substr(md5($application->id . $application->created_at), 0, 10)) }}</strong>
            </div>
            <div class="col-md-3">
                <span class="text-muted d-block" style="font-size: 0.66rem;">Document Forensic Status</span>
                @php
                    $aiResult = $application->document?->aiResult;
                    $class = $aiResult ? strtolower($aiResult->classification) : 'authentic';
                @endphp
                @if($class === 'authentic')
                    <span class="text-success fw-bold"><i class="fa-solid fa-check-circle me-1"></i> AUTHENTIC (CLEARED)</span>
                @else
                    <span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> FLAGGED (REVIEWED)</span>
                @endif
            </div>
            <div class="col-md-3">
                <span class="text-muted d-block" style="font-size: 0.66rem;">GWA Integrity</span>
                <span class="fw-bold" style="color: #0f5934;">
                    {{ $application->gwa !== null ? 'GWA ' . number_format((float)$application->gwa, 2) . ' (Passed)' : 'Pending' }}
                </span>
            </div>
            <div class="col-md-3">
                <span class="text-muted d-block" style="font-size: 0.66rem;">Date of Clearance</span>
                <strong>{{ $application->updated_at ? $application->updated_at->format('F d, Y') : date('F d, Y') }}</strong>
            </div>
        </div>
    </div>

    {{-- Footnote Revision --}}
    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top text-muted" style="font-size: 0.65rem;">
        <span>CLSU-OSA-AEGIS-FORM-001 (Rev. 2 • ISO/IEC 25010 & R.A. 10173 Compliant)</span>
        <span>A.E.G.I.S. Secure Educational Record Verification</span>
    </div>

</div>
