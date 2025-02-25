<?php

namespace App\Http\Controllers\Web;

use App\Events\UnseenCertificatesCount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Certificate\IndexAvailableForPurchaseRequest;
use App\Http\Requests\Web\Certificate\IndexRequest;
use App\Http\Requests\Web\Certificate\Store;
use App\Http\Requests\Web\Certificate\UseRequest;
use App\Http\Resources\Web\CertificateResource;
use App\Models\Certificate;
use App\Services\Web\AuthService;
use App\Services\Web\CertificateService;
use Illuminate\Http\JsonResponse;

class CertificateController extends Controller
{
    public function indexAvailableForPurchase(IndexAvailableForPurchaseRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => (new CertificateService())->indexAvailableForPurchase()
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $certificates = (new CertificateService())->index($request->validated(), auth()->user());

        return response()->json([
            'success' => true,
            'data' => CertificateResource::collection($certificates)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        $data = $request->validated();

        # Получаем id пользователя. Регистрируем, при необходимости.
        if ( ! auth()->check() && ! auth('sanctum')->check()) {

            $data['bought_by_user_id'] = (new AuthService())
                ->registerLazy($request->validated('email'))
                ->id;

        } else {
            $data['bought_by_user_id'] = auth()->id() ?? auth('sanctum')->id();
        }

        (new CertificateService())->store($data);

        UnseenCertificatesCount::dispatch();

        return response()->json([
            'success' => true,
            'message' => __('success.certificate.ordered'),
        ]);
    }

    public function use(UseRequest $request, Certificate $certificate): JsonResponse
    {
        (new CertificateService())->use($certificate, auth()->id());

        return response()->json([
            'success' => true,
            'message' => __('success.certificate.used')
        ]);
    }
}
