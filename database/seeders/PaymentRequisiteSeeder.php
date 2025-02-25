<?php

namespace Database\Seeders;

use App\Models\PaymentRequisite;
use Illuminate\Database\Seeder;

class PaymentRequisiteSeeder extends Seeder
{
    public function run()
    {
        PaymentRequisite::factory(5)->create();
    }
}
