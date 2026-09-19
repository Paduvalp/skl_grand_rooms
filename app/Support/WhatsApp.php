<?php

namespace App\Support;

/**
 * Links for the floating "Chat on WhatsApp" button.
 *
 * The number comes from Site Settings. Until a separate WhatsApp number is
 * entered there, the hotel phone number is used.
 */
class WhatsApp
{
    public const DEFAULT_MESSAGE = "Hi, I'd like to check room availability at SKL Grand Rooms.";

    /** Digits only, with country code, e.g. 919876543210. Null if none set. */
    public static function number(array $s): ?string
    {
        $number = preg_replace('/\D/', '', (string) ($s['whatsapp_number'] ?? ''));

        if ($number === '') {
            $number = preg_replace('/\D/', '', (string) ($s['phone'] ?? ''));
        }

        // A plain 10 digit Indian mobile needs the 91 in front for wa.me.
        if (strlen($number) === 10) {
            $number = '91'.$number;
        }

        return strlen($number) >= 11 ? $number : null;
    }

    public static function link(array $s, ?string $roomName = null): ?string
    {
        $number = self::number($s);

        if (! $number) {
            return null;
        }

        $message = $roomName
            ? "Hi, I'd like to check availability for the {$roomName} at SKL Grand Rooms."
            : self::DEFAULT_MESSAGE;

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}
