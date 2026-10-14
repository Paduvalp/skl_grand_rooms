@extends('layouts.admin')
@section('title', 'Edit Booking '.$booking->reference)
@section('heading', 'Edit Booking '.$booking->reference)

@section('content')
<a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to booking</a>

<form action="{{ route('admin.bookings.update', $booking) }}" method="POST" enctype="multipart/form-data">
    @include('admin.bookings._form', ['isEdit' => true])
</form>
@endsection
