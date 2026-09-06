@extends('layouts.admin')
@section('title', 'Messages')
@section('heading', 'Contact Messages')

@section('content')

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($messages as $message)
                    <tr class="{{ $message->is_read ? '' : 'fw-semibold' }}">
                        <td>
                            {{ $message->name }}
                            @unless ($message->is_read)<span class="badge bg-danger ms-1">New</span>@endunless
                            <div class="small text-muted fw-normal">{{ $message->email }}</div>
                        </td>
                        <td class="small">{{ $message->subject ?: '—' }}</td>
                        <td class="small text-muted fw-normal">{{ Str::limit($message->message, 70) }}</td>
                        <td class="small text-muted fw-normal">{{ $message->created_at->format('d M Y, h:i A') }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary">Read</a>
                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="d-inline"
                                  data-confirm="Delete this message?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No messages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $messages->links() }}</div>

@endsection
