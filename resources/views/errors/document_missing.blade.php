<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Not Found</title>
    <style>
        body { font-family: sans-serif; display: flex; flex-direction: column; justify-content: center; align-items: center; height: 90vh; color: #64748b; background: #f8fafc; text-align: center; padding: 20px; margin: 0; }
        svg { width: 48px; height: 48px; color: #f59e0b; margin-bottom: 12px; }
        h3 { margin: 0 0 6px 0; color: #0f172a; font-size: 16px; }
        p  { margin: 0; font-size: 13px; max-width: 320px; color: #64748b; }
    </style>
</head>
<body>
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <h3>File Missing on Server</h3>
    <p>{{ $message ?? "This file could not be found. Please contact the administrator." }}</p>
</body>
</html>