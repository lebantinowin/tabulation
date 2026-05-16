<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results – {{ $criteria->name }} | {{ $event->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #111;
            background: #ffffff;
            margin-bottom: 60px;
        }

        /* Header */
        .page-header {
            color: #111;
            padding: 12px 24px 8px;
            text-align: center;
            border-bottom: 2px solid #040D12;
            margin-bottom: 0;
        }

        .page-header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .page-header p {
            font-size: 8.5pt;
            color: #444;
            margin: 2px 0;
        }

        .criteria-badge {
            display: inline-block;
            background: #f0f0f0;
            border: 1px solid #ccc;
            padding: 2px 10px;
            border-radius: 8px;
            font-size: 8.5pt;
            font-weight: bold;
            margin-top: 4px;
        }

        .sub {
            font-size: 7.5pt;
            color: #777;
            display: inline-block;
            margin-top: 4px;
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
            padding: 8px 6px;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }

        thead th.center { text-align: center; }
        thead th.right  { text-align: right; }

        tbody td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
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

        .contestant-name { font-weight: bold; font-size: 9.5pt; }
        .score-value     { font-weight: bold; font-size: 10pt; color: #040D12; }

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
    <p>Category Results</p>
    <div class="criteria-badge">{{ $criteria->name }} &nbsp;·&nbsp; Weight: {{ $criteria->weight }}%</div><br>
    @if($event->date)
        <p>Event Date: {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}</p>
    @endif
    @if(isset($eventJudges) && $eventJudges->count() > 0)
        <p style="margin-top: 4px; font-size: 8pt;">
            @foreach($eventJudges as $j)
                <span style="margin-right: 12px;">{{ $j->judge_number ? 'Judge ' . $j->judge_number : 'Judge' }}: {{ $j->name }}</span>
            @endforeach
        </p>
    @endif
    <div class="sub">Generated: {{ now()->format('F d, Y h:i A') }}</div>
</div>

<div class="table-wrapper">
    @php
        $sortedResults = [];
        foreach($results as $result) {
            $avg = $result['criteria_scores'][$criteria->id]['average'] ?? 0;
            $scores = $result['criteria_scores'][$criteria->id]['scores'] ?? collect();
            $sortedResults[] = [
                'contestant' => $result['contestant'],
                'average'    => $avg,
                'weighted'   => $avg * ($criteria->weight / 100),
                'scores'     => $scores,
            ];
        }
        usort($sortedResults, fn($a, $b) => $b['weighted'] <=> $a['weighted']);
    @endphp

    <table>
        <thead>
            <tr>
                <th class="center" style="width: 50px;">Rank</th>
                <th>Contestant Name</th>
                @if(isset($eventJudges) && $eventJudges->count() > 0)
                    @foreach($eventJudges as $j)
                        <th class="center">{{ $j->judge_number ? 'Judge ' . $j->judge_number : $j->name }}</th>
                    @endforeach
                @endif
                <th class="right">WEIGHTED<br>SCORE</th>
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
                <td>
                    <div class="contestant-name">
                        {{ $row['contestant']->name }}
                        @if($row['contestant']->number)
                            <span style="font-weight: normal; color: #555; font-size: 8pt;">(#{{ $row['contestant']->number }})</span>
                        @endif
                    </div>
                </td>
                @if(isset($eventJudges) && $eventJudges->count() > 0)
                    @foreach($eventJudges as $j)
                        @php $js = $row['scores']->where('judge_id', $j->id)->first(); @endphp
                        <td style="text-align:center;">
                            @if($js)
                                <div>{{ number_format($js->score, 2) }}</div>
                                <div style="color:#888; font-size:7pt; margin-top:2px;">{{ number_format($js->score * ($criteria->weight / 100), 2) }}%</div>
                            @else
                                <span style="color:#ccc;">—</span>
                            @endif
                        </td>
                    @endforeach
                @endif
                <td style="text-align:right;">
                    <span class="score-value">{{ number_format($row['weighted'], 2) }}%</span>
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
    This document is auto-generated by the Tabulation System. &nbsp;|&nbsp; Category: {{ $criteria->name }}
</div>

</body>
</html>
