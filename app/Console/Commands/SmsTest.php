<?php

namespace App\Console\Commands;

use App\Support\SmsGateway;
use Illuminate\Console\Command;

/**
 * Sends one text from the command line, to check the phone gateway works
 * before trusting it with real guests.
 *
 *     php artisan sms:test 9876543210
 *     php artisan sms:test 9876543210 "Your room is confirmed"
 */
class SmsTest extends Command
{
    protected $signature = 'sms:test {number : Mobile number, with or without +91} {message?}';

    protected $description = 'Send one test SMS through the hotel phone gateway';

    public function handle(SmsGateway $sms): int
    {
        $digits = preg_replace('/\D/', '', (string) $this->argument('number')) ?? '';

        if (strlen($digits) < 10) {
            $this->error('That does not look like a mobile number.');

            return self::FAILURE;
        }

        $number = '+91'.substr($digits, -10);
        $message = (string) ($this->argument('message') ?: 'Test message from SKL Grand Rooms.');

        $this->line("Sending to {$number} using the {$sms->mode()} gateway...");

        $result = $sms->send([$number], $message);

        if (! $result['ok']) {
            $this->error($result['error']);
            $this->line('Used today: '.$sms->usedToday().' / '.$sms->limit());

            return self::FAILURE;
        }

        $this->info('Sent.');
        $this->line('Used today: '.$result['used_today'].' / '.$sms->limit());

        return self::SUCCESS;
    }
}
