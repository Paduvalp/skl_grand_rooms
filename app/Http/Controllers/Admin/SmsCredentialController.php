<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SmsGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

/**
 * The gateway details, editable from the admin panel instead of the .env
 * file. Saved in the settings table; the password is encrypted, and the
 * access code is stored only as a one way hash.
 */
class SmsCredentialController extends Controller
{
    /** Where the access code hash lives when set from this screen. */
    public const CODE_KEY = 'sms_access_code_hash';

    public function edit(SmsGateway $sms)
    {
        return view('admin.sms.credentials', [
            'mode' => $sms->mode(),
            'url' => $sms->url(),
            // Falls back to the .env username, so the box is not empty on
            // a site that has never used this screen.
            'username' => Setting::get(SmsGateway::KEYS['username'], '')
                ?: (string) config('services.sms_gateway.username'),
            'dailyLimit' => $sms->limit(),
            'hasPassword' => $sms->isConfigured(),
            'fromDatabase' => $sms->savedInDatabase(),
            'codeFromDatabase' => trim((string) Setting::get(self::CODE_KEY, '')) !== '',
        ]);
    }

    public function update(Request $request, SmsGateway $sms)
    {
        $data = $request->validate([
            'mode' => ['required', Rule::in(['cloud', 'local'])],
            'url' => ['nullable', 'string', 'max:190'],
            'username' => ['required', 'string', 'max:120'],
            'password' => ['nullable', 'string', 'max:190'],
            'daily_limit' => ['required', 'integer', 'min:1', 'max:1000'],
        ], [], [
            'url' => 'phone address',
            'daily_limit' => 'daily limit',
        ]);

        if ($data['mode'] === 'local' && trim((string) $data['url']) === '') {
            return back()->withInput()->with('error', 'Local mode needs the phone address, for example http://192.168.0.107:8080');
        }

        Setting::put(SmsGateway::KEYS['mode'], $data['mode']);
        Setting::put(SmsGateway::KEYS['url'], trim((string) $data['url']));
        Setting::put(SmsGateway::KEYS['username'], trim($data['username']));
        Setting::put(SmsGateway::KEYS['daily_limit'], (string) $data['daily_limit']);

        // An empty password box means "leave the saved one alone", so the
        // password never has to be retyped to change the daily limit.
        if (filled($data['password'])) {
            Setting::put(SmsGateway::KEYS['password'], Crypt::encryptString($data['password']));
        }

        return redirect()->route('admin.sms.credentials')->with('success', 'Gateway details saved.');
    }

    /** Forget the saved details, so the .env file is used again. */
    public function destroy()
    {
        Setting::whereIn('key', array_values(SmsGateway::KEYS))->delete();

        return redirect()->route('admin.sms.credentials')
            ->with('success', 'Saved details deleted. The site is back on the .env settings.');
    }

    /** Change the code that unlocks this section. */
    public function updateCode(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'min:4', 'max:64', 'confirmed'],
        ], [
            'code.confirmed' => 'The two codes do not match.',
            'code.min' => 'The code must be at least 4 characters.',
        ], ['code' => 'access code']);

        Setting::put(self::CODE_KEY, password_hash($data['code'], PASSWORD_BCRYPT));

        return back()->with('success', 'Access code changed. Use the new code next time.');
    }

    public function destroyCode()
    {
        Setting::where('key', self::CODE_KEY)->delete();

        return back()->with('success', 'Access code reset to the one in the .env file.');
    }

    /**
     * Ask the gateway who we are, without sending a message.
     * Proves the address, username and password before a real send.
     */
    public function test(SmsGateway $sms)
    {
        if (! $sms->isConfigured()) {
            return back()->with('error', 'Fill in the details and save them first.');
        }

        $url = $sms->mode() === 'cloud'
            ? SmsGateway::CLOUD_URL.'/messages'
            : rtrim($sms->url(), '/').'/health';

        try {
            $response = Http::withBasicAuth($sms->credentialsForTest()[0], $sms->credentialsForTest()[1])
                ->timeout(15)
                ->acceptJson()
                ->get($url);
        } catch (\Throwable $e) {
            return back()->with('error', $sms->mode() === 'local'
                ? 'The phone did not answer. Check the address, that it is on the same Wi-Fi, and that the app is running.'
                : 'Could not reach the SMS service. Check the internet connection.');
        }

        if ($response->status() === 401) {
            return back()->with('error', 'Connected, but the username or password was refused.');
        }

        // A local phone may answer 404 to /health on older builds. Reaching
        // it at all, without being refused, is the thing worth knowing.
        return back()->with('success', 'Connection works. The gateway answered and accepted the login.');
    }
}
