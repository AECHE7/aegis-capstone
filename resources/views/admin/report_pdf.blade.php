<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>A.E.G.I.S. Official Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #334155; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h3, .header h4, .header p { margin: 2px 0; }
        .text-green { color: #0a5c36; }
        
        /* Stats Styling */
        .stats-section { margin-bottom: 25px; padding: 12px; background-color: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        .stats-grid { width: 100%; border-collapse: collapse; margin-top: 0; }
        .stats-cell { width: 20%; padding: 4px 8px; text-align: center; }
        .stats-label { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: bold; }
        .stats-val { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 3px; }
        
        /* Table Styling */
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.data-table th, table.data-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        table.data-table th { background-color: #0a5c36; color: white; font-weight: bold; font-size: 10px; }
        
        .footer { margin-top: 40px; }
        .signature-line { width: 200px; border-top: 1px solid #000; margin-top: 30px; text-align: center; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h4>Republic of the Philippines</h4>
        <h3 class="text-green">CENTRAL LUZON STATE UNIVERSITY</h3>
        <p>Science City of Muñoz, Nueva Ecija</p>
        <br>
        <h4>OFFICE OF STUDENT AFFAIRS</h4>
        <p>Official Scholarship Application Report</p>
        <p>Date Generated: {{ date('F d, Y') }}</p>
    </div>

    <!-- Summary Statistics Section -->
    <div class="stats-section">
        <h4 style="margin: 0 0 8px 0; color: #0a5c36; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; font-size: 11px;">Report Summary Statistics</h4>
        <table class="stats-grid">
            <tr>
                <td class="stats-cell" style="border: none;">
                    <div class="stats-label">Total Records</div>
                    <div class="stats-val">{{ $stats['total'] }}</div>
                </td>
                <td class="stats-cell" style="border: none;">
                    <div class="stats-label">Approved</div>
                    <div class="stats-val" style="color: #16a34a;">{{ $stats['approved'] }}</div>
                </td>
                <td class="stats-cell" style="border: none;">
                    <div class="stats-label">Rejected</div>
                    <div class="stats-val" style="color: #dc2626;">{{ $stats['rejected'] }}</div>
                </td>
                <td class="stats-cell" style="border: none;">
                    <div class="stats-label">Pending / Review</div>
                    <div class="stats-val" style="color: #d97706;">{{ $stats['pending'] }}</div>
                </td>
                <td class="stats-cell" style="border: none;">
                    <div class="stats-label">Avg. Fraud Score</div>
                    <div class="stats-val" style="color: #2563eb;">{{ $stats['avg_fraud'] }}%</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Ref ID</th>
                <th>Student Name</th>
                <th>CLSU ID</th>
                <th>Program</th>
                <th>GWA</th>
                <th>Status</th>
                <th>Date Applied</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>APP-{{ $app->id }}</td>
                <td>{{ $app->user->name ?? 'Unknown' }}</td>
                <td>{{ $app->user->profile->clsu_id_number ?? 'N/A' }}</td>
                <td>{{ $app->program_name }}</td>
                <td>{{ $app->gwa }}</td>
                <td>{{ $app->status }}</td>
                <td>{{ $app->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Prepared by:</p>
        <div class="signature-line">
            <strong>OSA Administrator</strong><br>
            A.E.G.I.S. System
        </div>
    </div>

</body>
</html>