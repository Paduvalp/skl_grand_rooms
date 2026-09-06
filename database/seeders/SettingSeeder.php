<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Real details taken from the Google Maps listing for SKL GRAND ROOMS.
     *
     * Anything marked CHECK below is a best guess and should be confirmed,
     * then edited from Admin > Site Settings (no code change needed).
     */
    public function run(): void
    {
        $settings = [
            'site_name' => 'SKL GRAND ROOMS',
            'tagline' => 'Clean, comfortable rooms in RR Nagar, Bengaluru',

            'hero_title' => 'A comfortable stay in Rajarajeshwari Nagar',
            'hero_subtitle' => 'Clean rooms, friendly staff and honest prices, close to Bangalore University and the Mysore Road metro. Book your room in under a minute.',

            // From the Google Maps listing
            'phone' => '+91 78991 56936',
            'address' => 'Kenchenhalli, Rajarajeshwari Nagar, Bengaluru, Karnataka 560026',

            // CHECK - the listing has no email or website yet. Change this to
            // the address you actually want booking enquiries to reach.
            'email' => 'reservations@sklgrandrooms.com',

            // Google map of the hotel. Works without an API key.
            'map_embed' => '<iframe src="https://maps.google.com/maps?q=SKL+GRAND+ROOMS+Kenchenhalli+Rajarajeshwari+Nagar+Bengaluru+560026&output=embed" width="100%" height="100%" style="border:0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',

            'about_heading' => 'About SKL Grand Rooms',

            // CHECK - this is a safe starting draft. Replace it with your own
            // words from Admin > Site Settings.
            'about_text' => "SKL Grand Rooms is a budget hotel in Kenchenhalli, Rajarajeshwari Nagar, Bengaluru. We keep things simple and do the basics well: a clean, air conditioned room, a hot water geyser, free Wi-Fi, a TV, and parking for your vehicle.\n\nThe hotel is a short drive from Bangalore University and the Mysore Road side of the city, which makes it easy for students, families visiting them, and people here for work.\n\nIf you need anything during your stay, just ask at the front desk.",

            'about_points' => 'Kenchenhalli, RR Nagar location, Close to Bangalore University, Air conditioned rooms, Hot water geyser in every room, Free Wi-Fi, TV in every room, Parking on site',

            // CHECK - listing sites show 2 PM in, 12 noon out. Confirm this.
            'checkin_time' => '2:00 PM',
            'checkout_time' => '12:00 PM',

            'footer_text' => 'SKL Grand Rooms - a clean and comfortable place to stay in Rajarajeshwari Nagar, Bengaluru.',

            // ---------- SEO ----------

            // Shown under the page title in Google results. Keep it under
            // about 155 characters or Google cuts it off.
            'meta_description' => 'Budget hotel in Kenchenhalli, RR Nagar, Bengaluru. AC rooms with geyser, free Wi-Fi, TV and parking. Book online in a minute.',

            // The address split up, so Google can read it properly.
            'street_address' => 'Kenchenhalli, Rajarajeshwari Nagar',
            'address_locality' => 'Bengaluru',
            'address_region' => 'Karnataka',
            'postal_code' => '560026',

            // CHECK - map pin. On Google Maps, right click the hotel and
            // click the numbers that appear. Paste them here. Leave blank
            // and the map pin is simply left out of the search data.
            'geo_lat' => '',
            'geo_lng' => '',

            // CHECK - paste these in when you set up the accounts.
            // Google Analytics 4 measurement ID, looks like G-XXXXXXXXXX
            'ga_measurement_id' => '',
            // Google Search Console HTML tag verification code
            'search_console_code' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->command->info('Site settings seeded ('.count($settings).' values).');
    }
}
