@extends('layouts.app')
@section('title', 'Booking Received')
@section('robots', 'noindex, follow')

@section('content')

<section class="py-5">
    <div class="container" style="max-width:760px">
        <div class="card booking-box text-center">
            <div class="card-body p-5">
                <div class="service-icon mx-auto mb-3" style="width:76px;height:76px;font-size:2rem">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3 class="fw-bold">Thank you, {{ $booking->customer_name }}!</h3>
                <p class="text-muted">
                    Your booking request has been received. Keep this reference safe &mdash; you can use it any time
                    to check the status of your booking.
                </p>

                <div class="bg-hnp-soft rounded-3 py-3 px-4 d-inline-block my-3">
                    <div class="small text-muted">Booking reference</div>
                    <div class="fs-4 fw-bold" style="color:var(--hnp-primary)">{{ $booking->reference }}</div>
                </div>

                <div class="text-start mt-4">
                    <h6 class="fw-bold mb-3">Booking details</h6>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Room</span><strong>{{ $booking->room->name }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-in</span><strong>{{ $booking->check_in->format('d M Y') }} ({{ $settings['checkin_time'] ?? '12:00 PM' }})</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-out</span><strong>{{ $booking->check_out->format('d M Y') }} ({{ $settings['checkout_time'] ?? '11:00 AM' }})</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Nights</span><strong>{{ $booking->nights }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Rooms / Guests</span><strong>{{ $booking->rooms_count }} room(s), {{ $booking->guests }} guest(s)</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Status</span><span class="badge {{ $booking->statusBadgeClass() }}">{{ ucfirst($booking->status) }}</span></div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Estimated total</span><strong class="fs-5" style="color:var(--hnp-primary)">&#8377;{{ number_format($booking->total_price, 0) }}</strong></div>
                </div>

                <div class="alert alert-warning small mt-4 mb-4 text-start">
                    <i class="bi bi-info-circle me-1"></i>
                    Your booking is <strong>pending</strong> until our front desk confirms it. We will call or email you
                    on {{ $booking->customer_phone }} shortly.
                </div>

                <a href="{{ route('booking.status') }}" class="btn btn-hnp me-2">Check Status</a>
                <a href="{{ route('home') }}" class="btn btn-outline-dark">Back to Home</a>
            </div>
        </div>
    </div>
</section>

@endsection
