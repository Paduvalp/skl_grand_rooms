@extends('layouts.admin')
@section('title', 'Edit Room')
@section('heading', 'Edit Room')

@section('content')
<form action="{{ route('admin.rooms.update', $room) }}" method="POST" enctype="multipart/form-data">
    @include('admin.rooms._form', ['isEdit' => true])
</form>
@endsection
