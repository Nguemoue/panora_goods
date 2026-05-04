<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
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
            'role' => UserRoleEnum::ADMIN,
            'name' => 'Admin User',
            'password' => bcrypt('password'),
        ]);

        //the seller
        User::query()->updateOrCreate([
            'email' => 'seller@goulbam.com'
        ], [
            'role' => UserRoleEnum::SELLER,
            'name' => "Seller",
            'password' => bcrypt('password'),
        ]);

    }
}
