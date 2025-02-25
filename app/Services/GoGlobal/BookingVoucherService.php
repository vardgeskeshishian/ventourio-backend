<?php

namespace App\Services\GoGlobal;

use App\DTO\GoGlobal\GetVoucherDTO;
use App\Exceptions\GoGlobalApiException;
use Carbon\Carbon;
use Exception;

final class BookingVoucherService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.voucher_details');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @throws Exception
     */
    public function get(GetVoucherDTO $dto): array
    {
        $formattedData = $this->formatData($dto);

        $resultData = $this->sendRequest($formattedData);

        // test data to simulate response
//        $resultData = xml_to_array('<Root>
//<Header>
//<Agency>AgencyID</Agency>
//<User>UserName</User>
//<Password>Password</Password>
//<Operation>VOUCHER_DETAILS_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<GoBookingCode>62345</GoBookingCode>
//<HotelName>ASTORIA</HotelName>
//<Address>VIALE BARI 11</Address>
//<Phone>39-080-4323320</Phone>
//<Fax>39-080-4321290</Fax>
//<CheckInDate>03/Mar/11</CheckInDate>
//<RoomBasis>BB</RoomBasis>
//<Nights>3</Nights>
//<Rooms>Room for 1 Adult + Children / DELUXE NON-SMOKING : JOHN DOE MR., JAYNE DOE MRS., JULIANNE DOE CHD.; 1 Single / STANDARD : JACK DOE</Rooms>
//<Remarks>If possible, provide non-smoking room</Remarks>
//<BookingRemarks type="Agent">
//<Remark>
//<![CDATA[If possible, provide non-smoking room]]>
//</Remark>
//</BookingRemarks>
//<BookedAndPayableBy>Supplier</BookedAndPayableBy>
//<SupplierReferenceNumber>AB12345/3243212345/53</SupplierReferenceNumber>
//<EmergencyPhone>44 54 7791234</EmergencyPhone>
//</Main>
//</Root>');

        return $this->formatBack($resultData, $dto->getBookingCode());
    }

    /**
     * @throws Exception
     */
    private function formatData(GetVoucherDTO $dto): array
    {
        return [
            '_attributes' => [
                'Version' => $this->version,
            ],
            'GoBookingCode' => $dto->getBookingCode(),
            'GetEmergencyPhone' => true,
        ];
    }

    /**
     * @throws GoGlobalApiException
     */
    private function formatBack(array $resultData, string $externalCode): array
    {
        $main = $resultData['Main'] ?? null;
        if (empty($main)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        $bookingCode = $main['GoBookingCode'] ?? null;
        if ($bookingCode != $externalCode) {
            throw new GoGlobalApiException(__('errors.api.booking_not_found'));
        }

        return [
            'external_code'         => $bookingCode,
            'hotel_name'            => $main['HotelName'],
            'address'               => $main['Address'],
            'phone'                 => $main['Phone'],
            'fax'                   => $main['Fax'],
            'checkin_date'          => Carbon::createFromFormat('d/M/y', $main['CheckInDate']),
            'room_basis'            => $main['RoomBasis'],
            'nights'                => $main['Nights'],
            'rooms_info'            => $main['Rooms'],
            'remark'                => $main['Remarks'],
            'booked_and_payable_by' => $main['BookedAndPayableBy'],
            'reference_code'        => $main['SupplierReferenceNumber'],
            'emergency_phone'       => $main['EmergencyPhone'] ?? null,
            'raw'                   => $main
        ];
    }
}
