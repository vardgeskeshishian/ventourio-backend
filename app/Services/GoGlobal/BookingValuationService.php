<?php

namespace App\Services\GoGlobal;

use App\DTO\GoGlobal\ValuateGoGlobalBookingDTO;
use Carbon\Carbon;
use DOMException;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

final class BookingValuationService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.booking_valuation');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @throws Exception
     */
    public function valuate(ValuateGoGlobalBookingDTO $dto): array
    {
        $formattedData = $this->formatData($dto);

        $resultData = $this->sendRequest($formattedData);

//        $resultData = xml_to_array('<Root>
//<Header>
//<Agency>AgencyID</Agency>
//<User>UserName</User>
//<Password>Password</Password>
//<Operation>BOOKING_VALUATION_REQUEST</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<HotelSearchCode>890/30/70</HotelSearchCode>
//<ArrivalDate>2008-03-18</ArrivalDate>
//<CancellationDeadline>2008-01-01</CancellationDeadline>
//<Remarks>IN CASE OF AMENDMENTS OR NAME CHANGES RESERVATIONS HAVE TO BE CANCELLED AND REBOOKED SUBJECT TO AVAILABILITY AND RATE
//AT THE TIME OF REBOOKING</Remarks>
//<Rates currency="USD">1000</Rates>
//</Main>
//</Root>');

        $this->formatBack($resultData);

        return $resultData;
    }

    /**
     * @throws Exception
     */
    private function formatData(ValuateGoGlobalBookingDTO $dto): array
    {
        $result = [
            '_attributes' => [
                'Version' => $this->version,
            ],
            'HotelSearchCode' => $dto->getSearchCode()
        ];

        $this->addDates($result, $dto);

        return $result;
    }

    /**
     * @throws Exception
     */
    private function formatBack(array &$data): void
    {
        $main = $data['Main'] ?? null;
        if (empty($main)) {
            throw new Exception(__('errors.api.improper_format'));
        }

        $data = [
            'search_code' => $main['HotelSearchCode'],
            'arrival_date' => $main['ArrivalDate'],
            'cancellation_deadline' => $main['CancellationDeadline'],
            'remarks' => $main['Remarks'],
        ];

        $this->addRate($data, $main);
    }

    /**
     * @throws Exception
     */
    private function addDates(array &$result, ValuateGoGlobalBookingDTO $dto)
    {
        $arrivalDate = $dto->getArrivalDate() ?? null;
        if (empty($arrivalDate)) {
            throw new Exception(__('errors.system.empty_required_params'));
        }

        $result['ArrivalDate'] = $arrivalDate->format('Y-m-d');
    }

    /**
     * @throws Exception
     */
    private function addRate(array &$data, array $main)
    {
        $rates = $main['Rates'] ?? null;

        $price    = $rates['@content'] ?? null;
        $currency = $rates['@attributes']['currency'] ?? null;

        if (empty($price) || empty($currency)) {
            throw new Exception(__('errors.api.improper_format'));
        }

        $data['total_price'] = (float) $price;
        $data['currency']    = $currency;
    }
}
