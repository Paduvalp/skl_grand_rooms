@extends('layouts.app')
@section('title', 'Hotel Facilities')

@section('meta_description', 'Air conditioning, hot water geyser, free Wi-Fi, TV and on-site parking. Everything our guests in RR Nagar, Bengaluru get.')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Our Services</h1>
        <p class="mb-0 opacity-75">Home / Services</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse ($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi {{ $service->icon }}"></i></div>
                        <h5 class="fw-bold">{{ $service->title }}</h5>
                        <p class="text-muted mb-0">{{ $service->description }}</p>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">No services added yet. Add them from the admin panel.</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Facilities' => route('services')])) !!}</script>
@endpush
