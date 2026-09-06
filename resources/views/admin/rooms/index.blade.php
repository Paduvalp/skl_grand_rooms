@extends('layouts.admin')
@section('title', 'Rooms')
@section('heading', 'Rooms')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0">These rooms show on the website. Turn "Active" off to hide one without deleting it.</p>
    <a href="{{ route('admin.rooms.create') }}" class="btn btn-hnp"><i class="bi bi-plus-lg me-1"></i>Add Room</a>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:90px">Photo</th><th>Room</th><th>Type</th><th>Price / night</th>
                    <th>Capacity</th><th>Total rooms</th><th>Status</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    <tr>
                        <td><img src="{{ $room->imageUrl() }}" alt="{{ $room->name }}" loading="lazy" style="width:70px;height:50px;object-fit:cover;border-radius:6px"></td>
                        <td>
                            <strong>{{ $room->name }}</strong>
                            <div class="small text-muted">{{ Str::limit($room->short_description, 60) }}</div>
                        </td>
                        <td><span class="badge bg-secondary">{{ $room->type }}</span></td>
                        <td>&#8377;{{ number_format($room->price, 0) }}</td>
                        <td>{{ $room->capacity }}</td>
                        <td>{{ $room->total_rooms }}</td>
                        <td>
                            @if ($room->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Hidden</span>
                            @endif
                            @if ($room->is_featured)<span class="badge bg-info text-dark">Featured</span>@endif
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('rooms.show', $room->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="d-inline"
                                  data-confirm="Delete this room? Its bookings will be deleted too.">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No rooms yet. Click "Add Room" to create the first one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $rooms->links() }}</div>

@endsection
