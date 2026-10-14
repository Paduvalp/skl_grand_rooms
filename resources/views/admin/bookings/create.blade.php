@extends('layouts.admin')
@section('title', 'Add Booking')
@section('heading', 'Add Booking')

@section('content')
<a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to bookings</a>

<form action="{{ route('admin.bookings.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.bookings._form', ['isEdit' => false])
</form>
@endsection
