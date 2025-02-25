<?php

namespace App\Http\Controllers\Admin;

use App\Events\UnseenCertificatesCount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Certificate\IndexRequest;
use App\Http\Requests\Admin\Certificate\StoreRequest;
use App\Http\Requests\Admin\Certificate\UpdateRequest;
use App\Http\Resources\Admin\BaseCertificateResource;
use App\Http\Resources\Admin\CurrencyResource;
use App\Http\Resources\Admin\CertificateResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\BaseCertificate;
use App\Models\Currency;
use App\Models\Certificate;
use App\Models\User;
use App\Services\Admin\CertificateService;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $result = (new CertificateService())->getData($request->validated());
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create()
    {
        return response()->json([
            'data' => [
                'users' => UserResource::collection(User::all()),
                'base_certificates' => BaseCertificateResource::collection(BaseCertificate::all()),
                'currencies' => CurrencyResource::collection(Currency::whereIn('code', config('base_certificates.currencies'))->get())
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CertificateResource
     */
    public function store(StoreRequest $request)
    {
        $certificate = (new CertificateService())->store($request->validated());

        UnseenCertificatesCount::dispatch();

        return new CertificateResource($certificate->load(['currency', 'baseCertificate', 'usedByUser', 'boughtByUser']));
    }

    /**
     * Display the specified resource.
     *
     * @param Certificate $certificate
     * @return CertificateResource
     */
    public function show(Certificate $certificate): CertificateResource
    {

        $certificate->markAsSeen();

        UnseenCertificatesCount::dispatch();

        return new CertificateResource($certificate->load(['currency', 'baseCertificate', 'usedByUser', 'boughtByUser']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Certificate $certificate
     * @return CertificateResource
     */
    public function edit(Certificate $certificate)
    {
        return $this->create();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Certificate $certificate
     * @return CertificateResource
     */
    public function update(UpdateRequest $request, Certificate $certificate)
    {
        (new CertificateService())->update($request->validated(), $certificate);

        return new CertificateResource($certificate->load(['currency', 'baseCertificate', 'usedByUser', 'boughtByUser']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Certificate $certificate
     * @return JsonResponse
     */
    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
