<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results – {{ $event->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f0f0;
            color: #111;
        }

        /* Floating toolbar (hidden on print) */
        .toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #040D12;
            color: white;
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .toolbar h2 { font-size: 1rem; font-weight: 500; }

        .toolbar-actions { display: flex; gap: 0.75rem; }

        .btn-toolbar {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-print { background: #2F855A; color: white; }
        .btn-print:hover { background: #22543d; }
        .btn-back { background: rgba(255,255,255,0.1); color: white; }
        .btn-back:hover { background: rgba(255,255,255,0.2); }

        /* Page */
        .page {
            max-width: 900px;
            margin: 80px auto 40px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
        }

        /* Header */
        .page-header {
            background: #040D12;
            color: white;
            padding: 2.5rem;
            text-align: center;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .page-header p {
            opacity: 0.7;
            font-size: 0.9rem;
            margin: 0.25rem 0;
        }

        .generated-at {
            display: inline-block;
            margin-top: 0.75rem;
            background: rgba(255,255,255,0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* Table */
        .table-wrapper { padding: 2rem; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.85rem 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #040D12;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td { font-size: 0.9rem; }

        tr:last-child td { border-bottom: none; }

        tr:hover td { background: #f9f9f9; }

        /* Rank highlights */
        .rank-gold td { background: #fffbdc !important; }
        .rank-silver td { background: #f3f3f3 !important; }
        .rank-bronze td { background: #fff4e8 !important; }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.85rem;
            color: white;
        }

        .rank-1 .rank-badge { background: #d4a017; }
        .rank-2 .rank-badge { background: #8e9ba8; }
        .rank-3 .rank-badge { background: #a0552a; }
        .rank-other .rank-badge { background: #4A5568; }

        .contestant-name { font-weight: 600; }
        .contestant-number { font-size: 0.8rem; color: #666; }

        .score-total { font-weight: 700; font-size: 1.05rem; color: #040D12; }

        /* Footer */
        .page-footer {
            border-top: 1px solid #eee;
            padding: 1rem 2rem;
            text-align: center;
            font-size: 0.75rem;
            color: #999;
        }

        /* Print overrides */
        @media print {
            body { background: white; }
            .toolbar { display: none !important; }
            .page {
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

<!-- Floating Toolbar (non-print) -->
<div class="toolbar">
    <h2><i class="fas fa-file-pdf"></i> Results PDF — {{ $event->name }}</h2>
    <div class="toolbar-actions">
        <a href="{{ route('tabulation.results', ['event_id' => $event->id]) }}" class="btn-toolbar btn-back">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button class="btn-toolbar btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Save as PDF / Print
        </button>
    </div>
</div>

<!-- Printable Page -->
<div class="page">
    <div class="page-header">
        <h1>{{ $event->name }}</h1>
        <p>Overall Tabulation Results</p>
        @if($event->date)
            <p>Event Date: {{ $event->date }}</p>
        @endif
        <span class="generated-at">Generated: {{ now()->format('F d, Y h:i A') }}</span>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">Rank</th>
                    <th style="width: 60px;">No.</th>
                    <th>Contestant Name</th>
                    <th style="text-align: right;">Total Score</th>
                    <th style="text-align: right;">Average</th>
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
                    <td style="text-align: center;">
                        <span class="rank-badge">{{ $result['rank'] }}</span>
                    </td>
                    <td class="contestant-number">
                        {{ $result['contestant']->number ?? '—' }}
                    </td>
                    <td>
                        <div class="contestant-name">{{ $result['contestant']->name }}</div>
                        @if($result['message'])
                            <div style="font-size: 0.78rem; color: #c53030; margin-top: 0.2rem;">Note: {{ $result['message'] }}</div>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <span class="score-total">{{ number_format($result['total_score'], 4) }}</span>
                        @if($result['is_overridden'])
                            <br><span style="font-size: 0.7rem; color: #c05621; font-weight: 500;">★ Overridden</span>
                        @endif
                    </td>
                    <td style="text-align: right; color: #555;">
                        {{ number_format($result['average_score'], 4) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-footer">
        This document is auto-generated by the Tabulation System. &nbsp;|&nbsp; Powered by ECCENTRI, Inc.
    </div>
</div>

</body>
</html>
