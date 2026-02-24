<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback & Rating Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; }
        h2 { color: #2c2c5e; margin-bottom: 4px; }
        .sub { color: #888; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2c2c5e; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        tr:nth-child(even) { background: #f9f9f9; }
        .stars { color: #ff9f43; font-size: 12px; }
    </style>
</head>
<body>
    <h2>Feedback &amp; Rating Report</h2>
    <div class="sub">
        Period:
        {{ $dateFrom ? date('d M Y', strtotime($dateFrom)) : 'All' }}
        –
        {{ $dateTo ? date('d M Y', strtotime($dateTo)) : 'All' }}
        &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}
        &nbsp;|&nbsp; Total: {{ count($feedbacks) }} records
    </div>

    <table>
        <thead>
            <tr>
                <th>Booking No.</th>
                <th>Date</th>
                <th>Driver</th>
                <th>Booker</th>
                <th>Rating</th>
                <th>Tags</th>
                <th>Notes</th>
                <th>Destination</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($feedbacks as $row)
                <tr>
                    <td>{{ $row[0] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td>{{ $row[2] }}</td>
                    <td>{{ $row[3] }}<br><span style="color:#888;font-size:9px">{{ $row[4] }}</span></td>
                    <td><span class="stars">{{ str_repeat('★', $row[5]) }}{{ str_repeat('☆', 5 - $row[5]) }}</span></td>
                    <td>{{ $row[6] }}</td>
                    <td>{{ $row[7] }}</td>
                    <td>{{ $row[8] }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:20px;color:#888">No data found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
