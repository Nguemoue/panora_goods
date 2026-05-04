<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::query()->updateOrCreate([
            'email' => 'admin@goulbam.com',
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        //the seeler
        User::query()->updateOrCreate([
            'email' => 'seller@goulbam.com'
        ], [
            'name' => "Seller",
            'password' => bcrypt('password'),
        ]);

    }
}
