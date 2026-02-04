<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // password
                'email_verified_at' => now(),
            ]
        );

        // Factory generated users
        factory(User::class, 10)->create()->each(function ($user) {
            // Additional logic if needed
        });
    }
}
