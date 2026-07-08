@extends('emails.layout')

@section('subject', '[A.E.G.I.S.] Staff Account Invitation')

@section('content')
    <h2>Staff Account Invitation</h2>
    <p>Hello {{ $name }},</p>
    <p>You have been invited by the Super Admin to join the CLSU A.E.G.I.S. Portal as an OSA Staff member.</p>
    <p>Please click the button below to set up your password and activate your account:</p>
    
    <div class="cta-container">
        <a href="{{ $activationUrl }}" class="btn btn-gold">Activate Account</a>
    </div>
    
    <p>This invitation link will expire in 3 days. If you did not expect this invitation, no further action is required.</p>
@endsection
