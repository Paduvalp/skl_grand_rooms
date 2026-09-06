@extends('layouts.app')
@section('title', $room->name.' in RR Nagar, Bengaluru')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($room->short_description ?: $room->description), 150))

@section('content')

<div class="page-head">
    <div class="container">
        <h1>{{ $room->name }}</h1>
        <p class="mb-0 opacity-75">Home / Rooms / {{ $room->name }}</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }} at {{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }} in RR Nagar, Bengaluru" class="img-fluid rounded-3 mb-4" style="width:100%;max-height:430px;object-fit:cover">

                <h4 class="fw-bold">About this room</h4>
                @foreach (preg_split('/\r\n|\r|\n/', (string) $room->description) as $para)
                    @if (trim($para) !== '')
                        <p class="text-muted">{{ $para }}</p>
                    @endif
                @endforeach

                @if (count($room->amenityList()))
                    <h5 class="fw-bold mt-4 mb-3">Room amenities</h5>
                    <div class="row g-2">
                        @foreach ($room->amenityList() as $amenity)
                            <div class="col-sm-6 col-lg-4">
                                <i class="bi bi-check2-circle text-success me-2"></i>{{ $amenity }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card booking-box">
                    <div class="card-body p-4">
                        <div class="price h4 mb-1" style="color:var(--hnp-primary)">
                            &#8377;{{ number_format($room->price, 0) }}
                            <small class="text-muted fs-6 fw-normal">/ night</small>
                        </div>
                        <span class="badge bg-hnp mb-3">{{ $room->type }}</span>

                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Max guests</span><strong>{{ $room->capacity }}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Bed</span><strong>{{ $room->bed_type ?: '—' }}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Room size</span><strong>{{ $room->size ?: '—' }}</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Check-in</span><strong>{{ $settings['checkin_time'] ?? '12:00 PM' }}</strong></div>
                        <div class="d-flex justify-content-between py-2 small"><span class="text-muted">Check-out</span><strong>{{ $settings['checkout_time'] ?? '11:00 AM' }}</strong></div>

                        <a href="{{ route('booking', ['room' => $room->slug]) }}" class="btn btn-hnp w-100 mt-3">Book This Room</a>
                        <p class="text-muted small text-center mt-2 mb-0">No payment needed now. We confirm by phone or email.</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($otherRooms->count())
            <h4 class="fw-bold mt-5 mb-4">Other rooms you may like</h4>
            <div class="row g-4">
                @foreach ($otherRooms as $other)
                    <div class="col-md-4">
                        <div class="card room-card">
                            <img src="{{ $other->imageUrl() }}" alt="{{ $other->name }}" loading="lazy" width="800" height="560">
                            <div class="card-body">
                                <h6 class="fw-bold">{{ $other->name }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price fs-6">&#8377;{{ number_format($other->price, 0) }}</span>
                                    <a href="{{ route('rooms.show', $other->slug) }}" class="btn btn-sm btn-outline-dark">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::room($room, $settings)) !!}</script>
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs([
    'Home' => route('home'),
    'Rooms' => route('rooms.index'),
    $room->name => route('rooms.show', $room->slug),
])) !!}</script>
@endpush
