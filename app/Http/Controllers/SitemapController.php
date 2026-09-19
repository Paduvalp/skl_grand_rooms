<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    /**
     * /sitemap.xml - every public page.
     *
     * Admin pages, the booking confirmation and the booking status lookup are
     * left out on purpose. They are private or have nothing to rank for.
     *
     * Cached for a day. Saving or deleting a room clears it straight away
     * (see Room::booted), so a new room never waits a day to appear.
     */
    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addDay(), fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function build(): string
    {
        // The result is cached, so build it from APP_URL rather than from
        // whichever address (http, www) the first visitor happened to use.
        URL::forceRootUrl(rtrim((string) config('app.url'), '/'));

        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => route('rooms.index'), 'priority' => '0.9', 'freq' => 'weekly'],
            ['loc' => route('booking'), 'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('about'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('services'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('location'), 'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('gallery'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('privacy'), 'priority' => '0.3', 'freq' => 'yearly'],
            ['loc' => route('terms'), 'priority' => '0.3', 'freq' => 'yearly'],
        ];

        $rooms = Room::where('is_active', true)->orderBy('name')->get();

        foreach ($rooms as $room) {
            $urls[] = [
                'loc' => route('rooms.show', $room->slug),
                'priority' => '0.8',
                'freq' => 'weekly',
                'lastmod' => optional($room->updated_at)->toAtomString(),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>'."\n";

            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.$url['lastmod'].'</lastmod>'."\n";
            }

            $xml .= '    <changefreq>'.$url['freq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$url['priority'].'</priority>'."\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
