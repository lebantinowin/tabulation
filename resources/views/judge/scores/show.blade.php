@extends('layouts.app')

@section('title', 'View Score - Judge')

@section('content')
<div class="page-header">
    <h1>Score Details</h1>
    <a href="{{ route('scores.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-body">
        <div class="mb-3">
            <label>Contestant:</label>
            <div class="p-3" style="background: var(--color-main); border-radius: 8px;">
                {{ $score->contestant->name ?? 'N/A' }}
            </div>
        </div>
        <div class="mb-3">
            <label>Criteria:</label>
            <div class="p-3" style="background: var(--color-main); border-radius: 8px;">
                {{ $score->criteria->name ?? 'N/A' }}
            </div>
        </div>
        <div class="mb-3">
            <label>Score:</label>
            <div class="p-3" style="background: var(--color-main); border-radius: 8px; font-weight: bold; font-size: 1.2rem;">
                {{ $score->score }}
            </div>
        </div>
        <div class="mb-3">
            <label>Remarks:</label>
            <div class="p-3" style="background: var(--color-main); border-radius: 8px; min-height: 100px;">
                {{ $score->remarks ?? 'N/A' }}
            </div>
        </div>
        <div class="mb-4">
            <label>Date:</label>
            <div class="text-muted">
                {{ $score->created_at->format('M d, Y H:i') }}
            </div>
        </div>
        
        <div class="text-center">
            <a href="{{ route('scores.edit', $score->id) }}" class="btn btn-primary w-full">
                <i class="fas fa-edit"></i> Edit Score
            </a>
        </div>
    </div>
</div>
@endsection
