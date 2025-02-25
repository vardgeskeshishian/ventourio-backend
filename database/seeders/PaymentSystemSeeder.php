<?php

namespace Database\Seeders;

use App\Models\PaymentSystem;
use App\Models\PaymentWay;
use Illuminate\Database\Seeder;

class PaymentSystemSeeder extends Seeder
{
    public function run()
    {
        PaymentSystem::factory()->create([
            'title_l' => ['en' => 'Cash'],
            'payment_system' => 'cash',
            'enabled' => true,
        ])->each(function ($paymentSystem) {
            PaymentWay::factory()->create([
                'payment_system_id' => $paymentSystem->id,
                'enabled' => true,
            ]);
        });
    }
}
