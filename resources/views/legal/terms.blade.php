@extends('layouts.app')
@section('title', 'Terms and Conditions')

@section('meta_description', 'The terms you agree to when you book a room at ' . ($settings['site_name'] ?? 'SKL Grand Rooms') . ': bookings, check-in, cancellation, house rules and liability.')

@php
    $name = $settings['site_name'] ?? 'SKL GRAND ROOMS';
    $email = trim((string) ($settings['email'] ?? ''));
    $phone = trim((string) ($settings['phone'] ?? ''));
    $address = trim((string) ($settings['address'] ?? ''));
    $checkin = trim((string) ($settings['checkin_time'] ?? '')) ?: '12:00 PM';
    $checkout = trim((string) ($settings['checkout_time'] ?? '')) ?: '11:00 AM';
    $city = trim((string) ($settings['address_locality'] ?? '')) ?: 'Bengaluru';
    $state = trim((string) ($settings['address_region'] ?? '')) ?: 'Karnataka';
    $cancellation = trim((string) ($settings['cancellation_policy'] ?? ''));

    $sections = [
        'agreement' => 'Agreeing to these terms',
        'booking' => 'How a booking works',
        'rates' => 'Rates and payment',
        'checkin' => 'Check-in, check-out and ID',
        'cancellation' => 'Changes and cancellation',
        'no-show' => 'Late arrival and no-shows',
        'house-rules' => 'House rules',
        'damage' => 'Damage and lost property',
        'liability' => 'What we are and are not responsible for',
        'force-majeure' => 'Events outside anyone\'s control',
        'website' => 'Using this website',
        'law' => 'Governing law',
        'contact' => 'How to reach us',
    ];
