{{--
    Floating "Chat on WhatsApp" button. Public layout only, never admin.
    A room page sets $whatsappRoom so the message names that room.
--}}
@php $waLink = \App\Support\WhatsApp::link($settings, $whatsappRoom ?? null); @endphp

@if ($waLink)
    <a href="{{ $waLink }}" class="wa-float" target="_blank" rel="noopener"
       aria-label="Chat on WhatsApp" title="Chat on WhatsApp">
        <svg viewBox="0 0 32 32" width="30" height="30" aria-hidden="true" focusable="false">
            <path fill="currentColor" d="M16.04 3C8.86 3 3.03 8.82 3.03 16c0 2.3.6 4.54 1.74 6.52L3 29l6.64-1.74A12.95 12.95 0 0 0 16.04 29C23.2 29 29 23.18 29 16S23.2 3 16.04 3zm0 23.7c-1.95 0-3.87-.52-5.54-1.52l-.4-.24-3.94 1.03 1.05-3.84-.26-.4A10.66 10.66 0 0 1 5.33 16c0-5.9 4.8-10.7 10.71-10.7 5.9 0 10.66 4.8 10.66 10.7 0 5.9-4.77 10.7-10.66 10.7zm5.87-8.02c-.32-.16-1.9-.94-2.2-1.04-.29-.11-.5-.16-.72.16-.21.32-.83 1.04-1.01 1.26-.19.21-.37.24-.7.08-.32-.16-1.36-.5-2.59-1.6-.96-.85-1.6-1.9-1.8-2.23-.18-.32-.02-.5.15-.66.14-.14.32-.37.48-.56.16-.19.21-.32.32-.53.1-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.37-.26-.62-.53-.54-.72-.55h-.62c-.21 0-.56.08-.85.4-.29.32-1.12 1.1-1.12 2.66 0 1.57 1.14 3.08 1.3 3.3.16.2 2.25 3.43 5.44 4.8.76.33 1.35.53 1.82.68.76.24 1.46.2 2 .12.61-.09 1.9-.78 2.16-1.52.27-.75.27-1.39.19-1.52-.08-.14-.29-.22-.61-.38z"/>
        </svg>
    </a>
@endif
