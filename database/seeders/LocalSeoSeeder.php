<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Fills in the local search settings that drive the area, landmark and
 * "near me" pages.
 *
 * Safe to run on a live site. It only writes a setting that is currently
 * empty, so anything already typed into Admin > Site Settings is left alone.
 * Run it with:
 *
 *     php artisan db:seed --class=LocalSeoSeeder
 *
 * The landmark list deliberately carries no distances. Distances have to be
 * measured on Google Maps and typed in, because a wrong one in a search
 * result brings a guest to the door annoyed before they arrive.
 */
class LocalSeoSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // The address split up. Without these Google is given no address
            // at all, which is the single biggest thing holding back local
            // results.
            'street_address' => 'Kenchenhalli, Rajarajeshwari Nagar',
            'address_locality' => 'Bengaluru',
            'address_region' => 'Karnataka',
            'postal_code' => '560026',

            'meta_description' => 'Budget hotel in Kenchenhalli, RR Nagar, Bengaluru. Clean AC rooms with geyser, free Wi-Fi, TV and parking, near Kengeri and Mysore Road. Book online.',

            // Add "| distance" to each line once you have measured it, for
            // example "Kengeri Metro Station | 3 km".
            'nearby_landmarks' => implode("\n", [
                'Global Village Tech Park',
                'RNS Institute of Technology (RNSIT)',
                'Bangalore University, Jnanabharathi',
                'Rajarajeshwari Temple',
                'RajaRajeswari Medical College and Hospital',
                'BGS Global Hospital',
                'Kengeri Bus Stand',
                'Kengeri Metro Station',
                'Mysore Road',
                'NICE Road junction',
            ]),

            'service_areas' => implode("\n", [
                'Kenchenhalli',
                'Rajarajeshwari Nagar',
                'Kengeri',
                'Kengeri Satellite Town',
                'Jnanabharathi',
                'Nayandahalli',
                'Nagarbhavi',
                'Uttarahalli',
                'Banashankari 6th Stage',
                'Mysore Road',
            ]),

            'payment_accepted' => 'Cash, UPI, Credit Card, Debit Card',
        ];

        $written = [];
        $kept = [];

        foreach ($defaults as $key => $value) {
            $current = Setting::where('key', $key)->value('value');

            if (trim((string) $current) !== '') {
                $kept[] = $key;

                continue;
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            $written[] = $key;
        }

        $this->command->info('Filled '.count($written).' empty setting(s): '.(implode(', ', $written) ?: 'none'));

        if ($kept) {
            $this->command->line('Left alone (already set): '.implode(', ', $kept));
        }

        $this->command->warn('Next: measure each landmark on Google Maps and add "| 2 km" to its line in Admin > Site Settings > Local search.');

        if (trim((string) Setting::where('key', 'geo_lat')->value('value')) === '') {
            $this->command->warn('Map pin is still empty. Right click the hotel on Google Maps, copy the two numbers, and paste them into Map latitude / longitude.');
        }
    }
}
