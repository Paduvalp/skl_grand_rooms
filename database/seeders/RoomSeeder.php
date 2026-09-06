<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * PLACEHOLDER ROOMS - replace these with your real ones.
     *
     * The names, rates and room counts below are a reasonable guess for a
     * budget hotel in RR Nagar. They are NOT your actual rooms. Edit them
     * from Admin > Rooms, or change this file and re-run:
     *
     *     php artisan db:seed --class=RoomSeeder
     *
     * Every room lists only the five facilities the hotel really has:
     * air conditioning, hot water geyser, free Wi-Fi, TV and parking.
     * Nothing else is claimed anywhere on the site.
     *
     * Photos are uploaded from Admin > Rooms. Until then the site shows a
     * neutral placeholder image instead of a broken picture.
     */
    public function run(): void
    {
        $amenities = 'Air Conditioning, Hot Water Geyser, Free Wi-Fi, TV, Parking';

        $rooms = [
            [
                'name' => 'Standard Single Room',
                'slug' => 'standard-single-room',
                'type' => 'Standard',
                'short_description' => 'A neat, simple room for one guest.',
                'description' => "A clean and quiet room for one person, with a single bed and an attached bathroom.\n\nAir conditioning, a hot water geyser, free Wi-Fi and a TV are included, and there is parking on site if you are driving.",
                'price' => 899,
                'capacity' => 1,
                'bed_type' => '1 Single Bed',
                'size' => '120 sq ft',
                'amenities' => $amenities,
                'total_rooms' => 10,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Standard Double Room',
                'slug' => 'standard-double-room',
                'type' => 'Standard',
                'short_description' => 'A comfortable double room for two guests.',
                'description' => "A straightforward double room with one double bed and an attached bathroom. Good for couples and for colleagues travelling together.\n\nAir conditioning, a hot water geyser, free Wi-Fi and a TV are included, and there is parking on site.",
                'price' => 1199,
                'capacity' => 2,
                'bed_type' => '1 Double Bed',
                'size' => '160 sq ft',
                'amenities' => $amenities,
                'total_rooms' => 12,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Deluxe Double Room',
                'slug' => 'deluxe-double-room',
                'type' => 'Deluxe',
                'short_description' => 'A larger double room with a queen bed.',
                'description' => "Our larger double room, with a queen size bed and more floor space than the standard double. It has an attached bathroom.\n\nAir conditioning, a hot water geyser, free Wi-Fi and a TV are included, and there is parking on site.",
                'price' => 1499,
                'capacity' => 2,
                'bed_type' => '1 Queen Bed',
                'size' => '210 sq ft',
                'amenities' => $amenities,
                'total_rooms' => 6,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Family Room',
                'slug' => 'family-room',
                'type' => 'Family',
                'short_description' => 'Two double beds, room for a family of four.',
                'description' => "Our largest room, with two double beds and space for four guests. It has an attached bathroom and a large wardrobe.\n\nAir conditioning, a hot water geyser, free Wi-Fi and a TV are included, and there is parking on site.",
                'price' => 1999,
                'capacity' => 4,
                'bed_type' => '2 Double Beds',
                'size' => '300 sq ft',
                'amenities' => $amenities,
                'total_rooms' => 3,
                'is_featured' => true,
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['slug' => $room['slug']], $room);
        }

        // Sample room types from the first build that do not fit this hotel.
        $oldSamples = ['executive-suite', 'presidential-suite', 'budget-twin-room'];
        $removed = Room::whereIn('slug', $oldSamples)->delete();

        if ($removed) {
            $this->command->warn("Removed {$removed} sample room type(s) that do not fit this hotel.");
        }

        $this->command->info('Rooms seeded ('.count($rooms).' room types).');
        $this->command->line('   These are PLACEHOLDER rooms. Replace them with your real ones.');
    }
}
