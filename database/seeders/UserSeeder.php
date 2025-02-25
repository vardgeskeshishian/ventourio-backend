<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\RoleSeederService;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'first_name' => 'Test user',
                'email' => 'test@gmail.com',
                'password' => Hash::make('1111'),
                'balance' => 10000,
            ],
            [
                'first_name' => 'Test user 2',
                'email' => 'test1@gmail.com',
                'password' => Hash::make('1111'),
                'balance' => 10000,
            ],
            [
                'first_name' => 'Test user 3',
                'email' => 'test2@gmail.com',
                'password' => Hash::make('1111'),
                'balance' => 10000,
            ],
        ]);
    }
}
