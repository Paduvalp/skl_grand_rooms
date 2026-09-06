<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Sample bookings so the admin panel is not empty on day one.
     *
     * 'start' is counted in days from the day you run the seeder, so the list
     * always has past stays, a stay happening right now, and upcoming stays.
     * A negative number means the past.
     *
     * The total price is worked out from the room price, so you never have to
     * keep the amounts in sync by hand.
     *
     * Remove every sample row later with:
     *   DELETE FROM bookings WHERE reference LIKE 'SKL-SAMPLE-%';
     */
    public function run(): void
    {
        $samples = [
            [
                'ref' => '001', 'room' => 'deluxe-double-room',
                'name' => 'Ravi Kumar', 'email' => 'ravi.kumar@example.com', 'phone' => '+91 98450 11223',
                'start' => -20, 'nights' => 2, 'guests' => 2, 'rooms' => 1,
                'status' => 'completed',
                'remark' => 'Stay finished. Guest paid at checkout.',
            ],
            [
                'ref' => '002', 'room' => 'family-room',
                'name' => 'Anita Sharma', 'email' => 'anita.sharma@example.com', 'phone' => '+91 99011 44556',
                'start' => -14, 'nights' => 3, 'guests' => 4, 'rooms' => 1,
                'status' => 'completed',
                'notes' => 'Travelling with two children.',
                'remark' => 'Extra bedding provided on request.',
            ],
            [
                'ref' => '003', 'room' => 'standard-single-room',
                'name' => 'Mohammed Faizal', 'email' => 'faizal.m@example.com', 'phone' => '+91 90080 77665',
                'start' => -5, 'nights' => 4, 'guests' => 1, 'rooms' => 1,
                'status' => 'completed',
            ],
            [
                'ref' => '004', 'room' => 'deluxe-double-room',
                'name' => 'Priya Menon', 'email' => 'priya.menon@example.com', 'phone' => '+91 98860 33221',
                'start' => -1, 'nights' => 4, 'guests' => 2, 'rooms' => 1,
                'status' => 'confirmed',
                'notes' => 'Late arrival, around 11 PM.',
                'remark' => 'Room 501 kept ready. Night staff informed.',
            ],
            [
                'ref' => '005', 'room' => 'deluxe-double-room',
                'name' => 'James Fernandes', 'email' => 'james.f@example.com', 'phone' => '+91 97400 55889',
                'start' => 2, 'nights' => 2, 'guests' => 2, 'rooms' => 1,
                'status' => 'confirmed',
                'remark' => 'Confirmed by phone. Parking space kept free.',
            ],
            [
                'ref' => '006', 'room' => 'standard-double-room',
                'name' => 'Sneha Patil', 'email' => 'sneha.patil@example.com', 'phone' => '+91 88670 12345',
                'start' => 4, 'nights' => 1, 'guests' => 2, 'rooms' => 1,
                'status' => 'pending',
                'notes' => 'Two colleagues sharing.',
            ],
            [
                'ref' => '007', 'room' => 'family-room',
                'name' => 'Arun Nair', 'email' => 'arun.nair@example.com', 'phone' => '+91 99456 78901',
                'start' => 7, 'nights' => 4, 'guests' => 6, 'rooms' => 2,
                'status' => 'pending',
                'notes' => 'Family holiday. Need the two rooms next to each other.',
            ],
            [
                'ref' => '008', 'room' => 'family-room',
                'name' => 'Deepa Iyer', 'email' => 'deepa.iyer@example.com', 'phone' => '+91 98450 99887',
                'start' => 9, 'nights' => 3, 'guests' => 4, 'rooms' => 1,
                'status' => 'pending',
                'notes' => 'Anniversary stay.',
            ],
            [
                'ref' => '009', 'room' => 'standard-single-room',
                'name' => 'Vikram Rao', 'email' => 'vikram.rao@example.com', 'phone' => '+91 90350 44112',
                'start' => 12, 'nights' => 2, 'guests' => 1, 'rooms' => 1,
                'status' => 'pending',
            ],
            [
                'ref' => '010', 'room' => 'deluxe-double-room',
                'name' => 'Neha Gupta', 'email' => 'neha.gupta@example.com', 'phone' => '+91 99720 66554',
                'start' => 5, 'nights' => 2, 'guests' => 2, 'rooms' => 1,
                'status' => 'cancelled',
                'remark' => 'Guest cancelled by phone. Trip postponed.',
            ],
            [
                'ref' => '011', 'room' => 'deluxe-double-room',
                'name' => 'Karthik Reddy', 'email' => 'karthik.r@example.com', 'phone' => '+91 98801 23456',
                'start' => 18, 'nights' => 5, 'guests' => 2, 'rooms' => 1,
                'status' => 'pending',
                'notes' => 'Business trip, needs a printed bill at checkout.',
            ],
            [
                'ref' => '012', 'room' => 'standard-double-room',
                'name' => 'Farah Sheikh', 'email' => 'farah.sheikh@example.com', 'phone' => '+91 76190 88774',
                'start' => 25, 'nights' => 1, 'guests' => 2, 'rooms' => 1,
                'status' => 'pending',
            ],
        ];

        $rooms = Room::all()->keyBy('slug');
        $today = Carbon::today();
        $count = 0;

        foreach ($samples as $sample) {
            $room = $rooms->get($sample['room']);

            if (! $room) {
                $this->command->warn("  Skipped {$sample['ref']}: room '{$sample['room']}' not found.");
                continue;
            }

            $checkIn = $today->copy()->addDays($sample['start']);
            $checkOut = $checkIn->copy()->addDays($sample['nights']);

            Booking::updateOrCreate(
                ['reference' => 'SKL-SAMPLE-'.$sample['ref']],
                [
                    'room_id' => $room->id,
                    'customer_name' => $sample['name'],
                    'customer_email' => $sample['email'],
                    'customer_phone' => $sample['phone'],
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkOut->toDateString(),
                    'nights' => $sample['nights'],
                    'guests' => $sample['guests'],
                    'rooms_count' => $sample['rooms'],
                    'total_price' => $room->price * $sample['nights'] * $sample['rooms'],
                    'notes' => $sample['notes'] ?? null,
                    'status' => $sample['status'],
                    'admin_remark' => $sample['remark'] ?? null,
                ]
            );

            $count++;
        }

        $this->command->info("Bookings seeded ({$count} sample bookings).");
        $this->command->line("   Remove them later with: DELETE FROM bookings WHERE reference LIKE 'SKL-SAMPLE-%';");
    }
}
