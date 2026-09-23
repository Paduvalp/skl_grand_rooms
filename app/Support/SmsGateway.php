<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends text messages through the SMS Gateway app on the hotel's Android
 * phone (sms-gate.app), so they go out on the SIM's own free allowance.
 *
 * Every method returns an array rather than throwing, so a phone that is
 * switched off can never break an admin page:
 *
 *     ['ok' => true,  'used_today' => 12, 'response' => [...]]
 *     ['ok' => false, 'error' => 'Something a receptionist can understand']
 *
 * The daily count is kept in the cache, not the database, and resets by
 * itself at midnight.
 */
class SmsGateway
{
    /** The address of the cloud service, used when mode is not "local". */
    public const CLOUD_URL = 'https://api.sms-gate.app/3rdparty/v1';

    /**
     * Settings saved from Admin > SMS Marketing > Credentials.
     * Anything not saved there falls back to the .env file.
     */
    public const KEYS = [
        'mode' => 'sms_mode',
        'url' => 'sms_url',
        'username' => 'sms_username',
        'password' => 'sms_password',
        'daily_limit' => 'sms_daily_limit',
    ];

    /** Read once per request, not once per message. */
    private ?array $saved = null;

    /** A plain text message fits 160 characters; longer ones are cut into 153s. */
    public const SINGLE_LIMIT = 160;

    public const PART_LIMIT = 153;

