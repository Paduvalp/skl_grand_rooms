<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RequireSmsCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class SmsAccessController extends Controller
{
    public function show()
    {
        return view('admin.sms.unlock');
    }

    public function unlock(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:64']], [], ['code' => 'access code']);

        // Slows down anyone trying one code after another.
        $key = 'sms-code:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $wait = RateLimiter::availableIn($key);

            return back()->with('error', "Too many wrong codes. Try again in {$wait} seconds.");
        }

        if (! password_verify(trim($request->input('code')), $this->hash())) {
            RateLimiter::hit($key, 300);

            return back()->with('error', 'That code is not right.');
        }

        RateLimiter::clear($key);

        // A fresh session id, so a stolen one cannot be used to walk in.
        $request->session()->regenerate();
        $request->session()->put(RequireSmsCode::SESSION_KEY, now());

        $intended = $request->session()->pull('sms_intended');

        return redirect($intended ?: route('admin.sms.create'))
            ->with('success', 'Unlocked. SMS Marketing stays open for '.(RequireSmsCode::MINUTES / 60).' hours.');
    }

    public function lock(Request $request)
    {
        $request->session()->forget(RequireSmsCode::SESSION_KEY);

        return redirect()->route('admin.dashboard')->with('success', 'SMS Marketing locked.');
    }

    /**
     * The stored hash of the code. Never the code itself.
     * A code set from the Credentials screen wins over the .env one.
     */
    private function hash(): string
    {
        $saved = trim((string) \App\Models\Setting::get(SmsCredentialController::CODE_KEY, ''));

        return $saved !== '' ? $saved : (string) config('services.sms_gateway.access_code_hash');
    }
}
