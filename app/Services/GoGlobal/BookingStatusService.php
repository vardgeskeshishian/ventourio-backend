<?php

namespace App\Services\GoGlobal;

use App\Enums\Provider;
use App\Exceptions\GoGlobalApiException;
use App\Helpers\BookingStatusConverter;
use Exception;

final class BookingStatusService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.booking_status');

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
//<Operation>BOOKING_STATUS_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<GoBookingCode Status="X" GoReference="GO95100-95434-A(IR)" TotalPrice="82" Currency="EUR">95434</GoBookingCode>
//<GoBookingCode Status="X" GoReference="GO95101-95437-B(IR)" TotalPrice="60" Currency="EUR">95437</GoBookingCode>
//</Main>
//</Root>');

        return $this->formatBack($resultData, $data['external_code']);
    }

    /**
     * @throws Exception
     */
    private function formatData(array $data): array
    {
        $bookingCode = $data['external_code'] ?? null;
        if (empty($bookingCode)) {
            throw new Exception(__('errors.system.empty_required_params'));
        }

        return ['GoBookingCode' => $bookingCode];
    }

    /**
     * @throws Exception
     */
    private function formatBack(array $resultData, string $externalCode): array
    {
        $main = $resultData['Main'] ?? null;
        if (empty($main)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        $bookings = $main['GoBookingCode'] ?? null;
        if (empty($bookings)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        $status = null;

        foreach ($bookings as $booking) {

            $bookingCode = $booking['@content'] ?? null;
            if (empty($bookingCode)) {
                throw new GoGlobalApiException(__('errors.api.improper_format'));
            }

            if ($bookingCode !== $externalCode) {
                continue;
            }

            $status = $booking['@attributes']['Status'] ?? null;
            if (empty($status)) {
                throw new GoGlobalApiException(__('errors.api.improper_format'));
            }

            break;
        }

        if (empty($status)) {
            throw new GoGlobalApiException(__('errors.api.booking_not_found'));
        }

        return [
            'external_code' => $externalCode,
            'status' => BookingStatusConverter::convert($status, Provider::GOGLOBAL->value, Provider::DB->value)
        ];
    }
}
