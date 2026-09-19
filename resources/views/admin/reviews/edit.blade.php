@extends('layouts.admin')
@section('title', 'Edit Review')
@section('heading', 'Edit Review')

@section('content')
<form action="{{ route('admin.reviews.update', $review) }}" method="POST">
    @include('admin.reviews._form', ['isEdit' => true])
</form>
@endsection
