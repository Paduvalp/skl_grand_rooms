@extends('layouts.admin')
@section('title', 'Site Settings')
@section('heading', 'Site Settings')

@section('content')

<p class="text-muted small">Everything here shows on the website. Change it and press Save &mdash; no code editing needed.</p>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card stat-card mb-4">
                <div class="card-header bg-white"><strong>Hotel identity</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Hotel name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $values['site_name'] ?? '') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline" value="{{ old('tagline', $values['tagline'] ?? '') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Footer text</label>
                        <textarea name="footer_text" rows="2" class="form-control">{{ old('footer_text', $values['footer_text'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card stat-card mb-4">
                <div class="card-header bg-white"><strong>Home page banner</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Banner heading</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $values['hero_title'] ?? '') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner text</label>
                        <textarea name="hero_subtitle" rows="3" class="form-control">{{ old('hero_subtitle', $values['hero_subtitle'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card stat-card">
                <div class="card-header bg-white"><strong>About page</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">About heading</label>
                        <input type="text" name="about_heading" value="{{ old('about_heading', $values['about_heading'] ?? '') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">About text</label>
                        <textarea name="about_text" rows="7" class="form-control">{{ old('about_text', $values['about_text'] ?? '') }}</textarea>
                        <div class="form-text">Leave a blank line between paragraphs.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Highlight points</label>
                        <textarea name="about_points" rows="3" class="form-control">{{ old('about_points', $values['about_points'] ?? '') }}</textarea>
                        <div class="form-text">Separate each point with a comma.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card stat-card mb-4">
                <div class="card-header bg-white"><strong>Contact details</strong></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $values['phone'] ?? '') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $values['email'] ?? '') }}"
                               class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="3" class="form-control">{{ old('address', $values['address'] ?? '') }}</textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Google Map</label>
                        <textarea name="map_embed" rows="4" class="form-control" placeholder="Paste the Google Maps link for the hotel">{{ old('map_embed', $values['map_embed'] ?? '') }}</textarea>
                        <div class="form-text">
                            Paste <strong>either</strong> a plain Google Maps link (open the hotel on
                            Google Maps, press Share, copy the link) <strong>or</strong> the full
                            &lt;iframe&gt; embed code. Both work.<br>
                            Leave it empty and the map still shows, using the address above.
                        </div>
                    </div>
                </div>
            </div>

            <div class="card stat-card">
                <div class="card-header bg-white"><strong>Check-in / Check-out</strong></div>
                <div class="card-body row g-3">
                    <div class="col-6">
                        <label class="form-label">Check-in time</label>
                        <input type="text" name="checkin_time" value="{{ old('checkin_time', $values['checkin_time'] ?? '') }}" class="form-control" placeholder="12:00 PM">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Check-out time</label>
                        <input type="text" name="checkout_time" value="{{ old('checkout_time', $values['checkout_time'] ?? '') }}" class="form-control" placeholder="11:00 AM">
                    </div>
                </div>
            </div>

        </div>

        <div class="col-12">
            <div class="card stat-card">
                <div class="card-header bg-white"><strong>Search engines (SEO)</strong></div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label">Google description</label>
                        <textarea name="meta_description" rows="2" maxlength="200"
                                  class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $values['meta_description'] ?? '') }}</textarea>
                        <div class="form-text">
                            The grey text under your title in Google results. Keep it under about
                            155 characters or Google cuts it off. Mention the area and what makes
                            the hotel worth clicking.
                        </div>
                        @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <h6 class="fw-bold mt-4">Address for Google</h6>
                    <p class="text-muted small">
                        The same address as above, but split up so Google can read it and show
                        the hotel on Maps and in local results.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Street / area</label>
                            <input type="text" name="street_address" value="{{ old('street_address', $values['street_address'] ?? '') }}" class="form-control" placeholder="Kenchenhalli, Rajarajeshwari Nagar">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">City</label>
                            <input type="text" name="address_locality" value="{{ old('address_locality', $values['address_locality'] ?? '') }}" class="form-control" placeholder="Bengaluru">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">State</label>
                            <input type="text" name="address_region" value="{{ old('address_region', $values['address_region'] ?? '') }}" class="form-control" placeholder="Karnataka">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">PIN code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $values['postal_code'] ?? '') }}" class="form-control" placeholder="560026">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Map latitude</label>
                            <input type="text" name="geo_lat" value="{{ old('geo_lat', $values['geo_lat'] ?? '') }}" class="form-control" placeholder="12.9236">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Map longitude</label>
                            <input type="text" name="geo_lng" value="{{ old('geo_lng', $values['geo_lng'] ?? '') }}" class="form-control" placeholder="77.5127">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-text mb-2">
                                To get these two numbers: open Google Maps, right click on the
                                hotel, and click the pair of numbers at the top of the menu.
                                Leave them blank and the map pin is simply left out.
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4">Local search ("hotels near me")</h6>
                    <p class="text-muted small">
                        These fields are what make the hotel turn up when somebody
                        nearby searches. The landmarks are the important one: Google can
                        only match you to a place if that place is written on your site.
                    </p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Google Business Profile link</label>
                            <input type="url" name="google_business_url"
                                   value="{{ old('google_business_url', $values['google_business_url'] ?? '') }}"
                                   class="form-control @error('google_business_url') is-invalid @enderror"
                                   placeholder="https://maps.app.goo.gl/...">
                            <div class="form-text">
                                Open your hotel on Google Maps, press Share, and copy the link.
                                This tells Google that this website and that Maps listing are
                                the same business.
                            </div>
                            @error('google_business_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nearby landmarks</label>
                            <textarea name="nearby_landmarks" rows="6" class="form-control"
                                      placeholder="Bangalore University | 2 km&#10;RR Nagar Metro Station | 3 km&#10;Global Village Tech Park | 5 km">{{ old('nearby_landmarks', $values['nearby_landmarks'] ?? '') }}</textarea>
                            <div class="form-text">
                                One per line, written as <code>Place | distance</code>. The
                                distance part is optional. Use the names people actually
                                search for: stations, colleges, hospitals, tech parks,
                                temples, bus stands.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Areas you serve</label>
                            <textarea name="service_areas" rows="6" class="form-control"
                                      placeholder="Kenchenhalli&#10;Rajarajeshwari Nagar&#10;Jnanabharathi&#10;Nayandahalli">{{ old('service_areas', $values['service_areas'] ?? '') }}</textarea>
                            <div class="form-text">
                                One locality per line. These are the neighbourhoods guests
                                travel in from.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment accepted</label>
                            <input type="text" name="payment_accepted"
                                   value="{{ old('payment_accepted', $values['payment_accepted'] ?? '') }}"
                                   class="form-control" placeholder="Cash, UPI, Credit Card, Debit Card">
                            <div class="form-text">Shown in search results as how guests can pay.</div>
                        </div>

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input type="hidden" name="open_24_hours" value="">
                                <input type="checkbox" name="open_24_hours" value="1" id="open_24_hours"
                                       class="form-check-input"
                                       @checked(old('open_24_hours', $values['open_24_hours'] ?? '') === '1')>
                                <label class="form-check-label" for="open_24_hours">
                                    Reception is open 24 hours
                                </label>
                                <div class="form-text">
                                    Only tick this if it is true. Google shows it as
                                    "Open 24 hours", and a wrong one costs you guests.
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4">Privacy policy and terms pages</h6>
                    <p class="text-muted small">
                        Both pages are already written and linked in the footer. These two
                        boxes are the parts only you can fill in.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Your cancellation policy</label>
                            <textarea name="cancellation_policy" rows="4" class="form-control"
                                      placeholder="Cancel free of charge up to 24 hours before check-in. After that we may charge one night.">{{ old('cancellation_policy', $values['cancellation_policy'] ?? '') }}</textarea>
                            <div class="form-text">
                                Shown in section 5 of the Terms page. One paragraph per line.
                                Leave it empty and a fair general wording is shown instead, but
                                your own real policy is always better: it is the clause guests
                                argue about.
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Legal pages last updated</label>
                            <input type="date" name="legal_updated_at"
                                   value="{{ old('legal_updated_at', $values['legal_updated_at'] ?? '') }}"
                                   class="form-control">
                            <div class="form-text">
                                The date shown at the top of both pages. Change it whenever you
                                edit the wording.
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4">Tracking and verification</h6>
                    <p class="text-muted small">
                        Both are optional and stay switched off until you paste a value in.
                        Nothing is sent to Google while these are empty.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Google Analytics 4 ID</label>
                            <input type="text" name="ga_measurement_id" value="{{ old('ga_measurement_id', $values['ga_measurement_id'] ?? '') }}" class="form-control" placeholder="G-XXXXXXXXXX">
                            <div class="form-text">From analytics.google.com. Starts with G-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Search Console verification code</label>
                            <input type="text" name="search_console_code" value="{{ old('search_console_code', $values['search_console_code'] ?? '') }}" class="form-control" placeholder="paste only the content value">
                            <div class="form-text">
                                From search.google.com/search-console, HTML tag method. Paste only
                                the long code, not the whole meta tag.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <button class="btn btn-hnp btn-lg px-4">Save Settings</button>
        </div>
    </div>
</form>

@endsection
