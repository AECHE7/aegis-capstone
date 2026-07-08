@extends('emails.layout')

@section('subject', '[A.E.G.I.S.] Verification Code for Login')

@section('content')
    <h2>Multi-Factor Authentication Code</h2>
    <p>Hello,</p>
    <p>A sign-in request was made to your A.E.G.I.S. account. To proceed, please use the following 6-digit verification code:</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <span style="display: inline-block; padding: 15px 40px; background-color: #f1f5f9; border: 2px dashed #0C4E2D; color: #0C4E2D; font-size: 32px; font-weight: bold; letter-spacing: 5px; border-radius: 8px; font-family: monospace;">
            {{ $otp }}
        </span>
    </div>
    
    <p>This code is valid for <strong>10 minutes</strong>. If you did not make this request, you can safely ignore this email.</p>
@endsection
