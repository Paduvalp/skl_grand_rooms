<?php

namespace App\Support;

use App\Models\Room;
use App\Models\Service;


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

        if (! empty($s['checkin_time'])) {
            $data['checkinTime'] = $s['checkin_time'];
        }

        if (! empty($s['checkout_time'])) {
            $data['checkoutTime'] = $s['checkout_time'];
        }

        $amenities = self::amenities();

        if ($amenities) {
            $data['amenityFeature'] = $amenities;
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

    /** Lowest to highest nightly rate, shown to Google as a price band. */
    private static function priceRange(): ?string
    {
        try {
            $min = Room::where('is_active', true)->min('price');
            $max = Room::where('is_active', true)->max('price');
        } catch (\Throwable $e) {
            return null;
        }

        if (! $min) {
            return null;
        }

        return $min == $max
            ? '₹'.number_format((float) $min, 0)
            : '₹'.number_format((float) $min, 0).' - ₹'.number_format((float) $max, 0);
    }

    /** Hotel facilities, taken from the Services the admin has switched on. */
    private static function amenities(): array
    {
        try {
            $services = Service::where('is_active', true)->orderBy('sort_order')->pluck('title');
        } catch (\Throwable $e) {
            return [];
        }

        return $services->map(fn ($title) => [
            '@type' => 'LocationFeatureSpecification',
            'name' => $title,
            'value' => true,
        ])->all();
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
}
