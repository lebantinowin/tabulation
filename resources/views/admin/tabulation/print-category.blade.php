<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results – {{ $criteria->name }} | {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #111;
            background: #ffffff;
        }

        /* Header */
        .page-header {
            background: #040D12;
            color: #ffffff;
            padding: 24px 32px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .page-header p {
            font-size: 10pt;
            opacity: 0.75;
            margin: 3px 0;
        }

        .criteria-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 12px;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 8px;
        }

        .sub {
            font-size: 9pt;
            background: rgba(255,255,255,0.1);
            display: inline-block;
            padding: 3px 12px;
            border-radius: 12px;
            margin-top: 6px;
        }

        /* Table */
        .table-wrapper {
            padding: 24px 32px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #040D12;
            color: #ffffff;
            padding: 10px 12px;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }

        tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }

        /* Row highlights */
        .rank-gold   td { background: #fffbdc; }
        .rank-silver td { background: #f3f3f3; }
        .rank-bronze td { background: #fff4e8; }

        .rank-badge {
            display: inline-block;
            width: 26px;
            height: 26px;
            line-height: 26px;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            color: white;
        }

        .rank-1 .rank-badge { background: #d4a017; color: #000; }
        .rank-2 .rank-badge { background: #8e9ba8; }
        .rank-3 .rank-badge { background: #a0552a; }
        .rank-other .rank-badge { background: #4A5568; }

        .contestant-name { font-weight: bold; font-size: 10.5pt; }
        .score-value     { font-weight: bold; font-size: 11pt; color: #040D12; }

        /* Footer */
        .page-footer {
            border-top: 1px solid #e5e7eb;
            padding: 12px 32px;
            text-align: center;
            font-size: 8pt;
            color: #999;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<div class="page-header">
    <h1>{{ $event->name }}</h1>
    <p>Category Results</p>
    <div class="criteria-badge">{{ $criteria->name }} &nbsp;·&nbsp; Weight: {{ $criteria->weight }}%</div><br>
    @if($event->date)
        <p>Event Date: {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</p>
    @endif
    <div class="sub">Generated: {{ now()->format('F d, Y h:i A') }}</div>
</div>

<div class="table-wrapper">
    @php
        $sortedResults = [];
        foreach($results as $result) {
            $sortedResults[] = [
                'contestant' => $result['contestant'],
                'average'    => $result['criteria_scores'][$criteria->id]['average'] ?? 0,
            ];
        }
        usort($sortedResults, fn($a, $b) => $b['average'] <=> $a['average']);
    @endphp

    <table>
        <thead>
            <tr>
                <th class="center" style="width: 50px;">Rank</th>
                <th style="width: 60px;">No.</th>
                <th>Contestant Name</th>
                <th class="right">Average Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sortedResults as $index => $row)
            @php
                $rank = $index + 1;
                $rankClass = match($rank) {
                    1 => 'rank-gold rank-1',
                    2 => 'rank-silver rank-2',
                    3 => 'rank-bronze rank-3',
                    default => 'rank-other',
                };
            @endphp
            <tr class="{{ $rankClass }}">
                <td style="text-align:center;">
                    <span class="rank-badge">{{ $rank }}</span>
                </td>
                <td style="color:#666; font-size:9pt;">{{ $row['contestant']->number ?? '—' }}</td>
                <td>
                    <div class="contestant-name">{{ $row['contestant']->name }}</div>
                </td>
                <td style="text-align:right;">
                    <span class="score-value">{{ number_format($row['average'], 4) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="page-footer">
    This document is auto-generated by the Tabulation System. &nbsp;|&nbsp; Category: {{ $criteria->name }}
</div>

</body>
</html>
