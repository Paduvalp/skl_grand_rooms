@extends('layouts.admin')
@section('title', 'Bookings')
@section('heading', 'Bookings')

@section('content')

<form method="GET" action="{{ route('admin.bookings.index') }}" class="card stat-card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Reference, name, email or phone">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    @foreach (\App\Models\Booking::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Room</label>
                <select name="room_id" class="form-select">
                    <option value="">All rooms</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-hnp flex-grow-1">Filter</button>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Reference</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Check-out</th>
                    <th>Nights</th><th>Rooms</th><th>Total</th><th>Status</th><th>Booked on</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="small fw-semibold">{{ $booking->reference }}</td>
                        <td>
                            {{ $booking->customer_name }}
                            <div class="small text-muted">{{ $booking->customer_phone }}</div>
                            <div class="small text-muted">{{ $booking->customer_email }}</div>
                        </td>
                        <td class="small">{{ $booking->room?->name ?? '—' }}</td>
                        <td class="small">{{ $booking->check_in->format('d M Y') }}</td>
                        <td class="small">{{ $booking->check_out->format('d M Y') }}</td>
                        <td>{{ $booking->nights }}</td>
                        <td>{{ $booking->rooms_count }}</td>
                        <td class="text-nowrap">&#8377;{{ number_format($booking->total_price, 0) }}</td>
                        <td><span class="badge {{ $booking->statusBadgeClass() }}">{{ ucfirst($booking->status) }}</span></td>
                        <td class="small text-muted">{{ $booking->created_at->format('d M Y') }}</td>
                        <td class="text-end text-nowrap">
                            @if ($booking->status === 'pending')
                                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button class="btn btn-sm btn-success" title="Confirm"><i class="bi bi-check-lg"></i></button>
                                </form>
                            @endif
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="text-center text-muted py-4">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $bookings->links() }}</div>

@endsection
