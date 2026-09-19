@csrf
@if (isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Guest name <span class="text-danger">*</span></label>
                    <input type="text" name="guest_name" value="{{ old('guest_name', $review->guest_name) }}" maxlength="120"
                           class="form-control @error('guest_name') is-invalid @enderror" required>
                    @error('guest_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Review <span class="text-danger">*</span></label>
                    <textarea name="review_text" rows="5" maxlength="2000" class="form-control @error('review_text') is-invalid @enderror" required>{{ old('review_text', $review->review_text) }}</textarea>
                    <div class="form-text">Use the guest's own words. Only add reviews from real guests.</div>
                    @error('review_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Rating <span class="text-danger">*</span></label>
                    <select name="rating" class="form-select @error('rating') is-invalid @enderror">
                        @for ($r = 5; $r >= 1; $r--)
                            <option value="{{ $r }}" @selected((int) old('rating', $review->rating) === $r)>{{ $r }} star{{ $r > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                    @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Source</label>
                    <select name="source" class="form-select">
                        @foreach (\App\Models\Review::SOURCES as $key => $label)
                            <option value="{{ $key }}" @selected(old('source', $review->source) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stay date <span class="text-muted small">(optional)</span></label>
                    <input type="date" name="stay_date" value="{{ old('stay_date', optional($review->stay_date)->format('Y-m-d')) }}"
                           class="form-control @error('stay_date') is-invalid @enderror">
                    @error('stay_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Sort order</label>
                    <input type="number" name="sort_order" min="0" max="999" value="{{ old('sort_order', $review->sort_order ?? 0) }}" class="form-control">
                    <div class="form-text">Smaller number shows first.</div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="review_active"
                           @checked(old('is_active', $review->is_active ?? true))>
                    <label class="form-check-label" for="review_active">Show on website</label>
                </div>
            </div>
        </div>

        <button class="btn btn-hnp w-100">{{ isset($isEdit) && $isEdit ? 'Save Changes' : 'Add Review' }}</button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-link w-100 text-muted">Cancel</a>
    </div>
</div>
