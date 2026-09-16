<?php

namespace App\Http\Middleware;

use App\Support\Attribution;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notes how each visitor reached the site, on every public page.
 *
 * It only ever writes a cookie. It never blocks or changes the response, so
 * if anything here goes wrong the page still loads normally.
 */
class CaptureAttribution
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Nothing to remember about a form submission, a redirect chase or
        // the admin panel.
        if (! $request->isMethod('GET') || $request->is('admin', 'admin/*')) {
            return $response;
        }

        // Search engines and uptime checks are not customers.
        if ($request->is('sitemap.xml', 'robots.txt', 'up')) {
            return $response;
        }

        try {
            $existing = Attribution::remembered($request);
            $fresh = Attribution::fromRequest($request, $existing);

            if ($fresh !== null && method_exists($response, 'withCookie')) {
                $response->withCookie(Attribution::cookie($fresh));
            }
        } catch (\Throwable $e) {
            // Marketing data is never worth breaking a page for.
            report($e);
        }

        return $response;
    }
}
