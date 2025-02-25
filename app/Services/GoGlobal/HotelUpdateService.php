<?php

namespace App\Services\GoGlobal;

use App\DTO\GoGlobal\GetHotelInfoDTO;
use App\DTO\GoGlobal\GoGlobalLanguage;
use App\Models\Facility;
use App\Models\Hotel;
use Exception;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

final class HotelUpdateService
{
    private int $facilityCount = 3;
    private int $imagesCount = 1;
    private array $valuesForUpdate = [
        self::FACILITIES,
        self::IMAGES,
        self::HOTEL
    ];

    private const FACILITIES = 'facilities';
    private const IMAGES = 'images';
    private const HOTEL = 'hotel';

    public function __construct(?array $valuesForUpdate = null)
    {
        if ( ! empty($valuesForUpdate)) {
            $this->valuesForUpdate = $valuesForUpdate;
        }
    }

    /**
     * @throws Exception
     */
    public function do(Hotel $hotel, string $locale): void
    {
        $service = new HotelInfoService();

        $getHotelDto = new GetHotelInfoDTO($hotel->external_code, GoGlobalLanguage::create($locale));

        $result = $service->get($getHotelDto);

        $this->updateFacilities($result, $hotel, $locale);
        $this->updateImages($result, $hotel);
        $this->updateHotel($result, $hotel, $locale);
    }

    public function facilities(int $count): self
    {
        $this->facilityCount = $count;

        return $this;
    }

    public function images(int $count): self
    {
        $this->imagesCount = $count;

        return $this;
    }

    public function onlyFacilities(): self
    {
        $this->valuesForUpdate = [self::FACILITIES];

        return $this;
    }

    private function updateFacilities(array $result, Hotel $hotel, string $locale): void
    {
        $facilities = $result['facilities']['hotel'] ?? null;
        if (
            empty($facilities)
            || $this->facilityCount === 0
            || ! in_array(self::FACILITIES, $this->valuesForUpdate)
        ) { return; }

        $facilities = array_slice($facilities, 0, $this->facilityCount);

        $allFacilities = Facility::pluck('title_l->' . $locale . ' as title', 'id');

        $facilitiesForSync = [];

        foreach ($facilities as $facility) {

            $found = $allFacilities->filter(function ($item) use ($facility) {
                return strtolower($item) == strtolower($facility);
            });

            if ($found->isEmpty()) {

                $id = Facility::create([
                    'title_l' => [
                        $locale => $facility
                    ]
                ])->id;

            } else {
                $id = $found->keys()->first();
            }

            $facilitiesForSync[] = $id;
        }

        $hotel->facilities()->sync($facilitiesForSync);
    }

    private function updateImages(array $result, Hotel $hotel): void
    {
        $images = $result['images'] ?? null;
        if (
            empty($images)
            || $this->imagesCount === 0
            || ! in_array(self::IMAGES, $this->valuesForUpdate)
        ) { return; }

        $hotel->clearMediaCollection();

        $images = array_slice($images, 0, $this->imagesCount);

        foreach ($images as $image) {

            $url = $image['url'];

            try {
                $hotel->addMediaFromUrl($url)
                    ->toMediaCollection();
            } catch (FileDoesNotExist|FileIsTooBig|FileCannotBeAdded $e) {
                continue;
            }
        }
    }

    private function updateHotel(array $result, Hotel $hotel, string $locale): void
    {
        if (! in_array(self::HOTEL, $this->valuesForUpdate)) {
            return;
        }

        $description = $result['description'] ?? null;
        if ( ! empty($description) && is_string($description)) {

            $descriptionL = $hotel->description_l;
            if (empty($descriptionL) || ! is_array($descriptionL)) {
                $descriptionL = [];
            }

            $descriptionL[$locale] = $description;

            $hotel->description_l = $descriptionL;
        }

        $hotel->save();
    }
}
