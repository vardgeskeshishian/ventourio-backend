<?php

namespace Database\Factories;

use App\Models\QuestionAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class QuestionAnswerFactory extends Factory
{
    protected $model = QuestionAnswer::class;

    public function definition(): array
    {
        return [
            'page_id' => null,
            'question_l' => ['en' => $this->faker->words(10, true)],
            'answer_l' => ['en' => $this->faker->text()],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
