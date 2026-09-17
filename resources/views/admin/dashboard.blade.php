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

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card stat-card h-100">
            <div class="card-header bg-white">
                <strong>Where bookings come from</strong>
            </div>
            <div class="card-body">
                @if ($channels->isEmpty())
                    <p class="text-muted small mb-0">No bookings yet.</p>
                @else
                    @php $maxBookings = max($channels->max('bookings'), 1); @endphp

                    @foreach ($channels as $channel)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span>
                                    <span class="badge {{ $channel['colour'] }}">{{ $channel['name'] }}</span>
                                </span>
                                <span class="small">
                                    <strong>{{ $channel['bookings'] }}</strong>
                                    <span class="text-muted">
                                        {{ \Illuminate\Support\Str::plural('booking', $channel['bookings']) }}
                                        &middot; &#8377;{{ number_format($channel['value'], 0) }}
                                    </span>
                                </span>
                            </div>
                            <div class="progress" style="height:6px">
                                <div class="progress-bar bg-hnp"
                                     style="width: {{ round($channel['bookings'] / $maxBookings * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    <hr>
                    <p class="small text-muted mb-0">
                        {{ $taggedCount }} of {{ $totalTracked }}
                        {{ \Illuminate\Support\Str::plural('booking', $totalTracked) }}
                        arrived through a tagged link.
                        @if ($taggedCount === 0)
                            Add <code>?utm_source=…</code> to the links you share and they will
                            start showing up here.
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card stat-card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Campaigns</strong>
                <span class="small text-muted">{{ $recentTagged }} tagged in the last 30 days</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Campaign</th><th>Source</th><th class="text-end">Bookings</th>
                            <th class="text-end">Value</th><th class="text-end">Last one</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($campaigns as $campaign)
                            <tr>
                                <td class="small fw-semibold">
                                    <a href="{{ route('admin.bookings.index', ['campaign' => $campaign->utm_campaign]) }}">
                                        {{ $campaign->utm_campaign }}
                                    </a>
                                </td>
                                <td class="small text-muted">
                                    {{ \App\Support\Attribution::label($campaign->utm_source, null, null) }}
                                    @if ($campaign->utm_medium)
                                        <span class="fst-italic">/ {{ $campaign->utm_medium }}</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $campaign->bookings }}</td>
                                <td class="text-end text-nowrap">&#8377;{{ number_format($campaign->value, 0) }}</td>
                                <td class="text-end small text-muted text-nowrap">
                                    {{ \Illuminate\Support\Carbon::parse($campaign->last_booking)->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted py-4 px-3 small">
                                    No campaign has brought a booking yet. Tag the links you share,
                                    for example
                                    <code>{{ url('/') }}/?utm_source=instagram&amp;utm_medium=social&amp;utm_campaign=diwali</code>,
                                    and each campaign will be listed here with what it earned.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
