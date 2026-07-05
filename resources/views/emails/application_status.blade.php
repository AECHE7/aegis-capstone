<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f7f6; padding: 20px; }
        .email-container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #0F5934; color: #ffffff; padding: 20px; text-align: center; border-bottom: 4px solid #F2A900; }
        .content { padding: 30px; color: #333333; line-height: 1.6; }
        .status-badge { display: inline-block; padding: 8px 15px; border-radius: 4px; font-weight: bold; color: white; margin: 15px 0; }
        .approved { background-color: #198754; }
        .rejected { background-color: #dc3545; }
        .footer { background-color: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #6c757d; border-top: 1px solid #eeeeee; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2 style="margin: 0;">A.E.G.I.S. Portal</h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Office of Student Affairs | CLSU</p>
        </div>
        
        <div class="content">
            <p>Dear Applicant,</p>
            <p>This is an official notification regarding your scholarship application for the <strong>{{ $application->program_name }}</strong>.</p>
            
            <p>After a thorough review and digital forensics verification by the A.E.G.I.S. system, your application status has been updated to:</p>
            
            <div style="text-align: center;">
                @if($application->status == 'Approved')
                    <span class="status-badge approved">APPROVED</span>
                    <p>Congratulations! Your academic documents have been verified as authentic and you are eligible for the grant.</p>
                @else
                    <span class="status-badge rejected">REJECTED</span>
                    <p>Unfortunately, your application was denied. If your document flagged high for digital forgery, you may face disciplinary action. Please contact the OSA immediately.</p>
                @endif
            </div>

            @if($application->status == 'Approved')
                <div style="margin-top: 25px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; text-align: left;">
                    <h3 style="margin-top: 0; color: #0F5934; font-size: 15px; border-bottom: 2px solid #F2A900; padding-bottom: 5px; text-transform: uppercase; font-weight: bold;">Submitted Credentials & Responses</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 13px; color: #333333;">
                        <tr style="border-bottom: 1px solid #eeeeee;">
                            <td style="padding: 6px 0; font-weight: bold; color: #6c757d; width: 40%;">Student Name:</td>
                            <td style="padding: 6px 0; font-weight: bold;">{{ $application->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eeeeee;">
                            <td style="padding: 6px 0; font-weight: bold; color: #6c757d;">Student ID Number:</td>
                            <td style="padding: 6px 0; font-weight: bold; font-family: monospace;">{{ $application->user->profile?->clsu_id_number ?? 'N/A' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eeeeee;">
                            <td style="padding: 6px 0; font-weight: bold; color: #6c757d;">Course & Year Level:</td>
                            <td style="padding: 6px 0; font-weight: bold;">{{ $application->user->profile?->course ?? 'N/A' }} - {{ $application->user->profile?->year_level ?? 'N/A' }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #eeeeee;">
                            <td style="padding: 6px 0; font-weight: bold; color: #6c757d;">Declared GWA:</td>
                            <td style="padding: 6px 0; font-weight: bold; font-family: monospace;">{{ $application->gwa }}</td>
                        </tr>
                        @if($application->customFields && $application->customFields->count() > 0)
                            @foreach($application->customFields as $field)
                                @if(!str_starts_with($field->field_value, 'uploads/'))
                                    <tr style="border-bottom: 1px solid #eeeeee;">
                                        <td style="padding: 6px 0; font-weight: bold; color: #6c757d; text-transform: capitalize;">{{ $field->field_name }}:</td>
                                        <td style="padding: 6px 0; font-weight: bold;">{{ $field->field_value }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </table>
                </div>
            @endif

            <p style="margin-top: 30px;">Reference ID: <strong style="font-family: monospace;">APP-{{ $application->id }}</strong><br>
            Declared GWA: <strong style="font-family: monospace;">{{ $application->gwa }}</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated message generated by the A.E.G.I.S. System. Please do not reply directly to this email.</p>
            <p>&copy; {{ date('Y') }} Central Luzon State University.</p>
        </div>
    </div>
</body>
</html>