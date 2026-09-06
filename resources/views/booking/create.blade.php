@extends('layouts.app')
@section('title', 'Book a Room')

@section('meta_description', 'Book a room at SKL Grand Rooms, RR Nagar, Bengaluru. Pick your dates, get a booking reference straight away, no payment needed now.')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Book a Room</h1>
        <p class="mb-0 opacity-75">Home / Booking</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="card booking-box">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1">Your booking request</h5>
                        <p class="text-muted small mb-4">
                            Fill this in and submit. You will get a booking reference right away, and our front desk
                            confirms it shortly after.
                        </p>

                        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                            @csrf

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Choose a room <span class="text-danger">*</span></label>
                                    <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                        <option value="">-- Select a room --</option>
                                        @foreach ($rooms as $room)
                                            <option value="{{ $room->id }}"
                                                data-price="{{ $room->price }}"
                                                data-capacity="{{ $room->capacity }}"
                                                @selected(old('room_id', $selectedRoomId) == $room->id)>
                                                {{ $room->name }} &mdash; &#8377;{{ number_format($room->price, 0) }} / night (up to {{ $room->capacity }} guests)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Check-in date <span class="text-danger">*</span></label>
                                    <input type="date" name="check_in" id="check_in" min="{{ date('Y-m-d') }}"
                                           value="{{ old('check_in') }}"
                                           class="form-control @error('check_in') is-invalid @enderror" required>
                                    @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Check-out date <span class="text-danger">*</span></label>
                                    <input type="date" name="check_out" id="check_out" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                           value="{{ old('check_out') }}"
                                           class="form-control @error('check_out') is-invalid @enderror" required>
                                    @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Number of rooms <span class="text-danger">*</span></label>
                                    <input type="number" name="rooms_count" id="rooms_count" min="1" max="10"
                                           value="{{ old('rooms_count', 1) }}"
                                           class="form-control @error('rooms_count') is-invalid @enderror" required>
                                    @error('rooms_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Number of guests <span class="text-danger">*</span></label>
                                    <input type="number" name="guests" id="guests" min="1" max="20"
                                           value="{{ old('guests', 1) }}"
                                           class="form-control @error('guests') is-invalid @enderror" required>
                                    @error('guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12"><hr class="my-2"></div>

                                <div class="col-md-6">
                                    <label class="form-label">Your full name <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                           class="form-control @error('customer_name') is-invalid @enderror" required>
                                    @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                                           class="form-control @error('customer_email') is-invalid @enderror" required>
                                    @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone number <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
                                           class="form-control @error('customer_phone') is-invalid @enderror" required>
                                    @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Anything we should know? (optional)</label>
                                    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                              placeholder="Late arrival, extra bed, ground floor room...">{{ old('notes') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-hnp btn-lg px-4">Submit Booking</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card booking-box mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Your estimate</h6>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Rate per night</span><strong id="sumRate">&#8377;0</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Nights</span><strong id="sumNights">0</strong></div>
                        <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Rooms</span><strong id="sumRooms">1</strong></div>
                        <div class="d-flex justify-content-between py-3"><span class="fw-bold">Total</span><span class="fw-bold fs-5" id="sumTotal" style="color:var(--hnp-primary)">&#8377;0</span></div>
                        <p class="text-muted small mb-0">This is an estimate. Taxes and extras, if any, are told to you at confirmation.</p>
                    </div>
                </div>

                <div class="p-4 bg-hnp-soft rounded-3">
                    <h6 class="fw-bold mb-2">How it works</h6>
                    <ol class="small text-muted mb-3 ps-3">
                        <li class="mb-1">You submit this form.</li>
                        <li class="mb-1">You get a booking reference on screen.</li>
                        <li class="mb-1">Our front desk checks and confirms it.</li>
                        <li>You can check the status any time with your reference.</li>
                    </ol>
                    <a href="{{ route('booking.status') }}" class="btn btn-sm btn-outline-dark w-100">Check an existing booking</a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    var roomSelect = document.getElementById('room_id');
    var checkIn    = document.getElementById('check_in');
    var checkOut   = document.getElementById('check_out');
    var roomsCount = document.getElementById('rooms_count');

    function money(n) {
        return '₹' + Number(n || 0).toLocaleString('en-IN');
    }

    function recalc() {
        var opt   = roomSelect.options[roomSelect.selectedIndex];
        var rate  = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
        var rooms = parseInt(roomsCount.value || '1', 10);
        var nights = 0;

        if (checkIn.value && checkOut.value) {
            var inD  = new Date(checkIn.value);
            var outD = new Date(checkOut.value);
            var diff = Math.round((outD - inD) / 86400000);
            nights = diff > 0 ? diff : 0;
        }

        document.getElementById('sumRate').textContent   = money(rate);
        document.getElementById('sumNights').textContent = nights;
        document.getElementById('sumRooms').textContent  = rooms;
        document.getElementById('sumTotal').textContent  = money(rate * nights * rooms);
    }

    checkIn.addEventListener('change', function () {
        if (!checkIn.value) return;
        var next = new Date(checkIn.value);
        next.setDate(next.getDate() + 1);
        checkOut.min = next.toISOString().slice(0, 10);
        if (checkOut.value && checkOut.value <= checkIn.value) {
            checkOut.value = checkOut.min;
        }
        recalc();
    });

    [roomSelect, checkOut, roomsCount].forEach(function (el) {
        el.addEventListener('change', recalc);
        el.addEventListener('input', recalc);
    });

    recalc();
})();
</script>
@endpush

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Book a Room' => route('booking')])) !!}</script>
@endpush
