<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CompanyService\IndexRequest;
use App\Http\Resources\Web\CompanyServiceResource;
use App\Models\CompanyService as CompanyServiceModel;
use App\Services\Web\CompanyServiceService;
use Illuminate\Http\Request;

class CompanyServiceController extends Controller
{
    public function index(IndexRequest $request)
    {
        $result = (new CompanyServiceService())->getData($request->validated());
        return response()->json($result, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param CompanyServiceModel $companyService
     * @return CompanyServiceResource
     */
    public function show(string $companyServiceSlug)
    {
        $result = (new CompanyServiceService())->get(['slug' => $companyServiceSlug]);
        return new CompanyServiceResource($result);
    }
}
