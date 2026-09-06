@extends('layouts.admin')
@section('title', 'Services')
@section('heading', 'Services')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0">These appear on the Home page and the Services page.</p>
    <a href="{{ route('admin.services.create') }}" class="btn btn-hnp"><i class="bi bi-plus-lg me-1"></i>Add Service</a>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th style="width:60px">Icon</th><th>Title</th><th>Description</th><th>Order</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td class="fs-4" style="color:var(--hnp-primary)"><i class="bi {{ $service->icon }}"></i></td>
                        <td><strong>{{ $service->title }}</strong></td>
                        <td class="small text-muted">{{ Str::limit($service->description, 90) }}</td>
                        <td>{{ $service->sort_order }}</td>
                        <td>
                            @if ($service->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline"
                                  data-confirm="Delete this service?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No services yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $services->links() }}</div>

@endsection
