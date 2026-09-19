@extends('layouts.admin')
@section('title', 'Gallery')
@section('heading', 'Gallery')

@section('content')

<div class="card stat-card mb-4">
    <div class="card-header bg-white"><strong>Upload photos</strong></div>
    <div class="card-body">
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
            @csrf
            <div class="col-lg-5">
                <label class="form-label">Photos <span class="text-danger">*</span></label>
                <input type="file" name="images[]" multiple required accept=".jpg,.jpeg,.png,.webp"
                       class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
                <div class="form-text">JPG, PNG or WEBP, up to 4 MB each. Pick several at once (up to 20). Large photos are shrunk to 1600px wide.</div>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    @foreach (\App\Models\GalleryImage::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" @selected(old('category') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label class="form-label">Caption <span class="text-muted small">(optional)</span></label>
                <input type="text" name="caption" value="{{ old('caption') }}" maxlength="190" class="form-control" placeholder="Deluxe Double Room">
            </div>
            <div class="col-lg-2">
                <button class="btn btn-hnp w-100"><i class="bi bi-upload me-1"></i>Upload</button>
            </div>
        </form>
    </div>
</div>

<p class="text-muted small">
    Photos show on the Gallery page in the order below (smaller number first). The six newest
    shown photos also appear on the home page. Leave the caption empty and the alt text becomes
    "SKL Grand Rooms – category".
</p>

<div class="row g-3">
    @forelse ($images as $photo)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card stat-card h-100 {{ $photo->is_active ? '' : 'opacity-75' }}">
                <img src="{{ $photo->thumbUrl() }}" alt="{{ $photo->altText() }}" loading="lazy"
                     class="card-img-top" style="height:160px;object-fit:cover">
                <div class="card-body">
                    <div class="mb-2">
                        @if ($photo->is_active)
                            <span class="badge bg-success">Shown</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                        <span class="badge bg-light text-dark border">{{ $photo->categoryLabel() }}</span>
                    </div>

                    <form action="{{ route('admin.gallery.update', $photo) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="text" name="caption" value="{{ $photo->caption }}" maxlength="190"
                               class="form-control form-control-sm mb-2" placeholder="Caption" aria-label="Caption">
                        <div class="d-flex gap-2 mb-2">
                            <select name="category" class="form-select form-select-sm" aria-label="Category">
                                @foreach (\App\Models\GalleryImage::CATEGORIES as $key => $label)
                                    <option value="{{ $key }}" @selected($photo->category === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="sort_order" value="{{ $photo->sort_order }}" min="0" max="9999"
                                   class="form-control form-control-sm" style="width:80px" aria-label="Order" title="Order">
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   id="active{{ $photo->id }}" @checked($photo->is_active)>
                            <label class="form-check-label small" for="active{{ $photo->id }}">Show on website</label>
                        </div>
                        <button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-check2 me-1"></i>Save</button>
                    </form>

                    <div class="d-flex gap-2 mt-2">
                        <form action="{{ route('admin.gallery.toggle', $photo) }}" method="POST" class="flex-fill">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi {{ $photo->is_active ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i>{{ $photo->is_active ? 'Hide' : 'Show' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.gallery.destroy', $photo) }}" method="POST"
                              data-confirm="Delete this photo? The file is removed from the server too.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" aria-label="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card stat-card"><div class="card-body text-center text-muted py-4">No photos yet. Upload the first ones above.</div></div>
        </div>
    @endforelse
</div>

<div class="mt-3">{{ $images->links() }}</div>

@endsection
