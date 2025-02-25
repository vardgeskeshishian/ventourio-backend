<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Continent;
use App\Http\Controllers\Controller;
use App\Services\Admin\ContinentService;
use App\Http\Resources\Admin\ContinentResource;
use App\Http\Requests\Admin\Continent\StoreContinentRequest;
use App\Http\Requests\Admin\Continent\UpdateContinentRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ContinentController extends Controller
{
    /**
     * Display a listing of the resource
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new ContinentService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return AnonymousResourceCollection
     */
    public function create()
    {
        throw new \Exception('not implemented');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreContinentRequest $request
     * @return ContinentResource
     */
    public function store(StoreContinentRequest $request)
    {
        $continent = Continent::create($request->validated());
        return new ContinentResource($continent);
    }

    /**
     * Display the specified resource.
     *
     * @param  Continent $continent
     * @return ContinentResource
     */
    public function show(Continent $continent)
    {
        return new ContinentResource($continent);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Continent $continent
     * @return Response
     */
    public function edit(Continent $continent)
    {
        throw new \Exception('not implemented');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateContinentRequest $request
     * @param  Continent  $continent
     * @return ContinentResource
     */
    public function update(UpdateContinentRequest $request, Continent $continent)
    {

        $continent->update($request->validated());
        return new ContinentResource($continent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Continent $continent
     * @return JsonResponse
     */
    public function destroy(Continent $continent)
    {
        $continent->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ], 200);
    }
}
