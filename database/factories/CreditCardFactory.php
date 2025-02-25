<?php

namespace Database\Factories;

use App\Enums\CreditCardType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CreditCardFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => '************' . $this->faker->numberBetween('1111', '9999'),
            'holder_name' => $this->faker->name(),
            'type' => rand(0,1) ? CreditCardType::MASTERCARD : CreditCardType::VISA,
            'number' => Hash::make('1111111111111111'),
            'exp_month' => Hash::make('07'),
            'exp_year' => Hash::make('2024'),
            'svc' => Hash::make('243'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
