@extends('layouts.admin')
@section("title", "Send a message")
@section('heading', 'SMS Marketing')

@section('content')

@include('admin.sms._tabs')

@if (! $configured)
    <div class="alert alert-warning">
        <strong>SMS is not set up yet.</strong>
        Add <code>SMS_GATEWAY_URL</code>, <code>SMS_GATEWAY_USERNAME</code> and
        <code>SMS_GATEWAY_PASSWORD</code> to the <code>.env</code> file, then run
        <code>php artisan config:clear</code>. Messages cannot be sent until then.
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Send a message</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.sms.send') }}" method="POST" id="smsForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="numbers">Phone numbers <span class="text-danger">*</span></label>
                        <textarea name="numbers" id="numbers" rows="4" required
                                  class="form-control @error('numbers') is-invalid @enderror"
                                  placeholder="9876543210, 9845012345">{{ old('numbers') }}</textarea>
                        <div class="form-text">
                            Separate them with commas or write one per line. Indian mobile numbers only;
                            +91 is added for you. Or
                            <a href="{{ route('admin.sms.contacts') }}">pick them from your saved numbers</a>.
                        </div>
                        @error('numbers')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if ($templates->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label" for="template">Use a template</label>
                            <select class="form-select" id="template">
                                <option value="">Write a new message</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}" data-body="{{ $template->body }}">{{ $template->title }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Picking one fills the message below. You can still edit it before sending.
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="message">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" rows="5" maxlength="480" required
                                  class="form-control @error('message') is-invalid @enderror"
                                  placeholder="Rooms available this weekend at SKL Grand Rooms. Call 7022333005 to book.">{{ old('message') }}</textarea>
                        <div class="form-text" id="smsCounter">0 characters, 0 SMS per number</div>
                        <div class="form-text text-success d-none" id="nameNotice">
                            <i class="bi bi-person-check me-1"></i>Each guest gets their own text with their name in it.
                        </div>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button class="btn btn-hnp" id="smsSubmit" @disabled(! $configured)>
                        <i class="bi bi-send me-1"></i>Send SMS
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <div class="text-muted small">Used today</div>
                <div class="stat-value">{{ $usedToday }} <span class="text-muted fs-6 fw-normal">/ {{ $dailyLimit }}</span></div>
                @php $left = max(0, $dailyLimit - $usedToday); @endphp
                <div class="progress mt-2" style="height:6px">
                    <div class="progress-bar bg-success" style="width: {{ $dailyLimit ? min(100, round($usedToday / $dailyLimit * 100)) : 0 }}%"></div>
                </div>
                <div class="form-text mt-2">{{ $left }} left today. The count starts again at midnight.</div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body small text-muted">
                <div class="mb-2">
                    <strong class="text-body">Sending by:</strong>
                    {{ $mode === 'cloud' ? 'SMS Gateway cloud' : 'the hotel phone on this Wi-Fi' }}
                </div>
                <p class="mb-2">
                    Messages go out from the SIM in the hotel's Android phone, so they cost nothing
                    beyond the free daily allowance.
                </p>
                <p class="mb-0">
                    A message over 160 characters is sent as more than one SMS and counts more than
                    once against today's total.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card stat-card mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Sent messages</strong>
        <span class="text-muted small">Newest first</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th style="width:150px">When</th><th>Message</th><th>Sent to</th><th style="width:90px">Status</th><th style="width:120px">By</th></tr>
            </thead>
            <tbody>
                @forelse ($history as $sent)
                    <tr>
                        <td class="small text-nowrap">
                            {{ $sent->created_at->format('d M Y') }}
                            <div class="text-muted">{{ $sent->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="small">
                            {{ $sent->message }}
                            @if ($sent->parts > 1)
                                <span class="badge bg-light text-dark border ms-1">{{ $sent->parts }} SMS each</span>
                            @endif
                            @if ($sent->error)
                                <div class="text-danger mt-1">{{ $sent->error }}</div>
                            @endif
                        </td>
                        <td class="small">
                            <strong>{{ $sent->recipients }}</strong> {{ Str::plural('number', $sent->recipients) }}
                            <div class="text-muted text-break">{{ Str::limit($sent->numbers, 60) }}</div>
                        </td>
                        <td><span class="badge {{ $sent->statusBadgeClass() }}">{{ ucfirst($sent->status) }}</span></td>
                        <td class="small text-muted">{{ optional($sent->user)->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Nothing sent yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $history->links() }}</div>

@endsection

@push('scripts')
<script>
    (function () {
        var message = document.getElementById('message');
        var counter = document.getElementById('smsCounter');
        var form = document.getElementById('smsForm');
        var submit = document.getElementById('smsSubmit');

        if (message && counter) {
            var notice = document.getElementById('nameNotice');

            var count = function () {
                // A real name replaces {name}, so measure the longer version.
                var personal = message.value.indexOf('{name}') !== -1;
                var length = message.value.replace(/\{name\}/g, 'Ravi').length;
                var parts = length === 0 ? 0 : (length <= 160 ? 1 : Math.ceil(length / 153));

                counter.textContent = length + ' characters, ' + parts + ' SMS per number';

                if (notice) {
                    notice.classList.toggle('d-none', !personal);
                }
            };

            // Picking a template drops its text into the message box.
            var picker = document.getElementById('template');

            if (picker) {
                picker.addEventListener('change', function () {
                    var chosen = picker.options[picker.selectedIndex];

                    if (chosen && chosen.dataset.body) {
                        message.value = chosen.dataset.body;
                        count();
                    }
                });
            }

            message.addEventListener('input', count);
            count();
        }

        // One click, one send. A second click while the first is still going
        // would text everybody twice.
        if (form && submit) {
            form.addEventListener('submit', function () {
                submit.disabled = true;
                submit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';
            });
        }
    })();
</script>
@endpush
