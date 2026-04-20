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
        <h2 style="margin-bottom: 1.5rem;">Overall Rankings</h2>
        
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
                                @php
                                $imagePath = $result['contestant']->image;
                                $fullPath = '';
                                $imageFound = false;
                                
                                // Build list of possible paths based on stored image path
                                $possiblePaths = [];
                                
                                if ($imagePath) {
                                    // If the path already contains 'storage', use as-is
                                    if (str_contains($imagePath, 'storage/')) {
                                        $possiblePaths[] = $imagePath;
                                    }
                                    // If the path has contestants prefix
                                    elseif (str_contains($imagePath, 'contestants/')) {
                                        $possiblePaths[] = 'storage/' . $imagePath;
                                        $possiblePaths[] = $imagePath;
                                    }
                                    // Just the filename
                                    else {
                                        $possiblePaths[] = 'storage/contestants/' . $imagePath;
                                        $possiblePaths[] = 'storage/' . $imagePath;
                                        $possiblePaths[] = 'contestants/' . $imagePath;
                                        $possiblePaths[] = $imagePath;
                                    }
                                }
                                
                                foreach ($possiblePaths as $path) {
                                    if (file_exists(public_path($path))) {
                                        $fullPath = $path;
                                        $imageFound = true;
                                        break;
                                    }
                                }
                                @endphp
                                
                                @if($imageFound && $fullPath)
                                    <img src="{{ asset($fullPath) }}" alt="{{ $result['contestant']->name }}" class="profile-image">
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
                                        @php
                                        $crImagePath = $cr['contestant']->image;
                                        $crFullPath = '';
                                        $crImageFound = false;
                                        
                                        $crPossiblePaths = [];
                                        
                                        if ($crImagePath) {
                                            if (str_contains($crImagePath, 'storage/')) {
                                                $crPossiblePaths[] = $crImagePath;
                                            }
                                            elseif (str_contains($crImagePath, 'contestants/')) {
                                                $crPossiblePaths[] = 'storage/' . $crImagePath;
                                                $crPossiblePaths[] = $crImagePath;
                                            }
                                            else {
                                                $crPossiblePaths[] = 'storage/contestants/' . $crImagePath;
                                                $crPossiblePaths[] = 'storage/' . $crImagePath;
                                                $crPossiblePaths[] = 'contestants/' . $crImagePath;
                                                $crPossiblePaths[] = $crImagePath;
                                            }
                                        }
                                        
                                        foreach ($crPossiblePaths as $crPath) {
                                            if (file_exists(public_path($crPath))) {
                                                $crFullPath = $crPath;
                                                $crImageFound = true;
                                                break;
                                            }
                                        }
                                        @endphp
                                        
                                        @if($crImageFound && $crFullPath)
                                            <img src="{{ asset($crFullPath) }}" alt="{{ $cr['contestant']->name }}" class="profile-image-sm">
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
