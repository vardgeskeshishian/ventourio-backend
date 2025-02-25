<?php

namespace Database\Factories;

use App\Enums\RoomBasis;
use App\Models\Hotel;
use App\Models\RoomBase;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RoomBaseFactory extends Factory
{
    protected $model = RoomBase::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(1, 100, 2000);
        return [
            'title_l' => ['en' => $this->faker->word()],
            'booking_max_term' => $this->faker->randomNumber(4),
            'booking_range' => $this->faker->randomNumber(4),
            'cancel_range' => $this->faker->randomNumber(4),
            'basis' => RoomBasis::BED_AND_DINNER,
            'refundable' => $this->faker->boolean(),
            'adults_count' => $this->faker->randomNumber(1),
            'children_count' => $this->faker->randomNumber(1),
            'price' => $price,
            'base_price' => $price,
            'remark_l' => ['en' => $this->faker->text()],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'room_type_id' => RoomType::factory(),
        ];
    }
}
