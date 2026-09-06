<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ContactSeeder extends Seeder
{
    /**
     * Sample Contact Us messages, so Admin > Messages has something to show.
     * Two are left unread, so the red "New" badge and the dashboard count
     * can both be checked straight away.
     */
    public function run(): void
    {
        $messages = [
            [
                'name' => 'Sunita Desai',
                'email' => 'sunita.desai@example.com',
                'phone' => '+91 98200 11445',
                'subject' => 'Rooms for a group',
                'message' => "Hello,\n\nWe need four rooms for two nights next month for a family function nearby. Could you share your best rate for four rooms together, and confirm they are all air conditioned?\n\nThank you.",
                'is_read' => false,
                'days_ago' => 0,
            ],
            [
                'name' => 'Alok Bhatt',
                'email' => 'alok.bhatt@example.com',
                'phone' => '+91 99870 22331',
                'subject' => 'Late night check-in',
                'message' => "Hi, my train gets in very late and I will reach around 2 AM on Saturday. Is it possible to check in at that hour, or should I book from the previous night?",
                'is_read' => false,
                'days_ago' => 1,
            ],
            [
                'name' => 'Meera Krishnan',
                'email' => 'meera.k@example.com',
                'phone' => '+91 90190 55667',
                'subject' => 'Lost item',
                'message' => "I checked out on Tuesday from room 204 and I think I left a grey scarf in the wardrobe. Could someone please check and let me know? I can arrange a courier.",
                'is_read' => true,
                'days_ago' => 3,
            ],
            [
                'name' => 'Rohit Verma',
                'email' => 'rohit.verma@example.com',
                'phone' => null,
                'subject' => 'Long stay discount',
                'message' => "Do you offer a monthly rate? I will be in the city for around six weeks for work and would prefer to stay in one place the whole time.",
                'is_read' => true,
                'days_ago' => 6,
            ],
            [
                'name' => 'Grace Dsouza',
                'email' => 'grace.dsouza@example.com',
                'phone' => '+91 88840 99001',
                'subject' => 'Thank you',
                'message' => "Just wanted to say the staff were very helpful during our stay last weekend, especially at the front desk. The room was spotless. We will be back.",
                'is_read' => true,
                'days_ago' => 11,
            ],
        ];

        foreach ($messages as $message) {
            $daysAgo = $message['days_ago'];
            unset($message['days_ago']);

            $contact = Contact::updateOrCreate(
                ['email' => $message['email'], 'subject' => $message['subject']],
                $message
            );

            // Spread the messages out over the last two weeks.
            $when = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 9));
            $contact->forceFill(['created_at' => $when, 'updated_at' => $when])->save();
        }

        $this->command->info('Contact messages seeded ('.count($messages).' messages, 2 unread).');
    }
}
