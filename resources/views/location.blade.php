@extends('layouts.app')

@section('title', 'Hotel in RR Nagar, Bengaluru')

@section('meta_description', 'Where SKL Grand Rooms is: Kenchenhalli, Rajarajeshwari Nagar, Bengaluru 560026, near Kengeri, Mysore Road and NICE Road. Landmarks, directions and map.')

@php
    $name = $settings['site_name'] ?? 'SKL Grand Rooms';
    $address = trim((string) ($settings['address'] ?? ''));
    $phone = trim((string) ($settings['phone'] ?? ''));
    $pin = trim((string) ($settings['postal_code'] ?? ''));
    $mapUrl = \App\Support\Seo::mapUrl($settings);
    $mapIframe = \App\Support\MapEmbed::iframe($settings);

    // The admin can rewrite the directions from Site Settings. This is the
    // fallback, written to be true of the area rather than precise about
    // times, which only somebody who drives it every day should fill in.
    $directions = trim((string) ($settings['how_to_reach'] ?? ''));
@endphp

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Hotel in RR Nagar, Bengaluru</h1>
        <p class="mb-0 opacity-75">Home / Location</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-7">
                <h2 class="fw-bold h4 mb-3">Where we are</h2>

                <p>
                    {{ $name }} is in <strong>Kenchenhalli, Rajarajeshwari Nagar</strong>, on the
                    south-western side of Bengaluru{{ $pin ? ', postal code '.$pin : '' }}. It is
                    the part of the city that sits between Mysore Road and the NICE Road junction,
                    close to Kengeri, and far enough out that the nights are quiet.
                </p>

                <p>
                    People know this area by different names, and they are all the same place:
                    <strong>RR Nagar</strong>, <strong>R R Nagar</strong>,
                    <strong>Raja Rajeshwari Nagar</strong> and
                    <strong>Rajarajeshwari Nagar</strong>. If you have been given any of those, you
                    are looking for us.
                </p>

                <p>
                    Staying on the outskirts of Bangalore rather than in the centre buys you a calmer night
                    away from the traffic, and a lower rate for the same clean, air-conditioned
                    room. The trade is that you are a short ride from the middle of town rather
                    than in it, which suits most of our guests perfectly well.
                </p>

                @if ($address)
                    <div class="legal-contact mt-4">
                        <strong class="d-block mb-2">{{ $name }}</strong>
                        <div class="mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $address }}</div>
                        @if ($phone)
                            <div class="mb-1">
                                <i class="bi bi-telephone me-2"></i>
                                <a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a>
                            </div>
                        @endif
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @if ($mapUrl)
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="btn btn-hnp btn-sm">
                                    <i class="bi bi-map me-1"></i>Directions on Google Maps
                                </a>
                            @endif
                            <a href="{{ route('booking') }}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-calendar-check me-1"></i>Book a room
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-5">
                @if ($mapIframe)
                    <div class="ratio ratio-4x3 rounded-3 overflow-hidden border">
                        {!! $mapIframe !!}
                    </div>
                @endif

                <div class="legal-summary mt-4">
                    <strong class="d-block mb-2">At a glance</strong>
                    <ul class="small mb-0">
                        <li>Kenchenhalli, Rajarajeshwari Nagar, Bengaluru{{ $pin ? ' '.$pin : '' }}</li>
                        <li>Near Kengeri, Mysore Road and the NICE Road junction</li>
                        <li>Parking on site</li>
                        @if (!empty($fromPrice))
                            <li>Rooms from &#8377;{{ number_format((float) $fromPrice, 0) }} a night</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@include('partials.nearby')

<section class="py-5">
    <div class="container">
        <h2 class="fw-bold h4 mb-4">How to reach us</h2>

        @if ($directions)
            <div class="row">
                <div class="col-lg-8">
                    @foreach (preg_split('/\r\n|\r|\n/', $directions) as $line)
                        @if (trim($line) !== '')
                            <p>{{ $line }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi bi-train-front"></i></div>
                        <h3 class="fw-bold h6 mb-2">By metro</h3>
                        <p class="text-muted small mb-0">
                            Take the Purple Line towards Kengeri and get off at Mysore Road or
                            Kengeri, whichever your train reaches. An auto from either station
                            brings you to Kenchenhalli. Call us and we will tell the driver
                            where to turn.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi bi-bus-front"></i></div>
                        <h3 class="fw-bold h6 mb-2">By bus</h3>
                        <p class="text-muted small mb-0">
                            BMTC buses towards Kengeri and Rajarajeshwari Nagar run along Mysore
                            Road all day. Kengeri bus stand is the nearest big stop, with autos
                            waiting outside it.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi bi-car-front"></i></div>
                        <h3 class="fw-bold h6 mb-2">By car</h3>
                        <p class="text-muted small mb-0">
                            Come in along Mysore Road, or leave NICE Road at the Mysore Road
                            junction and head towards Rajarajeshwari Nagar. There is parking on
                            site, so you are not hunting for a space when you arrive.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi bi-airplane"></i></div>
                        <h3 class="fw-bold h6 mb-2">From the airport or station</h3>
                        <p class="text-muted small mb-0">
                            Kempegowda airport is on the far north side, so allow plenty of time
                            and use NICE Road if you are driving. From KSR Bengaluru City
                            station, Mysore Road is the straight run out to us.
                        </p>
                    </div>
                </div>
            </div>

            <p class="text-muted small mt-4 mb-0">
                Not sure which turn to take? Call {{ $phone ?: 'the front desk' }} and we will
                talk you in. It happens every day.
            </p>
        @endif
    </div>
</section>

@include('partials.guests')

@include('partials.faq')

<section class="py-5 bg-hnp text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-2">Know where we are? Pick your dates.</h2>
        <p class="mb-4">Clean, air-conditioned rooms in RR Nagar, with no payment needed to book.</p>
        <a href="{{ route('booking') }}" class="btn btn-accent btn-lg px-4">Book a Room</a>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Location' => route('location')])) !!}</script>
@endpush
