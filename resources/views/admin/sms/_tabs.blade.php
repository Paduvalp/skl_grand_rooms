{{-- The two halves of SMS Marketing: writing a message, and the phone book. --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.sms.create') ? 'active' : '' }}" href="{{ route('admin.sms.create') }}">
            <i class="bi bi-chat-dots me-1"></i>Send a message
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.sms.contacts') ? 'active' : '' }}" href="{{ route('admin.sms.contacts') }}">
            <i class="bi bi-person-lines-fill me-1"></i>Phone numbers
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.sms.templates') ? 'active' : '' }}" href="{{ route('admin.sms.templates') }}">
            <i class="bi bi-file-text me-1"></i>Templates
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.sms.credentials') ? 'active' : '' }}" href="{{ route('admin.sms.credentials') }}">
            <i class="bi bi-key me-1"></i>Credentials
        </a>
    </li>
    <li class="nav-item ms-auto d-flex align-items-center">
        <form action="{{ route('admin.sms.lock') }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-outline-secondary" title="Ask for the code again">
                <i class="bi bi-lock me-1"></i>Lock
            </button>
        </form>
    </li>
</ul>
