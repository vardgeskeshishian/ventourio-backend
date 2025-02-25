<?php

namespace App\Services\Web;

use App\Enums\BalanceChangeType;
use App\Events\CertificateCreated;
use App\Exceptions\BusinessException;
use App\Helpers\CertificateCodeGenerator;
use App\Models\BalanceChange;
use App\Models\BaseCertificate;
use App\Models\Currency;
use App\Models\Certificate;
use App\Helpers\CurrencyConverter;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CertificateService extends WebService
{
    private bool $useTransaction;

    public function __construct(bool $useTransaction = true)
    {
        parent::__construct();

        $this->useTransaction($useTransaction);
    }

    /**
     * @param array $data
     * @param User $user
     * @return Collection
     */
    public function index(array $data, User $user): Collection
    {
        if ( ! $user->relationLoaded('availableCertificates') || ! $user->relationLoaded('usedCertificates')) {
            $user->load(['availableCertificates', 'usedCertificates']);
        }

        $availableCertificates = $user->availableCertificates;
        $usedCertificates   = $user->usedCertificates;

        $certificates = collect();
        $certificates = $certificates->merge($availableCertificates);
        $certificates = $certificates->merge($usedCertificates);

        return $certificates;
    }

    public function store(array $data)
    {
        $certificate = Certificate::create([
            'base_certificate_id' => $data['base_certificate_id'],
            'bought_by_user_id' => $data['bought_by_user_id'],
            'currency_id' => $data['currency_id'],
            'code' => CertificateCodeGenerator::make()
        ]);

        CertificateCreated::dispatch($certificate);

        return $certificate;
    }

    public function use(Certificate|string $certificate, int $userId): void
    {
        if (is_string($certificate)) {
            $certificate = Certificate::where('code', $certificate)
                ->with(['currency', 'baseCertificate'])
                ->first();
        }

        if ( ! $certificate->exists) {
            throw new BusinessException(__('errors.system.contact_support'));
        }

        if($certificate->is_used){
            throw new BusinessException(__('errors.app.certificate.is_used'));
        }

        if( ! $certificate->is_paid){
            throw new BusinessException(__('errors.app.certificate.not_paid'));
        }

        if ( ! $certificate->relationLoaded('currency') || $certificate->relationLoaded('baseCertificate')) {
            $certificate->load('currency', 'baseCertificate');
        }

        if ($this->useTransaction) {
            DB::beginTransaction();
        }
        try {

            BalanceChange::create([
                'model_id' => $certificate->id,
                'model_type' => $certificate->getMorphClass(),
                'amount' => CurrencyConverter::toMain($certificate->amount, $certificate->currency),
                'type' => BalanceChangeType::ADD,
                'user_id' => $userId
            ]);

            $certificate->update([
                'used_at' => now(),
                'used_by_user_id' => $userId,
                'code' => null
            ]);

            if ($this->useTransaction) {
                DB::commit();
            }
        } catch (\Exception $e) {
            if ($this->useTransaction) {
                DB::rollBack();
            }
            Log::error($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new BusinessException(__('errors.system.contact_support'));
        }
    }

    public function indexAvailableForPurchase(): array
    {
        $availableCurrencies = config('base_certificates.currencies');
        $baseCertificates = BaseCertificate::all();

        $currencyCode = $this->currency;
        if ( ! in_array($currencyCode, $availableCurrencies)) {
            $currencyCode = $availableCurrencies[0];
        }

        $currency = Currency::where('code', $currencyCode)->firstOrFail(['id', 'symbol']);

        $result = [];
        foreach ($baseCertificates as $baseCertificate) {

            $amountValue = $baseCertificate->amount;

            $result[] = [
                'id' => $baseCertificate->id,
                'title' => $baseCertificate->title,
                'currency_id' => $currency->id,
                'amount' => [
                    'title' => $currency->symbol . ' ' . number_format($amountValue, 0, '', ' '),
                    'value' => $amountValue,
                ],
                'color_hex' => $baseCertificate->color_hex
            ];
        }

        return $result;
    }

    private function useTransaction(bool $useTransaction)
    {
        $this->useTransaction = $useTransaction;
    }
}
