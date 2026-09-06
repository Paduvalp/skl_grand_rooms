<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h6 class="mb-3">{{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}</h6>
                <p class="small mb-0">{{ $settings['footer_text'] ?? 'A comfortable place to stay.' }}</p>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Pages</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}">About</a></li>
                    <li class="mb-2"><a href="{{ route('rooms.index') }}">Rooms</a></li>
                    <li class="mb-2"><a href="{{ route('services') }}">Services</a></li>
                    <li class="mb-2"><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">Booking</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('booking') }}">Book a room</a></li>
                    <li class="mb-2"><a href="{{ route('booking.status') }}">Check my booking</a></li>
                    <li class="mb-2">Check-in: {{ $settings['checkin_time'] ?? '12:00 PM' }}</li>
                    <li class="mb-2">Check-out: {{ $settings['checkout_time'] ?? '11:00 AM' }}</li>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6 class="mb-3">Reach us</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>{{ $settings['address'] ?? '' }}</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i>{{ $settings['phone'] ?? '' }}</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $settings['email'] ?? '' }}</li>
                </ul>
            </div>
        </div>
        <hr class="mt-4" style="border-color: rgba(255,255,255,.15)">
        <div class="text-center small">
            <span>&copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'SKL GRAND ROOMS' }}. All rights reserved.</span>
        </div>
    </div>
</footer>
