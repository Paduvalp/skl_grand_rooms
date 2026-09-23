@extends('layouts.admin')
@section('title', 'Credentials')
@section('heading', 'SMS Marketing')

@section('content')

@include('admin.sms._tabs')

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card stat-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Gateway details</strong>
                <span class="badge {{ $fromDatabase ? 'bg-success' : 'bg-secondary' }}">
                    {{ $fromDatabase ? 'Saved here' : 'From .env file' }}
                </span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.sms.credentials.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" for="mode">How messages are sent <span class="text-danger">*</span></label>
                        <select name="mode" id="mode" class="form-select">
                            <option value="cloud" @selected(old('mode', $mode) === 'cloud')>Cloud - works anywhere (use this on the live site)</option>
                            <option value="local" @selected(old('mode', $mode) === 'local')>Local - phone on the same Wi-Fi as this site</option>
                        </select>
                    </div>

                    <div class="mb-3" id="urlRow">
                        <label class="form-label" for="url">Phone address <span class="text-muted small">(local mode only)</span></label>
                        <input type="text" name="url" id="url" value="{{ old('url', $url) }}" maxlength="190"
                               class="form-control @error('url') is-invalid @enderror" placeholder="http://192.168.0.107:8080">
                        <div class="form-text">
                            Shown in the phone app next to "Local address". It changes when the
                            phone reconnects to Wi-Fi.
                        </div>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" id="username" value="{{ old('username', $username) }}" required
                                   maxlength="120" class="form-control @error('username') is-invalid @enderror" autocomplete="off">
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" name="password" id="password" maxlength="190" autocomplete="new-password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="{{ $hasPassword ? 'Saved - leave blank to keep it' : 'From the phone app' }}">
                            <div class="form-text">Kept encrypted. It is never shown again.</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label" for="daily_limit">Daily limit <span class="text-danger">*</span></label>
                        <input type="number" name="daily_limit" id="daily_limit" min="1" max="1000"
                               value="{{ old('daily_limit', $dailyLimit) }}" required
                               class="form-control @error('daily_limit') is-invalid @enderror" style="max-width:160px">
                        <div class="form-text">
                            Stop sending after this many in a day. Keep it just under the SIM's free
                            allowance (Jio gives 100), so nothing is charged.
                        </div>
                        @error('daily_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-hnp"><i class="bi bi-save me-1"></i>Save details</button>
                        <button type="submit" class="btn btn-outline-dark" form="testForm">
                            <i class="bi bi-plug me-1"></i>Test connection
                        </button>
                        @if ($fromDatabase)
                            <button type="submit" class="btn btn-outline-danger ms-auto" form="clearForm">
                                <i class="bi bi-trash me-1"></i>Delete saved details
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <form id="testForm" action="{{ route('admin.sms.credentials.test') }}" method="POST" class="d-none">@csrf</form>
        <form id="clearForm" action="{{ route('admin.sms.credentials.destroy') }}" method="POST" class="d-none"
              data-confirm="Delete the saved details? The site will fall back to the .env file.">
            @csrf @method('DELETE')
        </form>
    </div>

    <div class="col-lg-5">
        <div class="card stat-card mb-3">
            <div class="card-header bg-white"><strong>Access code</strong></div>
            <div class="card-body">
                <p class="text-muted small">
                    The code asked for before this section opens. Only a scrambled copy is kept,
                    so it cannot be read back — if it is forgotten, set a new one here.
                </p>
                <form action="{{ route('admin.sms.credentials.code') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-2">
                        <label class="form-label" for="code">New code</label>
                        <input type="password" name="code" id="code" minlength="4" maxlength="64" required
                               autocomplete="new-password" class="form-control @error('code') is-invalid @enderror">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="code_confirmation">Type it again</label>
                        <input type="password" name="code_confirmation" id="code_confirmation" required
                               autocomplete="new-password" class="form-control">
                    </div>
                    <button class="btn btn-outline-dark w-100"><i class="bi bi-key me-1"></i>Change code</button>
                </form>

                @if ($codeFromDatabase)
                    <form action="{{ route('admin.sms.credentials.code.destroy') }}" method="POST" class="mt-2"
                          data-confirm="Reset the access code to the one in the .env file?">
                        @csrf @method('DELETE')
                        <button class="btn btn-link btn-sm text-muted w-100">Reset to the .env code</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-body small text-muted">
                <p class="mb-2">
                    <strong class="text-body">Where these come from:</strong>
                    open the SMS Gateway app on the hotel phone and tap START SERVICE. The
                    Cloud server box then shows a username and password.
                </p>
                <p class="mb-2">
                    Cloud and local have <strong>different</strong> logins. The live site can only
                    use cloud, because a server in a data centre cannot reach a phone on your Wi-Fi.
                </p>
                <p class="mb-0">
                    Details saved here are used instead of the ones in the .env file. Delete them
                    and the .env file takes over again.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        var mode = document.getElementById('mode');
        var urlRow = document.getElementById('urlRow');

        if (!mode || !urlRow) {
            return;
        }

        // The phone address only matters in local mode.
        function show() {
            urlRow.classList.toggle('d-none', mode.value === 'cloud');
        }

        mode.addEventListener('change', show);
        show();
    })();
</script>
@endpush
