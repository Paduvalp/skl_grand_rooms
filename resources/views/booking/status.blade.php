@extends('layouts.app')
@section('title', 'Check Booking')
@section('robots', 'noindex, follow')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Check Your Booking</h1>
        <p class="mb-0 opacity-75">Home / Booking Status</p>
    </div>
</div>

<section class="py-5">
    <div class="container" style="max-width:760px">

        <div class="card booking-box mb-4">
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Enter the reference we gave you along with the email you booked with.</p>
                <form action="{{ route('booking.status.lookup') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label">Booking reference</label>
                        <input type="text" name="reference" value="{{ old('reference') }}" class="form-control @error('reference') is-invalid @enderror" placeholder="SKL-20260101-A1B2C3" required>
                        @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Email used</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="form-control @error('customer_email') is-invalid @enderror" required>
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-hnp w-100">Check</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($booking)
            <div class="card booking-box">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="small text-muted">Reference</div>
                            <div class="fs-5 fw-bold" style="color:var(--hnp-primary)">{{ $booking->reference }}</div>
                        </div>
                        <span class="badge {{ $booking->statusBadgeClass() }} fs-6">{{ ucfirst($booking->status) }}</span>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Guest name</span><strong>{{ $booking->customer_name }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Room</span><strong>{{ $booking->room->name }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-in</span><strong>{{ $booking->check_in->format('d M Y') }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-out</span><strong>{{ $booking->check_out->format('d M Y') }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Nights</span><strong>{{ $booking->nights }}</strong></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Rooms / Guests</span><strong>{{ $booking->rooms_count }} / {{ $booking->guests }}</strong></div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Total</span><strong class="fs-5" style="color:var(--hnp-primary)">&#8377;{{ number_format($booking->total_price, 0) }}</strong></div>

                    @if ($booking->admin_remark)
                        <div class="alert alert-info small mt-3 mb-0">
                            <strong>Note from the hotel:</strong> {{ $booking->admin_remark }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</section>

@endsection
