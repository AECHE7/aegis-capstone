@extends('emails.layout')

@section('subject', "[A.E.G.I.S.] Scholarship Grant Successfully Renewed: " . $application->program_name)
@section('preheader', "Your renewal application for " . $application->program_name . " has been officially approved.")

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="display: inline-block; width: 64px; height: 64px; border-radius: 50%; background-color: #e8f5e9; line-height: 64px; margin-bottom: 12px;">
            <span style="font-size: 32px; color: #15803d;">&#10003;</span>
        </div>
        <h2 style="color: #0C4E2D; margin-bottom: 6px;">Scholarship Grant Renewed!</h2>
        <span style="display: inline-block; padding: 6px 18px; border-radius: 50px; font-weight: bold; color: #ffffff; background-color: #15803d; font-size: 13px; letter-spacing: 0.5px;">RENEWAL APPROVED</span>
    </div>

    <p>Dear <strong>{{ $application->user->name ?? 'Applicant' }}</strong>,</p>

    <p>We are pleased to inform you that your continuing scholarship renewal application for the <strong>{{ $application->program_name }}</strong> has been officially evaluated and <strong>APPROVED</strong> for the incoming term.</p>

    <div style="margin: 24px 0; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
        <h4 style="margin-top: 0; color: #0C4E2D; font-size: 14px; border-bottom: 2px solid #D97706; padding-bottom: 6px; text-transform: uppercase; font-weight: bold;">
            Renewal Grant Summary
        </h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; line-height: 1.8;">
            <tr>
                <td style="color: #64748b; width: 45%;">Student Name:</td>
                <td style="font-weight: 700; color: #1e293b;">{{ $application->user->name }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">CLSU Student ID:</td>
                <td style="font-weight: 700; font-family: monospace; color: #1e293b;">{{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Program / Grant:</td>
                <td style="font-weight: 700; color: #0C4E2D;">{{ $application->program_name }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Verified GWA:</td>
                <td style="font-weight: 700; font-family: monospace; color: #1e293b;">{{ $application->gwa !== null ? number_format($application->gwa, 2) : 'N/A' }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Reference Number:</td>
                <td style="font-weight: 700; font-family: monospace; color: #1e293b;">APP-{{ $application->id }} (Renewal)</td>
            </tr>
            @if($application->academicTerm)
            <tr>
                <td style="color: #64748b;">Effective Term:</td>
                <td style="font-weight: 700; color: #1e293b;">{{ $application->academicTerm->semester }} (A.Y. {{ $application->academicTerm->academic_year }})</td>
            </tr>
            @endif
        </table>
    </div>

    <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 14px 18px; border-radius: 8px; margin: 20px 0;">
        <p style="margin: 0; font-size: 13.5px; color: #065f46; line-height: 1.5;">
            <strong>Next Steps & Stipend Claim:</strong> Please find attached your official <strong>Renewal Evaluation Form (PDF)</strong>. Print a copy and present it along with your validated CLSU ID to the Office of Student Affairs (OSA) for grant release and payroll processing.
        </p>
    </div>

    @if($application->remarks)
        <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="color: #92400e; font-size: 13px;">Evaluator Remarks:</strong>
            <p style="margin: 4px 0 0; color: #78350f; font-size: 13px;">{{ $application->remarks }}</p>
        </div>
    @endif

    <div class="cta-container">
        <a href="{{ route('student.dashboard') }}" class="btn">View Student Portal Dashboard</a>
    </div>
@endsection
