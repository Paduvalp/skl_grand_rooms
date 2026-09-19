@extends('layouts.app')

@section('title', 'Photo Gallery')

@section('meta_description', 'Photos of SKL Grand Rooms in Kenchenhalli, RR Nagar, Bengaluru: the building, reception, rooms, bathrooms and parking.')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Photo Gallery</h1>
        <p class="mb-0 opacity-75">Home / Gallery</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        @if ($images->isEmpty())
            <div class="alert alert-info mb-0">Photos are on their way. Please check back soon.</div>
        @else
            @if (count($categories) > 1)
                <div class="gallery-tabs d-flex flex-wrap gap-2 mb-4" role="group" aria-label="Filter photos">
                    <button type="button" class="btn btn-sm btn-outline-dark active" data-filter="all" aria-pressed="true">All</button>
                    @foreach ($categories as $key => $label)
                        <button type="button" class="btn btn-sm btn-outline-dark" data-filter="{{ $key }}" aria-pressed="false">{{ $label }}</button>
                    @endforeach
                </div>
            @endif

            <div class="row g-3" id="galleryGrid">
                @foreach ($images as $photo)
                    @php [$pw, $ph] = $photo->dimensions(true); @endphp
                    <div class="col-6 col-md-4 col-lg-3" data-category="{{ $photo->category }}">
                        <a href="{{ $photo->url() }}" class="gallery-thumb d-block"
                           data-gallery-item data-caption="{{ $photo->caption }}">
                            <img src="{{ $photo->thumbUrl() }}" alt="{{ $photo->altText() }}"
                                 loading="lazy" width="{{ $pw }}" height="{{ $ph }}">
                        </a>
                        @if ($photo->caption)
                            <div class="small text-muted mt-1">{{ $photo->caption }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if ($images->isNotEmpty())
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-label="Photo viewer" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 py-2">
                    <span class="text-white-50 small" id="galleryCaption"></span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pt-0 position-relative">
                    <img alt="" id="galleryImage" class="rounded">
                    <button type="button" class="btn btn-light btn-sm position-absolute top-50 start-0 ms-2" id="galleryPrev" aria-label="Previous photo">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm position-absolute top-50 end-0 me-2" id="galleryNext" aria-label="Next photo">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<section class="py-5 bg-hnp text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-2">Like what you see?</h2>
        <p class="mb-4">Pick your dates. No payment is needed to book.</p>
        <a href="{{ route('booking') }}" class="btn btn-accent btn-lg px-4">Book a Room</a>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Gallery' => route('gallery')])) !!}</script>
@endpush

@push('scripts')
<script>
(function () {
    var grid = document.getElementById('galleryGrid');
    var modalEl = document.getElementById('galleryModal');

    if (!grid || !modalEl) {
        return;
    }

    var cells = Array.prototype.slice.call(grid.children);
    var img = document.getElementById('galleryImage');
    var caption = document.getElementById('galleryCaption');
    var modal = new bootstrap.Modal(modalEl);
    var current = [];
    var index = 0;

    // Category tabs: hide the photos from other categories.
    document.querySelectorAll('.gallery-tabs [data-filter]').forEach(function (button) {
        button.addEventListener('click', function () {
            var filter = button.getAttribute('data-filter');

            document.querySelectorAll('.gallery-tabs [data-filter]').forEach(function (b) {
                b.classList.toggle('active', b === button);
                b.setAttribute('aria-pressed', b === button ? 'true' : 'false');
            });

            cells.forEach(function (cell) {
                cell.classList.toggle('d-none', filter !== 'all' && cell.getAttribute('data-category') !== filter);
            });
        });
    });

    function visibleLinks() {
        return cells.filter(function (cell) {
            return !cell.classList.contains('d-none');
        }).map(function (cell) {
            return cell.querySelector('[data-gallery-item]');
        });
    }

    function show(i) {
        index = (i + current.length) % current.length;
        var link = current[index];
        var thumb = link.querySelector('img');

        img.src = link.getAttribute('href');
        img.alt = thumb.alt;
        caption.textContent = link.getAttribute('data-caption') || thumb.alt;
    }

    // Lightbox: open the full size photo, step through the visible ones.
    grid.addEventListener('click', function (event) {
        var link = event.target.closest('[data-gallery-item]');

        if (!link) {
            return;
        }

        event.preventDefault();
        current = visibleLinks();
        show(current.indexOf(link));
        modal.show();
    });

    document.getElementById('galleryPrev').addEventListener('click', function () { show(index - 1); });
    document.getElementById('galleryNext').addEventListener('click', function () { show(index + 1); });

    modalEl.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') { show(index - 1); }
        if (event.key === 'ArrowRight') { show(index + 1); }
    });
})();
</script>
@endpush
