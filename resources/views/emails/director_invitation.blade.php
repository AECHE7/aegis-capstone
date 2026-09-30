@extends('emails.layout')

@section('subject', 'You\'re Invited to Become the A.E.G.I.S. System Director')

@section('preheader', 'You have been invited to take ownership of the A.E.G.I.S. Scholarship Portal as OSA Director.')

@section('content')
    <h2>🎓 Director Role Invitation</h2>

    <p>Hello{{ $recipientName ? ', <strong>' . e($recipientName) . '</strong>' : '' }},</p>

    <p>
        You have been personally invited to take ownership of the <strong>A.E.G.I.S. Scholarship Management Portal</strong>
        as the <strong>OSA Director (System Owner)</strong>.
    </p>

    <div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 16px 18px; margin: 20px 0; border-radius: 6px; font-size: 14.5px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="color: #64748b; font-size: 13px; padding: 3px 0; width: 120px;">Invited by:</td>
                <td style="color: #0f172a; font-weight: 600; font-size: 13px; padding: 3px 0;">{{ $senderEmail }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; font-size: 13px; padding: 3px 0;">Your email:</td>
                <td style="color: #0f172a; font-weight: 600; font-size: 13px; padding: 3px 0;">{{ $recipientEmail }}</td>
            </tr>
            <tr>
                <td style="color: #64748b; font-size: 13px; padding: 3px 0;">Role granted:</td>
                <td style="font-size: 13px; padding: 3px 0;"><span style="background: #0C4E2D; color: white; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">OSA DIRECTOR · SUPERADMIN</span></td>
            </tr>
            <tr>
                <td style="color: #64748b; font-size: 13px; padding: 3px 0;">Link expires:</td>
                <td style="color: #dc2626; font-weight: 600; font-size: 13px; padding: 3px 0;">48 hours from now</td>
            </tr>
        </table>
    </div>

    <p>As the OSA Director, you will have full governance access including:</p>
    <ul style="margin: 0 0 16px; padding-left: 22px; line-height: 2;">
        <li>Scholarship program configuration &amp; management</li>
        <li>Staff (OSA Administrator) invitation &amp; management</li>
        <li>System-wide analytics &amp; reporting dashboards</li>
        <li>Full compliance &amp; audit log export access</li>
        <li>Global system settings and security controls</li>
    </ul>

    <div style="background-color: #fffbeb; border-left: 4px solid #D97706; padding: 14px 16px; margin: 20px 0; border-radius: 6px; font-size: 13.5px; color: #78350f;">
        <strong>⚠ Important:</strong> You must log in (or register a new account) using exactly
        <strong>{{ $recipientEmail }}</strong> to accept this invitation.
        Attempting to accept with a different email will be blocked.
    </div>

    <div class="cta-container">
        <a href="{{ $acceptUrl }}" class="btn" style="background: linear-gradient(135deg, #0C4E2D, #07331c); font-size: 16px; padding: 15px 40px;">
            Accept Director Role &rarr;
        </a>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
        If the button above does not work, copy and paste this link into your browser:<br>
        <a href="{{ $acceptUrl }}" style="word-break: break-all; color: #0C4E2D; font-size: 12.5px;">{{ $acceptUrl }}</a>
    </p>

    <p style="font-size: 13px; color: #94a3b8;">
        If you were not expecting this invitation, you can safely ignore this email.
        This link will automatically expire in 48 hours.
    </p>
@endsection
