<?php

namespace App\Support;

/**
 * Turns whatever the admin pasted into the "Google map" box into a safe,
 * working embedded map.
 *
 * It accepts any of these:
 *   - a full <iframe ...> embed code copied from Google Maps
 *   - a plain Google Maps link, the kind you get from the Share button
 *   - a link containing coordinates, like .../@12.9347,77.5127,17z/...
 *   - nothing at all, in which case it falls back to the map pin
 *     coordinates, and then to the hotel address
 *
 * Only the map URL is ever kept. Any other HTML, script or attribute that
 * came along with a pasted iframe is thrown away, so a bad paste can never
 * put script on the page.
 */
class MapEmbed
{
    /** Ready-to-print iframe HTML, or null when there is nothing to show. */
    public static function iframe(array $settings, string $classes = ''): ?string
    {
        $src = self::src($settings);

        if (! $src) {
            return null;
        }

        return '<iframe src="'.e($src).'"'
            .($classes ? ' class="'.e($classes).'"' : '')
            .' width="100%" height="100%" style="border:0"'
            .' allowfullscreen loading="lazy"'
            .' referrerpolicy="no-referrer-when-downgrade"'
            .' title="Map showing where the hotel is"></iframe>';
    }

    /** The https://maps.google.com/... URL the iframe should load. */
    public static function src(array $settings): ?string
    {
        $raw = trim((string) ($settings['map_embed'] ?? ''));

        // 1. A pasted iframe. Keep only its src, discard everything else.
        if ($raw !== '' && stripos($raw, '<iframe') !== false) {
            if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $raw, $m)) {
                $url = html_entity_decode($m[1], ENT_QUOTES);

                if (self::isGoogleMapsUrl($url)) {
                    return $url;
                }
            }
        }

        // 2. A pasted Google Maps link.
        if ($raw !== '' && self::isGoogleMapsUrl($raw)) {
            // Already an embed URL - use it as it is.
            if (str_contains($raw, '/maps/embed') || str_contains($raw, 'output=embed')) {
                return $raw;
            }

            // A normal share link. Pull the coordinates out of it if they
            // are there, because a pin is more accurate than a text search.
            if ($point = self::coordinatesFromUrl($raw)) {
                return self::byPoint($point[0], $point[1]);
            }

            // No coordinates, but the place name is usually in the path.
            if (preg_match('#/maps/place/([^/@?]+)#', $raw, $m)) {
                return self::byQuery(urldecode(str_replace('+', ' ', $m[1])));
            }
        }

        // 3. The map pin coordinates from Site Settings.
        if (! empty($settings['geo_lat']) && ! empty($settings['geo_lng'])) {
            return self::byPoint((float) $settings['geo_lat'], (float) $settings['geo_lng']);
        }

        // 4. Last resort - search Google for the hotel name and address.
        //    We need the address for this. The hotel name on its own could
        //    point at a place anywhere in the world, so we show no map
        //    rather than the wrong one.
        $address = trim((string) ($settings['address'] ?? ''));

        if ($address === '') {
            return null;
        }

        return self::byQuery(trim(($settings['site_name'] ?? '').' '.$address));
    }

    /**
     * Coordinates for this hotel, wherever we can find them.
     * Used by the search-engine data as well as the map.
     *
     * @return array{0: float, 1: float}|null  [latitude, longitude]
     */
    public static function coordinates(array $settings): ?array
    {
        if (! empty($settings['geo_lat']) && ! empty($settings['geo_lng'])) {
            return [(float) $settings['geo_lat'], (float) $settings['geo_lng']];
        }

        return self::coordinatesFromUrl((string) ($settings['map_embed'] ?? ''));
    }

    /** Pull "@12.9347,77.5127" or "!3d12.9347!4d77.5127" out of a Maps link. */
    private static function coordinatesFromUrl(string $url): ?array
    {
        if ($url === '') {
            return null;
        }

        if (preg_match('/@(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/', $url, $m)) {
            return self::validPoint((float) $m[1], (float) $m[2]);
        }

        if (preg_match('/!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)/', $url, $m)) {
            return self::validPoint((float) $m[1], (float) $m[2]);
        }

        if (preg_match('/[?&]q=(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)/', $url, $m)) {
            return self::validPoint((float) $m[1], (float) $m[2]);
        }

        return null;
    }

    private static function validPoint(float $lat, float $lng): ?array
    {
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }

        return [$lat, $lng];
    }

    private static function byPoint(float $lat, float $lng): string
    {
        return 'https://maps.google.com/maps?q='.$lat.','.$lng.'&z=16&hl=en&output=embed';
    }

    private static function byQuery(string $query): string
    {
        return 'https://maps.google.com/maps?q='.rawurlencode($query).'&z=16&hl=en&output=embed';
    }

    /** Guards against a link to somewhere that is not Google Maps. */
    private static function isGoogleMapsUrl(string $url): bool
    {
        $url = trim($url);

        // A real URL has no spaces, quotes or angle brackets in it. If any
        // turned up, someone tried to smuggle extra HTML in through the box.
        if (preg_match('/[\s"\'<>]/', $url)) {
            return false;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host === '') {
            return false;
        }

        $allowed = ['google.com', 'maps.google.com', 'www.google.com', 'maps.app.goo.gl', 'goo.gl'];

        foreach ($allowed as $ok) {
            if ($host === $ok || str_ends_with($host, '.'.$ok)) {
                return true;
            }
        }

        // Country domains, for example google.co.in
        return (bool) preg_match('/^(maps\.|www\.)?google\.[a-z.]{2,6}$/', $host);
    }
}
