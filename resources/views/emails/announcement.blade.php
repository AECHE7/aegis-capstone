@extends('emails.layout')

@section('subject', "[A.E.G.I.S.] Official Announcement: " . $title)

@section('content')
    <h2>Official Announcement</h2>
    <p>Dear Scholars and Applicants,</p>
    <p>An announcement has been published by the Office of Student Affairs:</p>
    
    <div style="margin: 25px 0; background-color: #f8fafc; border-left: 4px solid #D97706; padding: 20px; border-radius: 0 8px 8px 0;">
        <h3 style="margin-top: 0; color: #0C4E2D; font-size: 18px;">{{ $title }}</h3>
        <p style="margin-bottom: 0; font-size: 14.5px; white-space: pre-line; color: #334155;">{{ $content }}</p>
    </div>
    
    <p>Please log in to the portal if you need to view more details or complete any related actions.</p>
    
    <div class="cta-container">
        <a href="{{ url('/') }}" class="btn">Go to Portal</a>
    </div>
@endsection
