<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\Service;
use App\Models\Setting;
use App\Support\Seo;
use Illuminate\Console\Command;

/**
 * One-off content corrections for the live database.
 *
 *     php artisan skl:content-fixes --dry-run   (shows what would change)
 *     php artisan skl:content-fixes
 *
 * Safe to run more than once. Each fix only touches a value that still has
 * the problem, so anything already corrected in Admin > Site Settings or
 * Admin > Rooms is left alone. Nothing is deleted and nothing is re-seeded.
 */
class ContentFixes extends Command
{
    protected $signature = 'skl:content-fixes {--dry-run : List the changes without saving them}';

    protected $description = 'Fix "centre of the city" wording, false facility claims, Deluxe room guests, contact email and map pin';

    /** The mailbox guests should write to. */
    private const OFFICIAL_EMAIL = 'info@sklgrandrooms.com';

    /** Wording that places the hotel in the city centre. It is on the outskirts. */
    private const CENTRE_PHRASES = [
        'heart of the city', 'middle of the city', 'city centre', 'city center',
        'center of the city', 'centre of the city',
    ];

    /** Facilities the hotel does not have. Only AC, geyser, Wi-Fi, TV and parking are real. */
    private const FALSE_CLAIMS = [
        'restaurant', 'pool', 'conference', ' bar', 'gym', 'fitness', 'room service',
        'airport pickup', '24 hour front desk', 'central location', 'full service',
    ];

    private bool $dry = false;

    private int $changes = 0;

    /** Settings fixed in this run, so a dry run does not report them twice. */
    private array $fixed = [];

    public function handle(): int
    {
        $this->dry = (bool) $this->option('dry-run');

        if ($this->dry) {
            $this->warn('Dry run - nothing will be saved.');
        }

        $this->fixCentreWording();
        $this->fixAboutText();
        $this->fixDeluxeGuests();
        $this->fixEmail();
        $this->fillIfEmpty('geo_lat', '12.934789');
        $this->fillIfEmpty('geo_lng', '77.5112355');
        $this->fillIfEmpty('nearest_metro', 'Jnanabharathi');
        $this->reportLeftovers();

        if (! $this->dry) {
            Seo::forgetRoomCache();
        }

        $this->newLine();
        $this->info($this->dry
            ? "{$this->changes} change(s) would be made."
            : "{$this->changes} change(s) saved.");

        return self::SUCCESS;
    }

    private function fixCentreWording(): void
    {
        $hero = (string) Setting::get('hero_subtitle', '');

        if ($this->hasAny($hero, self::CENTRE_PHRASES)) {
            // Keep the rest of the sentence, just move the hotel to where it is.
            $fixed = str_ireplace(
                ['in the heart of the city', 'in the middle of the city', 'in the city centre', 'in the city center', 'in the centre of the city', 'in the center of the city'],
                'in RR Nagar, near Mysore Road',
                $hero
            );

            if ($this->hasAny($fixed, self::CENTRE_PHRASES)) {
                $fixed = 'Clean rooms, friendly staff and honest prices in RR Nagar, near Mysore Road.';
            }

            $this->saveSetting('hero_subtitle', $hero, $fixed);
        }

        $footer = (string) Setting::get('footer_text', '');

        if ($this->hasAny($footer, self::CENTRE_PHRASES)) {
            $this->saveSetting('footer_text', $footer, 'SKL Grand Rooms — a quiet, comfortable stay in RR Nagar, Bengaluru.');
        }
    }

    /** The About page text claimed a restaurant, pool and conference hall. */
    private function fixAboutText(): void
    {
        $about = (string) Setting::get('about_text', '');

        if ($this->hasAny($about, self::FALSE_CLAIMS)) {
            $this->saveSetting('about_text', $about,
                "SKL Grand Rooms offers clean, comfortable rooms in RR Nagar - Kenchenhalli, Rajarajeshwari Nagar, Bengaluru. We keep things simple and do the basics well: a clean, air conditioned room, a hot water geyser, free Wi-Fi, a TV, and parking for your vehicle.\n\n"
                ."The hotel is on the quieter south-western edge of the city, a short drive from Bangalore University and Mysore Road, which makes it easy for students, families visiting them, and people here for work.\n\n"
                .'If you need anything during your stay, just ask at the front desk.'
            );
        }

        $points = (string) Setting::get('about_points', '');

        if ($this->hasAny($points, self::FALSE_CLAIMS) || $this->hasAny($points, self::CENTRE_PHRASES)) {
            $this->saveSetting('about_points', $points,
                'Kenchenhalli, RR Nagar location, Close to Bangalore University, Air conditioned rooms, Hot water geyser in every room, Free Wi-Fi, TV in every room, Parking on site'
            );
        }
    }

    /** Deluxe Double Room has one queen bed, so it sleeps two. */
    private function fixDeluxeGuests(): void
    {
        $room = Room::where('slug', 'deluxe-double-room')->first();

        if (! $room) {
            $this->warn('Deluxe Double Room (slug deluxe-double-room) not found - set its guests in Admin > Rooms.');

            return;
        }

        if ($room->capacity <= 2) {
            $this->line("  ok   Deluxe Double Room already takes {$room->capacity} guest(s).");

            return;
        }

        $this->changes++;
        $this->line("  fix  Deluxe Double Room guests: {$room->capacity} -> 2");

        if (! $this->dry) {
            $room->update(['capacity' => 2]);
        }
    }

    /**
     * The website showed a personal Gmail address as the hotel's email.
     * Anything already on the hotel's own domain is left alone.
     */
    private function fixEmail(): void
    {
        $email = trim((string) Setting::get('email', ''));

        if ($email === '' || ! str_ends_with(mb_strtolower($email), '@sklgrandrooms.com')) {
            $this->saveSetting('email', $email, self::OFFICIAL_EMAIL);
        }
    }

    private function fillIfEmpty(string $key, string $value): void
    {
        $current = trim((string) Setting::get($key, ''));

        if ($current === '') {
            $this->saveSetting($key, $current, $value);
        }
    }

    /** Things this command will not guess at. Listed so they can be fixed by hand. */
    private function reportLeftovers(): void
    {
        foreach (Setting::all() as $setting) {
            if (! in_array($setting->key, $this->fixed, true) && $this->hasAny((string) $setting->value, self::CENTRE_PHRASES)) {
                $this->warn("  check  Setting '{$setting->key}' still mentions the city centre.");
            }
        }

        foreach (Room::all() as $room) {
            if ($this->hasAny($room->description.' '.$room->short_description, self::CENTRE_PHRASES)) {
                $this->warn("  check  Room '{$room->name}' description mentions the city centre.");
            }
        }

        foreach (Service::where('is_active', true)->get() as $service) {
            if ($this->hasAny(' '.$service->title, self::FALSE_CLAIMS)) {
                $this->warn("  check  Active service '{$service->title}' is not a real facility. Hide it in Admin > Services.");
            }
        }
    }

    private function saveSetting(string $key, string $old, string $new): void
    {
        if ($old === $new) {
            return;
        }

        $this->changes++;
        $this->fixed[] = $key;
        $this->line("  fix  {$key}");
        $this->line('       was: '.($old === '' ? '(empty)' : mb_strimwidth(str_replace("\n", ' ', $old), 0, 110, '...')));
        $this->line('       now: '.mb_strimwidth(str_replace("\n", ' ', $new), 0, 110, '...'));

        if (! $this->dry) {
            Setting::put($key, $new);
        }
    }

    private function hasAny(string $text, array $needles): bool
    {
        $text = mb_strtolower($text);

        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }
}
