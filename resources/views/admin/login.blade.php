<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login &mdash; {{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

<div class="login-wrap">
    <div class="container" style="max-width:430px">
        <div class="text-center text-white mb-4">
            <h4 class="fw-bold mb-1"><i class="bi bi-buildings me-2"></i>{{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}</h4>
            <p class="mb-0 small opacity-75">Admin Panel</p>
        </div>

        <div class="card booking-box">
            <div class="card-body p-4">

                <form action="{{ route('admin.login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                        <label class="form-check-label small" for="remember">Keep me logged in</label>
                    </div>
                    <button class="btn btn-hnp w-100">Log In</button>
                </form>

            </div>
        </div>

        <p class="text-center mt-3 mb-0">
            <a href="{{ route('home') }}" class="text-white-50 small">&larr; Back to website</a>
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('partials.alerts')

</body>
</html>
