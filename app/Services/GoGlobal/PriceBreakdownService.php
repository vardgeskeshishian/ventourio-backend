<?php

namespace App\Services\GoGlobal;

use App\Exceptions\GoGlobalApiException;
use Exception;

final class PriceBreakdownService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.price_breakdown');

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
//<Operation>PRICE_BREAKDOWN_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<HotelName>ASTORIA</HotelName>
//<Room>
//<RoomType>Room for 2 Adults</RoomType>
//<Children>0</Children>
//<Cots>0</Cots>
//<PriceBreakdown>
//<FromDate>2012-12-14</FromDate>
//<ToDate>2012-12-16</ToDate>
//<Price>182.43</Price>
//<Currency>EUR</Currency>
//</PriceBreakdown>
//<PriceBreakdown>
//<FromDate>2012-12-16</FromDate>
//<ToDate>2012-12-17</ToDate>
//<Price>167.25</Price>
//<Currency>EUR</Currency>
//</PriceBreakdown>
//</Room>
//<Room>
//<RoomType>Room for 1 Adult</RoomType>
//<Children>0</Children>
//<Cots>0</Cots>
//<PriceBreakdown>
//<FromDate>2012-12-14</FromDate>
//<ToDate>2012-12-16</ToDate>
//<Price>130</Price>
//<Currency>EUR</Currency>
//</PriceBreakdown>
//<PriceBreakdown>
//<FromDate>2012-12-16</FromDate>
//<ToDate>2012-12-17</ToDate>
//<Price>120.5</Price>
//<Currency>EUR</Currency>
//</PriceBreakdown>
//</Room>
//</Main>
//</Root>');

        return $this->formatBack($resultData);
    }

    /**
     * @throws Exception
     */
    private function formatData(array $data): array
    {
        $hotelSearchCode = $data['search_code'] ?? null;
        if (empty($hotelSearchCode)) {
            throw new Exception(__('errors.system.empty_required_params'));
        }

        return ['HotelSearchCode' => $hotelSearchCode];
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

        $hotelName = $main['HotelName'];

        $rooms = $main['Room'];

        # Значит массив $rooms это одна комната
        if (array_key_exists('RoomType', $rooms)) {
            $rooms = [
                $rooms
            ];
        }

        foreach ($rooms as &$room) {

            $priceBreakdowns = $room['PriceBreakdown'];

            # Значит массив $priceBreakDowns это одна цена
            if (array_key_exists('FromDate', $priceBreakdowns)) {
                $priceBreakdowns = [
                    $priceBreakdowns
                ];
            }

            foreach ($priceBreakdowns as &$priceBreakdown) {

                $priceBreakdown = [
                    'dates' => [
                        'from'     => $priceBreakdown['FromDate'],
                        'to'       => $priceBreakdown['ToDate'],
                        'price'    => $priceBreakdown['Price'],
                        'currency' => $priceBreakdown['Currency']
                    ]
                ];
            }

            $room = [
                'room_type'        => $room['RoomType'],
                'children'         => $room['Children'],
                'cots'             => $room['Cots'],
                'price_breakdowns' => $priceBreakdowns
            ];
        }

        return [
            'hotel_name' => $hotelName,
            'rooms' => $rooms
        ];
    }
}
