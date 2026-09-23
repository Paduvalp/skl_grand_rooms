<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A second lock on the SMS pages.
 *
 * Being logged in as an admin is not enough: texting guests spends the
 * hotel's daily allowance and reaches real people, so it asks for a short
 * code as well. Handy when a laptop is left open at the front desk.
 *
 * The code itself is never stored. Only a one way hash of it is kept, and
 * what is typed is hashed the same way and compared.
 */
class RequireSmsCode
{
    /** Where the "unlocked at" time is kept for this browser. */
    public const SESSION_KEY = 'sms_unlocked_at';

    /** Unlocked for this long, then it asks again. */
    public const MINUTES = 120;

    public function handle(Request $request, Closure $next): Response
    {
        $since = $request->session()->get(self::SESSION_KEY);

        if ($since && now()->diffInMinutes($since) <= self::MINUTES) {
            return $next($request);
        }

        $request->session()->forget(self::SESSION_KEY);

        // Remember where they were going, so they land there after unlocking.
        if ($request->isMethod('GET')) {
            $request->session()->put('sms_intended', $request->fullUrl());
        }

        return redirect()->route('admin.sms.unlock');
    }
}
