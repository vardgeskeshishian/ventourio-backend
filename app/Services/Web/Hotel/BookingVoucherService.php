<?php

namespace App\Services\Web\Hotel;

use App\DTO\GoGlobal\GetVoucherDTO;
use App\DTO\VoucherDetailsDTO;
use App\Enums\RoomBasis;
use App\Enums\Provider;
use App\Models\Booking;
use App\Services\Web\WebService;
use Carbon\Carbon;
use Exception;

final class BookingVoucherService extends WebService
{
    /**
     * @throws Exception
     */
    public function getDetails(Booking $booking): VoucherDetailsDTO
    {
        return match ($booking->provider) {
            Provider::DB => $this->getByDB($booking),
            Provider::GOGLOBAL => $this->getByGoGlobal($booking)
        };
    }

    private function getByDB(Booking $booking): VoucherDetailsDTO
    {
        $booking->load([
            'rooms' => function ($query) {
                $query->select(['rooms.id', 'room_base_id']);
                $query->with('roomBase:id,basis');
            },
            'user'
        ]);

        $rooms = $booking->rooms;

        $roomBasis = $rooms->pluck('roomBase.basis')->first();

        return new VoucherDetailsDTO(
            hotelTitle: $booking->hotel->title,
            address: $booking->hotel->address,
            phone: $booking->hotel->phone,
            fax: $booking->hotel->fax,
            roomBasis: $roomBasis,
            checkinDate: Carbon::create($booking->arrival_date),
            departureDate: Carbon::create($booking->departure_date),
            leadPearson: $booking->user->fullName,
            remark: null,
            emergencyPhone: null
        );
    }

    /**
     * @throws Exception
     */
    private function getByGoGlobal(Booking $booking): VoucherDetailsDTO
    {
        $dto = new GetVoucherDTO(bookingCode: $booking->external_code);

        $details = (new \App\Services\GoGlobal\BookingVoucherService())->get($dto);

        /** @var Carbon $checkinDate */
        $checkinDate = $details['checkin_date'];

        return new VoucherDetailsDTO(
            hotelTitle: $booking->hotel->title,
            address: $booking->hotel->address,
            phone: $booking->hotel->phone,
            fax: $booking->hotel->fax,
            roomBasis: RoomBasis::from($details['room_basis']),
            checkinDate: $checkinDate,
            departureDate: $checkinDate->addDays($details['nights']),
            leadPearson: $details['booked_and_payable_by'],
            remark: $details['remark'],
            emergencyPhone: $details['emergency_phone']
        );
    }
}
