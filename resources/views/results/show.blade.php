@extends('layouts.app')

@section('title', $event->name . ' - Results')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $event->name }}</h1>
        <p style="color: #666;">
            <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
            
            @php
                $statusClass = '';
                $statusText = '';
                
                if($event->status == 'ongoing') {
                    $statusClass = 'badge-success';
                    $statusText = 'Ongoing';
                } elseif($event->status == 'upcoming') {
                    $statusClass = 'badge-warning';
                    $statusText = 'Upcoming';
                } elseif($event->status == 'completed') {
                    $statusClass = 'badge-secondary';
                    $statusText = 'Completed';
                } elseif($event->status == 'active') {
                    $statusClass = 'badge-success';
                    $statusText = 'Active';
                } elseif($event->status == 'inactive') {
                    $statusClass = 'badge-warning';
                    $statusText = 'Inactive';
                } else {
                    $statusClass = 'badge-secondary';
                    $statusText = $event->status;
                }
            @endphp
            
            <span class="badge {{ $statusClass }}" style="margin-left: 10px;">{{ $statusText }}</span>
        </p>
    </div>
    <a href="{{ route('results.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Events
    </a>
</div>

@if(count($results) == 0)
    <div class="alert alert-danger">
        No results available yet.
    </div>
@else
    <div class="card">
        <div class="flex justify-between items-center mb-4" style="flex-wrap: wrap; gap: 1rem;">
            <h2 style="margin-bottom: 0;">Overall Rankings</h2>
            
            @auth
            @if(auth()->user()->isAdmin())
            <div class="flex gap-2" style="flex-wrap: wrap;">
                <a href="{{ route('tabulation.print', ['event_id' => $event->id]) }}" class="btn" target="_blank" style="background: var(--color-info);">
                    <i class="fas fa-file-pdf"></i> Export PDF (Overall)
                </a>
                @if(count($criterias) > 0)
                    <div class="dropdown" style="position: relative; display: inline-block;">
                        <button class="btn" style="background: var(--color-secondary);" onclick="toggleDropdown()">
                            <i class="fas fa-file-pdf"></i> Export by Category <i class="fas fa-caret-down"></i>
                        </button>
                        <div id="categoryDropdown" class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; background: var(--color-white); border: 1px solid var(--color-border); border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 100; min-width: 200px; overflow: hidden; text-align: left;">
                            @foreach($criterias as $criteria)
                                <a href="{{ route('tabulation.print-category', ['criteriaId' => $criteria->id]) }}" target="_blank" style="display: block; padding: 0.75rem 1rem; color: var(--color-text); text-decoration: none; font-size: 0.9rem; border-bottom: 1px solid var(--color-border); transition: background 0.2s;" onmouseover="this.style.background='var(--color-main)'" onmouseout="this.style.background='transparent'">
                                    {{ $criteria->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif
            @endauth
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">Rank</th>
                    <th>Contestant</th>
                    @if(count($criterias) > 0)
                        @foreach($criterias as $criteria)
                            <th style="text-align: center;">{{ $criteria->name }}</th>
                        @endforeach
                    @endif
                    <th style="text-align: center;">Total Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $result)
                    <tr>
                        <td style="text-align: center;">
                            @if($result['rank'] == 1)
                                <span style="display: inline-block; width: 30px; height: 30px; line-height: 30px; background: #FFD700; color: #000; border-radius: 50%; font-weight: bold;">1</span>
                            @elseif($result['rank'] == 2)
                                <span style="display: inline-block; width: 30px; height: 30px; line-height: 30px; background: #C0C0C0; color: #000; border-radius: 50%; font-weight: bold;">2</span>
                            @elseif($result['rank'] == 3)
                                <span style="display: inline-block; width: 30px; height: 30px; line-height: 30px; background: #CD7F32; color: #fff; border-radius: 50%; font-weight: bold;">3</span>
                            @else
                                <span style="font-weight: bold;">{{ $result['rank'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                @if($result['contestant']->image_url)
                                    <img src="{{ $result['contestant']->image_url }}" alt="{{ $result['contestant']->name }}" class="profile-image">
                                @else
                                    <div class="user-avatar">
                                        {{ substr($result['contestant']->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $result['contestant']->name }}</strong>
                                    @if($result['contestant']->number)
                                        <br><small style="color: #666;">#{{ $result['contestant']->number }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @if(count($criterias) > 0)
                            @foreach($criterias as $criteria)
                                <td style="text-align: center;">
                                    {{ number_format($result['criteria_scores'][$criteria->id]['average'] ?? 0, 2) }}
                                </td>
                            @endforeach
                        @endif
                        <td style="text-align: center;">
                            <strong style="font-size: 1.1rem;">{{ number_format($result['total_score'], 2) }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if(count($criterias) > 0)
        @foreach($criterias as $criteria)
            <div class="card">
                <h3 style="margin-bottom: 1rem;">{{ $criteria->name }} - Breakdown</h3>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">Rank</th>
                            <th>Contestant</th>
                            <th style="text-align: center;">Average Score</th>
                            <th style="text-align: center;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $criteriaResults = [];
                            foreach($results as $result) {
                                $criteriaResults[] = [
                                    'contestant' => $result['contestant'],
                                    'average' => $result['criteria_scores'][$criteria->id]['average'] ?? 0,
                                    'total' => $result['criteria_scores'][$criteria->id]['total'] ?? 0,
                                ];
                            }
                            usort($criteriaResults, function($a, $b) {
                                return $b['average'] - $a['average'];
                            });
                        @endphp
                        
                        @foreach($criteriaResults as $index => $cr)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        @if($cr['contestant']->image_url)
                                            <img src="{{ $cr['contestant']->image_url }}" alt="{{ $cr['contestant']->name }}" class="profile-image-sm">
                                        @else
                                            <div class="user-avatar">
                                                {{ substr($cr['contestant']->name, 0, 1) }}
                                            </div>
                                        @endif
                                        {{ $cr['contestant']->name }}
                                    </div>
                                </td>
                                <td style="text-align: center;">{{ number_format($cr['average'], 2) }}</td>
                                <td style="text-align: center;">{{ number_format($cr['total'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
@endif
@endsection
