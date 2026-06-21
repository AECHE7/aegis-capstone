<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>A.E.G.I.S. Official Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h3, .header h4, .header p { margin: 2px 0; }
        .text-green { color: #0a5c36; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #0a5c36; color: white; }
        .footer { margin-top: 50px; }
        .signature-line { width: 200px; border-top: 1px solid #000; margin-top: 40px; text-align: center; padding-top: 5px; }
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

    <table>
        <thead>
            <tr>
                <th>Ref ID</th>
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