<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>A.E.G.I.S. — AI Document Scan Log</title>
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
        .badge-authentic { background: #dcfce7; color: #15803d; }
        .badge-tampered  { background: #fee2e2; color: #b91c1c; }
        .badge-scanning  { background: #fef9c3; color: #a16207; }
        .badge-other     { background: #f1f5f9; color: #475569; }
        .footer { margin-top: 12px; padding: 6px 20px; border-top: 1px solid #e2e8f0; font-size: 7.5px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>A.E.G.I.S. &mdash; AI Document Scan Log</h1>
        <div style="font-size:8px;opacity:0.7;margin-top:2px;">Automated Electronic Grant Information System</div>
        <div class="meta">
            Generated: {{ now()->format('F d, Y \a\t h:i A') }}<br>
            Total Scans: {{ $results->count() }}
        </div>
    </div>

    <div class="subheader">
        <span>Date From: <strong>{{ $dateRange['from'] ?? 'All Time' }}</strong></span>
        <span>Date To: <strong>{{ $dateRange['to'] ?? 'Present' }}</strong></span>
        <span>Export Type: <strong>AI Scan & Integrity Report</strong></span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 10%;">Scan ID</th>
                <th style="width: 12%;">App Ref</th>
                <th style="width: 18%;">Student Name</th>
                <th style="width: 15%;">Document Type</th>
                <th style="width: 12%;">Classification</th>
                <th style="width: 13%;">Fraud Probability</th>
                <th style="width: 15%;">Anomaly Flags</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $i => $res)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>#{{ $res->id }}</td>
                <td>APP-{{ $res->document?->application_id ?? 'N/A' }}</td>
                <td>{{ $res->document?->application?->user?->name ?? 'Unknown' }}</td>
                <td>{{ $res->document?->document_type ?? 'N/A' }}</td>
                <td>
                    @php
                        $c = strtolower($res->classification ?? '');
                        $cls = match(true) {
                            $c === 'authentic' => 'badge-authentic',
                            $c === 'tampered'  => 'badge-tampered',
                            $c === 'scanning'  => 'badge-scanning',
                            default            => 'badge-other',
                        };
                    @endphp
                    <span class="badge {{ $cls }}">{{ ucfirst($res->classification) }}</span>
                </td>
                <td style="font-weight: bold; color: {{ $res->fraud_probability >= 50 ? '#b91c1c' : '#1e293b' }}">
                    {{ $res->fraud_probability }}%
                </td>
                <td>
                    @php
                        $flags = '';
                        if (!empty($res->anomaly_indicators)) {
                            $flagsArray = is_string($res->anomaly_indicators) ? json_decode($res->anomaly_indicators, true) : $res->anomaly_indicators;
                            $flags = is_array($flagsArray) ? implode(', ', $flagsArray) : '';
                        }
                    @endphp
                    {{ $flags ?: 'None' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:16px;color:#94a3b8;">No AI scan records found for the selected period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        A.E.G.I.S. Grade Integrity System &mdash; Confidential Document &mdash; {{ now()->format('Y') }}
    </div>
</body>
</html>
