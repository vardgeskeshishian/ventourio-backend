<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ExternalPaymentMethodType;
use App\Enums\Helper;
use App\Enums\Provider;
use App\Helpers\BookingStatus as BookingStatusHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Booking\CancelExternalRequest;
use App\Http\Requests\Admin\Booking\IndexRequest;
use App\Http\Requests\Admin\Booking\StoreExternalRequest;
use App\Http\Requests\Admin\Booking\StoreRequest;
use App\Http\Requests\Admin\Booking\UpdateRequest;
use App\Http\Resources\Admin\BookingResource;
use App\Http\Resources\Admin\CreditCardResource;
use App\Http\Resources\Admin\HotelResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Booking;
use App\Models\CreditCard;
use App\Models\Hotel;
use App\Models\User;
use App\Services\Admin\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $result = (new BookingService())->index($request->validated());

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return JsonResponse
     */
    public function create(): JsonResponse
    {
        $providers = Helper::toArray(Provider::cases());

        return response()->json([
            'success' => true,
            'data' => [
                'statuses' => BookingStatusHelper::toAdmin(),
                'providers' => array_combine($providers, $providers),
                'users' => UserResource::collection(User::all(['id', 'email', 'balance'])),
                'hotels' => HotelResource::collection(Hotel::all(['id', 'title_l']))
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return BookingResource
     */
    public function store(StoreRequest $request): BookingResource
    {
        throw new \Exception('not implemented');
    }

    /**
     * Display the specified resource.
     *
     * @param Booking $booking
     * @return BookingResource
     */
    public function show(Booking $booking): BookingResource
    {
        return BookingResource::make($booking->load('externalPaymentMethod','user','hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Booking $booking
     * @return JsonResponse
     */
    public function edit(Booking $booking): JsonResponse
    {
        return $this->create();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Booking $booking
     * @return BookingResource
     */
    public function update(UpdateRequest $request, Booking $booking): BookingResource
    {
        (new BookingService())->update($request->validated(), $booking);

        return new BookingResource($booking);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BookingResource $bookingResource
     * @return JsonResponse
     */
    public function destroy(BookingResource $bookingResource): JsonResponse
    {
        $bookingResource->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully!'
        ]);
    }

    public function createExternal(): JsonResponse
    {
        $types = Helper::toArray(ExternalPaymentMethodType::cases());

        return response()->json([
            'success' => true,
            'data' => [
                'types' => array_combine($types, ['Credit', 'Credit Card']),
                'credit_cards' => CreditCardResource::collection(CreditCard::all(['id', 'title']))
            ]
        ]);
    }

    public function storeExternal(StoreExternalRequest $request)
    {
        $booking = (new BookingService())->storeExternal($request->validated());

        return BookingResource::make($booking->load('externalPaymentMethod','user','hotel'));
    }

    public function cancelExternal(CancelExternalRequest $request)
    {
        $booking = (new BookingService())->cancelExternal($request->validated());

        return BookingResource::make($booking->load('externalPaymentMethod','user','hotel'));
    }
}
