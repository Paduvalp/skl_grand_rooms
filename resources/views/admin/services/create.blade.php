@extends('layouts.admin')
@section('title', 'Add Service')
@section('heading', 'Add Service')

@section('content')
<form action="{{ route('admin.services.store') }}" method="POST">
    @include('admin.services._form', ['isEdit' => false])
</form>
@endsection
