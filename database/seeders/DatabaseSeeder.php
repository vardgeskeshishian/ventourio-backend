<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $this->call([
                AdminSeeder::class,
                CookieSeeder::class,
                LanguageSeeder::class,
                CurrencySeeder::class,
                DiscountSeeder::class,
                CreditCardSeeder::class,
                FacilityCategorySeeder::class,
                FacilitySeeder::class,
                LocationAndObjectsSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                CertificateSeeder::class,
                AdminDefaultRoleSeeder::class,
                CommonPagesSeeder::class,
                QASeeder::class,
                CompanyServiceSeeder::class,
                ArticleCategorySeeder::class,
                PaymentRequisiteSeeder::class,
                PaymentSystemSeeder::class
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
