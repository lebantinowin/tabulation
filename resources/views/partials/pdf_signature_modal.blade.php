<div id="pdfSignatureModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <button type="button" class="modal-close" onclick="document.getElementById('pdfSignatureModal').classList.remove('active')">
            <i class="fas fa-times"></i>
        </button>
        <h3 class="mb-3"><i class="fas fa-file-signature"></i> Configure PDF Signatories</h3>
        <p class="text-muted mb-4" style="font-size: 0.9rem;">Select who will sign this document. They will be added to the signature block at the bottom of the PDF.</p>

        <form id="pdfExportForm" method="GET" action="">
            <!-- Hidden inputs will be populated by JS -->
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="form-group mb-4">
                <label>Admin Signatory</label>
                <input type="text" name="admin_name" class="form-control" value="{{ auth()->user()->name }}" required>
            </div>

            <div class="form-group mb-4">
                <label>Judges Signatories</label>
                <div style="background: var(--color-main); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); max-height: 200px; overflow-y: auto;">
                    @foreach($event->judges as $judge)
                        <label style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; cursor: pointer; font-weight: normal;">
                            <input type="checkbox" name="judges[]" value="{{ $judge->name }}" checked style="width: 16px; height: 16px;">
                            {{ $judge->name }}
                        </label>
                    @endforeach
                    @if($event->judges->isEmpty())
                        <p class="text-muted" style="margin: 0; font-size: 0.85rem;">No judges assigned to this event yet.</p>
                    @endif
                </div>
            </div>

            <div class="flex gap-2" style="justify-content: flex-end; margin-top: 2rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('pdfSignatureModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn" style="background: var(--color-success);">
                    <i class="fas fa-download"></i> Generate PDF
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPdfModal(actionUrl) {
        document.getElementById('pdfExportForm').action = actionUrl;
        document.getElementById('pdfSignatureModal').classList.add('active');
    }
</script>
