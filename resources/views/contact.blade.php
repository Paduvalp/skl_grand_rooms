@extends('layouts.app')
@section('title', 'Contact Us')

@section('meta_description', 'Call or write to SKL Grand Rooms in Kenchenhalli, Rajarajeshwari Nagar, Bengaluru. Address, phone number and map.')

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Contact Us</h1>
        <p class="mb-0 opacity-75">Home / Contact Us</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <h4 class="fw-bold mb-3">Talk to us</h4>
                <p class="text-muted">Call us, write to us, or drop by. We answer every message.</p>

                <div class="d-flex mb-3">
                    <div class="service-icon me-3 flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem"><i class="bi bi-geo-alt"></i></div>
                    <div><strong class="d-block">Address</strong><span class="text-muted">{{ $settings['address'] ?? '' }}</span></div>
                </div>
                <div class="d-flex mb-3">
                    <div class="service-icon me-3 flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem"><i class="bi bi-telephone"></i></div>
                    <div><strong class="d-block">Phone</strong><span class="text-muted">{{ $settings['phone'] ?? '' }}</span></div>
                </div>
                <div class="d-flex mb-3">
                    <div class="service-icon me-3 flex-shrink-0" style="width:44px;height:44px;font-size:1.1rem"><i class="bi bi-envelope"></i></div>
                    <div><strong class="d-block">Email</strong><span class="text-muted text-break">{{ $settings['email'] ?? '' }}</span></div>
                </div>

                @php $mapIframe = \App\Support\MapEmbed::iframe($settings); @endphp
                @if ($mapIframe)
                    <div class="ratio ratio-4x3 mt-4 rounded-3 overflow-hidden border">
                        {!! $mapIframe !!}
                    </div>
                    <a href="https://maps.google.com/?q={{ urlencode(($settings['site_name'] ?? '').' '.($settings['address'] ?? '')) }}"
                       target="_blank" rel="noopener" class="btn btn-sm btn-outline-dark mt-3">
                        <i class="bi bi-geo-alt me-1"></i>Open in Google Maps
                    </a>
                @endif
            </div>

            <div class="col-lg-7">
                <div class="card booking-box">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Send a message</h5>

                        <form action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Your name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" value="{{ old('subject') }}" class="form-control @error('subject') is-invalid @enderror">
                                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                    @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-hnp px-4">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Contact Us' => route('contact')])) !!}</script>
@endpush
