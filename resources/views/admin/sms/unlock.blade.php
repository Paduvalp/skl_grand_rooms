@extends('layouts.admin')
@section('title', 'Access code')
@section('heading', 'SMS Marketing')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card stat-card">
            <div class="card-body p-4 text-center">
                <div class="service-icon mx-auto"><i class="bi bi-lock"></i></div>

                <h5 class="fw-bold mb-2">Enter the access code</h5>
                <p class="text-muted small mb-4">
                    Sending texts reaches real guests and uses the hotel's daily allowance,
                    so this section asks for a code as well as your login.
                </p>

                <form action="{{ route('admin.sms.unlock.submit') }}" method="POST">
                    @csrf
                    <input type="password" name="code" required autofocus autocomplete="off"
                           class="form-control form-control-lg text-center @error('code') is-invalid @enderror"
                           placeholder="Access code" aria-label="Access code">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <button class="btn btn-hnp w-100 mt-3">
                        <i class="bi bi-unlock me-1"></i>Unlock
                    </button>
                </form>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-link text-muted mt-2">Back to dashboard</a>
            </div>
        </div>

        <p class="text-muted small text-center mt-3 mb-0">
            Unlocks for {{ \App\Http\Middleware\RequireSmsCode::MINUTES / 60 }} hours on this computer.
        </p>
    </div>
</div>

@endsection
