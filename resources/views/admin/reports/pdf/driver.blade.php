<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Booking Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h2 { color: #2c2c5e; margin-bottom: 4px; }
        .sub { color: #888; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2c2c5e; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Driver Booking Report</h2>
    <div class="sub">
        Period:
        {{ $dateFrom ? date('d M Y', strtotime($dateFrom)) : 'All' }}
        –
        {{ $dateTo ? date('d M Y', strtotime($dateTo)) : 'All' }}
        &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}
        &nbsp;|&nbsp; Total: {{ count($bookings) }} records
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking No.</th>
                <th>Employee</th>
                <th>Driver</th>
                <th>Date</th>
                <th>Time</th>
                <th>Destination</th>
                <th>Purpose</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $row)
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td>{{ $row[1] }}<br><span style="color:#888;font-size:9px">{{ $row[2] }}</span></td>
                    <td>{{ $row[3] }}</td>
                    <td>{{ $row[5] }}</td>
                    <td>{{ $row[6] }}–{{ $row[7] }}</td>
                    <td>{{ $row[9] }}</td>
                    <td>{{ $row[10] }}</td>
                    <td>{{ $row[11] }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:20px;color:#888">No data found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
