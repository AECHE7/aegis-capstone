@extends('emails.layout')

@section('subject', '[A.E.G.I.S.] Please Verify Your Email Address')

@section('content')
    <h2>Verify Your Email Address</h2>
    <p>Hello {{ $name }},</p>
    <p>Thank you for registering a student account at the <strong>{{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}</strong> {{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }} Portal.</p>
    <p>To activate your account and start applying for scholarship grants, please verify your email address by clicking the button below:</p>
    
    <div class="cta-container">
        <a href="{{ $verificationUrl }}" class="btn btn-gold">Verify Email Address</a>
    </div>

    <div style="background-color: #f8fafc; border-left: 4px solid #0C4E2D; padding: 14px 18px; border-radius: 6px; margin: 24px 0; font-size: 14px; color: #475569;">
        <p style="margin: 0 0 6px; font-weight: 600; color: #0C4E2D;">🎓 Institutional Verification</p>
        <p style="margin: 0; font-size: 13px;">This email verification link ensures that your CLSU student email address is authentic and authorized to access active grant applications.</p>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
        If you are having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:<br>
        <a href="{{ $verificationUrl }}" style="color: #0C4E2D; word-break: break-all; font-family: monospace; font-size: 12px;">{{ $verificationUrl }}</a>
    </p>
@endsection
