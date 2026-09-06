<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * /sitemap.xml - every public page, built fresh each time.
     *
     * Admin pages, the booking confirmation and the booking status lookup are
     * left out on purpose. They are private or have nothing to rank for.
     */
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => route('rooms.index'), 'priority' => '0.9', 'freq' => 'weekly'],
            ['loc' => route('booking'), 'priority' => '0.8', 'freq' => 'monthly'],
            ['loc' => route('about'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('services'), 'priority' => '0.6', 'freq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.6', 'freq' => 'monthly'],
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

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * /robots.txt - served by Laravel so the sitemap line always points at
     * whatever APP_URL is set to, with no editing when the domain changes.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /booking/success',
            'Disallow: /booking/status',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
