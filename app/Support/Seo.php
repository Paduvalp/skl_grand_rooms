<?php

namespace App\Support;

use App\Models\Room;
use App\Models\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Builds the structured data (JSON-LD) that Google reads.
 *
 * Everything is built as a PHP array and then encoded, so quotes and
 * apostrophes in the hotel text can never break the page.
 */
class Seo
{
    /** Turn an array into a safe <script type="application/ld+json"> body. */
    public static function json(array $data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_PRETTY_PRINT
        );
    }

    /** The hotel itself. Goes on every public page. */
    public static function hotel(array $s): array
    {
        $name = $s['site_name'] ?? 'SKL GRAND ROOMS';

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Hotel',
            '@id' => url('/').'#hotel',
            'name' => $name,
            'url' => url('/'),
        ];

        if (! empty($s['meta_description'])) {
            $data['description'] = $s['meta_description'];
        }

        if (! empty($s['phone'])) {
            $data['telephone'] = $s['phone'];
        }

        if (! empty($s['email'])) {
            $data['email'] = $s['email'];
        }

        $address = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $s['street_address'] ?? null,
            'addressLocality' => $s['address_locality'] ?? null,
            'addressRegion' => $s['address_region'] ?? null,
            'postalCode' => $s['postal_code'] ?? null,
            'addressCountry' => 'IN',
        ]);

        // Only claim an address if we have more than the country.
        if (count($address) > 2) {
            $data['address'] = $address;
        }

        // Coordinates come from the Site Settings fields, and if those are
        // blank we read them out of the Google Maps link instead.
        if ($point = MapEmbed::coordinates($s)) {
            $data['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $point[0],
                'longitude' => $point[1],
            ];
        }

        if ($image = self::shareImage()) {
            $data['image'] = $image;
        }

        if ($range = self::priceRange()) {
            $data['priceRange'] = $range;
        }

        // Settings hold "12:00 PM" for people to read. Schema wants 24 hour time.
        if ($in = self::isoTime($s['checkin_time'] ?? null)) {
            $data['checkinTime'] = $in;
        }

        if ($out = self::isoTime($s['checkout_time'] ?? null)) {
            $data['checkoutTime'] = $out;
        }

        $data['amenityFeature'] = self::amenities();

        // The Google Business Profile / Maps listing. Tying the website and
        // the Maps pin together is the strongest on-page signal that the two
        // are the same business, and that pairing is what local results rank.
        if ($map = self::mapUrl($s)) {
            $data['hasMap'] = $map;
        }

        if ($sameAs = self::sameAs($s)) {
            $data['sameAs'] = $sameAs;
        }

        // The localities people search from when they look for a room here.
        if ($areas = self::serviceAreas($s)) {
            $data['areaServed'] = array_map(
                fn ($area) => ['@type' => 'Place', 'name' => $area],
                $areas
            );
        }

        $data['currenciesAccepted'] = 'INR';

        if (! empty($s['payment_accepted'])) {
            $data['paymentAccepted'] = $s['payment_accepted'];
        }

        // Only claimed when the admin has actually ticked the box. A wrong
        // "Open 24 hours" in search results is worse than none at all.
        if (! empty($s['open_24_hours'])) {
            $data['openingHoursSpecification'] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => [
                    'Monday', 'Tuesday', 'Wednesday', 'Thursday',
                    'Friday', 'Saturday', 'Sunday',
                ],
                'opens' => '00:00',
                'closes' => '23:59',
            ];
        }

        return $data;
    }

    /** A single room, for the room detail page. */
    public static function room(Room $room, array $s): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'HotelRoom',
            'name' => $room->name,
            'url' => route('rooms.show', $room->slug),
            'bed' => $room->bed_type ?: null,
            'occupancy' => [
                '@type' => 'QuantitativeValue',
                'maxValue' => $room->capacity,
                'unitCode' => 'C62',
            ],
            'containedInPlace' => [
                '@type' => 'Hotel',
                '@id' => url('/').'#hotel',
                'name' => $s['site_name'] ?? 'SKL GRAND ROOMS',
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => (string) round((float) $room->price, 2),
                'priceCurrency' => 'INR',
                'availability' => 'https://schema.org/InStock',
                'url' => route('booking', ['room' => $room->slug]),
            ],
        ];

        if ($room->short_description) {
            $data['description'] = $room->short_description;
        }

        if ($room->image) {
            $data['image'] = $room->imageUrl();
        }

        $features = [];

        foreach ($room->amenityList() as $amenity) {
            $features[] = ['@type' => 'LocationFeatureSpecification', 'name' => $amenity, 'value' => true];
        }

        if ($features) {
            $data['amenityFeature'] = $features;
        }

        return array_filter($data, fn ($v) => $v !== null);
    }

    /**
     * Breadcrumb trail.
     *
     * @param  array<string, string>  $trail  ['Label' => 'https://...']
     */
    public static function breadcrumbs(array $trail): array
    {
        $items = [];
        $position = 1;

        foreach ($trail as $label => $url) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $label,
                'item' => $url,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    /** Cache keys that depend on room rates. Cleared whenever a room changes. */
    public const ROOM_CACHE_KEYS = ['seo.room_prices', 'sitemap.xml'];

    /** Forget everything worked out from the rooms table. */
    public static function forgetRoomCache(): void
    {
        foreach (self::ROOM_CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Lowest and highest nightly rate of the rooms shown on the website.
     * Hidden rooms are left out, so the range always matches the rooms page.
     *
     * @return array{min: float, max: float}|null
     */
    public static function roomPrices(): ?array
    {
        try {
            $prices = Cache::remember('seo.room_prices', now()->addDay(), function () {
                $row = Room::where('is_active', true)
                    ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                    ->first();

                // Stored as an array, never null, so "no rooms" is cached too.
                return $row && $row->min_price !== null
                    ? ['min' => (float) $row->min_price, 'max' => (float) $row->max_price]
                    : [];
            });
        } catch (\Throwable $e) {
            return null;
        }

        return $prices ?: null;
    }

    /** The cheapest nightly rate, for "Rooms from ₹…" text. */
    public static function fromPrice(): ?float
    {
        return self::roomPrices()['min'] ?? null;
    }

    /** Lowest to highest nightly rate, shown to Google as a price band. */
    public static function priceRange(): ?string
    {
        $prices = self::roomPrices();

        if (! $prices || ! $prices['min']) {
            return null;
        }

        return $prices['min'] == $prices['max']
            ? '₹'.number_format($prices['min'], 0)
            : '₹'.number_format($prices['min'], 0).' - ₹'.number_format($prices['max'], 0);
    }

    /**
     * The five facilities the hotel really has, for the Hotel schema.
     *
     * Fixed here rather than read from Admin > Services on purpose: a
     * facility claimed to Google that the hotel does not have is worse than
     * one left out.
     */
    public const AMENITIES = ['Air conditioning', 'Hot water', 'Free Wi-Fi', 'TV', 'Free parking'];

    private static function amenities(): array
    {
        return array_map(fn ($name) => [
            '@type' => 'LocationFeatureSpecification',
            'name' => $name,
            'value' => true,
        ], self::AMENITIES);
    }

    /** "2:00 PM" becomes "14:00". Null when it cannot be read. */
    private static function isoTime(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Picture used when the site is shared on WhatsApp, Facebook etc.
     * Uses the first room photo that has actually been uploaded.
     */
    public static function shareImage(): ?string
    {
        try {
            $room = Room::where('is_active', true)
                ->whereNotNull('image')
                ->orderByDesc('is_featured')
                ->first();
        } catch (\Throwable $e) {
            return null;
        }

        return $room && $room->image ? $room->imageUrl() : null;
    }

    /**
     * A plain link to the hotel on Google Maps.
     *
     * The Business Profile link is used when the admin has pasted one,
     * because that points at the real listing. Otherwise the link is built
     * from the map pin coordinates.
     */
    public static function mapUrl(array $s): ?string
    {
        $listing = trim((string) ($s['google_business_url'] ?? ''));

        if ($listing !== '' && filter_var($listing, FILTER_VALIDATE_URL)) {
            return $listing;
        }

        if ($point = MapEmbed::coordinates($s)) {
            return 'https://www.google.com/maps?q='.$point[0].','.$point[1];
        }

        return null;
    }

    /**
     * Other places on the web that are unmistakably this same hotel.
     *
     * @return array<int, string>
     */
    public static function sameAs(array $s): array
    {
        $urls = [];

        foreach (['google_business_url'] as $key) {
            $url = trim((string) ($s[$key] ?? ''));

            if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    /**
     * Nearby areas the hotel wants to be found from, one per line in
     * Site Settings.
     *
     * @return array<int, string>
     */
    public static function serviceAreas(array $s): array
    {
        return self::lines($s['service_areas'] ?? '');
    }

    /**
     * Landmarks near the hotel, written one per line as
     * "Bangalore University | 2 km", or just "Bangalore University".
     *
     * @return array<int, array{name: string, distance: string|null}>
     */
    public static function landmarks(array $s): array
    {
        $out = [];

        foreach (self::lines($s['nearby_landmarks'] ?? '') as $line) {
            $parts = array_map('trim', explode('|', $line, 2));

            $out[] = [
                'name' => $parts[0],
                'distance' => ($parts[1] ?? '') !== '' ? $parts[1] : null,
            ];
        }

        return $out;
    }

    /**
     * The questions someone searching nearby actually types, answered from
     * the settings the admin has filled in.
     *
     * Only questions we genuinely have an answer for are returned, so the
     * page never shows a blank or invented answer.
     *
     * @return array<int, array{question: string, answer: string}>
     */
    public static function faqs(array $s): array
    {
        $name = $s['site_name'] ?? 'SKL GRAND ROOMS';
        $faqs = [];

        if ($address = trim((string) ($s['address'] ?? ''))) {
            $faqs[] = [
                'question' => 'Where is '.$name.' located?',
                'answer' => $name.' is at '.$address.'.',
            ];
        }

        foreach (self::landmarks($s) as $landmark) {
            if (! $landmark['distance']) {
                continue;
            }

            $faqs[] = [
                'question' => 'How far is '.$name.' from '.$landmark['name'].'?',
                'answer' => $name.' is about '.$landmark['distance'].' from '.$landmark['name'].'.',
            ];
        }

        // "rooms under 2000", "cheap rooms in RR Nagar" and the rest are all
        // really the same question, answered from the live room rates.
        if ($range = self::priceRange()) {
            $faqs[] = [
                'question' => 'How much does a room cost at '.$name.'?',
                'answer' => 'Rooms are '.$range.' per night, depending on the room type and how many guests. '
                    .'The rate for each room is shown on the rooms page, and nothing is charged until you arrive.',
            ];
        }

        // Answered from the facilities the admin has switched on, so it can
        // never claim something the hotel does not have.
        $facilities = self::amenityNames();

        if ($facilities) {
            $faqs[] = [
                'question' => 'What facilities does '.$name.' have?',
                'answer' => 'Every stay includes '.self::sentenceList($facilities).'.',
            ];
        }

        $in = trim((string) ($s['checkin_time'] ?? ''));
        $out = trim((string) ($s['checkout_time'] ?? ''));

        if ($in !== '' && $out !== '') {
            $faqs[] = [
                'question' => 'What are the check-in and check-out times at '.$name.'?',
                'answer' => 'Check-in is from '.$in.' and check-out is by '.$out.'. '
                    .'Every adult guest needs to bring a valid government photo ID.',
            ];
        }

        if ($phone = trim((string) ($s['phone'] ?? ''))) {
            $faqs[] = [
                'question' => 'How do I book a room at '.$name.'?',
                'answer' => 'Book online at '.route('booking').', or call '.$phone.'. '
                    .'No payment is needed to hold a room.',
            ];
        }

        return $faqs;
    }

    /**
     * FAQPage structured data. Pass the same list that is printed on the
     * page: Google requires the answers to be visible to the visitor.
     *
     * @param  array<int, array{question: string, answer: string}>  $faqs
     */
    public static function faqPage(array $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ], $faqs),
        ];
    }

    /**
     * The old-style geo meta tags. Google ignores them, but Bing and several
     * local directories that scrape the site still read them.
     *
     * @return array<string, string>
     */
    public static function geoMeta(array $s): array
    {
        $meta = [];

        $locality = trim((string) ($s['address_locality'] ?? ''));
        $region = trim((string) ($s['address_region'] ?? ''));

        if ($region !== '') {
            $meta['geo.region'] = 'IN-'.strtoupper(substr($region, 0, 2));
        }

        if ($locality !== '') {
            $meta['geo.placename'] = $locality;
        }

        if ($point = MapEmbed::coordinates($s)) {
            $meta['geo.position'] = $point[0].';'.$point[1];
            $meta['ICBM'] = $point[0].', '.$point[1];
        }

        return $meta;
    }

    /**
     * The facilities the admin has switched on, as plain names.
     *
     * @return array<int, string>
     */
    public static function amenityNames(): array
    {
        try {
            return Service::where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('title')
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** ['a', 'b', 'c'] becomes "a, b and c", so answers read like English. */
    private static function sentenceList(array $items): string
    {
        $items = array_values(array_filter(array_map('trim', $items)));

        if (count($items) <= 1) {
            return (string) ($items[0] ?? '');
        }

        $last = array_pop($items);

        return implode(', ', $items).' and '.$last;
    }

    /**
     * Split a textarea into clean, non-empty lines.
     *
     * @return array<int, string>
     */
    private static function lines($value): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $value) ?: [];

        return array_values(array_filter(array_map('trim', $lines), fn ($l) => $l !== ''));
    }
}
