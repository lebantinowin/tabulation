@extends('layouts.app')

@section('title', 'New Report')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit text-muted"></i> New Report</h1>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="max-width: 620px;">
    <form action="{{ route('admin.reports.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label>Report Type</label>
            <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal; padding: 0.75rem 1.25rem; border: 2px solid var(--color-border); border-radius: 8px; flex: 1; transition: border-color 0.2s;" id="type-contribution-wrap">
                    <input type="radio" name="type" value="contribution" {{ old('type', 'contribution') === 'contribution' ? 'checked' : '' }} onchange="updateTypeUI()" id="type-contribution">
                    <i class="fas fa-clipboard-check" style="color: var(--color-btn);"></i>
                    <span><strong>Contribution</strong><br><small style="font-weight:normal; color: var(--color-muted);">Share what you've done</small></span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal; padding: 0.75rem 1.25rem; border: 2px solid var(--color-border); border-radius: 8px; flex: 1; transition: border-color 0.2s;" id="type-bug-wrap">
                    <input type="radio" name="type" value="bug" {{ old('type') === 'bug' ? 'checked' : '' }} onchange="updateTypeUI()" id="type-bug">
                    <i class="fas fa-bug" style="color: #dc2626;"></i>
                    <span><strong>Bug / Error</strong><br><small style="font-weight:normal; color: var(--color-muted);">Report a problem</small></span>
                </label>
            </div>
            @error('type') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Brief summary of your report">
            @error('title') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="body">Details</label>
            <textarea id="body" name="body" rows="6" required placeholder="Describe in detail what you contributed or what issue you encountered..." style="width: 100%; resize: vertical;">{{ old('body') }}</textarea>
            @error('body') <div class="text-danger" style="font-size: 0.85rem; margin-top: 4px;">{{ $message }}</div> @enderror
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Submit Report
            </button>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function updateTypeUI() {
    const contribution = document.getElementById('type-contribution');
    const bug = document.getElementById('type-bug');
    document.getElementById('type-contribution-wrap').style.borderColor = contribution.checked ? 'var(--color-btn)' : 'var(--color-border)';
    document.getElementById('type-bug-wrap').style.borderColor = bug.checked ? '#dc2626' : 'var(--color-border)';
}
updateTypeUI();
</script>
@endsection
