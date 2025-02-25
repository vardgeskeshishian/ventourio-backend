<?php

namespace App\Services\GoGlobal;

use App\Enums\Provider;
use App\Exceptions\GoGlobalApiException;
use App\Helpers\BookingStatusConverter;
use Exception;

final class BookingDetailsService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.booking_search');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @throws Exception
     */
    public function get(array $data): array
    {
        $formattedData = $this->formatData($data);

        $resultData = $this->sendRequest($formattedData);

        // test data to simulate response
//        $resultData = xml_to_array('<Root>
//<Header>
//<Agency>AgencyID</Agency>
//<User>UserName</User>
//<Password>Password</Password>
//<Operation>BOOKING_SEARCH_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<GoBookingCode>62345</GoBookingCode>
//<GoReference>GO95345-33456-A(IR)</GoReference>
//<ClientBookingCode>Test AgRef</ClientBookingCode>
//<BookingStatus>C</BookingStatus>
//<TotalPrice>1042</TotalPrice>
//<Currency>EUR</Currency>
//<HotelName>AC</HotelName>
//<HotelSearchCode>12345/3243212345/53</HotelSearchCode>
//<CityCode>123</CityCode>
//<RoomType>STANDARD</RoomType>
//<RoomBasis>RO</RoomBasis>
//<ArrivalDate>2005-09-06</ArrivalDate>
//<CancellationDeadline>2005-09-04</CancellationDeadline>
//<Nights>3</Nights>
//<NoAlternativeHotel>1</NoAlternativeHotel>
//<Leader LeaderPersonID="1">JOHN DOE</Leader>
//<Rooms>
//<RoomType Type="" Adults="1">
//<Room RoomID="1" Category="DOUBLE OCEAN VIEW">
//<PersonName PersonID="1">JOHN DOE MR.</PersonName>
//</Room>
//</RoomType>
//<RoomType Type="" Adults="2">
//<Room RoomID="1" Category="VILLA BEACH FRONT">
//<PersonName PersonID="1">
//<FirstName>JIM</FirstName>
//<LastName>DOE</LastName>
//<Title>MR.</Title>
//</PersonName>
//<PersonName PersonID="2">
//<FirstName>JAYNE</FirstName>
//<LastName>DOE</LastName>
//<Title>MRS.</Title>
//</PersonName>
//<ExtraBed PersonID="3" ChildAge="9">
//<FirstName>JERRY</FirstName>
//<LastName>DOE</LastName>
//</ExtraBed>
//</Room>
//</RoomType>
//</Rooms>
//</Main>
//</Root>');

        return $this->formatBack($resultData);
    }

    /**
     * @throws Exception
     */
    public function formatData(array $data): array
    {
        $bookingCode = $data['external_code'] ?? null;
        if (empty($bookingCode)) {
            throw new Exception(__('errors.system.empty_required_params'));
        }

        $includeCommission = $data['with_commission'] ? 'true' : 'false';

        return [
            'GoBookingCode' => $bookingCode,
            'IncludeCommission' => $includeCommission
        ];
    }

    /**
     * @throws GoGlobalApiException
     */
    private function formatBack(array $resultData): array
    {
        $main = $resultData['Main'] ?? null;
        if (empty($main)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        return [
            'external_code'   => $main['GoBookingCode'],
            'hotel_id'        => $main['HotelId'],
            'hotel_name'      => $main['HotelName'],
            'arrival_date'    => $main['ArrivalDate'],
            'cancel_deadline' => $main['CancellationDeadline'],
            'nights'          => $main['Nights'],
            'search_code'     => $main['HotelSearchCode'],
            'total_price'     => $main['TotalPrice'],
            'currency'        => $main['Currency'],
            'room_basis'      => $main['RoomBasis'],
            'status'          => BookingStatusConverter::convert($main['BookingStatus'], Provider::GOGLOBAL->value, Provider::DB->value),
            'raw'             => $main
        ];
    }
}
