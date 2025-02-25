<?php

namespace App\Services\GoGlobal;

use App\DTO\GoGlobal\BookHotelDTO;
use App\Enums\Provider;
use App\Exceptions\BusinessException;
use App\Helpers\BookingStatusConverter;
use App\Helpers\RSAEncryptor;
use Exception;

final class BookingCreationService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.booking_insert');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @throws Exception
     */
    public function book(BookHotelDTO $dto): array
    {
        $formattedData = $this->formatData($dto);

        $resultData = $this->sendRequest($formattedData);

//         test data to simulate response
//        $resultData = xml_to_array('<Root>
//<Header>
//<Agency>AgencyID</Agency>
//<User>UserName</User>
//<Password>Password</Password>
//<Operation>BOOKING_INSERT_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<GoBookingCode>33456</GoBookingCode>
//<GoReference>GO95345-33456-A(IR)</GoReference>
//<ClientBookingCode>Test AgRef</ClientBookingCode>
//<BookingStatus>C</BookingStatus>
//<TotalPrice>1042</TotalPrice>
//<Currency>EUR</Currency>
//<HotelId>5432</HotelId>
//<HotelName>AC</HotelName>
//<HotelSearchCode>12135/3243212345/53</HotelSearchCode>
//<RoomType>STANDARD</RoomType>
//<RoomBasis>RO</RoomBasis>
//<ArrivalDate>2012-09-06</ArrivalDate>
//<CancellationDeadline>2005-09-04</CancellationDeadline>
//<Nights>3</Nights>
//<NoAlternativeHotel>1</NoAlternativeHotel>
//<Leader LeaderPersonID="1"/>
//<Rooms>
//<RoomType Adults="1">
//<Room RoomID="1">
//<PersonName PersonID="1" Title="MR." FirstName="JOHN" LastName="DOE"/>
//</Room>
//</RoomType>
//<RoomType Adults="2">
//<Room RoomID="1">
//<PersonName PersonID="1" Title="MRS." FirstName="JAYNE" LastName="DOE"/>
//<PersonName PersonID="2" Title="MR." FirstName="JIM" LastName="DOE"/>
//<ExtraBed PersonID="3" FirstName="JERRY" LastName="DOE" ChildAge="9"/>
//</Room>
//<Room RoomID="2">
//<PersonName PersonID="1" Title="MISS" FirstName="JOY" LastName="DOE"/>
//<PersonName PersonID="2" Title="MR." FirstName="JAKE" LastName="DOE"/>
//<ExtraBed PersonID="3" FirstName="JEMMA" LastName="DOE" ChildAge="6"/>
//</Room>
//</RoomType>
//</Rooms>
//<PaymentInfo>
//<PaymentResult>
//<Successful>true</Successful>
//<Amount>100</Amount>
//<Currency>USD</Currency>
//<ApprovalCode>111900</ApprovalCode>
//</PaymentResult>
//</PaymentInfo>
//</Main>
//</Root>');

        return $this->formatBack($resultData);
    }

    /**
     * @throws Exception
     */
    private function formatData(BookHotelDTO $dto): array
    {
        $result = [
            '_attributes' => [
                'Version' => $this->version,
            ],
            'AgentReference' => '',
            'HotelSearchCode' => $dto->getSearchCode(),
            'NoAlternativeHotel' => 1,
        ];

        $this->addDates($result, $dto);
        $this->addRooms($result, $dto);
        $this->addCreditCard($result, $dto);

        return $result;
    }

    /**
     * @throws Exception
     */
    private function formatBack(array $resultData): array
    {
        $main = $resultData['Main'] ?? null;
        if (empty($main)) {
            throw new Exception(__('errors.api.improper_format'));
        }

        $booking = [
            'external_code'       => $main['GoBookingCode'],
            'hotel_external_code' => $main['HotelId'],
            'hotel_name'          => $main['HotelName'],
            'arrival_date'        => $main['ArrivalDate'],
            'cancel_deadline'     => $main['CancellationDeadline'],
            'nights'              => $main['Nights'],
            'search_code'         => $main['HotelSearchCode'],
            'total_price'         => $main['TotalPrice'],
            'currency'            => $main['Currency'],
            'room_basis'          => $main['RoomBasis'],
            'status'              => BookingStatusConverter::convert($main['BookingStatus'], Provider::GOGLOBAL->value, Provider::DB->value)
        ];

        $this->addPaymentStatus($booking, $main);

        $booking['raw'] = $main;

        return $booking;
    }

    private function addDates(array &$result, BookHotelDTO $dto)
    {
        $arrivalDate = $dto->getArrivalDate();
        $departureDate = $dto->getDepartureDate();

        $nights = $departureDate->diffInDays($arrivalDate);

        $result['ArrivalDate'] = $arrivalDate->format('Y-m-d');
        $result['Nights'] = $nights;
    }

    private function addRooms(array &$result, BookHotelDTO $dto)
    {
        // todo check restrictions
        //All characters (names) should be non-unicode only.
        //Pax names must start with a letter and the lastname have min 2 letters at the start

        $rooms = $dto->getRooms();
        if (empty($rooms)) {
            throw new BusinessException(__('errors.system.empty_required_params'));
        }

         # We only allow search for up to a total of 8 rooms in a single search.
        if (count($rooms) > 8) {
            throw new BusinessException(__('errors.api.rooms_limit_exceeded'));
        }

        $roomsByType = collect($rooms)->groupBy(function ($room) {
            return count($room['adults']);
        });

        # ALL rooms in a request must have Adults.
        if ($roomsByType->has(0)) {
            throw new BusinessException(__('errors.api.no_adults'));
        }

        $roomTypes = [];

        $index = 0;
        foreach ($roomsByType as $roomType => $rooms) {

            $roomTypes[$index]['_attributes'] = [
                'Adults' => $roomType,
//                'Cots' => 1,
            ];

            $roomsData = [];

            foreach ($rooms as $roomIndex => $room) {

                $this->addRoom($roomsData, $room, $roomIndex + 1);
            }

            $roomTypes[$index]['Room'] = $roomsData;

            $index++;
        }

        $result['Rooms']  = ['RoomType' => $roomTypes];
        $result['Leader'] = ['_attributes' => ['LeaderPersonID' => 1]];
    }

    /**
     * @throws Exception
     */
    private function addCreditCard(array &$result, BookHotelDTO $dto)
    {
        $payment = $dto->getPaymentInfo();
        if (empty($payment)) {
            throw new Exception(__('errors.system.empty_required_params'));
        }

        $cardHolderName  = $payment['card_holder_name'] ?? null;
        $cardName        = $payment['card_name'] ?? null;
        $cardNumber      = $payment['card_number'] ?? null;
        $expirationMonth = $payment['exp_month'] ?? null;
        $expirationYear  = $payment['exp_year'] ?? null;
        $securityCode    = $payment['security_code'] ?? null;
        $email           = $payment['email'] ?? null;

        if (empty($cardHolderName) || empty($cardName) || empty($cardNumber) || empty($expirationMonth) || empty($expirationYear) || empty($securityCode) || empty($email)) {
            throw new Exception(__('errors.system.empty_required_params'));
        }

        $dataString = "<CardHolderName>$cardHolderName</CardHolderName><CardName>$cardName</CardName><CardNumber>$cardNumber</CardNumber><ExpirationMonth>$expirationMonth</ExpirationMonth><ExpirationYear>$expirationYear</ExpirationYear><SecurityCode>$securityCode</SecurityCode><Email>$email</Email>";

        $rsaHash = RSAEncryptor::encrypt($dataString);

        $result['PaymentCreditCard'] = $rsaHash;
    }

    private function addRoom(array &$roomsData, array $room, int $roomId)
    {
        $roomData = [
            '_attributes' => [
                'RoomID' => $roomId,
            ]
        ];

        $this->addPeople($roomData, $room);

        $roomsData[] = $roomData;
    }

    private function addPeople(array &$roomData, array $room)
    {
        $adults = $room['adults'];
        $children = $room['children'] ?? [];

        $adultsCount = count($adults);
        $childrenCount = count($children);

        # The maximum amount of persons (adults + children), per room, is 8.
        if ($adultsCount + $childrenCount > 8) {
            throw new BusinessException(__('errors.api.person_limit_exceeded'));
        }

        # The limit of children, per room, is 4
        if ($childrenCount > 4) {
            throw new BusinessException(__('errors.api.children_limit_exceeded'));
        }

        $personIndex = 1;
        foreach ($adults as $adult) {
            $this->addAdult($roomData, $adult, $personIndex);
        }

        foreach ($children as $child) {
            $this->addChild($roomData, $child, $personIndex);
        }
    }

    private function addAdult(array &$roomData, array $adult, int &$personIndex)
    {
        $title = match ($adult['sex']) {
            'male' => 'MR.',
            'female' => 'MS.',
            default => null,
        };

        $roomData['PersonName'][] = [
            '_attributes' => [
                'PersonID' => $personIndex,
                'Title' => $title,
                'FirstName' => $adult['first_name'],
                'LastName' => $adult['last_name'],
            ]
        ];

        $personIndex++;
    }

    private function addChild(array &$roomData, array $child, int &$personIndex)
    {
        $roomData['ExtraBed'][] = [
            '_attributes' => [
                'PersonID' => $personIndex,
                'FirstName' => $child['first_name'],
                'LastName' => $child['last_name'],
                'ChildAge' => $child['age'],
            ]
        ];

        $personIndex++;
    }

    /**
     * @throws Exception
     */
    private function addPaymentStatus(array &$booking, array $main)
    {
        $info = $main['PaymentInfo']['PaymentResult'] ?? null;
        if (empty($info)) {
            throw new Exception(__('errors.api.improper_format'));
        }

        $booking['paid'] = (bool) $info['Successful'];
    }
}
