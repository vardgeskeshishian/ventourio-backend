<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomBase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RoomBase::factory(15)
            ->create()
            ->each( function ($roomBase) {
                Room::factory(5)
                    ->create(['room_base_id' => $roomBase->id]);
            });
    }
}
