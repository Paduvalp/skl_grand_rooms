@extends('layouts.app')
@section('title', 'Privacy Policy')

@section('meta_description', 'How ' . ($settings['site_name'] ?? 'SKL Grand Rooms') . ' collects, uses and protects the personal information you give us when you book a room or send a message.')

@php
    $name = $settings['site_name'] ?? 'SKL GRAND ROOMS';
    $email = trim((string) ($settings['email'] ?? ''));
    $phone = trim((string) ($settings['phone'] ?? ''));
    $address = trim((string) ($settings['address'] ?? ''));

    // The table of contents is built from this list, so a new section only
    // has to be added here and written below with the same id.
    $sections = [
        'what-we-collect' => 'What we collect',
        'why-we-use-it' => 'Why we use it',
        'legal-basis' => 'Why we are allowed to hold it',
        'sharing' => 'Who else sees it',
        'cookies' => 'Cookies and analytics',
        'how-long' => 'How long we keep it',
        'security' => 'How we protect it',
        'your-rights' => 'Your rights',
        'children' => 'Children',
        'changes' => 'Changes to this policy',
        'contact' => 'How to reach us',
    ];
@endphp

@section('content')

<div class="page-head">
    <div class="container">
        <h1>Privacy Policy</h1>
        <p class="mb-0 opacity-75">Home / Privacy Policy</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row g-5">

            <div class="col-lg-4">
                <nav class="legal-toc" aria-label="Sections of this policy">
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
                        <li>We ask for your name, email and phone only so we can hold and confirm your room.</li>
                        <li>We never take card or bank details on this website. Payment happens at the hotel.</li>
                        <li>We do not sell your information, and we do not pass it to advertisers.</li>
                        <li>You can ask us to show you, correct or delete what we hold. Just write to us.</li>
                    </ul>
                </div>

                <div class="legal-body mt-5">

                    <p>
                        This policy explains what {{ $name }} does with the personal information you
                        give us through this website. It is written in plain language on purpose. If
                        anything here is unclear, please ask us and we will explain it.
                    </p>

                    <section id="what-we-collect">
                        <h2><span class="legal-num">1.</span>What we collect</h2>

                        <p>We only collect what you type into one of our two forms.</p>

                        <div class="table-responsive">
                            <table class="table legal-table align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">When you</th>
                                        <th scope="col">We collect</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Book a room</strong></td>
                                        <td>
                                            Your name, email address and phone number, the room you
                                            picked, your check-in and check-out dates, how many guests
                                            and rooms you need, and any note you add to the booking.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Send us a message</strong></td>
                                        <td>
                                            Your name, email address, and the subject and text of your
                                            message. Your phone number too, if you choose to add it.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Arrive from a link we shared</strong></td>
                                        <td>
                                            The tag on that link saying where it came from, for
                                            example that it was our Instagram post rather than a
                                            search result, and the first page you landed on. No
                                            name is attached to this. See
                                            <a href="#cookies">cookies</a> below.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Check a booking</strong></td>
                                        <td>
                                            Your booking reference and the email address you booked
                                            with. These are checked against a booking you already made.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h3>What we deliberately do not collect</h3>
                        <p>
                            This website does not take payments. There is no card number, UPI ID,
                            net-banking detail or bank account on any form here, so none of it is
                            stored. You pay at the hotel. Anyone asking you to pay through this
                            website is not us, and you should tell us if that happens.
                        </p>
                        <p>
                            We also do not ask for your date of birth, your ID numbers, or anything
                            about your health, religion, caste or political views.
                        </p>
                    </section>

                    <section id="why-we-use-it">
                        <h2><span class="legal-num">2.</span>Why we use it</h2>
                        <ul>
                            <li>To hold the room you asked for and confirm it back to you.</li>
                            <li>To contact you about that booking, for example if the room you chose is no longer free, or to agree an arrival time.</li>
                            <li>To answer the message you sent us.</li>
                            <li>To let you look up your own booking with your reference and email.</li>
                            <li>To keep our own records of who stayed and when, which a hotel is required to do.</li>
                        </ul>
                        <p>
                            That is the whole list. We do not use your details to advertise to you,
                            and we do not add you to a mailing list because you booked a room.
                        </p>
                    </section>

                    <section id="legal-basis">
                        <h2><span class="legal-num">3.</span>Why we are allowed to hold it</h2>
                        <p>
                            When you fill in a booking or contact form and submit it, you are giving
                            us your details so that we can do the thing you asked for. We hold and
                            use them for that purpose, and for the record-keeping that applies to
                            hotels in India.
                        </p>
                        <p>
                            You can withdraw that agreement at any time by writing to us, and we will
                            act on it. Where we are required by law to keep a record of a stay, we
                            will tell you that we are keeping that part, and why.
                        </p>
                    </section>

                    <section id="sharing">
                        <h2><span class="legal-num">4.</span>Who else sees it</h2>
                        <p><strong>We do not sell your information, and we do not rent or trade it.</strong></p>
                        <p>Apart from our own staff, it is seen by:</p>
                        <ul>
                            <li>
                                <strong>Our web hosting company</strong>, because the website and its
                                database sit on their servers. They hold the data for us and are not
                                permitted to use it for anything of their own.
                            </li>
                            <li>
                                <strong>Government authorities</strong>, where the law requires it, for
                                example a lawful request from the police, or the guest records that
                                hotels are obliged to maintain and produce when asked.
                            </li>
                        </ul>
                        <p>
                            If we ever need to share your details with anyone else, we will ask you
                            first.
                        </p>
                    </section>

                    <section id="cookies">
                        <h2><span class="legal-num">5.</span>Cookies and analytics</h2>

                        <h3>The cookie we cannot do without</h3>
                        <p>
                            This site sets one small cookie in your browser to keep your session
                            working and to protect our forms from being submitted by someone else on
                            your behalf. Without it the booking form cannot work. It holds no
                            personal information about you and it is not used to follow you around
                            the web.
                        </p>

                        <h3>Remembering which link brought you here</h3>
                        <p>
                            When we share a link to this site, in an advertisement, a social media
                            post or a message, that link can carry a short tag saying where it came
                            from. We keep that tag in a second cookie for 30 days, so that if you go
                            on to book a room we can see which of our own posts or advertisements
                            actually help people find us.
                        </p>
                        <p>
                            This is about our own advertising, and nothing else. The tag holds no
                            name and nothing that identifies you. This website is the only site that
                            can read it, we do not share it with any advertising network, and it is
                            not used to follow you anywhere else. If you do make a booking, the tag
                            is stored with that booking so we know which link it came from.
                        </p>
                        <p>
                            Clearing cookies in your browser removes it, and nothing on this site
                            stops working if you do.
                        </p>

                        <h3>Visitor statistics</h3>
                        @if (!empty($settings['ga_measurement_id']))
                            <p>
                                We use Google Analytics to see how many people visit and which pages
                                they read, so we know what to improve. It tells us numbers and
                                patterns, not names. Google sets its own cookies to do this, and
                                Google's handling of that data is covered by
                                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google's privacy policy</a>.
                            </p>
                            <p>
                                You can opt out for every site you visit with the
                                <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener">Google Analytics opt-out add-on</a>,
                                or by turning off cookies in your browser settings. Nothing on this
                                site stops working if you do.
                            </p>
                        @else
                            <p>
                                Website analytics are currently switched off. No tracking or
                                measurement script is loaded on this site, and nothing about your
                                visit is sent to any third party.
                            </p>
                        @endif
                    </section>

                    <section id="how-long">
                        <h2><span class="legal-num">6.</span>How long we keep it</h2>
                        <ul>
                            <li><strong>Bookings</strong> are kept for as long as we are required to keep guest records, and so that we can settle any question about a past stay.</li>
                            <li><strong>Messages</strong> sent through the contact form are kept until the matter is dealt with, and then cleared out periodically.</li>
                        </ul>
                        <p>
                            When information is no longer needed for either reason, it is deleted. If
                            you want your details removed sooner, see <a href="#your-rights">your rights</a> below.
                        </p>
                    </section>

                    <section id="security">
                        <h2><span class="legal-num">7.</span>How we protect it</h2>
                        <p>
                            The site is served over an encrypted connection, so what you type into a
                            form cannot be read in transit. The area where bookings and messages are
                            managed is behind a password and is reachable only by the people who run
                            the hotel. Passwords are stored scrambled, never as readable text.
                        </p>
                        <p>
                            We should be honest with you: no website anywhere can promise it will
                            never be broken into. What we can promise is that we keep the amount we
                            hold small, we do not hold payment details at all, and if a breach ever
                            affected your information we would tell you rather than stay quiet.
                        </p>
                    </section>

                    <section id="your-rights">
                        <h2><span class="legal-num">8.</span>Your rights</h2>
                        <p>You can ask us to:</p>
                        <ul>
                            <li><strong>Show you</strong> what we hold about you.</li>
                            <li><strong>Correct</strong> anything that is wrong or out of date.</li>
                            <li><strong>Delete</strong> what we hold, unless we are required to keep it as a guest record.</li>
                            <li><strong>Stop using</strong> your details, by withdrawing the agreement you gave when you submitted the form.</li>
                            <li><strong>Complain</strong>, if you think we have handled your information badly.</li>
                        </ul>
                        <p>
                            Write to us using the details at the bottom of this page. We will reply
                            within a reasonable time, and we will not charge you for asking. We may
                            need to check that you are who you say you are before we hand over or
                            delete anything, which protects you as much as us.
                        </p>
                    </section>

                    <section id="children">
                        <h2><span class="legal-num">9.</span>Children</h2>
                        <p>
                            This website is meant for adults making a booking. We do not knowingly
                            collect information from children on their own. Children are of course
                            welcome to stay as part of a booking made by a parent or guardian. If you
                            believe a child has sent us their details directly, tell us and we will
                            remove them.
                        </p>
                    </section>

                    <section id="changes">
                        <h2><span class="legal-num">10.</span>Changes to this policy</h2>
                        <p>
                            If we change how we handle your information, we will update this page and
                            change the date at the top. There is no other version of this policy
                            kept elsewhere, so what you read here is what applies.
                        </p>
                    </section>

                    <section id="contact">
                        <h2><span class="legal-num">11.</span>How to reach us</h2>
                        <p>
                            For anything about your information, including a request or a complaint,
                            contact us directly:
                        </p>

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
                    See also our <a href="{{ route('terms') }}">Terms and Conditions</a>, which cover
                    bookings, check-in and cancellations.
                </p>
            </div>

        </div>
    </div>
</section>

@endsection

@push('schema')
<script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs(['Home' => route('home'), 'Privacy Policy' => route('privacy')])) !!}</script>
@endpush
