<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Admin logins for the panel at /admin/login
     *
     * ------------------------------------------------------------------
     *  MAIN ADMIN     admin@sklgrandrooms.com   /  admin123
     *  FRONT DESK     desk@sklgrandrooms.com    /  desk123
     * ------------------------------------------------------------------
     *
     * Change these passwords below before you put the site online.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Hotel Admin',
                'email' => 'admin@sklgrandrooms.com',
                'password' => 'admin123',
            ],
            [
                'name' => 'Front Desk',
                'email' => 'desk@sklgrandrooms.com',
                'password' => 'desk123',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'is_admin' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // The hotel used to be called HNP Hotel. If you already seeded the old
        // logins, remove them so only the two above are left.
        $removed = User::whereIn('email', ['admin@hnphotel.com', 'desk@hnphotel.com'])->delete();

        if ($removed) {
            $this->command->warn("Removed {$removed} old @hnphotel.com admin login(s).");
        }

        $this->command->info('Admin logins ready:');
        $this->command->line('   admin@sklgrandrooms.com / admin123');
        $this->command->line('   desk@sklgrandrooms.com  / desk123');
    }
}
