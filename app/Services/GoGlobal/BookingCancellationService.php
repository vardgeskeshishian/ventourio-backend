<?php

namespace App\Services\GoGlobal;

use App\Enums\Provider;
use App\Exceptions\GoGlobalApiException;
use App\Helpers\BookingStatusConverter;
use Exception;

final class BookingCancellationService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.booking_cancel');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @throws Exception
     */
    public function cancel(array $data): array
    {
        $formattedData = $this->formatData($data);

        $resultData = $this->sendRequest($formattedData);

        // test data to simulate response
//        $resultData = xml_to_array('<Root>
//<Header>
//<Agency>AgencyID</Agency>
//<User>UserName</User>
//<Password>Password</Password>
//<Operation>BOOKING_CANCEL_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<GoBookingCode>95437</GoBookingCode>
//<BookingStatus>RX</BookingStatus>
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

        $bookingStatus = $main['BookingStatus'] ?? null;
        if (empty($bookingStatus)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        return [
            'external_code' => $externalCode,
            'status' => BookingStatusConverter::convert($bookingStatus, Provider::GOGLOBAL->value, Provider::DB->value)
        ];
    }
}
