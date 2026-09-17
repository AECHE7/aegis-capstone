@extends('emails.layout')

@section('subject', "[A.E.G.I.S.] Official Notice: Scholarship Grant Revocation (" . $application->program_name . ")")
@section('preheader', "Official notification regarding the termination/revocation of your scholarship grant.")

@section('content')
    <div style="text-align: center; margin-bottom: 24px;">
        <div style="display: inline-block; width: 64px; height: 64px; border-radius: 50%; background-color: #fee2e2; line-height: 64px; margin-bottom: 12px;">
            <span style="font-size: 30px; color: #dc2626;">&#10007;</span>
        </div>
        <h2 style="color: #991b1b; margin-bottom: 6px;">Notice of Scholarship Grant Revocation</h2>
        <span style="display: inline-block; padding: 6px 18px; border-radius: 50px; font-weight: bold; color: #ffffff; background-color: #dc2626; font-size: 13px; letter-spacing: 0.5px;">GRANT REVOKED / REMOVED</span>
    </div>

    <p>Dear <strong>{{ $application->user->name ?? 'Student Scholar' }}</strong>,</p>

    <p>This is an official communication from the <strong>Central Luzon State University Office of Student Affairs (OSA)</strong>. Following administrative compliance review and scholarship evaluation board guidelines, your scholarship grant under the <strong>{{ $application->program_name }}</strong> has been officially <strong>REVOKED</strong>.</p>

    <div style="margin: 24px 0; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px;">
        <h4 style="margin-top: 0; color: #991b1b; font-size: 14px; border-bottom: 2px solid #ef4444; padding-bottom: 6px; text-transform: uppercase; font-weight: bold;">
            Revocation Details & Cause
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
                <td style="font-weight: 700; color: #991b1b;">{{ $application->program_name }}</td>
            </tr>
            <tr>
                <td style="color: #64748b;">Reference Number:</td>
                <td style="font-weight: 700; font-family: monospace; color: #1e293b;">APP-{{ $application->id }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; vertical-align: top;">Official Reason for Revocation:</td>
                <td style="font-weight: 700; color: #b91c1c;">{{ $reason }}</td>
            </tr>
        </table>
    </div>

    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 14px 18px; border-radius: 8px; margin: 20px 0;">
        <h5 style="margin: 0 0 6px; color: #334155; font-size: 13px; font-weight: bold;">Right to Appeal & Clarification:</h5>
        <p style="margin: 0; font-size: 12.5px; color: #64748b; line-height: 1.5;">
            If you believe this revocation was made in error or have documented mitigating circumstances regarding your academic standing or document submission, you may file a written appeal to the Office of Student Affairs within five (5) working days from the receipt of this notice.
        </p>
    </div>

    <div class="cta-container">
        <a href="{{ route('student.dashboard') }}" class="btn" style="background-color: #475569;">Access Student Dashboard</a>
    </div>
@endsection
