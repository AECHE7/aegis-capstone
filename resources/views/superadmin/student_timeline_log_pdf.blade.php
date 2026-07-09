<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>A.E.G.I.S. — Student Lifecycle Timeline Log</title>
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
        .badge-approved { background: #dcfce7; color: #15803d; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; }
        .badge-pending  { background: #fef9c3; color: #a16207; }
        .badge-review   { background: #e0e7ff; color: #4338ca; }
        .badge-other    { background: #f1f5f9; color: #475569; }
        .footer { margin-top: 12px; padding: 6px 20px; border-top: 1px solid #e2e8f0; font-size: 7.5px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>A.E.G.I.S. &mdash; Student Lifecycle Timeline Log</h1>
        <div style="font-size:8px;opacity:0.7;margin-top:2px;">Automated Electronic Grant Information System</div>
        <div class="meta">
            Generated: {{ now()->format('F d, Y \a\t h:i A') }}<br>
            Total Applications: {{ $applications->count() }}
        </div>
    </div>

    <div class="subheader">
        <span>Date From: <strong>{{ $dateRange['from'] ?? 'All Time' }}</strong></span>
        <span>Date To: <strong>{{ $dateRange['to'] ?? 'Present' }}</strong></span>
        <span>Export Type: <strong>Student Application Processing & Decision Timelines</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 15%;">Student Name</th>
                <th style="width: 12%;">CLSU ID</th>
                <th style="width: 10%;">App Ref</th>
                <th style="width: 18%;">Scholarship Program</th>
                <th style="width: 15%;">Submitted At</th>
                <th style="width: 15%;">Evaluated At</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applications as $i => $app)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-weight: bold;">{{ $app->user->name ?? 'Unknown' }}</td>
                <td>{{ $app->user->profile?->clsu_id_number ?? 'N/A' }}</td>
                <td>APP-{{ $app->id }}</td>
                <td>{{ $app->program_name }}</td>
                <td>{{ $app->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                    @php
                        $evalDate = $app->statusLogs->whereIn('status', ['Approved', 'Rejected'])->first()?->created_at;
                    @endphp
                    {{ $evalDate ? $evalDate->format('Y-m-d H:i:s') : 'Not yet evaluated' }}
                </td>
                <td>
                    @php
                        $s = strtolower($app->status ?? '');
                        $cls = match(true) {
                            $s === 'approved' => 'badge-approved',
                            $s === 'rejected' => 'badge-rejected',
                            $s === 'pending'  => 'badge-pending',
                            $s === 'under review' => 'badge-review',
                            default           => 'badge-other',
                        };
                    @endphp
                    <span class="badge {{ $cls }}">{{ $app->status }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:16px;color:#94a3b8;">No application timelines found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        A.E.G.I.S. Scholarship Management System &mdash; Confidential Document &mdash; {{ now()->format('Y') }}
    </div>
</body>
</html>
