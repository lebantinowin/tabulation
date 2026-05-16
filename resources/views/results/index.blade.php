@extends('layouts.app')

@section('title', 'Event Results')

@section('content')
<style>
    .event-result-card {
        padding: 0; 
        overflow: hidden; 
        cursor: pointer; 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 16px;
        background: var(--color-white);
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .event-result-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        border-color: rgba(0,0,0,0.1);
    }
    .event-result-card .event-banner-container {
        width: 100%; 
        height: 180px; 
        overflow: hidden;
        position: relative;
    }
    .event-result-card .event-banner-container img {
        width: 100%; 
        height: 100%; 
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .event-result-card:hover .event-banner-container img {
        transform: scale(1.08);
    }
    .event-result-card .event-details {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .event-result-card h3 {
        margin-bottom: 0.5rem; 
        color: var(--color-btn); 
        font-size: 1.3rem;
        font-weight: 600;
        line-height: 1.3;
    }
    .event-result-card .event-date {
        color: var(--color-muted); 
        margin-bottom: 1.5rem; 
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .event-result-card .event-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    .event-result-card .view-btn {
        color: var(--color-info);
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        transition: gap 0.3s ease;
    }
    .event-result-card:hover .view-btn {
        gap: 0.7rem;
    }
    .fallback-banner {
        width: 100%; 
        height: 100%; 
        background: linear-gradient(135deg, #040D12 0%, #2B6CB0 100%); 
        display: flex; 
        align-items: center; 
        justify-content: center;
        transition: transform 0.5s ease;
    }
    .event-result-card:hover .fallback-banner {
        transform: scale(1.08);
    }
</style>

<div class="page-header">
    <h1>Event Results</h1>
</div>

@if(count($events) == 0)
    <div class="alert alert-danger">
        No events found.
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
        @foreach($events as $event)
            <a href="{{ route('results.show', $event->id) }}" style="text-decoration: none; color: inherit; display: block;">
                <div class="card event-result-card">
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
                        <div class="event-banner-container">
                            <img src="{{ asset($bannerFullPath) }}" alt="{{ $event->name }}">
                        </div>
                    @else
                        <div class="event-banner-container">
                            <div class="fallback-banner">
                                <i class="fas fa-trophy" style="font-size: 3.5rem; color: rgba(255,255,255,0.2);"></i>
                            </div>
                        </div>
                    @endif
                    
                    <div class="event-details">
                        <h3>{{ $event->name }}</h3>
                        
                        <p class="event-date">
                            <i class="fas fa-calendar-day"></i> {{ \Carbon\Carbon::parse($event->date)->format('F d, Y') }}
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
                        
                        <div class="event-footer">
                            <span class="badge {{ $eventStatusClass }}">{{ $eventStatusText }}</span>
                            <div class="view-btn">
                                View Results <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
    
    @if($events->hasPages())
    <div class="mt-4 flex justify-between items-center" style="flex-wrap: wrap; gap: 1rem; margin-top: 2rem;">
        <div class="pagination-info" style="font-size: 0.85rem; color: var(--color-muted);">
            Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} events
        </div>
        {{ $events->links('pagination::custom') }}
    </div>
    @endif
@endif
@endsection
