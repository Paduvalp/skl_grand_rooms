@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-calendar-check me-1"></i>Total bookings</div>
                <div class="stat-value">{{ $stats['bookings'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-hourglass-split me-1"></i>Pending</div>
                <div class="stat-value text-warning">{{ $stats['pending'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-check-circle me-1"></i>Confirmed</div>
                <div class="stat-value text-success">{{ $stats['confirmed'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-cash-stack me-1"></i>Confirmed value</div>
                <div class="stat-value">&#8377;{{ number_format($stats['revenue'], 0) }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-door-open me-1"></i>Room types</div>
                <div class="stat-value">{{ $stats['rooms'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-stars me-1"></i>Services</div>
                <div class="stat-value">{{ $stats['services'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="text-muted small"><i class="bi bi-envelope-exclamation me-1"></i>Unread messages</div>
                <div class="stat-value">{{ $stats['unread_messages'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card h-100 bg-hnp text-white">
            <div class="card-body d-flex flex-column justify-content-center">
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-accent btn-sm mb-2">+ Add Room</a>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-light btn-sm">View Bookings</a>
            </div>
        </div>
    </div>
</div>

<div class="card stat-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Latest bookings</strong>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-dark">See all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Reference</th><th>Guest</th><th>Room</th><th>Dates</th><th>Total</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentBookings as $booking)
                    <tr>
                        <td class="small fw-semibold">{{ $booking->reference }}</td>
                        <td>
                            {{ $booking->customer_name }}
                            <div class="small text-muted">{{ $booking->customer_phone }}</div>
                        </td>
                        <td class="small">{{ $booking->room?->name ?? '—' }}</td>
                        <td class="small">{{ $booking->check_in->format('d M') }} &rarr; {{ $booking->check_out->format('d M Y') }}</td>
                        <td>&#8377;{{ number_format($booking->total_price, 0) }}</td>
                        <td><span class="badge {{ $booking->statusBadgeClass() }}">{{ ucfirst($booking->status) }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No bookings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
