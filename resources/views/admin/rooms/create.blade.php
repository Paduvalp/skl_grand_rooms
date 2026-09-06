@extends('layouts.admin')
@section('title', 'Add Room')
@section('heading', 'Add Room')

@section('content')
<form action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.rooms._form', ['isEdit' => false])
</form>
@endsection
