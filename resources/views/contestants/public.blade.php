@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center mb-5">
        <h1>Contestants</h1>
        <p class="text-muted">Meet our talented contestants</p>
    </div>
    
    <div class="row">
        @forelse($contestants as $contestant)
        <div class="col-md-4 mb-4">
            <div class="card contestant-card">
                <div class="card-img-top-wrapper">
                    @php
                        $imagePath = $contestant->image;
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
                        <img src="{{ asset($fullPath) }}" alt="{{ $contestant->name }}" class="card-img-top">
                    @else
                        <div class="no-image">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="card-body text-center">
                    <div class="contestant-number">{{ $contestant->number }}</div>
                    <h5 class="card-title">{{ $contestant->name }}</h5>
                    @if($contestant->description)
                        <p class="card-text text-muted">{{ $contestant->description }}</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted">No contestants available yet.</p>
        </div>
        @endforelse
    </div>
</div>

<style>
.contestant-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}

.contestant-card:hover {
    transform: translateY(-5px);
}

.card-img-top-wrapper {
    width: 100%;
    height: 250px;
    overflow: hidden;
    background: #f8f9fa;
}

.card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    color: #adb5bd;
    font-size: 3rem;
}

.contestant-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: -35px auto 15px;
    position: relative;
    z-index: 1;
    border: 3px solid white;
}

.card-title {
    font-weight: bold;
    margin-bottom: 10px;
}
</style>
@endsection
