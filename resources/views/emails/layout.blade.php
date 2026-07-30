<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', \App\Models\Setting::get('app_name', 'A.E.G.I.S.'))</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f2f0eb;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            width: 100% !important;
            color: #334155;
        }
        .wrapper {
            width: 100%;
            background-color: #f2f0eb;
            padding: 40px 12px;
            box-sizing: border-box;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07), 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #07331c;
            background: linear-gradient(135deg, #07331c 0%, #0C4E2D 100%);
            padding: 32px 24px;
            text-align: center;
            border-bottom: 4px solid #D97706;
        }
        .header img {
            height: 52px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            margin-bottom: 12px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.02em;
            font-family: 'Poppins', 'Inter', sans-serif;
        }
        .header .sub-badge {
            display: inline-block;
            margin-top: 6px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fcd34d;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 3px 12px;
            border-radius: 50px;
        }
        .content {
            padding: 36px 30px;
            color: #334155;
            line-height: 1.65;
            font-size: 15px;
        }
        .content h2 {
            color: #0C4E2D;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 15px;
            color: #334155;
        }
        .cta-container {
            text-align: center;
            margin: 32px 0;
        }
        .btn {
            background-color: #00754A;
            color: #ffffff !important;
            text-decoration: none;
            padding: 13px 32px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 50px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 117, 74, 0.25);
            transition: all 0.2s ease;
        }
        .btn-gold {
            background-color: #D97706;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            color: #64748b;
            font-size: 12.5px;
            line-height: 1.6;
        }
        .footer p {
            margin: 0 0 6px;
        }
        .footer a {
            color: #0C4E2D;
            text-decoration: none;
            font-weight: 600;
        }
        @media only screen and (max-width: 480px) {
            .wrapper { padding: 12px 6px !important; }
            .content { padding: 24px 18px !important; }
            .header  { padding: 24px 16px !important; }
            .footer  { padding: 20px 16px !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                @php
                    $logoSrc = null;
                    $logoPath = public_path('logo.png');
                    if (file_exists($logoPath)) {
                        $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                    } else {
                        $logoSrc = \App\Models\Setting::getLogoUrl();
                    }
                @endphp
                <img src="{{ $logoSrc }}" alt="CLSU Logo" style="height: 56px; width: auto; max-width: 160px; object-fit: contain; display: block; margin: 0 auto 12px;">
                <h1>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</h1>
                <div class="sub-badge">{{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}</div>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <p>This is an official automated notification from the <strong>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</strong> portal.</p>
                <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }} Office of Student Affairs. All rights reserved.</p>
                <p style="margin-top: 10px;">
                    <a href="{{ url('/') }}">Access Portal</a> &bull; 
                    <a href="mailto:osa@clsu.edu.ph">Contact OSA Support</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
