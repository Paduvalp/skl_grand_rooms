@csrf
@if (isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $service->title) }}"
                           class="form-control @error('title') is-invalid @enderror" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Icon class</label>
                    <input type="text" name="icon" value="{{ old('icon', $service->icon ?: 'bi-star') }}" class="form-control" placeholder="bi-wifi">
                    <div class="form-text">
                        Any Bootstrap Icon name, for example <code>bi-wifi</code>, <code>bi-cup-hot</code>,
                        <code>bi-water</code>, <code>bi-car-front</code>, <code>bi-people</code>, <code>bi-bell</code>.
                        Full list at icons.getbootstrap.com
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" name="sort_order" min="0" max="999" value="{{ old('sort_order', $service->sort_order ?? 0) }}" class="form-control">
                    <div class="form-text">Smaller number shows first.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="svc_active"
                           @checked(old('is_active', $service->is_active ?? true))>
                    <label class="form-check-label" for="svc_active">Show on website</label>
                </div>
            </div>
        </div>

        <button class="btn btn-hnp w-100">{{ isset($isEdit) && $isEdit ? 'Save Changes' : 'Add Service' }}</button>
        <a href="{{ route('admin.services.index') }}" class="btn btn-link w-100 text-muted">Cancel</a>
    </div>
</div>
