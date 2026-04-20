@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="text-center" style="padding: 6rem 0; margin-top: 18px;">

    <h1>Tabulation System</h1>
    <p style="margin: 1.5rem 0; color: #666;">A comprehensive solution for judging and scoring events</p>
    
    <div style="display: flex; gap: 2rem; justify-content: center; margin-top: 3rem;">
        <div class="card" style="padding: 2rem; width: 300px;">
            <h3><i class="fas fa-user-tie"></i> For Judges</h3>
            <p style="color: #666; margin: 1rem 0;">Submit your scores and provide feedback</p>
            <a href="{{ route('login') }}" class="btn">Enter System</a>
        </div>
    </div>

    <footer><p style="margin: 1rem 0; color: #666;">Powered By: ECCENTRI, Inc.</p></footer>
</div>
@endsection
