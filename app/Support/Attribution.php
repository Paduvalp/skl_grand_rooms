<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;

/**
 * Remembers how a visitor reached the site, so the booking they make can be
 * credited to the campaign that brought them.
 *
 * How it decides:
 *
 *   - A link carrying utm_ tags always wins, and replaces whatever was
 *     remembered before. If somebody sees your Instagram post, comes back a
 *     week later through a Google ad and then books, the booking belongs to
 *     the Google ad. That is the click that did the work.
 *   - A visitor with no tags keeps whatever was remembered from an earlier
 *     visit, so a guest who clicks your ad today and books on Friday is still
 *     credited to the ad.
 *   - With nothing remembered at all, we fall back to the site that referred
 *     them, and failing that the booking is simply "Direct".
 *
 * It is kept in one first-party cookie that this site sets and reads itself.
 * Nothing is sent anywhere, and no other website can read it.
 */
class Attribution
{
    /** The cookie name. Deliberately boring, so it is obvious what it is. */
    public const COOKIE = 'skl_source';

    /** How long a click keeps the credit for, in minutes. 30 days. */
    public const LIFETIME = 60 * 24 * 30;

    /** The link tags we read. These are the standard ones every ad tool uses. */
    public const TAGS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    /**
     * Work out what to remember for this visit.
     *
     * Returns the data to store, or null when there is nothing new to record
     * and whatever is already remembered should be left alone.
     */
    public static function fromRequest(Request $request, ?array $existing): ?array
    {
        $tags = self::tagsIn($request);

        // A tagged link. This one takes over.
        if ($tags) {
            return array_merge($tags, [
                'referrer_host' => self::referrerHost($request),
                'landing_page' => self::path($request),
                'first_seen_at' => now()->toIso8601String(),
            ]);
        }

        // Already remembered from an earlier click. Leave it be.
        if ($existing) {
            return null;
        }

        // No tags and nothing remembered: record where they came from, which
        // is the best we can do for an untagged visit.
        return [
            'utm_source' => null,
            'utm_medium' => null,
            'utm_campaign' => null,
            'utm_term' => null,
            'utm_content' => null,
            'referrer_host' => self::referrerHost($request),
            'landing_page' => self::path($request),
            'first_seen_at' => now()->toIso8601String(),
        ];
    }

    /**
     * The utm_ values present on this request, cleaned up.
     *
     * @return array<string, string|null>  Empty when the link carried no tags.
     */
    private static function tagsIn(Request $request): array
    {
        $found = [];
        $any = false;

        foreach (self::TAGS as $tag) {
            $value = self::clean($request->query($tag), $tag === 'utm_source' || $tag === 'utm_medium' ? 100 : 150);

            $found[$tag] = $value;

            if ($value !== null) {
                $any = true;
            }
        }

        return $any ? $found : [];
    }

    /**
     * Trim a value, drop it if empty, and cut it to the column width.
     *
     * Anything that is not a plain string is ignored, which covers
     * ?utm_source[]=x and other attempts to push something odd into the
     * database through the address bar.
     */
    private static function clean($value, int $limit): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        // Strip control characters, then collapse runs of whitespace.
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? '';
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        if ($value === '') {
            return null;
        }

        return mb_substr($value, 0, $limit);
    }

    /** The domain that sent the visitor, ignoring links from our own pages. */
    private static function referrerHost(Request $request): ?string
    {
        $referrer = (string) $request->headers->get('referer');

        if ($referrer === '') {
            return null;
        }

        $host = strtolower((string) parse_url($referrer, PHP_URL_HOST));

        if ($host === '' || $host === strtolower((string) $request->getHost())) {
            return null;
        }

        return mb_substr(preg_replace('/^www\./', '', $host), 0, 191);
    }

    /** The page they landed on, path only, never the query string. */
    private static function path(Request $request): ?string
    {
        $path = '/'.ltrim($request->path(), '/');

        return mb_substr($path, 0, 255);
    }

    /**
     * Read what is currently remembered.
     *
     * @return array<string, mixed>|null
     */
    public static function remembered(Request $request): ?array
    {
        $raw = $request->cookie(self::COOKIE);

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return null;
        }

        return $data;
    }

    /**
     * The columns to save against a new booking.
     *
     * @return array<string, mixed>
     */
    public static function forBooking(Request $request): array
    {
        $data = self::remembered($request) ?? [];

        $out = [];

        foreach (self::TAGS as $tag) {
            $value = $data[$tag] ?? null;
            $out[$tag] = is_string($value) && $value !== '' ? $value : null;
        }

        $out['referrer_host'] = is_string($data['referrer_host'] ?? null) ? $data['referrer_host'] : null;
        $out['landing_page'] = is_string($data['landing_page'] ?? null) ? $data['landing_page'] : null;

        $out['first_seen_at'] = null;

        if (! empty($data['first_seen_at'])) {
            try {
                $out['first_seen_at'] = Carbon::parse($data['first_seen_at']);
            } catch (\Throwable $e) {
                $out['first_seen_at'] = null;
            }
        }

        return $out;
    }

    /** A cookie holding the given data, ready to attach to the response. */
    public static function cookie(array $data): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::make(
            self::COOKIE,
            (string) json_encode($data),
            self::LIFETIME,
            null,
            null,
            null,
            true,   // not readable by JavaScript
            false,
            'Lax'   // still sent when arriving from an ad on another site
        );
    }

    /**
     * A short, readable name for where a booking came from.
     *
     * This is what the admin screens show, so it has to make sense to
     * somebody who has never heard the phrase "utm_source".
     */
    public static function label(?string $source, ?string $medium, ?string $referrerHost): string
    {
        if ($source) {
            return self::pretty($source);
        }

        if ($referrerHost) {
            return self::pretty($referrerHost);
        }

        return 'Direct';
    }

    /**
     * Groups sources into the handful of channels a hotel actually thinks in.
     * Returns [label, bootstrap colour class].
     *
     * @return array{0: string, 1: string}
     */
    public static function channel(?string $source, ?string $medium, ?string $referrerHost): array
    {
        $needle = strtolower(trim(($source ?: '').' '.($medium ?: '').' '.($referrerHost ?: '')));

        $paid = ['cpc', 'ppc', 'paid', 'ads', 'adwords', 'display', 'sponsored'];

        foreach ($paid as $word) {
            if (str_contains($needle, $word)) {
                return ['Paid ads', 'bg-danger'];
            }
        }

        foreach (['whatsapp', 'instagram', 'facebook', 'fb', 'youtube', 'twitter', 'x.com', 'linkedin', 'social', 'telegram'] as $word) {
            if (str_contains($needle, $word)) {
                return ['Social', 'bg-info text-dark'];
            }
        }

        foreach (['google', 'bing', 'organic', 'search', 'seo'] as $word) {
            if (str_contains($needle, $word)) {
                return ['Search', 'bg-success'];
            }
        }

        foreach (['email', 'newsletter', 'mail', 'sms'] as $word) {
            if (str_contains($needle, $word)) {
                return ['Email / SMS', 'bg-warning text-dark'];
            }
        }

        if ($source || $referrerHost) {
            return ['Referral', 'bg-secondary'];
        }

        return ['Direct', 'bg-light text-dark border'];
    }

    /** "google_ads" and "GOOGLE ADS" both come out as "Google Ads". */
    private static function pretty(string $value): string
    {
        $value = str_replace(['_', '-'], ' ', $value);

        return ucwords(mb_strtolower(trim($value)));
    }
}
