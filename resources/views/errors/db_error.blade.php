<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Temporary Offline | A.E.G.I.S.</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --clsu-green: #0F5934;
            --clsu-green-dark: #093720;
            --clsu-gold: #F2A900;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            box-sizing: border-box;
        }

        .error-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 3rem 2.5rem;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03), 0 1px 3px rgba(0,0,0,0.01);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #fef3c7;
            color: #d97706;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 0 1rem;
            color: var(--clsu-green-dark);
        }

        p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin: 0 0 2rem;
        }

        .btn-reload {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--clsu-green) 0%, #16703f 100%);
            color: #ffffff;
            border: none;
            padding: 12px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(15, 89, 52, 0.25);
        }

        .btn-reload:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(15, 89, 52, 0.35);
        }

        .footer-note {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.4);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 12px rgba(217, 119, 6, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(217, 119, 6, 0);
            }
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-wrapper">
            <i class="fa-solid fa-database"></i>
        </div>
        <h1>System Temporarily Busy</h1>
        <p>
            {{ $message ?? 'The database connection is temporarily busy or locked under heavy load. Please reload the page to retry your request.' }}
        </p>
        <button onclick="window.location.reload();" class="btn-reload">
            <i class="fa-solid fa-rotate-right"></i> Reload Page
        </button>
        <div class="footer-note">
            <i class="fa-solid fa-shield-halved text-success"></i> A.E.G.I.S. Grade Integrity System<br>
            Central Luzon State University
        </div>
    </div>
</body>
</html>
