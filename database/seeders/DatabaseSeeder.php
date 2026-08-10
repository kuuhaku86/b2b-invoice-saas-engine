<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // This seeder runs against the central connection (no tenant
        // active), so this row lands in the central `users` table — it's
        // the landlord/admin login for /admin and /horizon, not a tenant
        // user. Change the password before deploying anywhere real.
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@saas.test',
        ]);

        $this->call(PlanSeeder::class);
    }
}
