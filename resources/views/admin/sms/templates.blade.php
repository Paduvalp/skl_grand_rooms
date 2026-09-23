@extends('layouts.admin')
@section('title', 'SMS Templates')
@section('heading', 'SMS Marketing')

@section('content')

@include('admin.sms._tabs')

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Saved templates</strong></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Name</th><th>Message</th><th style="width:80px">Status</th><th style="width:100px"></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $template)
                            <tr>
                                <td><strong>{{ $template->title }}</strong></td>
                                <td class="small">
                                    {{ $template->body }}
                                    <div class="text-muted mt-1">
                                        {{ mb_strlen($template->body) }} characters,
                                        {{ mb_strlen($template->body) <= 160 ? 1 : ceil(mb_strlen($template->body) / 153) }} SMS
                                        @if (\App\Models\SmsTemplate::needsName($template->body))
                                            <span class="badge bg-light text-dark border ms-1">personalised</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $template->is_active ? 'On' : 'Off' }}
                                    </span>
                                </td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                                            data-bs-target="#edit{{ $template->id }}" aria-label="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" form="toggle{{ $template->id }}"
                                            title="{{ $template->is_active ? 'Switch off' : 'Switch on' }}">
                                        <i class="bi {{ $template->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" form="delete{{ $template->id }}" aria-label="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="edit{{ $template->id }}">
                                <td colspan="4" class="bg-light">
                                    <form action="{{ route('admin.sms.templates.update', $template) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="mb-2">
                                            <label class="form-label small">Name</label>
                                            <input type="text" name="title" value="{{ $template->title }}" maxlength="120" class="form-control form-control-sm" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Message</label>
                                            <textarea name="body" rows="3" maxlength="480" class="form-control form-control-sm" required>{{ $template->body }}</textarea>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                                   id="active{{ $template->id }}" @checked($template->is_active)>
                                            <label class="form-check-label small" for="active{{ $template->id }}">Available when sending</label>
                                        </div>
                                        <button class="btn btn-sm btn-hnp"><i class="bi bi-check2 me-1"></i>Save changes</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No templates yet. Write your first one on the right.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-3">{{ $templates->links() }}</div>
        </div>

        @foreach ($templates as $template)
            <form id="toggle{{ $template->id }}" action="{{ route('admin.sms.templates.toggle', $template) }}" method="POST" class="d-none">
                @csrf @method('PATCH')
            </form>
            <form id="delete{{ $template->id }}" action="{{ route('admin.sms.templates.destroy', $template) }}" method="POST" class="d-none"
                  data-confirm="Delete this template?">
                @csrf @method('DELETE')
            </form>
        @endforeach
    </div>

    <div class="col-lg-5">
        <div class="card stat-card">
            <div class="card-header bg-white"><strong>New template</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.sms.templates.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="title">Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" maxlength="120" required
                               class="form-control @error('title') is-invalid @enderror" placeholder="Weekend offer">
                        <div class="form-text">Only you see this, to find the template later.</div>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="body">Message <span class="text-danger">*</span></label>
                        <textarea name="body" id="body" rows="5" maxlength="480" required
                                  class="form-control @error('body') is-invalid @enderror"
                                  placeholder="Hi {name}, this is Shashi. Our hotel SKL Grand Rooms is open in RR Nagar. See rooms: sklgrandrooms.com/stay">{{ old('body') }}</textarea>
                        <div class="form-text" id="tplCounter"></div>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-light border small">
                        <strong>Use {name} for the guest's name.</strong>
                        It becomes their first name from Phone numbers, so
                        <em>"Hi {name}, this is Shashi"</em> arrives as <em>"Hi Ravi, this is Shashi"</em>.
                        A number with no name saved gets "there". Each guest is then sent their own
                        text, so 10 guests use 10 of the daily allowance.
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="insertName">
                            Insert {name}
                        </button>
                    </div>

                    <button class="btn btn-hnp w-100"><i class="bi bi-save me-1"></i>Save template</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        var body = document.getElementById('body');
        var counter = document.getElementById('tplCounter');
        var insert = document.getElementById('insertName');

        function count() {
            // {name} is a placeholder; a real name is usually longer, so
            // show the worst case rather than a figure that flatters.
            var text = body.value.replace(/\{name\}/g, 'Ravi');
            var length = text.length;
            var parts = length === 0 ? 0 : (length <= 160 ? 1 : Math.ceil(length / 153));

            counter.textContent = length + ' characters, ' + parts + ' SMS per guest'
                + (body.value.indexOf('{name}') !== -1 ? ' (counted with a name filled in)' : '');
        }

        if (body && counter) {
            body.addEventListener('input', count);
            count();
        }

        if (insert && body) {
            insert.addEventListener('click', function () {
                var at = body.selectionStart || body.value.length;

                body.value = body.value.slice(0, at) + '{name}' + body.value.slice(at);
                body.focus();
                body.selectionEnd = at + 6;
                count();
            });
        }
    })();
</script>
@endpush
