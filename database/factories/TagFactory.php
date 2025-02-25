<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'title_l' => ['en' => $this->tags()[array_rand($this->tags())]],
            'color_hex' => $this->faker->hexColor()
        ];
    }

    private function tags(){
        return [
            'Adventure',
            'Activities',
            'Beaches',
            'Shopping',
            'Food',
            'Family'
        ];
    }
}
