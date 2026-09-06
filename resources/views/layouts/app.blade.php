<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
        $siteName = $settings['site_name'] ?? 'SKL GRAND ROOMS';

        $defaultDescription = $settings['meta_description']
            ?? $settings['tagline']
            ?? 'Clean, comfortable rooms at honest prices.';

        // Each page can set its own title and description. If it does not,
        // the site-wide defaults above are used.
        $pageTitle = trim($__env->yieldContent('title', 'Home'));
        $metaDescription = trim($__env->yieldContent('meta_description', $defaultDescription));
        $metaRobots = trim($__env->yieldContent('robots', 'index, follow'));

        $fullTitle = $pageTitle === $siteName
            ? $siteName
            : $pageTitle.' | '.$siteName;

        $shareImage = \App\Support\Seo::shareImage();
    @endphp

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="robots" content="{{ $metaRobots }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Shown when the page is shared on WhatsApp, Facebook, LinkedIn --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="en_IN">
    @if ($shareImage)
        <meta property="og:image" content="{{ $shareImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    @if (!empty($settings['search_console_code']))
        <meta name="google-site-verification" content="{{ $settings['search_console_code'] }}">
    @endif

    <meta name="theme-color" content="#14532d">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    {{-- Structured data: tells Google this is a hotel, where it is, and what it costs --}}
    <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::hotel($settings)) !!}</script>
    @stack('schema')

    @if (!empty($settings['ga_measurement_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['ga_measurement_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', @json($settings['ga_measurement_id']));
        </script>
    @endif
</head>
<body>

@include('partials.nav')

<main>
    @yield('content')
</main>

@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
@include('partials.alerts')
@stack('scripts')
</body>
</html>
