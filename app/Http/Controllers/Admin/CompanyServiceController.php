<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyService\StoreCompanyServiceRequest;
use App\Http\Requests\Admin\CompanyService\UpdateCompanyServiceRequest;
use App\Http\Resources\Admin\CompanyServiceResource;
use App\Models\CompanyService;
use App\Services\Admin\CompanyServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function response;

class CompanyServiceController extends Controller
{
    /** Display a listing of the resource. */
    public function index(Request $request): JsonResponse
    {
        $result = (new CompanyServiceService())->getData($request);
        return response()->json($result);
    }

    /** Show the form for creating a new resource. */
    public function create()
    {
        throw new \Exception('not implemented');
    }

    /** Store a newly created resource in storage.  */
    public function store(StoreCompanyServiceRequest $request): CompanyServiceResource
    {
        $result = (new CompanyServiceService())->store($request);
        return new CompanyServiceResource($result);
    }

    /** Display the specified resource. */
    public function show(CompanyService $companyService): CompanyServiceResource
    {
        return new CompanyServiceResource($companyService->load('page'));
    }

    /** Show the form for editing the specified resource. */
    public function edit(CompanyService $companyService)
    {
        throw new \Exception('not implemented');
    }

    /** Update the specified resource in storage. */
    public function update(UpdateCompanyServiceRequest $request, CompanyService $companyService): CompanyServiceResource
    {
        $result = (new CompanyServiceService())->update($request,$companyService);

        return new CompanyServiceResource($result);
    }

    /** Remove the specified resource from storage. */
    public function destroy(CompanyService $companyService): JsonResponse
    {
        $companyService->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }
}
