@extends('layouts.admin')
@section('title', 'Booking '.$booking->reference)
@section('heading', 'Booking '.$booking->reference)

@section('content')

<a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to bookings</a>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card stat-card mb-4">
            <div class="card-header bg-white"><strong>Stay details</strong></div>
            <div class="card-body">
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Room</span><strong>{{ $booking->room?->name ?? '—' }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-in</span><strong>{{ $booking->check_in->format('d M Y') }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Check-out</span><strong>{{ $booking->check_out->format('d M Y') }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Nights</span><strong>{{ $booking->nights }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Rooms</span><strong>{{ $booking->rooms_count }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Guests</span><strong>{{ $booking->guests }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Rate per night</span><strong>&#8377;{{ number_format($booking->room?->price ?? 0, 0) }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span class="text-muted">Total</span><strong class="fs-5">&#8377;{{ number_format($booking->total_price, 0) }}</strong></div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Guest details</strong></div>
            <div class="card-body">
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Name</span><strong>{{ $booking->customer_name }}</strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Phone</span><strong><a href="tel:{{ $booking->customer_phone }}">{{ $booking->customer_phone }}</a></strong></div>
                <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Email</span><strong><a href="mailto:{{ $booking->customer_email }}">{{ $booking->customer_email }}</a></strong></div>
                <div class="d-flex justify-content-between py-2"><span class="text-muted">Booked on</span><strong>{{ $booking->created_at->format('d M Y, h:i A') }}</strong></div>

                @if ($booking->notes)
                    <div class="alert alert-light border mt-3 mb-0 small">
                        <strong>Guest note:</strong><br>{{ $booking->notes }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card stat-card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Status</strong>
                <span class="badge {{ $booking->statusBadgeClass() }} fs-6">{{ ucfirst($booking->status) }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                    @csrf @method('PATCH')

                    <label class="form-label">Change status to</label>
                    <select name="status" class="form-select mb-3">
                        @foreach (\App\Models\Booking::STATUSES as $status)
                            <option value="{{ $status }}" @selected($booking->status === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>

                    <label class="form-label">Note for the guest (optional)</label>
                    <textarea name="admin_remark" rows="3" class="form-control mb-3"
                              placeholder="Room 204 is ready. Please carry a photo ID.">{{ old('admin_remark', $booking->admin_remark) }}</textarea>
                    <div class="form-text mb-3">The guest sees this when they check their booking status.</div>

                    <button class="btn btn-hnp w-100">Update Booking</button>
                </form>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body">
                <h6 class="fw-bold text-danger">Danger zone</h6>
                <p class="small text-muted">Deleting removes this booking from the system for good.</p>
                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST"
                      data-confirm="Delete this booking permanently?">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100">Delete Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
