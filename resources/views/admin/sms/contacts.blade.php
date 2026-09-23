@extends('layouts.admin')
@section('title', 'Phone Numbers')
@section('heading', 'SMS Marketing')

@section('content')

@include('admin.sms._tabs')

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card stat-card">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Saved numbers</strong>
                <span class="text-muted small">{{ $activeTotal }} of {{ $total }} switched on</span>
            </div>
            <div class="card-body pb-0">
                <form method="GET" action="{{ route('admin.sms.contacts') }}" class="d-flex gap-2 mb-3">
                    <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm"
                           placeholder="Search by name or number">
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                    @if ($search !== '')
                        <a href="{{ route('admin.sms.contacts') }}" class="btn btn-sm btn-link text-muted">Clear</a>
                    @endif
                </form>
            </div>

            <form action="{{ route('admin.sms.contacts.use') }}" method="POST" id="useForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px"><input type="checkbox" class="form-check-input" id="checkAll" aria-label="Select all"></th>
                                <th>Name</th><th>Number</th><th style="width:90px">Status</th><th style="width:90px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contacts as $contact)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $contact->id }}"
                                               class="form-check-input row-check" @disabled(! $contact->is_active)
                                               aria-label="Select {{ $contact->name ?: $contact->phone }}">
                                    </td>
                                    <td>
                                        {{ $contact->name ?: '—' }}
                                        @if ($contact->note)
                                            <div class="small text-muted">{{ $contact->note }}</div>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $contact->prettyPhone() }}</td>
                                    <td>
                                        <span class="badge {{ $contact->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $contact->is_active ? 'On' : 'Off' }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <button class="btn btn-sm btn-outline-secondary" form="toggle{{ $contact->id }}"
                                                title="{{ $contact->is_active ? 'Switch off' : 'Switch on' }}">
                                            <i class="bi {{ $contact->is_active ? 'bi-bell-slash' : 'bi-bell' }}"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" form="delete{{ $contact->id }}" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No numbers yet. Import a file or add one on the right.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($contacts->count())
                    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                        <button class="btn btn-hnp btn-sm"><i class="bi bi-send me-1"></i>Send SMS to ticked numbers</button>
                        <span class="text-muted small">Only numbers that are switched on can be ticked.</span>
                    </div>
                @endif
            </form>

            <div class="px-3 pb-3">{{ $contacts->links() }}</div>
        </div>

        {{-- Kept outside the table so these are not forms inside a form. --}}
        @foreach ($contacts as $contact)
            <form id="toggle{{ $contact->id }}" action="{{ route('admin.sms.contacts.toggle', $contact) }}" method="POST" class="d-none">
                @csrf @method('PATCH')
            </form>
            <form id="delete{{ $contact->id }}" action="{{ route('admin.sms.contacts.destroy', $contact) }}" method="POST" class="d-none"
                  data-confirm="Remove this number from the list?">
                @csrf @method('DELETE')
            </form>
        @endforeach
    </div>

    <div class="col-lg-5">
        <div class="card stat-card mb-3">
            <div class="card-header bg-white"><strong>Import from CSV or Excel</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.sms.contacts.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".csv,.txt,.xlsx" required
                           class="form-control @error('file') is-invalid @enderror">
                    @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">
                        A file with a name column and a mobile number column, like this:
                        <div class="bg-light border rounded p-2 mt-2 mb-2 small font-monospace">
                            Name, Phone<br>
                            Ravi Kumar, 9876543210<br>
                            Anita Sharma, 9845012345
                        </div>
                        A heading row is fine, and so is a file of numbers on their own.
                        Numbers already saved are left as they are, so importing the
                        same list twice does no harm.
                        <div class="mt-2">
                            <a href="{{ route('admin.sms.contacts.sample') }}">
                                <i class="bi bi-download me-1"></i>Download a sample file
                            </a>
                            <span class="text-muted">- fill it in and upload it back.</span>
                        </div>
                    </div>
                    <button class="btn btn-hnp w-100 mt-2"><i class="bi bi-upload me-1"></i>Import file</button>
                </form>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Add one number</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.sms.contacts.store') }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label" for="name">Name <span class="text-muted small">(optional)</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" maxlength="120" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label" for="phone">Mobile number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                               class="form-control @error('phone') is-invalid @enderror" placeholder="9876543210">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="note">Note <span class="text-muted small">(optional)</span></label>
                        <input type="text" name="note" id="note" value="{{ old('note') }}" maxlength="190"
                               class="form-control" placeholder="Stayed in October">
                    </div>
                    <button class="btn btn-outline-dark w-100"><i class="bi bi-plus-lg me-1"></i>Add to list</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        var all = document.getElementById('checkAll');

        if (!all) {
            return;
        }

        all.addEventListener('change', function () {
            document.querySelectorAll('.row-check:not([disabled])').forEach(function (box) {
                box.checked = all.checked;
            });
        });
    })();
</script>
@endpush
