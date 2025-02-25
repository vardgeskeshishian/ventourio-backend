<?php

namespace App\Services\Admin;

use App\DTO\CreateHotelDTO;
use App\DTO\Geo;
use App\DTO\GoGlobal\GetHotelInfoDTO;
use App\DTO\GoGlobal\GoGlobalLanguage;
use App\DTO\PhoneNumber;
use App\Enums\Provider;
use App\Models\City;
use App\Models\Hotel;
use App\Services\GoGlobal\HotelInfoService;
use Exception;

final class HotelCreationService
{
    /**
     * @throws Exception
     */
    public function create(CreateHotelDTO $dto): int
    {
        return match ($dto->getCreateBy()) {
            Provider::DB => $this->createByDB($dto),
            Provider::GOGLOBAL => $this->createByGoGlobal($dto),
        };
    }

    /**
     * @throws Exception
     */
    private function createByGoGlobal(CreateHotelDTO $dto): int
    {
        $getHotelDto = new GetHotelInfoDTO($dto->getExternalCode(), GoGlobalLanguage::create(app()->getLocale()));

        $hotelInfo = (new HotelInfoService())->get($getHotelDto);

        $city = City::where('external_code', $hotelInfo['city_code'])->first('id');
        if ( ! $city) {
            throw new Exception('City with external_code ' . $hotelInfo['city_code'] . ' is not exists');
        }

        // todo add images

        $dto->setTitleL([app()->getLocale() => $hotelInfo['hotel_name']])
            ->setAddress($hotelInfo['address'])
            ->setFax($hotelInfo['fax'])
            ->setGeo(Geo::create($hotelInfo['geo']['latitude'], $hotelInfo['geo']['longitude']))
            ->setStars($hotelInfo['stars'])
            ->setCityId($city->id);

        if ( ! empty($hotelInfo['phone'])) {
            $dto->setPhoneNumber(PhoneNumber::create($hotelInfo['phone']));
        }

        return $this->createByDB($dto);
    }

    /**
     * @throws Exception
     */
    private function createByDB(CreateHotelDTO $dto)
    {
        $data = [
            'city_id' => $dto->getCityId(),
            'title_l' => $dto->getTitleL(),
            'address' => $dto->getAddress(),
            'external_code' => $dto->getExternalCode(),
            'fax' => $dto->getFax(),
            'stars' => $dto->getStars(),
            'is_apartment' => $dto->getIsApartment(),
            'giata_code' => $dto->getGiataCode()
        ];

        if ($dto->getPhoneNumber() !== null) {
            $data['phone'] = $dto->getPhoneNumber()->value();
        }

        if ($dto->getGeo() !== null) {
            $data['geo'] = $dto->getGeo()->value();
        }

        $hotel = Hotel::create($data);

        if ( ! $hotel) {
            throw new Exception('Hotel creation error');
        }

        return $hotel->id;
    }
}
