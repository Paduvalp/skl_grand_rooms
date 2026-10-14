<div class="border rounded p-2 mb-2" data-row>
    <div class="d-flex gap-2 mb-2">
        <input type="text" name="documents[{{ $index }}][label]" class="form-control form-control-sm"
               placeholder="What is it? e.g. Aadhaar - Ravi" maxlength="100">
        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-row title="Remove this row"><i class="bi bi-x-lg"></i></button>
    </div>
    <input type="file" name="documents[{{ $index }}][files][]" multiple
           accept="image/jpeg,image/png,image/webp,application/pdf" class="form-control form-control-sm">
    <div class="d-flex flex-wrap gap-2 mt-2" data-previews></div>
</div>
