<?php

namespace Database\Factories;

use App\Models\PaymentRequisite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentRequisiteFactory extends Factory
{
    protected $model = PaymentRequisite::class;

    public function definition(): array
    {
        return [
            'data' => $this->faker->realText(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
