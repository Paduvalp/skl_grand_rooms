@extends('layouts.app')
@section('title', 'Page Not Found')
@section('robots', 'noindex, follow')

@section('content')

<section class="py-5">
    <div class="container text-center" style="max-width:640px">
        <div class="service-icon mx-auto mb-3" style="width:76px;height:76px;font-size:2rem">
            <i class="bi bi-signpost-2"></i>
        </div>
        <h1 class="fw-bold">We could not find that page</h1>
        <p class="text-muted mb-4">
            The link may be old, or the address may have a typo in it. Everything on the
            site is one click away below.
        </p>
        <a href="{{ route('home') }}" class="btn btn-hnp me-2">Go to Home</a>
        <a href="{{ route('rooms.index') }}" class="btn btn-outline-dark me-2">See Rooms</a>
        <a href="{{ route('contact') }}" class="btn btn-outline-dark">Contact Us</a>
    </div>
</section>

@endsection
