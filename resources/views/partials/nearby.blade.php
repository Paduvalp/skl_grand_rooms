{{--
    Landmarks near the hotel.

    This is the part of the page that wins "hotel near <place>" searches:
    Google can only match the hotel to a landmark if the landmark is written
    on the page in readable text. Edit the list in
    Admin -> Site Settings -> Local search.

    The whole block disappears when no landmarks have been entered.
--}}
@php
    $landmarks = \App\Support\Seo::landmarks($settings);
    $areas = \App\Support\Seo::serviceAreas($settings);
    $mapUrl = \App\Support\Seo::mapUrl($settings);
@endphp

@if ($landmarks || $areas)
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="fw-bold mb-2">What is nearby</h2>
            <p class="text-muted mb-4">
                How far {{ $settings['site_name'] ?? 'the hotel' }} is from the places
                guests usually arrive from.
            </p>

            @if ($landmarks)
                <div class="row g-3">
                    @foreach ($landmarks as $landmark)
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start h-100 p-3 bg-white rounded-3 border">
                                <div class="service-icon me-3 flex-shrink-0"
                                     style="width:40px;height:40px;font-size:1rem">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div>
                                    <strong class="d-block">{{ $landmark['name'] }}</strong>
                                    @if ($landmark['distance'])
                                        <span class="text-muted small">{{ $landmark['distance'] }} away</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($areas)
                <p class="text-muted mt-4 mb-0">
                    Guests also reach us easily from
                    {{-- Written as a sentence so it reads naturally, not as a keyword list --}}
                    {{ implode(', ', array_slice($areas, 0, max(count($areas) - 1, 1))) }}@if (count($areas) > 1) and {{ end($areas) }}@endif.
                </p>
            @endif

            @if ($mapUrl)
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener"
                   class="btn btn-outline-dark btn-sm mt-4">
                    <i class="bi bi-map me-1"></i>Get directions on Google Maps
                </a>
            @endif
        </div>
    </section>
@endif
