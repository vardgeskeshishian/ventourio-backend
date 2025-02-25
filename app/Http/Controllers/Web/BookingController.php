<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\BookingPriceHasChangedException;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Booking\BookRequest;
use App\Http\Requests\Web\Booking\CancelRequest;
use App\Http\Requests\Web\Booking\Checkout\OfferRequest;
use App\Http\Requests\Web\Booking\Checkout\RoomsRequest;
use App\Http\Requests\Web\Booking\IndexRequest;
use App\Models\Booking;
use App\Services\Web\AuthService;
use App\Services\Web\Hotel\BookService;
use App\Services\Web\Hotel\CheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;

final class BookingController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => (new BookService())->index($request->validated(), auth()->user() ?? auth('sanctum')->user()),
        ]);
    }

    public function book(BookRequest $request): JsonResponse
    {
        $data = $request->validated();

        # Получаем пользователя. Регистрируем, при необходимости.
        if ( ! auth()->hasUser() && ! auth('sanctum')->hasUser()) {
            $user = (new AuthService())->registerLazy( $request->validated('lead_person.email') );
            $token = $user->createToken("API TOKEN")->plainTextToken;
        } else {
            $user = auth()->user() ?? auth('sanctum')->user();
        }

        try {

            $result = (new BookService())->book($data, $user);

        } catch (BookingPriceHasChangedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getUserMessage(),
                'new_price' => $e->getNewPrice(),
                'token' => $token ?? null,
            ], 400);
        } catch (BusinessException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getUserMessage(),
                'token' => $token ?? null,
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result,
            'token' => $token ?? null
        ]);
    }

    public function checkoutOffer(OfferRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => (new CheckoutService())->offer($request->validated())
        ]);
    }

    public function checkoutRooms(RoomsRequest $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => (new CheckoutService())->rooms($request->validated())
        ]);
    }

    public function cancel(CancelRequest $request, Booking $booking): JsonResponse
    {
        (new BookService())->cancel($request->validated(), $booking);

        return response()->json([
            'success' => true,
            'message' => __('success.booking.cancel_requested')
        ]);
    }
}
