@extends('layouts.app')

@section('title', 'Tabulation Results')

@section('content')
<div class="page-header">
    <h1>Tabulation Results</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <form method="GET" action="{{ route('tabulation.results') }}">
        <div class="form-group">
            <label for="event_id">Select Event</label>
            <select id="event_id" name="event_id" onchange="this.form.submit()">
                <option value="">-- Select Event --</option>
                @foreach($events as $evt)
                    <option value="{{ $evt->id }}" {{ (isset($event) && $event && $event->id == $evt->id) ? 'selected' : '' }}>
                        {{ $evt->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

@if(isset($event) && $event)
<div class="card">
    <h2>{{ $event->name }} - Results</h2>
    
    @php
    $contestants = \App\Models\Contestant::where('event_id', $event->id)->get();
    $criterias = \App\Models\Criteria::where('event_id', $event->id)->get();
    @endphp
    
    <table>
        <thead>
            <tr>
                <th>Contestant</th>
                @foreach($criterias as $criteria)
                <th>{{ $criteria->name }} ({{ $criteria->percentage }}%)</th>
                @endforeach
                <th>Total</th>
                <th>Rank</th>
            </tr>
        </thead>
        <tbody>
            @php
            $results = [];
            foreach($contestants as $contestant) {
                $totalScore = 0;
                $scoresData = [];
                
                foreach($criterias as $criteria) {
                    $score = \App\Models\Score::where('contestant_id', $contestant->id)
                        ->where('criteria_id', $criteria->id)
                        ->avg('score');
                    
                    $weightedScore = $score * ($criteria->percentage / 100);
                    $totalScore += $weightedScore;
                    $scoresData[$criteria->id] = $score ?? 0;
                }
                
                $results[] = [
                    'contestant' => $contestant,
                    'total' => $totalScore,
                    'scores' => $scoresData
                ];
            }
            
            usort($results, function($a, $b) {
                return $b['total'] - $a['total'];
            });
            @endphp
            
            @foreach($results as $index => $result)
            <tr>
                <td>{{ $result['contestant']->name }}</td>
                @foreach($criterias as $criteria)
                <td>{{ number_format($result['scores'][$criteria->id] ?? 0, 2) }}</td>
                @endforeach
                <td><strong>{{ number_format($result['total'], 2) }}</strong></td>
                <td><span class="badge badge-{{ $index + 1 <= 3 ? 'success' : '' }}">#{{ $index + 1 }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
