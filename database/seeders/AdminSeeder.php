<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::insert([
                [
                    'name' => 'Admin',
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('123456789'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
