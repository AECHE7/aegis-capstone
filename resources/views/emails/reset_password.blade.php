@extends('emails.layout')

@section('subject', '[A.E.G.I.S.] Reset Your Password')

@section('content')
    <h2>Password Reset Request</h2>
    <p>Hello {{ $name }},</p>
    <p>We received a request to reset the password for your <strong>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</strong> account associated with this email address.</p>
    <p>Click the button below to choose a new secure password:</p>
    
    <div class="cta-container">
        <a href="{{ $resetUrl }}" class="btn">Reset Password</a>
    </div>
    
    <div style="background-color: #f8fafc; border-left: 4px solid #D97706; padding: 14px 18px; border-radius: 6px; margin: 24px 0; font-size: 14px; color: #475569;">
        <p style="margin: 0 0 6px; font-weight: 600; color: #0C4E2D;">🔒 Security Notice</p>
        <p style="margin: 0; font-size: 13px;">This password reset link will expire in <strong>60 minutes</strong>. If you did not request a password reset, no further action is required and your account remains completely secure.</p>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
        If you are having trouble clicking the "Reset Password" button, copy and paste the following URL into your web browser:<br>
        <a href="{{ $resetUrl }}" style="color: #0C4E2D; word-break: break-all; font-family: monospace; font-size: 12px;">{{ $resetUrl }}</a>
    </p>
@endsection
