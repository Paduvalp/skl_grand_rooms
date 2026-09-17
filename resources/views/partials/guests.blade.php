{{--
    Who the hotel is handy for.

    This targets the searches people make by reason rather than by place:
    "rooms for hospital visitors", "rooms for parents visiting college",
    "hotel for tech park employees". It is written as who we are convenient
    for, not as a claim about who actually stays, because only the front desk
    knows that.

    Edit the wording here if the mix of guests changes.
--}}
@php
    $name = $settings['site_name'] ?? 'SKL Grand Rooms';

    $guestTypes = [
        [
            'icon' => 'bi-heart-pulse',
            'title' => 'Visiting someone in hospital',
            'text' => 'A quiet room near the RR Nagar hospitals, so family can stay close by without paying city-centre rates. Late arrivals are fine, just tell us.',
        ],
        [
            'icon' => 'bi-mortarboard',
            'title' => 'Parents visiting a student',
            'text' => 'Handy for Bangalore University at Jnanabharathi and the engineering colleges around RR Nagar, for admissions, visits and results day.',
        ],
        [
            'icon' => 'bi-briefcase',
            'title' => 'Here for work',
            'text' => 'Close to Global Village Tech Park and the Mysore Road business stretch. Free Wi-Fi, an early start and a clean room to come back to.',
        ],
        [
            'icon' => 'bi-pencil-square',
            'title' => 'In town for an exam or interview',
            'text' => 'A calm night before a test or an interview, away from the traffic, with hot water in the morning and a quick way back to the metro.',
        ],
        [
            'icon' => 'bi-people',
            'title' => 'Family and wedding guests',
            'text' => 'Rooms that take couples and families, so relatives attending a function nearby can stay together without splitting across the city.',
        ],
        [
            'icon' => 'bi-signpost-2',
            'title' => 'Breaking a journey',
            'text' => 'On the Mysore Road side of Bengaluru with parking on site, which suits anyone driving in or out of the city rather than through it.',
        ],
    ];
@endphp

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Who we are handy for</h2>
            <p class="text-muted mb-0">
                Most people who stay at {{ $name }} are here for one of these reasons.
            </p>
        </div>

        <div class="row g-4">
            @foreach ($guestTypes as $type)
                <div class="col-md-6 col-lg-4">
                    <div class="p-4 h-100 border rounded-3">
                        <div class="service-icon"><i class="bi {{ $type['icon'] }}"></i></div>
                        <h3 class="fw-bold h6 mb-2">{{ $type['title'] }}</h3>
                        <p class="text-muted small mb-0">{{ $type['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
