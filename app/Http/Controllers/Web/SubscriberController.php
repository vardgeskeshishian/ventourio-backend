<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Subscribe\StoreSubscriberRequest;
use App\Models\Subscriber;
use App\Services\Web\SubscriberService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use function __;
use function dd;
use function response;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreSubscriberRequest $request
     * @return JsonResponse
     */
    public function store(StoreSubscriberRequest $request)
    {
        Subscriber::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('success.subscription.verify_message')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Subscriber $subscriber
     * @return Response
     */
    public function show(Subscriber $subscriber)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subscriber $subscriber
     * @return Response
     */
    public function destroy(Subscriber $subscriber)
    {
        //
    }

    /**
     * Verifying email callback.
     *
     * @param $token
     * @return Application|RedirectResponse|Redirector
     */
    public function verify($token)
    {
        try{

            $result = (new SubscriberService($token))->verify();

            return redirect( config('front.web_url') . '/callback' . "?status=success&message=" . __('success.subscription.stored'));

        }catch(\Exception $e){

            return redirect(config('front.web_url') . '/callback' . "?status=error&message=" . __('errors.subscription.stored'));

        }


    }
}
