<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Results - {{ $criteria->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header h2 {
            margin: 5px 0;
            color: #666;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #888;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .rank-1 {
            background-color: #ffd700;
        }
        .rank-2 {
            background-color: #c0c0c0;
        }
        .rank-3 {
            background-color: #cd7f32;
        }
        .print-btn {
            display: none;
        }
        @media print {
            .print-btn {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $event->name }}</h1>
        <h2>Category: {{ $criteria->name }}</h2>
        <p>Criteria Weight: {{ $criteria->weight }}%</p>
        <p>Date: {{ $event->date ? $event->date->format('F d, Y') : 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Number</th>
                <th>Contestant Name</th>
                <th>Total Score</th>
                <th>Average</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            <tr class="rank-{{ $result['rank'] }}">
                <td>{{ $result['rank'] }}</td>
                <td>{{ $result['contestant']->number }}</td>
                <td>{{ $result['contestant']->name }}</td>
                <td>{{ number_format($result['total_score'], 2) }}</td>
                <td>{{ number_format($result['average_score'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="print-btn">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <a href="{{ route('tabulation.results', ['event_id' => $event->id]) }}" class="btn btn-secondary">Back</a>
    </div>

    <script>
        window.onload = function() {
            // Auto print when page loads
            // window.print();
        }
    </script>
</body>
</html>
