<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * The five facilities SKL Grand Rooms actually offers.
     *
     * Nothing here is guessed. If you add a facility later, add it from
     * Admin > Services rather than editing this file.
     *
     * The icon value is any Bootstrap Icons class name.
     * Full list: https://icons.getbootstrap.com
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Air Conditioning',
                'icon' => 'bi-snow',
                'sort_order' => 1,
                'description' => 'Every room is air conditioned, so you stay comfortable through the Bengaluru afternoons.',
            ],
            [
                'title' => 'Hot Water Geyser',
                'icon' => 'bi-droplet-half',
                'sort_order' => 2,
                'description' => 'A geyser in every bathroom, so hot water is ready whenever you need it.',
            ],
            [
                'title' => 'Free Wi-Fi',
                'icon' => 'bi-wifi',
                'sort_order' => 3,
                'description' => 'Wi-Fi in every room, free for all guests. No vouchers and no hourly limits.',
            ],
            [
                'title' => 'TV',
                'icon' => 'bi-tv',
                'sort_order' => 4,
                'description' => 'A television in every room with cable channels, for when you want to wind down.',
            ],
            [
                'title' => 'Parking',
                'icon' => 'bi-p-square',
                'sort_order' => 5,
                'description' => 'On-site parking for guests. Tell us at check-in if you are arriving by car.',
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['title' => $service['title']],
                $service + ['is_active' => true]
            );
        }

        // These were sample facilities from the first build that the hotel does
        // not actually offer. Remove them so nothing false shows on the site.
        $oldSamples = [
            'Restaurant & Bar',
            'Swimming Pool',
            'Airport Pickup',
            'Conference Hall',
            '24x7 Room Service',
            'Fitness Centre',
            'Free Parking',
        ];

        $removed = Service::whereIn('title', $oldSamples)->delete();

        if ($removed) {
            $this->command->warn("Removed {$removed} sample facility/facilities the hotel does not offer.");
        }

        $this->command->info('Services seeded ('.count($services).' real facilities).');
    }
}
