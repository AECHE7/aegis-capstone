<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied | A.E.G.I.S.</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --clsu-green: #0C4E2D;
            --clsu-green-dark: #072e1a;
            --clsu-gold: #F2A900;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            box-sizing: border-box;
        }

        .error-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 3.5rem 2.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 45px rgba(220, 38, 38, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.8);
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #dc2626, var(--clsu-gold));
        }

        .badge-code {
            display: inline-block;
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 50px;
            margin-bottom: 1.5rem;
        }

        .icon-wrapper {
            width: 88px;
            height: 88px;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.75rem;
        }

        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.75rem;
            color: var(--text-dark);
        }

        p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin: 0 0 2.25rem;
        }

        .btn-group-custom {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary-custom {
            background-color: var(--clsu-green);
            color: #ffffff;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(12, 78, 45, 0.25);
        }

        .btn-primary-custom:hover {
            background-color: var(--clsu-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(12, 78, 45, 0.35);
            color: #ffffff;
        }

        .btn-secondary-custom {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 0.8rem 1.6rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-secondary-custom:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="badge-code">Access Restricted</div>
        <div class="icon-wrapper">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h1>Access Denied</h1>
        <p>You do not have the required administrative clearance or role permissions to access this confidential portal module or resource.</p>
        <div class="btn-group-custom">
            <a href="javascript:history.back()" class="btn-secondary-custom">
                <i class="fa-solid fa-arrow-left"></i> Go Back
            </a>
            <a href="{{ url('/') }}" class="btn-primary-custom">
                <i class="fa-solid fa-house"></i> Return to Portal
            </a>
        </div>
    </div>
</body>
</html>
