@extends('layouts.app')
{{-- Kept under ~60 characters with a single "|", so Google shows it as written. --}}
@section('title', 'Rooms in RR Nagar, Bengaluru'.(!empty($fromPrice) ? ' from ₹'.number_format((float) $fromPrice, 0) : ''))

@section('meta_description', 'Clean AC rooms with geyser, free Wi-Fi, TV and parking in Kenchenhalli, RR Nagar, Bengaluru. Near Kengeri and Mysore Road. Book online in a minute.')

@section('content')

<section class="hero">
    <div class="container text-center">
        <p class="text-uppercase text-accent fw-semibold mb-2" style="letter-spacing:2px">
            {{ $settings['tagline'] ?? 'Comfort, care and a warm welcome' }}
        </p>
        <h1 class="display-5 mb-3">{{ $settings['hero_title'] ?? 'A comfortable stay, every single time' }}</h1>
        <p class="lead mx-auto mb-4" style="max-width:640px">
            {{ $settings['hero_subtitle'] ?? 'Clean rooms, friendly staff and honest prices in RR Nagar, near Mysore Road.' }}
        </p>
        <div class="hero-actions d-flex flex-column flex-sm-row align-items-center justify-content-center gap-3">
            <a href="{{ route('booking') }}" class="btn btn-accent btn-lg px-4">Book a Room</a>
            <a href="{{ route('rooms.index') }}" class="btn btn-outline-light btn-lg px-4">See Our Rooms</a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-lg-3">
                <div class="service-icon mx-auto"><i class="bi bi-snow"></i></div>
                <h6 class="fw-bold mb-1">Air Conditioning</h6>
                <p class="text-muted small mb-0">Every room is air conditioned.</p>
            </div>
            <div class="col-6 col-lg-3">
                <div class="service-icon mx-auto"><i class="bi bi-droplet-half"></i></div>
                <h6 class="fw-bold mb-1">Hot Water Geyser</h6>
                <p class="text-muted small mb-0">A geyser in every bathroom.</p>
            </div>
            <div class="col-6 col-lg-3">
                <div class="service-icon mx-auto"><i class="bi bi-wifi"></i></div>
                <h6 class="fw-bold mb-1">Free Wi-Fi</h6>
                <p class="text-muted small mb-0">Free for all guests, in every room.</p>
            </div>
            <div class="col-6 col-lg-3">
                <div class="service-icon mx-auto"><i class="bi bi-p-square"></i></div>
                <h6 class="fw-bold mb-1">Parking</h6>
                <p class="text-muted small mb-0">On-site parking for guests.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-hnp-soft">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Rooms</h2>
            <p class="text-muted mb-0">Pick the room that fits your trip. Every rate below is per night.</p>
        </div>

        <div class="row g-4">
            @forelse ($featuredRooms as $room)
                <div class="col-md-6 col-lg-4">
                    <div class="card room-card">
                        <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }} at {{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}" loading="lazy" width="800" height="560">
                        <div class="card-body">
                            <span class="badge bg-hnp mb-2">{{ $room->type }}</span>
                            <h5 class="card-title fw-bold">{{ $room->name }}</h5>
                            <p class="text-muted small">{{ $room->short_description }}</p>
                            <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                <span><i class="bi bi-people me-1"></i>{{ $room->capacity }} guests</span>
                                <span><i class="bi bi-door-closed me-1"></i>{{ $room->bed_type }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price">&#8377;{{ number_format($room->price, 0) }} <small class="text-muted fw-normal">/ night</small></span>
                                <a href="{{ route('rooms.show', $room->slug) }}" class="btn btn-sm btn-hnp">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">No rooms have been added yet. Add them from the admin panel.</div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('rooms.index') }}" class="btn btn-outline-dark">See all rooms</a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">What We Offer</h2>
            <p class="text-muted mb-0">Small things that make a stay easy.</p>
        </div>
        <div class="row g-4">
            @foreach ($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi {{ $service->icon }}"></i></div>
                        <h6 class="fw-bold">{{ $service->title }}</h6>
                        <p class="text-muted small mb-0">{{ $service->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if ($galleryImages->isNotEmpty())
    <section class="py-5 bg-hnp-soft">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="fw-bold">See the place</h2>
                <p class="text-muted mb-0">Real photos of the hotel, the rooms and the parking.</p>
            </div>
            <div class="row g-3">
                @foreach ($galleryImages as $photo)
                    @php [$pw, $ph] = $photo->dimensions(true); @endphp
                    <div class="col-6 col-md-4">
                        <a href="{{ route('gallery') }}" class="gallery-thumb d-block">
                            <img src="{{ $photo->thumbUrl() }}" alt="{{ $photo->altText() }}"
                                 loading="lazy" width="{{ $pw }}" height="{{ $ph }}">
                        </a>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('gallery') }}" class="btn btn-outline-dark">View gallery</a>
            </div>
        </div>
    </section>
@endif

@if ($reviews->isNotEmpty())
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">What our guests say</h2>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($reviews as $review)
                    <div class="col-md-6 col-lg-4">
                        <figure class="review-card h-100 mb-0">
                            <div class="review-stars mb-2" aria-label="{{ $review->rating }} out of 5 stars">
                                @for ($star = 1; $star <= 5; $star++)
                                    <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}" aria-hidden="true"></i>
                                @endfor
                            </div>
                            <blockquote class="mb-3">
                                <p class="mb-0">{{ $review->review_text }}</p>
                            </blockquote>
                            <figcaption class="small text-muted">
                                <strong class="text-body">{{ $review->guest_name }}</strong>
                                &middot; {{ $review->sourceLabel() }}
                            </figcaption>
                        </figure>
                    </div>
                @endforeach
            </div>
            @if (!empty($settings['google_business_url']))
                <div class="text-center mt-4">
                    <a href="{{ $settings['google_business_url'] }}" target="_blank" rel="noopener" class="btn btn-outline-dark">
                        <i class="bi bi-google me-1"></i>Read more reviews on Google
                    </a>
                </div>
            @endif
        </div>
    </section>
@endif

@include('partials.nearby')

@push('schema')
{{-- Tells Google the site's name, so results are headed "SKL Grand Rooms" rather than a guess. --}}
<script type="application/ld+json">{!! \App\Support\Seo::json([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => 'SKL Grand Rooms',
    'alternateName' => ['SKL GRAND ROOMS', 'sklgrandrooms.com'],
    'url' => rtrim((string) config('app.url'), '/').'/',
]) !!}</script>
@endpush

<section class="py-5 bg-hnp text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-2">Ready when you are</h2>
        <p class="mb-4">Send us your dates and we will confirm your room shortly.</p>
        <a href="{{ route('booking') }}" class="btn btn-accent btn-lg px-4">Book Now</a>
    </div>
</section>

@endsection
