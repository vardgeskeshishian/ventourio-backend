<?php

namespace App\Services\GoGlobal;

use App\Exceptions\GoGlobalApiException;
use App\Services\MainService;
use DOMException;
use Exception;
use Illuminate\Support\Facades\Log;
use Mtownsend\XmlToArray\XmlToArray;
use Spatie\ArrayToXml\ArrayToXml;

abstract class GoGlobalService extends MainService
{
    protected int $agencyId;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        if (config('goglobal.auth.testing')) {
            $agencyId = config('goglobal.auth.dev.agency');
            $user     = config('goglobal.auth.dev.user');
            $password = config('goglobal.auth.dev.password');

        } else {
            $agencyId = config('goglobal.auth.prod.agency');
            $user     = config('goglobal.auth.prod.user');
            $password = config('goglobal.auth.prod.password');
        }

        $this->agencyId = $agencyId;
        $this->user     = $user;
        $this->password = $password;
    }

    /**
     * @throws DOMException
     * @throws GoGlobalApiException
     */
    public function sendRequest(array $data): array
    {
        $xml = $this->xml($data);

        $response = $this->request($xml);

        return $this->getResult($response);
    }

    /**
     * @throws DOMException
     */
    private function xml(array $data): string
    {
        $cData = [
            'Header' => [
                'Agency' => $this->agencyId,
                'User' => $this->user,
                'Password' => $this->password,
                'Operation' => $this->operation,
                'OperationType' => 'Request'
            ],
            'Main' => $data
        ];

        $cDataXml = new ArrayToXml($cData,'Root');

        $cDataXml = $cDataXml->dropXmlDeclaration()->toXml();

        $data = [
            'soap12:Body' => [
                'MakeRequest' => [
                    '_attributes' => [
                        'xmlns' => 'http://www.goglobal.travel/'
                    ],
                    'requestType' => $this->code,
                    'xmlRequest' => [
                        '_cdata' => $cDataXml
                    ]
                ]
            ]
        ];

        $rootElement = [
            'rootElementName' => 'soap12:Envelope',
            '_attributes' => [
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:soap12' => 'http://www.w3.org/2003/05/soap-envelope'
            ]
        ];

        return ArrayToXml::convert(
            $data,
            $rootElement,
            true,
            'utf-8',
        );
    }

    /**
     * @throws GoGlobalApiException
     */
    private function request(string $xml): bool|string
    {
        $soapUrl = "https://personaltravel.xml.goglobal.travel/xmlwebservice.asmx";

        $headers = array(
            "Content-Type: application/soap+xml; charset=utf-8",
            'API-Operation' => $this->operation,
            'API-AgencyID' => $this->agencyId,
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $soapUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if ( ! $response) {
            throw new GoGlobalApiException(__METHOD__ . ' Error while send request');
        }

        return $response;
    }

    /**
     * @throws GoGlobalApiException
     */
    private function getResult(bool|string $response): array
    {
        $responseArray = XmlToArray::convert($response);

        $soapBody = $responseArray['soap:Body'] ?? null;
        if (empty($soapBody)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        if ( ! empty($soapFault = $soapBody['soap:Fault'] ?? null)) {
            $message = $soapFault['soap:Reason']['soap:Text']['@content'] ?? null;
            Log::error(__METHOD__ . " $message");
            throw new GoGlobalApiException('Error while send request');
        }

        $requestResult = $soapBody['MakeRequestResponse']['MakeRequestResult'] ?? null;
        if (empty($requestResult)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        $result = json_decode($requestResult, true);
        if ( ! is_array($result)) {
            $result = xml_to_array($requestResult);
        }

        if (empty($result)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        $header = $result['Header'] ?? null;
        if (empty($header)) {
            throw new GoGlobalApiException(__('errors.api.improper_format'));
        }

        if ($header['OperationType'] !== 'Response') {
            $main = $result['Main'] ?? null;
            if (empty($main)) {
                Log::error(__METHOD__ . " $requestResult");
                throw new GoGlobalApiException(__('errors.api.improper_format'));
            }

            throw new GoGlobalApiException($main['Error']['@content'] ?? $main['Error']['Message'] ?? '');
        }

        return $result;
    }
}
