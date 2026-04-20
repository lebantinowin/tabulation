@extends('layouts.app')

@section('title', 'Event Results')

@section('content')
<div class="page-header">
    <h1>Event Results</h1>
</div>

@if(count($events) == 0)
    <div class="alert alert-danger">
        No events found.
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
        @foreach($events as $event)
            <a href="{{ route('results.show', $event->id) }}" style="text-decoration: none; color: inherit;">
                <div class="card" style="padding: 0; overflow: hidden; cursor: pointer; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    @php
                    $bannerPath = $event->banner;
                    $bannerFullPath = '';
                    $bannerImageFound = false;
                    
                    $bannerPossiblePaths = [];
                    
                    if ($bannerPath) {
                        if (str_contains($bannerPath, 'storage/')) {
                            $bannerPossiblePaths[] = $bannerPath;
                        }
                        elseif (str_contains($bannerPath, 'events/')) {
                            $bannerPossiblePaths[] = 'storage/' . $bannerPath;
                            $bannerPossiblePaths[] = $bannerPath;
                        }
                        else {
                            $bannerPossiblePaths[] = 'storage/events/' . $bannerPath;
                            $bannerPossiblePaths[] = 'storage/' . $bannerPath;
                            $bannerPossiblePaths[] = 'events/' . $bannerPath;
                            $bannerPossiblePaths[] = $bannerPath;
                        }
                    }
                    
                    foreach ($bannerPossiblePaths as $bannerP) {
                        if (file_exists(public_path($bannerP))) {
                            $bannerFullPath = $bannerP;
                            $bannerImageFound = true;
                            break;
                        }
                    }
                    @endphp
                    
                    @if($bannerImageFound && $bannerFullPath)
                        <div class="event-banner">
                            <img src="{{ asset($bannerFullPath) }}" alt="{{ $event->name }}" style="width: 100%; height: 120px; object-fit: cover;">
                        </div>
                    @else
                        <div style="width: 100%; height: 120px; background: linear-gradient(135deg, #040D12 0%, #1a2634 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt" style="font-size: 3rem; color: rgba(255,255,255,0.3);"></i>
                        </div>
                    @endif
                    
                    <div style="padding: 0.75rem;">
                        <h3 style="margin-bottom: 0.25rem; color: #040D12; font-size: 1.1rem;">{{ $event->name }}</h3>
                        
                        <p style="color: #666; margin-bottom: 0.25rem; font-size: 0.9rem;">
                            <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
                        </p>
                        
                        @php
                            $eventStatusClass = '';
                            $eventStatusText = '';
                            
                            if($event->status == 'upcoming') {
                                $eventStatusClass = 'badge-warning';
                                $eventStatusText = 'Upcoming';
                            } elseif($event->status == 'ongoing') {
                                $eventStatusClass = 'badge-success';
                                $eventStatusText = 'Ongoing';
                            } elseif($event->status == 'completed') {
                                $eventStatusClass = 'badge-secondary';
                                $eventStatusText = 'Completed';
                            } else {
                                $eventStatusClass = 'badge-secondary';
                                $eventStatusText = $event->status;
                            }
                        @endphp
                        
                        <span class="badge {{ $eventStatusClass }}">{{ $eventStatusText }}</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
