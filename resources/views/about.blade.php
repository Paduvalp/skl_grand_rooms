@extends('layouts.app')
@section('title', 'About Us')

@section('meta_description', 'About SKL Grand Rooms, a budget hotel in Kenchenhalli, Rajarajeshwari Nagar, Bengaluru, close to Bangalore University.')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>{{ $settings['about_heading'] ?? 'About Us' }}</h1>
        <p class="mb-0 opacity-75">Home / About</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7">
                @foreach (preg_split('/\r\n|\r|\n/', (string) ($settings['about_text'] ?? '')) as $para)
                    @if (trim($para) !== '')
                        <p>{{ $para }}</p>
                    @endif
                @endforeach

                @php
                    $points = array_filter(array_map('trim', explode(',', (string) ($settings['about_points'] ?? ''))));
                @endphp

                @if (count($points))
                    <ul class="list-unstyled mt-4">
                        @foreach ($points as $point)
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $point }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-lg-5">
                <div class="p-4 bg-hnp-soft rounded-3">
                    <h5 class="fw-bold mb-3">Quick facts</h5>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Total rooms</span><strong>{{ $roomCount }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-in</span><strong>{{ $settings['checkin_time'] ?? '12:00 PM' }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-out</span><strong>{{ $settings['checkout_time'] ?? '11:00 AM' }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Phone</span><strong>{{ $settings['phone'] ?? '' }}</strong></div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Email</span><strong class="text-break">{{ $settings['email'] ?? '' }}</strong></div>
                    <a href="{{ route('booking') }}" class="btn btn-hnp w-100 mt-3">Book a Room</a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'About' => route('about')])) !!}</script>
@endpush
