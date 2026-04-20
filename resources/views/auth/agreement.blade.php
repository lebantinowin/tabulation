@extends('layouts.app')

@section('title', 'Judge Agreement')

@section('content')
<div style="max-width: 600px; margin: 2rem auto;">
    <div class="card">
        <h2 class="text-center">Judge Agreement</h2>
        
        <div style="margin: 2rem 0;">
            <h3>Terms and Conditions</h3>
            <p>As a judge in this tabulation system, you agree to:</p>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li>Evaluate all contestants fairly and impartially</li>
                <li>Provide scores based on the established criteria</li>
                <li>Maintain confidentiality of the scoring process</li>
                <li>Submit scores within the specified timeframe</li>
                <li>Follow all rules and guidelines set by the event organizers</li>
            </ul>
            
            <p>By clicking "Accept Agreement", you confirm that you have read and agree to abide by these terms.</p>
        </div>
        
        <form method="POST" action="{{ route('agreement.accept') }}">
            @csrf
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="accept" required style="width: auto;">
                    I have read and agree to the terms and conditions
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Accept Agreement</button>
        </form>
    </div>
</div>
@endsection