    /**
     * Send one message to one or more numbers.
     *
     * @param  array<int, string>  $numbers  In +91XXXXXXXXXX form.
     */
    public function send(array $numbers, string $message): array
    {
        $numbers = array_values(array_filter($numbers));

        if (! $numbers) {
            return ['ok' => false, 'error' => 'No phone numbers were given.'];
        }

        if (trim($message) === '') {
            return ['ok' => false, 'error' => 'The message is empty.'];
        }

        if ($missing = $this->missingSettings()) {
            return ['ok' => false, 'error' => 'SMS is not set up yet. Still needed: '.implode(', ', $missing).'. Add it under the Credentials tab.'];
        }

        // One text to three numbers costs three. A long text costs more again.
        $parts = $this->parts($message);
        $cost = count($numbers) * $parts;
        $used = $this->usedToday();
        $limit = $this->limit();

        if ($used + $cost > $limit) {
            return [
                'ok' => false,
                'error' => "This would go over today's limit of {$limit} messages. "
                    ."{$used} already sent today, and this one needs {$cost} more. Try again tomorrow.",
            ];
        }

        try {
            $response = $this->post($this->body($numbers, $message));

            // Older builds of the phone app only understand the plain
            // "message" field. Newer ones, and the cloud, want
            // "textMessage". If the first shape is rejected as bad input,
            // try the other one rather than troubling the receptionist.
            if ($response->clientError() && in_array($response->status(), [400, 404, 415, 422], true)) {
                Log::info('SMS gateway rejected the newer format, retrying with the older one', [
                    'status' => $response->status(),
                ]);

                $response = $this->post($this->body($numbers, $message, true));
            }
        } catch (\Throwable $e) {
            Log::error('SMS send failed: gateway not reachable', [
                'mode' => $this->mode(),
                'numbers' => count($numbers),
                'parts' => $parts,
                'reason' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'error' => $this->mode() === 'local'
                    ? 'The phone did not answer. Check it is on the same Wi-Fi, the SMS Gateway app is open, and the local server is switched on.'
                    : 'Could not reach the SMS service. Check the internet connection and try again.',
            ];
        }

        if ($response->failed()) {
            Log::error('SMS send failed', [
                'mode' => $this->mode(),
                'numbers' => count($numbers),
                'parts' => $parts,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'ok' => false,
                'error' => $response->status() === 401
                    ? 'The gateway refused the username or password. Check them under the Credentials tab.'
                    : 'The gateway returned an error ('.$response->status().'): '.trim($response->body()),
            ];
        }

        $used = $this->addToCount($cost);

        Log::info('SMS sent', [
            'mode' => $this->mode(),
            'numbers' => count($numbers),
            'parts' => $parts,
            'used_today' => $used,
        ]);

        return ['ok' => true, 'used_today' => $used, 'response' => $response->json()];
    }

    /**
     * The login the gateway is called with, for the connection test.
     *
     * @return array{0: string, 1: string}
     */
    public function credentialsForTest(): array
    {
        return [$this->username(), $this->password()];
    }

    /** How many messages have gone out today. */
    public function usedToday(): int
    {
        return (int) Cache::get($this->cacheKey(), 0);
    }

    public function limit(): int
    {
        $saved = (int) $this->saved('daily_limit');

        return $saved > 0 ? $saved : max(0, (int) config('services.sms_gateway.daily_limit', 95));
    }

    /** How many messages one text costs per number. */
    public function parts(string $message): int
    {
        $length = mb_strlen($message);

        if ($length === 0) {
            return 0;
        }

        return $length <= self::SINGLE_LIMIT ? 1 : (int) ceil($length / self::PART_LIMIT);
    }

    /** True once the .env values are filled in. */
    public function isConfigured(): bool
    {
        return $this->missingSettings() === [];
    }

    public function mode(): string
    {
        $mode = $this->saved('mode') ?: config('services.sms_gateway.mode');

        return $mode === 'cloud' ? 'cloud' : 'local';
    }

    /** The phone's address, used in local mode only. */
    public function url(): string
    {
        return trim($this->saved('url') ?: (string) config('services.sms_gateway.url'));
    }

    /** True when the details come from the admin screen rather than .env. */
    public function savedInDatabase(): bool
    {
        return $this->saved('username') !== '' || $this->saved('password') !== '';
    }

    /**
     * A value saved from the Credentials screen, or '' when there is none.
     * The password is kept encrypted, so it is decrypted on the way out.
     */
    private function saved(string $name): string
    {
        if ($this->saved === null) {
            $this->saved = [];

            try {
                $rows = Setting::whereIn('key', array_values(self::KEYS))->pluck('value', 'key');
            } catch (\Throwable $e) {
                return '';   // database not ready; .env still works
            }

            foreach (self::KEYS as $short => $key) {
                $this->saved[$short] = (string) ($rows[$key] ?? '');
            }

            if ($this->saved['password'] !== '') {
                try {
                    $this->saved['password'] = Crypt::decryptString($this->saved['password']);
                } catch (\Throwable $e) {
                    // Saved under a different APP_KEY, so it cannot be read.
                    $this->saved['password'] = '';
                }
            }
        }

        return trim($this->saved[$name] ?? '');
    }

    /**
     * The .env keys that still need a value.
     *
     * @return array<int, string>
     */
    private function missingSettings(): array
    {
        $missing = [];

        if ($this->mode() === 'local' && $this->url() === '') {
            $missing[] = 'the phone address';
        }

        if ($this->username() === '') {
            $missing[] = 'the username';
        }

        if ($this->password() === '') {
            $missing[] = 'the password';
        }

        return $missing;
    }

    /** Where the request goes. The two modes use different paths. */
    private function endpoint(): string
    {
        return $this->mode() === 'cloud'
            ? self::CLOUD_URL.'/messages'
            : rtrim($this->url(), '/').'/message';
    }

    private function post(array $body)
    {
        return Http::withBasicAuth($this->username(), $this->password())
            ->timeout(15)
            ->acceptJson()
            ->post($this->endpoint(), $body);
    }

    /**
     * What to send.
     *
     * The cloud, and phone app v1.75 and newer, take "textMessage".
     * Older phone builds only take a plain "message", which is what
     * $legacy asks for.
     */
    private function body(array $numbers, string $message, bool $legacy = false): array
    {
        return $legacy && $this->mode() === 'local'
            ? ['message' => $message, 'phoneNumbers' => $numbers]
            : ['textMessage' => ['text' => $message], 'phoneNumbers' => $numbers];
    }

    private function username(): string
    {
        return $this->saved('username') ?: trim((string) config('services.sms_gateway.username'));
    }

    private function password(): string
    {
        return $this->saved('password') ?: (string) config('services.sms_gateway.password');
    }

    /** Counts are per day and thrown away after midnight. */
    private function cacheKey(): string
    {
        return 'sms_count_'.Carbon::now()->toDateString();
    }

    private function addToCount(int $cost): int
    {
        $used = $this->usedToday() + $cost;

        Cache::put($this->cacheKey(), $used, Carbon::now()->endOfDay());

        return $used;
    }
}
