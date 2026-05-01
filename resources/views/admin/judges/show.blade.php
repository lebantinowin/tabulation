@extends('layouts.app')

@section('title', 'Judge Details - Admin')

@section('content')
<div class="page-header">
    <h1>Judge Details</h1>
    <a href="{{ route('judges.index') }}" class="btn" title="Back to Judges List">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="judge-details-container" style="display: flex; min-height: 500px;">

        {{-- LEFT: Image column --}}
        <div class="judge-image" style="flex: 0 0 350px; position: relative;">
            @php
                $imagePath = $judge->image;
                $fullPath = '';
                $imageFound = false;
                $possiblePaths = [];

                if ($imagePath) {
                    if (str_contains($imagePath, 'storage/')) {
                        $possiblePaths[] = $imagePath;
                    } elseif (str_contains($imagePath, 'judges/')) {
                        $possiblePaths[] = 'storage/' . $imagePath;
                        $possiblePaths[] = $imagePath;
                    } else {
                        $possiblePaths[] = 'storage/judges/' . $imagePath;
                        $possiblePaths[] = 'storage/' . $imagePath;
                        $possiblePaths[] = 'judges/' . $imagePath;
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
                <img src="{{ asset($fullPath) }}" alt="{{ $judge->name }}"
                     style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0;">
            @else
                <div class="user-avatar"
                     style="width: 100%; height: 100%; display: flex; align-items: center;
                            justify-content: center; font-size: 5rem;
                            position: absolute; top: 0; left: 0;">
                    {{ strtoupper(substr($judge->name, 0, 1)) }}
                </div>
            @endif
        </div>

        {{-- RIGHT: Details column --}}
        <div class="judge-info"
             style="flex: 1; padding: 2rem; display: flex; flex-direction: column;
                    justify-content: center; position: relative;">

            {{-- Action buttons: top-right of the details panel --}}
            <div class="actions"
                 style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem;">
                <a href="{{ route('judges.edit', $judge->id) }}"
                   class="btn-icon btn-icon-edit" title="Edit Judge">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('judges.destroy', $judge->id) }}"
                      method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-icon btn-icon-delete"
                            onclick="confirmForm(this.closest('form'), 'This judge will be permanently deleted.', {title: 'Delete Judge?'})"
                            title="Delete Judge">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>

            <h2>{{ $judge->name }}</h2>

            <div class="form-group">
                <label>Login Code:</label>
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <span id="loginCode"
                          style="font-size: 1.5rem; font-weight: bold; letter-spacing: 4px;
                                 background: #f5f5f5; padding: 0.5rem 1rem;
                                 border-radius: 8px; border: 2px dashed var(--color-btn);">
                        {{ $judge->login_code ?? 'N/A' }}
                    </span>
                    <button type="button" class="btn-icon" id="copyBtn" onclick="copyLoginCode()" title="Copy Login Code">
                        <i class="fas fa-copy"></i>
                    </button>
                    <a href="{{ route('judges.edit', $judge->id) }}" class="btn-icon" title="Regenerate Login Code">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
                <small style="color: var(--color-muted);">Share this code with the judge for login</small>
            </div>

            <div class="form-group">
                <label>Status:</label>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span class="badge {{ $judge->is_active ? 'badge-success' : 'badge-secondary' }}">
                        {{ $judge->is_active ? 'Active' : 'Inactive' }}
                    </span>
                        <form action="{{ route('judges.toggleActive', $judge->id) }}" method="POST" style="display: inline;" id="toggleActiveForm">
                        @csrf
                        <button type="button"
                                class="btn-icon {{ $judge->is_active ? 'btn-icon-delete' : 'btn-icon-view' }}"
                                onclick="confirmForm(document.getElementById('toggleActiveForm'), 'Are you sure you want to {{ $judge->is_active ? 'deactivate' : 'activate' }} this judge?', {title: '{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge?', danger: '{{ $judge->is_active ? 'high' : 'medium' }}'})"
                                title="{{ $judge->is_active ? 'Deactivate' : 'Activate' }} Judge">
                            <i class="fas fa-{{ $judge->is_active ? 'ban' : 'check' }}"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="form-group">
                <label>Assigned Event:</label>
                @php
                    $event = \App\Models\Event::find($judge->event_id);
                @endphp
                @if($event)
                    <p>
                        <span class="badge badge-info">{{ $event->name }}</span>
                        <small style="color: var(--color-muted);">({{ $event->date }})</small>
                    </p>
                @else
                    <p style="color: var(--color-muted);">No event assigned</p>
                @endif
            </div>

            <div class="form-group">
                <label>Created At:</label>
                <p>{{ $judge->created_at->format('F d, Y') }}</p>
            </div>

        </div>
    </div>
</div>

<style>
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        background: var(--color-btn);
        color: white;
    }

    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-icon-edit { background: #D4A574; }
    .btn-icon-edit:hover { background: #b8956a; }

    .btn-icon-delete { background: #8B4513; }
    .btn-icon-delete:hover { background: #6B3410; }

    .btn-icon-activate { background: #28a745; }
    .btn-icon-activate:hover { background: #218838; }

    .btn-icon-deactivate { background: #dc3545; }
    .btn-icon-deactivate:hover { background: #c82333; }

    @media (max-width: 768px) {
        .judge-details-container {
            flex-direction: column !important;
        }
        .judge-image {
            flex: 0 0 300px !important;
            min-height: 300px;
        }
    }
</style>

<script>
function copyLoginCode() {
    var loginCode = document.getElementById('loginCode').innerText.trim();
    if (!loginCode || loginCode === 'N/A') return;

    function onSuccess() {
        // Change icon to checkmark briefly
        var btn = document.getElementById('copyBtn');
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(function () { btn.innerHTML = '<i class="fas fa-copy"></i>'; }, 2000);

        showBanner('<i class="fas fa-check-circle"></i> Login code <strong>' + loginCode + '</strong> copied to clipboard!');
    }

    // Modern clipboard API (works on HTTPS or localhost)
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(loginCode).then(onSuccess).catch(fallbackCopy);
    } else {
        fallbackCopy();
    }

    function fallbackCopy() {
        var temp = document.createElement('textarea');
        temp.value = loginCode;
        temp.style.position = 'fixed';
        temp.style.opacity = '0';
        document.body.appendChild(temp);
        temp.focus();
        temp.select();
        try {
            document.execCommand('copy');
            onSuccess();
        } catch (e) {
            showBanner('<i class="fas fa-exclamation-circle"></i> Could not copy. Please copy manually: <strong>' + loginCode + '</strong>');
        }
        document.body.removeChild(temp);
    }
}

function showBanner(message) {
    var banner = document.createElement('div');
    banner.className = 'alert alert-success';
    banner.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;min-width:300px;text-align:center;box-shadow:0 4px 6px rgba(0,0,0,0.1);';
    banner.innerHTML = message;
    document.body.appendChild(banner);
    setTimeout(function () {
        banner.style.opacity = '0';
        banner.style.transition = 'opacity 0.5s';
        setTimeout(function () { banner.remove(); }, 500);
    }, 3000);
}
</script>
@endsection