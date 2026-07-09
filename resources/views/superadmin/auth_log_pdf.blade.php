<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>A.E.G.I.S. — Login & Auth Event Log</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8.5px; color: #1e293b; background: #fff; }
        .header { background: #07331c; color: white; padding: 14px 20px; }
        .header h1 { font-size: 16px; font-weight: 700; letter-spacing: 1px; }
        .header .meta { font-size: 8px; opacity: 0.75; float: right; text-align: right; margin-top: -30px; }
        .subheader { background: #f1f5f9; padding: 8px 20px; border-bottom: 2px solid #e2e8f0; }
        .subheader span { font-size: 8px; color: #64748b; margin-right: 20px; }
        .subheader strong { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #07331c; color: white; }
        thead th { padding: 7px 10px; text-align: left; font-size: 8px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 7.5px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-failed  { background: #fee2e2; color: #b91c1c; }
        .footer { margin-top: 12px; padding: 6px 20px; border-top: 1px solid #e2e8f0; font-size: 7.5px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>A.E.G.I.S. &mdash; Login & Auth Event Log</h1>
        <div style="font-size:8px;opacity:0.7;margin-top:2px;">Automated Electronic Grant Information System</div>
        <div class="meta">
            Generated: {{ now()->format('F d, Y \a\t h:i A') }}<br>
            Total Events: {{ $logs->count() }}
        </div>
    </div>

    <div class="subheader">
        <span>Date From: <strong>{{ $dateRange['from'] ?? 'All Time' }}</strong></span>
        <span>Date To: <strong>{{ $dateRange['to'] ?? 'Present' }}</strong></span>
        <span>Export Type: <strong>System Access & Authentication Security Audit</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">Log ID</th>
                <th style="width: 15%;">User Name</th>
                <th style="width: 20%;">Email Attempted</th>
                <th style="width: 15%;">Event Type</th>
                <th style="width: 12%;">IP Address</th>
                <th style="width: 13%;">Status</th>
                <th style="width: 10%;">Timestamp</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>#{{ $log->id }}</td>
                <td>{{ $log->user?->name ?? 'Guest/Unknown' }}</td>
                <td>{{ $log->email_attempted ?: 'N/A' }}</td>
                <td>{{ strtoupper(str_replace('_', ' ', $log->event_type)) }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>
                    <span class="badge {{ $log->status === 'success' ? 'badge-success' : 'badge-failed' }}">
                        {{ strtoupper($log->status) }}
                    </span>
                </td>
                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:16px;color:#94a3b8;">No auth log events found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        A.E.G.I.S. Grade Integrity System &mdash; Security Audit &mdash; {{ now()->format('Y') }}
    </div>
</body>
</html>
