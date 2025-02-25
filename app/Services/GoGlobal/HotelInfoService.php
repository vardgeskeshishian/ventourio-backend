<?php

namespace App\Services\GoGlobal;

use App\DTO\GoGlobal\GetHotelInfoDTO;
use App\Exceptions\GoGlobalApiException;
use Exception;
use Illuminate\Support\Str;

final class HotelInfoService extends GoGlobalService
{
    public string $operation;
    public int $code;
    private ?float $version;

    public function __construct()
    {
        parent::__construct();

        $requestType = config('goglobal.request_types.hotel_info');

        $this->operation = $requestType['operation'];
        $this->code      = $requestType['code'];
        $this->version   = $requestType['version'];
    }

    /**
     * @throws Exception
     */
    public function get(GetHotelInfoDTO $dto): array
    {
        $formattedData = $this->formatData($dto);

        $resultData = $this->sendRequest($formattedData);

        // test data to simulate response
//        $resultData = xml_to_array('<Root>
//<Header>
//<Agency>AgencyID</Agency>
//<User>UserName</User>
//<Password>Password</Password>
//<Operation>HOTEL_INFO_RESPONSE</Operation>
//<OperationType>Response</OperationType>
//</Header>
//<Main>
//<HotelSearchCode>12345/3243212345/53</HotelSearchCode>
//<HotelName>HERITAGE</HotelName>
//<HotelId>23421</HotelId>
//<Address>8 MINNIE STREET, CAIRNS, QUEENSLAND 4870, AUSTRALIA</Address>
//<CityCode>123</CityCode>
//<GeoCodes>
//<Longitude>-4.886657</Longitude>
//<Latitude>52.37346</Latitude>
//</GeoCodes>
//<Phone>61-7-40511211</Phone>
//<Fax>61-7-40511380</Fax>
//<Category><![CDATA[3]]></Category>
//<Description>
//Exterior
//Low rise modern building opened 2001.
//General
//Very nice little modern property, in a quiet location, with a small swimming pool. Friendly and affordable, if one isn\'t fussy about having a restaurant on the premises...
//</Description>
//<HotelFacilities>Built in 2000, Car parking, Car rental services, Coach drop-off services, Porter, 1 pools, Non Smoking Rooms, Laundry services, Baby Sitting...
//</HotelFacilities>
//<RoomFacilities>AirCondition, Television, Video, Radio, Direct dial phone, Minibar, Hair Dryer, Trouser Press, Wake Up Call, Voltage: 240 V...
//</RoomFacilities>
//<RoomCount>24 rooms, 24 twin rooms.</RoomCount>
//<Pictures>
//<Picture Description="Balcony">https://cdn.tourismcloudservice.com/HotelsV3/97378/t97378_12016624151942955.jpg</Picture>
//<Picture Description="Balcony2">https://cdn.tourismcloudservice.com/HotelsV3/97378/t97378_12016624151942955.jpg</Picture>
//</Pictures>
//</Main>
//</Root>');

        return $this->formatBack($resultData, $dto->getId());
    }

    /**
     * @throws Exception
     */
    private function formatData(GetHotelInfoDTO $dto): array
    {
        return [
            '_attributes' => [
                'Version' => $this->version,
            ],
            'InfoHotelId' => $dto->getId(),
            'InfoLanguage' => $dto->getLanguage()->value()
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

        $fax = $main['Fax'] ?? null;
        if (empty($fax)) {
            $fax = '';
        }

        $phone = $main['PhoneNumber'] ?? null;
        if (empty($phone)) {
            $phone = '';
        }

        $description = $main['Description'] ?? null;
        if (empty($description)) {
            $description = null;
        } else {
            $description = str_replace("\n", "", $description);
        }

        $result = [
            'external_code' => $externalCode,
            'search_code'   => $main['HotelSearchCode'] ?? null,
            'hotel_name'    => $main['HotelName'] ?? null,
            'address'       => $main['Address'] ?? null,
            'city_code'     => $main['CityCode'] ?? null,
            'phone'         => $phone,
            'fax'           => $fax,
            'stars'         => (int) $main['Category'] ?? null,
            'description'   => $description,
        ];

        $this->addGeo($result, $main);
        $this->addFacilities($result, $main);
        $this->addImages($result, $main);

//        $result['raw'] = $main;

        return $result;
    }

    private function addGeo(array &$result, array $main)
    {
        $geoCodes = $main['GeoCodes'] ?? null;
        if (empty($geoCodes)) {
            return;
        }

        $result['geo'] = [
            'longitude' => $geoCodes['Longitude'],
            'latitude' => $geoCodes['Latitude'],
        ];
    }

    private function addFacilities(array &$result, array $main)
    {
        $hotelFacilities = $main['HotelFacilities'] ?? null;
        $roomFacilities  = $main['RoomFacilities'] ?? null;

        if (empty($hotelFacilities) || empty($roomFacilities)) {
            return;
        }

        $sep1 = '<BR />';
        $sep2 = '</br>';

        if (Str::contains($hotelFacilities, $sep1)) {
            $sep = $sep1;
        } else {
            $sep = $sep2;
        }

        $hotelFacilities = array_filter(explode($sep, $hotelFacilities));

        if (Str::contains($roomFacilities, $sep1)) {
            $sep = $sep1;
        } else {
            $sep = $sep2;
        }

        $roomFacilities = array_filter(explode($sep, $roomFacilities));

        $hotelFacilitiesResult = [];
        foreach ($hotelFacilities as $facility) {

            if (Str::contains($facility, ', ')) {
                $hotelFacilitiesResult = array_merge($hotelFacilitiesResult, explode(', ', $facility));
            } else {
                $hotelFacilitiesResult[] = $facility;
            }
        }

        $roomFacilitiesResult = [];
        foreach ($roomFacilities as &$facility) {

            if (Str::contains($facility, ', ')) {
                $roomFacilitiesResult = array_merge($roomFacilitiesResult, explode(', ', $facility));
            } else {
                $roomFacilitiesResult[] = $facility;
            }
        }

        $result['facilities'] = [
            'hotel' => $hotelFacilitiesResult,
            'room' => $roomFacilitiesResult
        ];
    }

    private function addImages(array &$result, mixed $main)
    {
        $pictures = $main['Pictures']['Picture'] ?? null;
        if (empty($pictures)) {
            return;
        }

        $images = [];

        if (is_string($pictures)) {
            $pictures = [
                $pictures
            ];

        } else {
            # Значит массив pictures это одна фотография
            if (array_key_exists('@content', $pictures)) {
                $pictures = [
                    $pictures
                ];
            }
        }

        foreach ($pictures as $picture) {

            if (is_string($picture)) {
                $images[] = [
                    'url' => $picture,
                    'description' => '',
                ];
                continue;
            }

            $url = $picture['@content'] ?? null;
            $description = $picture['@attributes']['Description'] ?? null;

            if (empty($url)) {
                continue;
            }

            $images[] = [
                'url' => $url,
                'description' => $description
            ];
        }

        $result['images'] = $images;
    }
}
