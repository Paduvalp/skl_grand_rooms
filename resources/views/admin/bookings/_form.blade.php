@csrf
@if (isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card mb-4">
            <div class="card-header bg-white"><strong>Stay details</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                            <option value="">Choose a room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" data-price="{{ $room->price }}"
                                        @selected(old('room_id', $booking->room_id) == $room->id)>
                                    {{ $room->name }} &mdash; &#8377;{{ number_format($room->price, 0) }}/night{{ $room->is_active ? '' : ' (hidden)' }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rooms <span class="text-danger">*</span></label>
                        <input type="number" name="rooms_count" min="1" max="50" value="{{ old('rooms_count', $booking->rooms_count) }}"
                               class="form-control @error('rooms_count') is-invalid @enderror" required>
                        @error('rooms_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Guests <span class="text-danger">*</span></label>
                        <input type="number" name="guests" min="1" max="100" value="{{ old('guests', $booking->guests) }}"
                               class="form-control @error('guests') is-invalid @enderror" required>
                        @error('guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Check-in <span class="text-danger">*</span></label>
                        <input type="date" name="check_in" value="{{ old('check_in', $booking->check_in?->format('Y-m-d')) }}"
                               class="form-control @error('check_in') is-invalid @enderror" required>
                        @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Check-out <span class="text-danger">*</span></label>
                        <input type="date" name="check_out" value="{{ old('check_out', $booking->check_out?->format('Y-m-d')) }}"
                               class="form-control @error('check_out') is-invalid @enderror" required>
                        @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total (&#8377;)</label>
                        <input type="number" name="total_price" step="0.01" min="0"
                               value="{{ old('total_price', $isEdit ? $booking->total_price : '') }}"
                               class="form-control @error('total_price') is-invalid @enderror" id="totalPrice">
                        <div class="form-text" id="totalHint">Leave blank to work it out from the room rate.</div>
                        @error('total_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Guest details</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $booking->customer_name) }}"
                               class="form-control @error('customer_name') is-invalid @enderror" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $booking->customer_phone) }}"
                               class="form-control @error('customer_phone') is-invalid @enderror" required>
                        @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $booking->customer_email) }}"
                               class="form-control @error('customer_email') is-invalid @enderror" required>
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Other guests staying</label>
                            <button type="button" class="btn btn-sm btn-hnp" id="addGuest">
                                <i class="bi bi-person-plus me-1"></i>Add guest
                            </button>
                        </div>
                        @php
                            $otherGuests = old('additional_guests', $booking->additional_guests ?? []);
                        @endphp
                        <div id="guestRows">
                            @foreach ($otherGuests as $i => $guest)
                                @include('admin.bookings._guest-row', ['index' => $i, 'guest' => $guest])
                            @endforeach
                        </div>
                        <div class="form-text" id="noGuests" @if (count($otherGuests)) hidden @endif>
                            Only the guest above. Use "Add guest" for everyone else in the room.
                        </div>
                        <template id="guestTemplate">
                            @include('admin.bookings._guest-row', ['index' => '__INDEX__', 'guest' => []])
                        </template>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Guest note</label>
                        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $booking->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select mb-3 @error('status') is-invalid @enderror">
                    @foreach (\App\Models\Booking::STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('status', $booking->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback mb-3">{{ $message }}</div>@enderror

                <label class="form-label">Note for the guest (optional)</label>
                <textarea name="admin_remark" rows="3" class="form-control">{{ old('admin_remark', $booking->admin_remark) }}</textarea>
                <div class="form-text">The guest sees this when they check their booking status.</div>
            </div>
        </div>

        <div class="card stat-card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Documents</label>
                    <button type="button" class="btn btn-sm btn-hnp" id="addDocument">
                        <i class="bi bi-plus-lg me-1"></i>Add document
                    </button>
                </div>

                <div id="documentRows">
                    @include('admin.bookings._document-row', ['index' => 0])
                </div>
                <template id="documentTemplate">
                    @include('admin.bookings._document-row', ['index' => '__INDEX__'])
                </template>

                <div class="form-text">
                    Guest IDs, payment proof and so on. Pick several files in one go if you like.
                    JPG, PNG, WEBP or PDF, up to 5 MB each and {{ \App\Models\BookingDocument::MAX_PER_SAVE }} files per save.
                </div>
                @error('documents')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @foreach (collect($errors->getMessages())->filter(fn ($m, $k) => str_starts_with($k, 'documents.'))->flatten()->unique() as $message)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @endforeach
                @if ($errors->any())
                    <div class="form-text text-warning-emphasis">Browsers forget picked files after an error, so please choose them again.</div>
                @endif

                @if ($isEdit && $booking->documents->isNotEmpty())
                    <hr>
                    <div class="small text-muted mb-2">Already uploaded. Tick "Remove" on any you want gone; it happens when you save.</div>
                    @include('admin.bookings._documents', ['markForRemoval' => true])
                @endif
            </div>
        </div>

        <button class="btn btn-hnp w-100">{{ $isEdit ? 'Save Changes' : 'Add Booking' }}</button>
        <a href="{{ $isEdit ? route('admin.bookings.show', $booking) : route('admin.bookings.index') }}" class="btn btn-link w-100 text-muted">Cancel</a>
    </div>
</div>

@push('scripts')
<script>
    // Repeating rows for guests and documents. Each new row gets a fresh
    // index so its fields arrive as their own group on the server.
    (function () {
        var nextIndex = Date.now();

        function addRow(templateId, containerId) {
            var html = document.getElementById(templateId).innerHTML.replace(/__INDEX__/g, nextIndex++);
            var holder = document.createElement('div');
            holder.innerHTML = html.trim();
            var row = holder.firstElementChild;
            document.getElementById(containerId).appendChild(row);
            return row;
        }

        function syncGuestHint() {
            document.getElementById('noGuests').hidden = document.querySelectorAll('#guestRows [data-row]').length > 0;
        }

        document.getElementById('addGuest').addEventListener('click', function () {
            addRow('guestTemplate', 'guestRows').querySelector('input').focus();
            syncGuestHint();
        });

        document.getElementById('addDocument').addEventListener('click', function () {
            addRow('documentTemplate', 'documentRows').querySelector('input').focus();
        });

        document.addEventListener('click', function (event) {
            var remove = event.target.closest('[data-remove-row]');

            if (remove) {
                remove.closest('[data-row]').remove();
                syncGuestHint();
                return;
            }

            // Drops one picked file. A file input's list can't be edited
            // directly, so it is rebuilt without that file.
            var drop = event.target.closest('[data-drop-file]');

            if (drop) {
                var input = drop.closest('[data-row]').querySelector('input[type=file]');
                var keep = new DataTransfer();

                Array.from(input.files).forEach(function (file, i) {
                    if (i !== parseInt(drop.dataset.dropFile, 10)) {
                        keep.items.add(file);
                    }
                });

                input.files = keep.files;
                showPreviews(input);
            }
        });

        document.addEventListener('change', function (event) {
            if (event.target.matches('#documentRows input[type=file]')) {
                showPreviews(event.target);
            }
        });

        function showPreviews(input) {
            var list = input.closest('[data-row]').querySelector('[data-previews]');
            list.innerHTML = '';

            Array.from(input.files).forEach(function (file, i) {
                var tile = document.createElement('div');
                tile.className = 'position-relative border rounded bg-light d-flex align-items-center justify-content-center';
                tile.style.cssText = 'width:72px;height:72px;overflow:hidden';
                tile.title = file.name;

                if (file.type.indexOf('image/') === 0) {
                    var img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover';
                    img.onload = function () { URL.revokeObjectURL(img.src); };
                    tile.appendChild(img);
                } else {
                    tile.innerHTML = '<i class="bi bi-file-earmark-pdf text-danger fs-3"></i>';
                }

                var x = document.createElement('button');
                x.type = 'button';
                x.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 p-0 lh-1';
                x.style.cssText = 'width:20px;height:20px';
                x.dataset.dropFile = i;
                x.title = 'Remove ' + file.name;
                x.innerHTML = '&times;';
                tile.appendChild(x);

                list.appendChild(tile);
            });
        }
    })();
</script>
<script>
    // Shows what the total will come to when the price box is left blank.
    (function () {
        var room = document.querySelector('select[name="room_id"]');
        var rooms = document.querySelector('input[name="rooms_count"]');
        var checkIn = document.querySelector('input[name="check_in"]');
        var checkOut = document.querySelector('input[name="check_out"]');
        var total = document.getElementById('totalPrice');
        var hint = document.getElementById('totalHint');

        function update() {
            var option = room.options[room.selectedIndex];
            var price = option ? parseFloat(option.dataset.price || '0') : 0;
            var nights = Math.round((new Date(checkOut.value) - new Date(checkIn.value)) / 86400000);
            var count = parseInt(rooms.value, 10) || 0;

            if (!price || !(nights > 0) || !count) {
                hint.textContent = 'Leave blank to work it out from the room rate.';
                return;
            }

            var amount = price * nights * count;
            hint.textContent = nights + ' night(s) × ' + count + ' room(s) at the room rate = ₹' + amount.toLocaleString('en-IN')
                + (total.value === '' ? '' : '. Your amount above will be used instead.');
        }

        [room, rooms, checkIn, checkOut, total].forEach(function (el) {
            el.addEventListener('input', update);
            el.addEventListener('change', update);
        });
        update();
    })();
</script>
@endpush
