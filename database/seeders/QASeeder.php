<?php

namespace Database\Seeders;

use App\Models\QuestionAnswer;
use Illuminate\Database\Seeder;

class QASeeder extends Seeder
{
    public function run()
    {
        QuestionAnswer::factory(3)->create();
    }
}
