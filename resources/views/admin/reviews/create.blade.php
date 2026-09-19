@extends('layouts.admin')
@section('title', 'Add Review')
@section('heading', 'Add Review')

@section('content')
<form action="{{ route('admin.reviews.store') }}" method="POST">
    @include('admin.reviews._form', ['isEdit' => false])
</form>
@endsection
