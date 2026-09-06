<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') &mdash; Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="admin-body">

<div class="container-fluid">
    <div class="row">

        <aside class="col-lg-2 col-md-3 p-0 admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <i class="bi bi-buildings me-1"></i>{{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}
            </a>
            <ul class="nav flex-column py-2">
                <li><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}"><i class="bi bi-calendar-check me-2"></i>Bookings</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}" href="{{ route('admin.rooms.index') }}"><i class="bi bi-door-open me-2"></i>Rooms</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}"><i class="bi bi-stars me-2"></i>Services</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}"><i class="bi bi-envelope me-2"></i>Messages</a></li>
                <li><a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}"><i class="bi bi-gear me-2"></i>Site Settings</a></li>
                <li class="mt-3 pt-2" style="border-top:1px solid rgba(255,255,255,.08)">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>View Website</a>
                </li>
            </ul>
        </aside>

        <main class="col-lg-10 col-md-9 px-4 py-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold mb-0">@yield('heading', 'Dashboard')</h4>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                    </form>
                </div>
            </div>

            @yield('content')

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('partials.alerts', ['swalToast' => true])
@stack('scripts')
</body>
</html>
