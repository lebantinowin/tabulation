<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results – {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #111;
            background: #ffffff;
            margin-bottom: 60px; /* space for footer */
        }

        /* Header */
        .page-header {
            background: #040D12;
            color: #ffffff;
            padding: 16px 24px;
            text-align: center;
            margin-bottom: 0;
        }

        .page-header h1 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .page-header p {
            font-size: 9pt;
            opacity: 0.8;
            margin: 2px 0;
        }

        .page-header .sub {
            font-size: 9pt;
            background: rgba(255,255,255,0.1);
            display: inline-block;
            padding: 3px 12px;
            border-radius: 12px;
            margin-top: 8px;
        }

        /* Table */
        .table-wrapper {
            padding: 16px 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #040D12;
            color: #ffffff;
            padding: 6px 4px;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }

        tbody td {
            padding: 6px 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 8pt;
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

        .contestant-name { font-weight: bold; font-size: 8.5pt; }
        .score-total { font-weight: bold; font-size: 9pt; color: #040D12; }
        .overridden-badge {
            font-size: 7pt;
            color: #c05621;
            font-weight: bold;
        }

        /* Signatures */
        .signatures-wrapper {
            padding: 24px;
            margin-top: 20px;
        }
        
        .signatures-title {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .signature-grid {
            width: 100%;
            table-layout: fixed;
            margin-top: 30px;
        }
        
        .signature-box {
            text-align: center;
            vertical-align: bottom;
            padding-bottom: 20px;
        }
        
        .signature-line {
            width: 80%;
            margin: 0 auto 5px auto;
            border-top: 1px solid #000;
        }
        
        .signature-name {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        
        .signature-role {
            font-size: 8pt;
            color: #555;
        }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            border-top: 1px solid #e5e7eb;
            padding: 10px 32px;
            text-align: center;
            font-size: 7.5pt;
            color: #999;
            background: #ffffff;
        }
    </style>
</head>
<body>

<div class="page-header">
    <h1>{{ $event->name }}</h1>
    <p>Overall Tabulation Results</p>
    @if($event->date)
        <p>Event Date: {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</p>
    @endif
    <div class="sub">Generated: {{ now()->format('F d, Y h:i A') }}</div>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th class="center" style="width: 50px;">Rank</th>
                <th style="width: 60px;">No.</th>
                <th>Contestant Name</th>
                @foreach($criterias as $criteria)
                    <th class="center">{{ $criteria->name }}<br><span style="font-weight:400; font-size:8pt;">({{ $criteria->weight }}%)</span></th>
                @endforeach
                <th class="right">Total Score</th>
                <th class="right">Average</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
            @php
                $rankClass = match($result['rank']) {
                    1 => 'rank-gold rank-1',
                    2 => 'rank-silver rank-2',
                    3 => 'rank-bronze rank-3',
                    default => 'rank-other',
                };
            @endphp
            <tr class="{{ $rankClass }}">
                <td class="center">
                    <span class="rank-badge">{{ $result['rank'] ?? '–' }}</span>
                </td>
                <td style="color:#666; font-size:9pt;">{{ $result['contestant']->number ?? '—' }}</td>
                <td>
                    <div class="contestant-name">{{ $result['contestant']->name }}</div>
                    @if($result['message'])
                        <div style="font-size:8pt; color:#c53030;">Note: {{ $result['message'] }}</div>
                    @endif
                </td>
                @foreach($criterias as $criteria)
                    <td style="text-align:center;">{{ number_format($result['criteria_scores'][$criteria->id]['average'] ?? 0, 2) }}</td>
                @endforeach
                <td style="text-align:right;">
                    <span class="score-total">{{ number_format($result['total_score'], 4) }}</span>
                    @if($result['is_overridden'])
                        <br><span class="overridden-badge">&#9733; Overridden</span>
                    @endif
                </td>
                <td style="text-align:right; color:#555;">
                    {{ number_format($result['average_score'], 4) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if((isset($judges) && count($judges) > 0) || !empty($adminName))
<div class="signatures-wrapper">
    <div class="signatures-title">CERTIFIED BY:</div>
    <table class="signature-grid">
        @php
            $signatories = [];
            if (isset($judges) && is_array($judges)) {
                foreach ($judges as $judge) {
                    $signatories[] = ['name' => $judge, 'role' => 'Judge'];
                }
            }
            if (!empty($adminName)) {
                $signatories[] = ['name' => $adminName, 'role' => 'Administrator'];
            }
            $chunks = array_chunk($signatories, 3);
        @endphp

        @foreach($chunks as $row)
            <tr>
                @foreach($row as $person)
                    <td class="signature-box" style="padding-top: 50px;">
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $person['name'] }}</div>
                        <div class="signature-role">{{ $person['role'] }}</div>
                    </td>
                @endforeach
                @for($i = count($row); $i < 3; $i++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </table>
</div>
@endif

<div class="page-footer">
    This document is auto-generated by the Tabulation System. &nbsp;|&nbsp; Powered by ECCENTRI, Inc.
</div>

</body>
</html>
