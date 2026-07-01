<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>A.E.G.I.S. — Email Dispatch Log</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #1e293b; background: #fff; }
        .header { background: #1e40af; color: white; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 16px; font-weight: 700; letter-spacing: 1px; }
        .header .meta { font-size: 8px; opacity: 0.75; text-align: right; }
        .subheader { background: #f1f5f9; padding: 8px 20px; border-bottom: 2px solid #e2e8f0; display: flex; gap: 24px; }
        .subheader span { font-size: 8px; color: #64748b; }
        .subheader strong { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #1e40af; color: white; }
        thead th { padding: 7px 10px; text-align: left; font-size: 8px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 8.5px; vertical-align: top; }
        .subject-cell { max-width: 200px; word-break: break-word; }
        .footer { margin-top: 12px; padding: 6px 20px; border-top: 1px solid #e2e8f0; font-size: 7.5px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>A.E.G.I.S. &mdash; Email Dispatch Log</h1>
            <div style="font-size:8px;opacity:0.7;margin-top:2px;">Automated Electronic Grant Information System</div>
        </div>
        <div class="meta">
            Generated: {{ now()->format('F d, Y \a\t h:i A') }}<br>
            Total Records: {{ $logs->count() }}
        </div>
    </div>

    <div class="subheader">
        <span>Date From: <strong>{{ $dateRange['from'] ?? 'All Time' }}</strong></span>
        <span>Date To: <strong>{{ $dateRange['to'] ?? 'Present' }}</strong></span>
        <span>Export Type: <strong>Email Notification Dispatch History</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Log ID</th>
                <th>App Ref</th>
                <th>Student Name</th>
                <th>Recipient Email</th>
                <th>Subject</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>#{{ $log->id }}</td>
                <td>APP-{{ $log->application->id ?? 'N/A' }}</td>
                <td>{{ $log->application?->user?->name ?? 'Unknown' }}</td>
                <td>{{ $log->recipient }}</td>
                <td class="subject-cell">{{ $log->subject }}</td>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:16px;color:#94a3b8;">No email log records found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        A.E.G.I.S. Scholarship Management System &mdash; Confidential Document &mdash; {{ now()->format('Y') }}
    </div>
</body>
</html>
