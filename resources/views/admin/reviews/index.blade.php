@extends('layouts.admin')
@section('title', 'Reviews')
@section('heading', 'Guest Reviews')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted small mb-0">The first three shown reviews appear on the home page under "What our guests say".</p>
    <a href="{{ route('admin.reviews.create') }}" class="btn btn-hnp"><i class="bi bi-plus-lg me-1"></i>Add Review</a>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Guest</th><th>Rating</th><th>Review</th><th>Source</th><th>Order</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr>
                        <td>
                            <strong>{{ $review->guest_name }}</strong>
                            @if ($review->stay_date)
                                <div class="small text-muted">Stayed {{ $review->stay_date->format('M Y') }}</div>
                            @endif
                        </td>
                        <td class="text-nowrap" style="color:var(--hnp-accent)">
                            @for ($star = 1; $star <= 5; $star++)
                                <i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </td>
                        <td class="small text-muted">{{ Str::limit($review->review_text, 90) }}</td>
                        <td>{{ $review->sourceLabel() }}</td>
                        <td>{{ $review->sort_order }}</td>
                        <td>
                            @if ($review->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline"
                                  data-confirm="Delete this review?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No reviews yet. Copy a few real ones from your Google listing.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $reviews->links() }}</div>

@endsection