@endphp

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Terms and Conditions</h1>
        <p class="mb-0 opacity-75">Home / Terms and Conditions</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">

            <div class="col-lg-4">
                <nav class="legal-toc" aria-label="Sections of these terms">
                    <h6>On this page</h6>
                    @foreach ($sections as $id => $label)
                        <a href="#{{ $id }}">{{ $label }}</a>
                    @endforeach
                </nav>
            </div>

            <div class="col-lg-8">
                <span class="legal-meta">
                    <i class="bi bi-clock-history"></i> Last updated {{ $updated }}
                </span>

                <div class="legal-summary mt-4">
                    <strong class="d-block mb-2">The short version</strong>
                    <ul class="small">
                        <li>Booking online sends us a request. Your room is held once we confirm it back to you.</li>
                        <li>Nothing is charged on this website. You pay at the hotel.</li>
                        <li>Check-in from {{ $checkin }}, check-out by {{ $checkout }}. Bring a valid photo ID for every adult guest.</li>
                        <li>Tell us as early as you can if your plans change, and we will do our best to help.</li>
                    </ul>
                </div>

                <div class="legal-body mt-5">

                    <p>
                        These terms apply when you book a room at {{ $name }} through this website,
                        and while you are staying with us. Please read them before you book. They are
                        written to be clear rather than clever, because a guest who is surprised at
                        the front desk is a guest we have let down.
                    </p>

                    <section id="agreement">
                        <h2><span class="legal-num">1.</span>Agreeing to these terms</h2>
                        <p>
                            By submitting a booking on this website, you confirm that you are 18 or
                            older, that the details you have given are correct, and that you accept
                            these terms on behalf of everyone in your party. If you are booking for
                            somebody else, please make sure they know what is written here.
                        </p>
                    </section>

                    <section id="booking">
                        <h2><span class="legal-num">2.</span>How a booking works</h2>
                        <p>
                            <strong>Submitting the form is a request, not a confirmed room.</strong>
                            This is the part guests most often misunderstand, so it is worth being
                            exact about it:
                        </p>
                        <ol>
                            <li>You fill in the booking form and submit it.</li>
                            <li>You are shown a <strong>booking reference</strong> straight away. Keep it. It is how you look your booking up later.</li>
                            <li>Your request reaches us and we check the room is actually free for those dates.</li>
                            <li>We contact you to confirm. <strong>Your room is held from that moment, not before.</strong></li>
                        </ol>
                        <p>
                            Until we confirm, the room is not reserved for you. On busy dates a room
                            can be taken by someone else in between, and we will tell you honestly if
                            that has happened and offer you whatever else we have.
                        </p>
                        <p>
                            You can check where your booking stands at any time on the
                            <a href="{{ route('booking.status') }}">booking status page</a>, using your
                            reference and the email address you booked with.
                        </p>
                        <p>
                            We may decline a booking. We do not have to give a reason, but in practice
                            it is because the room is gone, the dates are wrong, the contact details
                            do not work, or a previous stay ended badly.
                        </p>
                    </section>

                    <section id="rates">
                        <h2><span class="legal-num">3.</span>Rates and payment</h2>
                        <ul>
                            <li>Rates shown on this site are <strong>per room, per night, in Indian Rupees</strong>.</li>
                            <li>Government taxes apply as required by law and are added to your bill.</li>
                            <li>The rate that applies is the one shown when you booked and that we confirmed to you.</li>
                            <li>Anything extra you use during your stay is billed separately at check-out.</li>
                        </ul>

                        <h3>No payment is taken on this website</h3>
                        <p>
                            There is no card, UPI or net-banking form anywhere on this site, and we
                            never ask for those details by email or message. You pay at the hotel,
                            by the methods we accept at the front desk. If anyone contacts you
                            claiming to be us and asks you to transfer money in advance, do not pay,
                            and please tell us.
                        </p>
                        <p>
                            We take care to keep rates on this site correct. If an obvious error
                            appears, for example a price that is clearly a typing mistake, we will
                            tell you the right rate before confirming, and you are free to walk away
                            at no cost.
                        </p>
                    </section>

                    <section id="checkin">
                        <h2><span class="legal-num">4.</span>Check-in, check-out and ID</h2>
                        <div class="table-responsive">
                            <table class="table legal-table align-middle">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 12rem">Check-in from</th>
                                        <td>{{ $checkin }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Check-out by</th>
                                        <td>{{ $checkout }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p>
                            Arriving earlier or leaving later is sometimes possible. Ask us, and we
                            will say yes if the room allows it. A late check-out that has not been
                            agreed may be charged.
                        </p>

                        <h3>Photo ID is compulsory</h3>
                        <p>
                            Every adult guest must present a valid government photo ID at check-in.
                            This is a legal requirement for hotels in India, not a house preference,
                            and we cannot let a room without it. Guests who are not Indian nationals
                            must present a passport and valid visa, which we are required to report
                            to the authorities.
                        </p>
                        <p>
                            The number of guests staying must match the booking. If more people
                            arrive than were booked, we may charge for the extra guests or decline
                            them, depending on what the room takes.
                        </p>
                    </section>

                    <section id="cancellation">
                        <h2><span class="legal-num">5.</span>Changes and cancellation</h2>
                        @if ($cancellation)
                            @foreach (preg_split('/\r\n|\r|\n/', $cancellation) as $line)
                                @if (trim($line) !== '')
                                    <p>{{ $line }}</p>
                                @endif
                            @endforeach
                        @else
                            <p>
                                To change or cancel a booking, contact us with your booking reference
                                as early as you can. Because nothing is charged when you book, a
                                cancellation made in good time costs you nothing.
                            </p>
                            <p>
                                Please do tell us. An empty room we could have let to somebody else
                                is the one thing that genuinely costs a small hotel, and a message
                                takes you a minute.
                            </p>
                        @endif
                        <p>
                            If we ever have to cancel or move your booking, which happens very
                            rarely, we will contact you as soon as we know, explain why, and either
                            offer you another room or help you find somewhere else nearby.
                        </p>
                    </section>

                    <section id="no-show">
                        <h2><span class="legal-num">6.</span>Late arrival and no-shows</h2>
                        <p>
                            Tell us if you are going to arrive late and we will hold your room. If we
                            have heard nothing from you and you have not arrived by the morning after
                            your check-in date, we may treat the booking as cancelled and let the
                            room.
                        </p>
                    </section>

                    <section id="house-rules">
                        <h2><span class="legal-num">7.</span>House rules</h2>
                        <p>These exist so that every guest gets a decent night's sleep.</p>
                        <ul>
                            <li>Please keep noise down between 10:00 PM and 7:00 AM.</li>
                            <li>Visitors who are not staying must be registered at the front desk and may not stay overnight.</li>
                            <li>Smoking is not permitted inside the rooms.</li>
                            <li>Illegal substances and anything unlawful are not permitted anywhere on the premises.</li>
                            <li>Please treat our staff and other guests with courtesy.</li>
                        </ul>
                        <p>
                            We may ask a guest whose behaviour is threatening, unlawful, or seriously
                            disturbing other guests to leave, without a refund for the remaining
                            nights. We do not do this lightly.
                        </p>
                        <p>
                            Parking, where available, is used at your own risk. Please ask us about
                            pets before you book rather than after you arrive.
                        </p>
                    </section>

                    <section id="damage">
                        <h2><span class="legal-num">8.</span>Damage and lost property</h2>
                        <p>
                            You are responsible for damage to the room, its contents or the building
                            caused by you or anyone in your party, beyond ordinary wear and tear, and
                            we may charge you the reasonable cost of repair or replacement.
                        </p>
                        <p>
                            Anything left behind will be kept for a reasonable period and returned if
                            you tell us. After that, uncollected items may be disposed of or given
                            away.
                        </p>
                    </section>

                    <section id="liability">
                        <h2><span class="legal-num">9.</span>What we are and are not responsible for</h2>
                        <p>
                            We take proper care of our guests and our building, and we are
                            responsible where our own negligence causes you harm. Nothing in these
                            terms limits our liability for death or personal injury caused by our
                            negligence, or for fraud. No wording on a website can remove that, and we
                            would not want it to.
                        </p>
                        <p>
                            Beyond that, please note:
                        </p>
                        <ul>
                            <li>Bring valuables to the front desk if you want them kept securely. We cannot accept responsibility for cash, jewellery or electronics left unattended in a room.</li>
                            <li>Interruptions to water, electricity or internet caused by the supplier or the local authority are outside our control, though we will always tell you what is happening and what we are doing about it.</li>
                            <li>Where we are liable for a booking going wrong, our responsibility is limited to the amount you paid for that booking.</li>
                        </ul>
                    </section>

                    <section id="force-majeure">
                        <h2><span class="legal-num">10.</span>Events outside anyone's control</h2>
                        <p>
                            Neither of us is responsible for failing to keep to these terms because
                            of something genuinely beyond our control, such as a natural disaster,
                            fire, flood, epidemic, strike, civil unrest, or an order from a
                            government authority. If such a thing stops your stay going ahead, we
                            will contact you and work out what is fair.
                        </p>
                    </section>

                    <section id="website">
                        <h2><span class="legal-num">11.</span>Using this website</h2>
                        <p>
                            The text, photographs, and design of this site belong to {{ $name }}.
                            Please do not copy them for your own commercial use without asking.
                        </p>
                        <p>
                            Room photographs show our actual rooms. Small differences between rooms of
                            the same type are normal, and furnishings change over time.
                        </p>
                        <p>
                            Please do not submit false bookings, attempt to break into or overload
                            the site, or use it for anything unlawful.
                        </p>
                        <p>
                            How we handle the details you give us is set out in our
                            <a href="{{ route('privacy') }}">Privacy Policy</a>, which forms part of
                            these terms.
                        </p>
                    </section>

                    <section id="law">
                        <h2><span class="legal-num">12.</span>Governing law</h2>
                        <p>
                            These terms are governed by the laws of India, and the courts at
                            {{ $city }}, {{ $state }} have jurisdiction over any dispute arising from
                            them.
                        </p>
                        <p>
                            If any part of these terms turns out to be unenforceable, the rest of
                            them still stand. We may update these terms from time to time; the
                            version on this page at the time you book is the one that applies to
                            your booking.
                        </p>
                        <p>
                            Before any of that, please just talk to us. Almost everything is settled
                            with a phone call.
                        </p>
                    </section>

                    <section id="contact">
                        <h2><span class="legal-num">13.</span>How to reach us</h2>

                        <div class="legal-contact mt-3">
                            <strong class="d-block mb-2">{{ $name }}</strong>
                            @if ($address)
                                <div class="mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $address }}</div>
                            @endif
                            @if ($phone)
                                <div class="mb-1"><i class="bi bi-telephone me-2"></i><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a></div>
                            @endif
                            @if ($email)
                                <div class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:{{ $email }}">{{ $email }}</a></div>
                            @endif
                            <a href="{{ route('contact') }}" class="btn btn-hnp btn-sm mt-3">
                                <i class="bi bi-chat-left-text me-1"></i>Use the contact form
                            </a>
                        </div>
                    </section>

                </div>

                <hr class="my-5">
                <p class="small text-muted mb-0">
                    See also our <a href="{{ route('privacy') }}">Privacy Policy</a>, which explains
                    what we do with the details you give us.
                </p>
            </div>

        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Terms and Conditions' => route('terms')])) !!}</script>
@endpush
