@extends('emails.layout')

@section('subject', 'A.E.G.I.S. Portal — Master Privilege Transfer Request')

@section('content')
    <h2>Master Account Privilege Transfer</h2>
    <p>Hello,</p>
    <p>The A.E.G.I.S. Portal Master account (<strong>{{ $senderEmail }}</strong>) has initiated a request to transfer the Master privilege to your email address (<strong>{{ $recipientEmail }}</strong>).</p>
    
    <div style="background-color: #fffbeb; border-left: 4px solid #D97706; padding: 15px; margin: 20px 0; border-radius: 4px; font-size: 14.5px;">
        <strong style="color: #b45309;"><i class="fa-solid fa-triangle-exclamation"></i> Important Notice:</strong>
        <p style="margin: 5px 0 0 0; color: #451a03; line-height: 1.45;">
            Accepting this transfer will immediately make your account the new system Master, granting you full role-switching privileges. At that exact moment, the original sender's Master privilege will be permanently revoked.
        </p>
    </div>

    <p>Please click the button below to review and accept this request. If you do not have an account yet, you will be prompted to register first using this email address.</p>

    <div class="cta-container">
        <a href="{{ $acceptUrl }}" class="btn btn-gold">Review & Accept Transfer</a>
    </div>

    <p style="font-size: 13px; color: #64748b; margin-top: 30px;">
        If the button above does not work, copy and paste the following link into your browser: <br>
        <a href="{{ $acceptUrl }}" style="word-break: break-all; color: #0C4E2D;">{{ $acceptUrl }}</a>
    </p>

    <p>If you did not expect this request, you can safely ignore this email.</p>
@endsection
