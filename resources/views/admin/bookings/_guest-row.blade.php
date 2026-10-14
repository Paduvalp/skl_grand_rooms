<div class="row g-2 mb-2 align-items-center" data-row>
    <div class="col">
        <input type="text" name="additional_guests[{{ $index }}][name]" value="{{ $guest['name'] ?? '' }}"
               class="form-control" placeholder="Guest name" maxlength="120">
    </div>
    <div class="col">
        <input type="text" name="additional_guests[{{ $index }}][phone]" value="{{ $guest['phone'] ?? '' }}"
               class="form-control" placeholder="Phone (optional)" maxlength="30">
    </div>
    <div class="col-auto">
        <button type="button" class="btn btn-outline-danger" data-remove-row title="Remove guest"><i class="bi bi-x-lg"></i></button>
    </div>
</div>
