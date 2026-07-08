<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            width: 100% !important;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f6f8;
            padding: 30px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #0C4E2D; /* CLSU Green */
            padding: 30px;
            text-align: center;
            border-bottom: 4px solid #D97706; /* CLSU Gold */
        }
        .header img {
            height: 50px;
            margin-bottom: 10px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .content h2 {
            color: #0C4E2D;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }
        .content p {
            margin: 0 0 16px;
            font-size: 15px;
        }
        .cta-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            background-color: #0C4E2D;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 30px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 50px;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(12, 78, 45, 0.2);
            transition: all 0.2s ease;
            border: 2px solid #0C4E2D;
        }
        .btn-gold {
            background-color: #D97706;
            border-color: #D97706;
            box-shadow: 0 4px 6px rgba(217, 119, 6, 0.2);
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 13px;
        }
        .footer p {
            margin: 0 0 8px;
        }
        .footer a {
            color: #0C4E2D;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                @if(\App\Models\Setting::get('app_logo'))
                    <img src="{{ route('system.logo') }}" alt="CLSU Logo">
                @endif
                <h1>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</h1>
            </div>
            <div class="content">
                @yield('content')
            </div>
            <div class="footer">
                <p>This is an automated message from the <strong>{{ \App\Models\Setting::get('app_name', 'A.E.G.I.S.') }}</strong> system.</p>
                <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('university_name', 'Central Luzon State University') }}. All rights reserved.</p>
                <p><a href="{{ url('/') }}">Visit the Portal</a> | <a href="mailto:osa@clsu.edu.ph">Contact OSA Support</a></p>
            </div>
        </div>
    </div>
</body>
</html>
