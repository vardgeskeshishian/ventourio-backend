<?php

namespace App\Services\Admin;

use App\Events\CertificatePaid;
use App\Exceptions\BusinessException;
use App\Helpers\CertificateCodeGenerator;
use App\Http\Resources\Admin\CertificateResource;
use App\Models\Certificate;

class CertificateService
{
    /**
     * @param array $data
     * @return array
     */
    public function getData(array $data): array
    {
        if (!isset($data['page'])) {
            $data['page'] = 1;
        }
        if (!isset($data['count'])) {
            $data['count'] = 8;
        }

        $certificates = Certificate::query();

        $count = $certificates->count();

        $skip = $data['count'] * ($data['page'] - 1);
        $certificates = $certificates->take($data['count'])->skip($skip);

        return [
            'success' => true,
            'data' => CertificateResource::collection($certificates->with('currency', 'baseCertificate', 'boughtByUser', 'usedByUser')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $data['count']),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): Certificate
    {
        $certificate = Certificate::create([
            'bought_by_user_id' => $data['bought_by_user_id'],
            'currency_id' => $data['currency_id'],
            'base_certificate_id' => $data['base_certificate_id'],
            'code' => CertificateCodeGenerator::make(),
            'paid_at' => $data['paid_at'] ?? null,
        ]);

        if ($certificate->is_paid) {
            CertificatePaid::dispatch($certificate);
        }

        return $certificate;
    }

    public function update(array $data, Certificate &$certificate): void
    {
        if ($certificate->is_used) {
            throw new BusinessException(__('errors.app.certificate.is_used'));
        }

        $certificate->update([
            'currency_id' => $data['currency_id'],
            'base_certificate_id' => $data['base_certificate_id'],
            'paid_at' => $data['paid_at'] ?? null,
        ]);

        if ($certificate->wasChanged('paid_at') && $certificate->is_paid) {
            CertificatePaid::dispatch($certificate);
        }
    }
}
