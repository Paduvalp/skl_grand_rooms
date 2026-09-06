@csrf
@if (isset($isEdit) && $isEdit)
    @method('PUT')
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Room name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $room->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <input type="text" name="type" value="{{ old('type', $room->type ?? 'Standard') }}"
                               class="form-control @error('type') is-invalid @enderror" placeholder="Deluxe / Suite / Family" required>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Short description <span class="text-muted small">(one line, shown on room cards)</span></label>
                        <input type="text" name="short_description" value="{{ old('short_description', $room->short_description) }}"
                               class="form-control @error('short_description') is-invalid @enderror" maxlength="300">
                        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Full description</label>
                        <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror">{{ old('description', $room->description) }}</textarea>
                        <div class="form-text">Leave a blank line between paragraphs.</div>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Amenities</label>
                        <input type="text" name="amenities" value="{{ old('amenities', $room->amenities) }}"
                               class="form-control @error('amenities') is-invalid @enderror"
                               placeholder="Free Wi-Fi, Air Conditioning, Smart TV">
                        <div class="form-text">Separate each one with a comma.</div>
                        @error('amenities')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Price per night (&#8377;) <span class="text-danger">*</span></label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $room->price) }}"
                           class="form-control @error('price') is-invalid @enderror" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Guests per room <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" min="1" max="20" value="{{ old('capacity', $room->capacity ?? 2) }}"
                           class="form-control @error('capacity') is-invalid @enderror" required>
                    @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">How many rooms of this type <span class="text-danger">*</span></label>
                    <input type="number" name="total_rooms" min="1" max="500" value="{{ old('total_rooms', $room->total_rooms ?? 1) }}"
                           class="form-control @error('total_rooms') is-invalid @enderror" required>
                    <div class="form-text">Used to check availability for the dates a guest picks.</div>
                    @error('total_rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Bed type</label>
                    <input type="text" name="bed_type" value="{{ old('bed_type', $room->bed_type) }}"
                           class="form-control" placeholder="1 Queen Bed">
                </div>
                <div class="mb-3">
                    <label class="form-label">Room size</label>
                    <input type="text" name="size" value="{{ old('size', $room->size) }}"
                           class="form-control" placeholder="280 sq ft">
                </div>
            </div>
        </div>

        <div class="card stat-card mb-3">
            <div class="card-body">
                <label class="form-label">Room photo</label>
                <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                <div class="form-text">JPG, PNG or WEBP. Up to 4 MB.</div>
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror

                @if ($room->image)
                    <img src="{{ $room->imageUrl() }}" alt="" class="img-fluid rounded mt-3" style="max-height:150px">
                    <div class="form-text">Upload a new file to replace this photo.</div>
                @endif
            </div>
        </div>

        <div class="card stat-card mb-3">
            <div class="card-body">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', $room->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Show on website</label>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured"
                           @checked(old('is_featured', $room->is_featured ?? false))>
                    <label class="form-check-label" for="is_featured">Feature on home page</label>
                </div>
            </div>
        </div>

        <button class="btn btn-hnp w-100">{{ isset($isEdit) && $isEdit ? 'Save Changes' : 'Add Room' }}</button>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-link w-100 text-muted">Cancel</a>
    </div>
</div>
