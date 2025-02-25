<?php

namespace Database\Seeders;

use App\Models\CreditCard;
use Illuminate\Database\Seeder;

class CreditCardSeeder extends Seeder
{
    public function run()
    {
        CreditCard::factory(2)->create();
    }
}
