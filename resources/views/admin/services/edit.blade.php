@extends('layouts.admin')
@section('title', 'Edit Service')
@section('heading', 'Edit Service')

@section('content')
<form action="{{ route('admin.services.update', $service) }}" method="POST">
    @include('admin.services._form', ['isEdit' => true])
</form>
@endsection
