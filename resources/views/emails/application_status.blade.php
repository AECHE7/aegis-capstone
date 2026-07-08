@extends('emails.layout')

@section('subject', "[A.E.G.I.S.] Official Update: Application " . strtoupper($application->status))

@section('content')
    <h2>Application Status Update</h2>
    <p>Dear Applicant,</p>
    <p>This is an official notification regarding your scholarship application for the <strong>{{ $application->program_name }}</strong>.</p>
    
    <p>After a thorough review and digital forensics verification by the A.E.G.I.S. system, your application status has been updated to:</p>
    
    <div style="text-align: center; margin: 20px 0;">
        @if($application->status == 'Approved')
            <span style="display: inline-block; padding: 10px 20px; border-radius: 50px; font-weight: bold; color: white; background-color: #198754; font-size: 16px; letter-spacing: 0.5px;">APPROVED</span>
            <p style="margin-top: 15px; font-weight: 500;">Congratulations! Your academic documents have been verified as authentic and you are eligible for the grant.</p>
        @elseif($application->status == 'Cancelled' && $application->forfeit_reason)
            <span style="display: inline-block; padding: 10px 20px; border-radius: 50px; font-weight: bold; color: white; background-color: #64748b; font-size: 16px; letter-spacing: 0.5px;">FORFEITED / BACKED OUT</span>
            <p style="margin-top: 15px; font-weight: 500; color: #475569;">You have successfully backed out of the scholarship program. Your slots have been released. Reason: <em>{{ $application->forfeit_reason }}</em></p>
        @else
            <span style="display: inline-block; padding: 10px 20px; border-radius: 50px; font-weight: bold; color: white; background-color: #dc3545; font-size: 16px; letter-spacing: 0.5px;">REJECTED</span>
            <p style="margin-top: 15px; font-weight: 500; color: #7f1d1d;">Unfortunately, your application was denied. If your document flagged high for digital forgery, you may face disciplinary action. Please contact the OSA immediately.</p>
        @endif
    </div>

    @if($application->status == 'Approved')
        <div style="margin-top: 25px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; text-align: left;">
            <h3 style="margin-top: 0; color: #0C4E2D; font-size: 15px; border-bottom: 2px solid #D97706; padding-bottom: 5px; text-transform: uppercase; font-weight: bold;">Submitted Credentials & Responses</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #333333;">
                <tr style="border-bottom: 1px solid #eeeeee;">
                    <td style="padding: 8px 0; font-weight: bold; color: #64748b; width: 40%;">Student Name:</td>
                    <td style="padding: 8px 0; font-weight: bold; color: #1e293b;">{{ $application->user->name ?? 'N/A' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eeeeee;">
                    <td style="padding: 8px 0; font-weight: bold; color: #64748b;">Student ID Number:</td>
                    <td style="padding: 8px 0; font-weight: bold; font-family: monospace; color: #1e293b;">{{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eeeeee;">
                    <td style="padding: 8px 0; font-weight: bold; color: #64748b;">Course & Year Level:</td>
                    <td style="padding: 8px 0; font-weight: bold; color: #1e293b;">{{ $application->user->profile?->course ?? 'N/A' }} - {{ $application->user->profile?->year_level ?? 'N/A' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #eeeeee;">
                    <td style="padding: 8px 0; font-weight: bold; color: #64748b;">Declared GWA:</td>
                    <td style="padding: 8px 0; font-weight: bold; font-family: monospace; color: #1e293b;">{{ $application->gwa !== null ? number_format($application->gwa, 2) : 'N/A' }}</td>
                </tr>
                @if($application->customFields && $application->customFields->count() > 0)
                    @foreach($application->customFields as $field)
                        @if(!str_starts_with($field->field_value, 'uploads/'))
                            <tr style="border-bottom: 1px solid #eeeeee;">
                                <td style="padding: 8px 0; font-weight: bold; color: #64748b; text-transform: capitalize;">{{ $field->field_name }}:</td>
                                <td style="padding: 8px 0; font-weight: bold; color: #1e293b;">{{ $field->field_value }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            </table>
        </div>
    @endif

    <p style="margin-top: 30px; font-size: 14px; color: #64748b;">
        Reference ID: <strong style="font-family: monospace; color: #334155;">APP-{{ $application->id }}</strong><br>
        Declared GWA: <strong style="font-family: monospace; color: #334155;">{{ $application->gwa !== null ? number_format($application->gwa, 2) : 'N/A' }}</strong>
    </p>
@endsection