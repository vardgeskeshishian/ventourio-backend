<?php

namespace Database\Seeders;

use App\Models\BaseCertificate;
use App\Models\Certificate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $baseCertificateOptions = config('base_certificates.options');

        foreach ($baseCertificateOptions as $option) {

            BaseCertificate::factory()
                ->create([
                    'title' => $option['title'],
                    'amount' => $option['amount'],
                    'color_hex' => $option['color_hex']
                ])
                ->each(function (BaseCertificate $certificate) {
                    Certificate::factory(3)->create(['base_certificate_id' => $certificate->id]);
                });
        }
    }
}
