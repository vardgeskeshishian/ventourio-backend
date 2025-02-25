<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tag\StoreTagRequest;
use App\Http\Requests\Admin\Tag\UpdateTagRequest;
use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use App\Services\Admin\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $result = (new TagService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return AnonymousResourceCollection
     */
    public function create(): AnonymousResourceCollection
    {
        throw new \Exception('Not implemeted');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTagRequest $request
     * @return TagResource
     */
    public function store(StoreTagRequest $request): TagResource
    {
        $sight = Tag::create($request->validated());
        return new TagResource($sight);
    }

    /**
     * Display the specified resource.
     *
     * @param Tag $tag
     * @return TagResource
     */
    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tag $tag
     * @return void
     */
    public function edit(Tag $tag)
    {
        throw new \Exception('Not implemeted');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTagRequest $request
     * @param Tag $tag
     * @return TagResource
     */
    public function update(UpdateTagRequest $request, Tag $tag): TagResource
    {
        $tag->update($request->validated());
        return new TagResource($tag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @return JsonResponse
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $tag->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ], 200);
    }
}
