@extends('layouts.admin')
@section('title', 'Message')
@section('heading', 'Message')

@section('content')

<a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to messages</a>

<div class="card stat-card" style="max-width:760px">
    <div class="card-body">
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Name</span><strong>{{ $contact->name }}</strong></div>
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Email</span><strong><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></strong></div>
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Phone</span><strong>{{ $contact->phone ?: '—' }}</strong></div>
        <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Subject</span><strong>{{ $contact->subject ?: '—' }}</strong></div>
        <div class="d-flex justify-content-between py-2"><span class="text-muted">Received</span><strong>{{ $contact->created_at->format('d M Y, h:i A') }}</strong></div>

        <div class="bg-light rounded-3 p-3 mt-3" style="white-space:pre-line">{{ $contact->message }}</div>

        <div class="d-flex gap-2 mt-4">
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" class="btn btn-hnp">Reply by Email</a>
            <form action="{{ route('admin.messages.destroy', $contact) }}" method="POST"
                  data-confirm="Delete this message?">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection
