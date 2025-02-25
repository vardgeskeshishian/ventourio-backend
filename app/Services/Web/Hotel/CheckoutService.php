<?php

namespace App\Services\Web\Hotel;

use App\Enums\Provider;
use App\Exceptions\RoomAlreadyBookedException;
use App\Helpers\BookingSearchCodeHandler;
use App\Models\Room;
use App\Services\Web\WebService;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class CheckoutService extends WebService
{
    public function offer(array $data): array
    {
        $result = [
            'search_code' => $data['offer']['search_code'],
            'provider' => $data['offer']['provider'],
        ];

        $this->addDetails($result, $data);
        $this->addRooms($result, $data);
        $this->addPayment($result, $data);

        return $result;
    }

    public function rooms(array $data): array
    {
        $checkIn = Carbon::createFromFormat('d.m.Y', $data['dates']['arrival']);
        $checkOut = Carbon::createFromFormat('d.m.Y', $data['dates']['departure']);

        $roomBases = $data['room_bases'];

        $rooms = collect();
        foreach ($roomBases as $roomBase) {

            $roomBaseId = $roomBase['id'];
            $roomCount = $roomBase['count'];

            $availableRooms = Room::available($checkIn, $checkOut)
                ->where('room_base_id', $roomBaseId)
                ->with([
                    'roomBase' => function ($query) {
                        $query->select([
                            'id',
                            'price',
                            'refundable',
                            'cancel_range',
                            'title_l->' . $this->locale . ' as title',
                            'adults_count',
                            'children_count',
                        ]);
                    }
                ])
                ->limit($roomCount)
                ->get();

            if ($availableRooms->count() != $roomCount) {
                throw new RoomAlreadyBookedException();
            }

            /** @var Room $room */
            foreach ($availableRooms as $room) {

                $roomData  = [
                    'id' => $room->id,
                    'title' => $room->title,
                    'adults' => $room->roomBase->adults_count,
                    'price' => $room->roomBase->price,
                    'children' => $room->roomBase->children_count,
                    'refundable' => $room->roomBase->refundable,
                    'cancel_range' => $room->roomBase->cancel_range,
                ];

                if (! empty($roomData['refundable']) && ! empty($roomData['cancel_range'])) {
                    $roomData['cancel_deadline'] = (clone($checkIn))->subDays($roomData['cancel_range'])->format('d.m.Y');
                }

                $rooms[] = $roomData;
            }
        }

        $refundable = $rooms->min('refundable');
        if (! empty($refundable)) {
            $cancelDeadline = $rooms->min('cancel_deadline');
        } else {
            $cancelDeadline = null;
        }

        $totalPrice = $rooms->sum('price');

        $data['offer'] = [
            'rooms' => $rooms->pluck('title'),
            'cancel_deadline' => $cancelDeadline,
            'total_price' => $totalPrice,
            'commission' => null,
        ];

        $result = [
            'search_code' => BookingSearchCodeHandler::create(['hotel_id' => $data['hotel_id'], 'rooms' => $rooms->pluck('id')->toArray()], Provider::DB),
            'provider' => Provider::DB->value
        ];

        $this->addDetails($result, $data);
        $result['rooms'] = $rooms;
        $this->addPayment($result, $data);

        return $result;
    }

    private function addDetails(array &$result, array $data): void
    {
        $details = [];

        $checkIn = Carbon::createFromFormat('d.m.Y', $data['dates']['arrival']);
        $checkOut = Carbon::createFromFormat('d.m.Y', $data['dates']['departure']);

        $checkInText = Str::ucfirst($checkIn->locale($this->locale)->getTranslatedShortDayName()) . ' ' . $checkIn->format('d M Y');
        $checkOutText = Str::ucfirst($checkOut->locale($this->locale)->getTranslatedShortDayName()) . ' ' . $checkOut->format('d M Y');

        $details['check_in'] = [
            'date' => $checkInText,
            'time' => null,
        ];

        $details['check_out'] = [
            'date' => $checkOutText,
            'time' => null,
        ];

        $details['nights'] = $data['offer']['nights'] ?? $checkIn->diffInDays($checkOut);
        $details['rooms'] = $data['offer']['rooms'];

        if ( ! empty($cancellationDeadline = $data['offer']['cancel_deadline'] ?? null)) {
            $cancellationDeadline = Carbon::createFromFormat('d.m.Y', $cancellationDeadline);
            $details['cancel_deadline'] = $cancellationDeadline->locale($this->locale)->translatedFormat('d F Y');
        }

        $result['details'] = $details;
    }

    private function addPayment(array &$result, array $data)
    {
        $payment = [];

        $price = $data['offer']['total_price'];
        $commission = $data['offer']['commission'] ?? null;

        $payment['rooms_count'] = count($result['rooms']) ?? null;
        $payment['price'] = $price;
        $payment['commission'] = $commission;
        $payment['commission_percent'] = null;
        $payment['total_price'] = !empty($commission) ? ($price + $commission) : $price;

        $result['payment'] = $payment;
    }

    private function addRooms(array &$result, array $data)
    {
        $rooms = [];

        $roomsFilter = $data['rooms'];
        $roomsOffer  = $data['offer']['rooms'];

        foreach ($roomsFilter as $index => $roomFilter) {

            if (count($roomsOffer) !== $roomsFilter) {
                $title = $roomsOffer[0] ?? null;
            } else {
                $title = $roomsOffer[$index] ?? null;
            }

            $rooms[] = [
                'title' => $title,
                'adults' => $roomFilter['adults'],
                'children' => $roomFilter['children'],
            ];
        }

        $result['rooms'] = $rooms;
    }
}
