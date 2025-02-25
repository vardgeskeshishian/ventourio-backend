<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\IndexRequest;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\Admin\PageResource;
use App\Services\Admin\PageService;

class PageController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        return response()->json(
            (new PageService())->getData($request->validated())
        );
    }

    public function create(): JsonResponse
    {
        throw new \Exception('Not implemented');
    }

    public function store(PageRequest $request): PageResource
    {
        $page = (new PageService())->store($request->validated());

        return new PageResource($page->load('infoBlocks'));
    }

    public function show(Page $page): PageResource
    {
        return new PageResource($page->load('infoBlocks'));
    }

    public function edit(Page $page): JsonResponse
    {
        return $this->create();
    }

    public function update(PageRequest $request, Page $page): PageResource
    {
        (new PageService())->update($request->validated(), $page);

        return new PageResource($page->load('infoBlocks'));
    }

    public function destroy(Page $page): JsonResponse
    {
        $page->delete();

        return response()->json([
            'status' => true,
            'message' => 'Page Deleted successfully!'
        ]);
    }
}
