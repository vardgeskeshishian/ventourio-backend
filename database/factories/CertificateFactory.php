<?php

namespace Database\Factories;

use App\Models\BaseCertificate;
use App\Models\Currency;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;
    /**
     * Run the database seeds.
     *
     * @return array
     */
    public function definition(): array
    {
        $paidAt = rand(0,1) ? now()->subDays($this->faker->numberBetween(3, 20)) : null;

        if (empty($paidAt)) {
            $usedAt = null;
            $usedBy = null;
        } else {
            $usedAt = rand(0,1) ? null : (clone $paidAt)->addDays($this->faker->numberBetween(1,3)) ;
            $usedBy = User::inRandomOrder()->first('id')->id;
        }

        return [
            'base_certificate_id' => BaseCertificate::factory(),
            'bought_by_user_id' => User::inRandomOrder()->first('id')->id,
            'paid_at' => $paidAt,
            'used_at' => $usedAt,
            'currency_id' => Currency::select("id")->inRandomOrder()->first()->id,
            'code' => $this->faker->unique()->uuid,
            'used_by_user_id' => $usedBy,
        ];
    }
}
