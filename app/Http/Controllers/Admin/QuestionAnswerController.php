<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionAnswer\StoreQuestionAnswerRequest;
use App\Http\Requests\Admin\QuestionAnswer\UpdateQuestionAnswerRequest;
use App\Http\Resources\Admin\PageResource;
use App\Http\Resources\Admin\QuestionAnswerResource;
use App\Models\Page;
use App\Models\QuestionAnswer;
use App\Services\Admin\QuestionAnswerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class QuestionAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $result = (new QuestionAnswerService())->getData($request);
        return response()->json($result, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return AnonymousResourceCollection
     */
    public function create()
    {
        $pages = Page::all(['id', 'slug', 'content_l']);
        return PageResource::collection($pages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreQuestionAnswerRequest $request
     * @return QuestionAnswerResource
     */
    public function store(StoreQuestionAnswerRequest $request)
    {
        $qa = QuestionAnswer::create($request->validated());
        return new QuestionAnswerResource($qa->load('page'));
    }

    /**
     * Display the specified resource.
     *
     * @param QuestionAnswer $qa
     * @return QuestionAnswerResource
     */
    public function show(QuestionAnswer $qa)
    {
        return new QuestionAnswerResource($qa->load('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param QuestionAnswer $qa
     * @return JsonResponse
     */
    public function edit(QuestionAnswer $qa)
    {
        $pages = Page::all(['id', 'slug', 'content_l']);

        return response()->json([
            'success' => true,
            'data' => [
                'pages' => PageResource::collection($pages),
                'qa' => new QuestionAnswerResource($qa->load('page'))
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateQuestionAnswerRequest $request
     * @param QuestionAnswer $qa
     * @return QuestionAnswerResource
     */
    public function update(UpdateQuestionAnswerRequest $request, QuestionAnswer $qa)
    {
        $qa->update($request->validated());
        return new QuestionAnswerResource($qa->load('page'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param QuestionAnswer $qa
     * @return JsonResponse
     */
    public function destroy(QuestionAnswer $qa)
    {
        $qa->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ], 200);
    }
}
