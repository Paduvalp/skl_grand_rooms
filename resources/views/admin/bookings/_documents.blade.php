{{-- Thumbnails for images, an icon for PDFs. Both open in a new tab.
     canDelete: a delete button per file (booking page).
     markForRemoval: a checkbox per file, removed when the edit form saves. --}}
@if ($booking->documents->isEmpty())
    <p class="small text-muted mb-0">No documents uploaded yet.</p>
@else
    <div class="row g-2">
        @foreach ($booking->documents as $document)
            @php $url = route('admin.bookings.documents.show', [$booking, $document]); @endphp
            <div class="col-6">
                <div class="border rounded p-2 h-100 d-flex flex-column" data-document>
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="d-block text-center mb-2 text-decoration-none">
                        @if ($document->isImage())
                            <img src="{{ $url }}" alt="{{ $document->original_name }}" loading="lazy"
                                 class="rounded" style="width:100%;height:110px;object-fit:cover">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:110px">
                                <i class="bi bi-file-earmark-pdf text-danger" style="font-size:2.5rem"></i>
                            </div>
                        @endif
                    </a>
                    @if ($document->label)
                        <div class="small fw-semibold text-truncate" title="{{ $document->label }}">{{ $document->label }}</div>
                    @endif
                    <div class="small text-truncate" title="{{ $document->original_name }}">{{ $document->original_name }}</div>
                    <div class="small text-muted mb-2">{{ $document->sizeLabel() }} &middot; {{ $document->created_at->format('d M Y') }}</div>
                    <div class="d-flex gap-1 mt-auto align-items-center">
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary flex-grow-1">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Open
                        </a>
                        @if ($canDelete ?? false)
                            <button type="submit" form="delete-document-{{ $document->id }}" class="btn btn-sm btn-outline-danger" title="Remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                        @if ($markForRemoval ?? false)
                            <input type="checkbox" class="btn-check" name="remove_documents[]" value="{{ $document->id }}"
                                   id="remove-document-{{ $document->id }}" autocomplete="off"
                                   onchange="this.closest('[data-document]').classList.toggle('opacity-50', this.checked)"
                                   @checked(in_array($document->id, old('remove_documents', [])))>
                            <label class="btn btn-sm btn-outline-danger" for="remove-document-{{ $document->id }}" title="Remove when saved">
                                <i class="bi bi-trash"></i> Remove
                            </label>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($canDelete ?? false)
        @foreach ($booking->documents as $document)
            <form id="delete-document-{{ $document->id }}" method="POST" class="d-none"
                  action="{{ route('admin.bookings.documents.destroy', [$booking, $document]) }}"
                  data-confirm="Remove {{ $document->label ?: $document->original_name }}?">
                @csrf @method('DELETE')
            </form>
        @endforeach
    @endif
@endif
