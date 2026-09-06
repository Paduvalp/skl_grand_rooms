@extends('layouts.app')
@section('title', 'Rooms and Rates')

@section('meta_description', 'See every room at SKL Grand Rooms with nightly rates, bed sizes and how many guests each one takes. Filter by guests and budget.')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Our Rooms</h1>
        <p class="mb-0 opacity-75">Home / Rooms</p>
    </div>
</div>

<section class="py-5">
    <div class="container">

        <form method="GET" action="{{ route('rooms.index') }}" class="row g-3 align-items-end mb-4 p-3 bg-hnp-soft rounded-3">
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Guests</label>
                <select name="guests" class="form-select">
                    <option value="">Any</option>
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" @selected(request('guests') == $i)>{{ $i }}+ guest(s)</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1 small fw-semibold">Max price per night</label>
                <input type="number" name="max_price" min="0" step="100" value="{{ request('max_price') }}" class="form-control" placeholder="e.g. 5000">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-hnp flex-grow-1">Filter</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="row g-4">
            @forelse ($rooms as $room)
                <div class="col-md-6 col-lg-4">
                    <div class="card room-card">
                        <img src="{{ $room->imageUrl() }}" alt="{{ $room->name }} at {{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}" loading="lazy" width="800" height="560">
                        <div class="card-body d-flex flex-column">
                            <span class="badge bg-hnp mb-2 align-self-start">{{ $room->type }}</span>
                            <h5 class="card-title fw-bold">{{ $room->name }}</h5>
                            <p class="text-muted small">{{ $room->short_description }}</p>
                            <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                <span><i class="bi bi-people me-1"></i>{{ $room->capacity }} guests</span>
                                <span><i class="bi bi-rulers me-1"></i>{{ $room->size ?: '—' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="price">&#8377;{{ number_format($room->price, 0) }} <small class="text-muted fw-normal">/ night</small></span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('rooms.show', $room->slug) }}" class="btn btn-sm btn-outline-dark">Details</a>
                                    <a href="{{ route('booking', ['room' => $room->slug]) }}" class="btn btn-sm btn-hnp">Book</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">No rooms match your filter. Try changing the guests or price.</div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $rooms->links() }}
        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Rooms' => route('rooms.index')])) !!}</script>
@endpush
