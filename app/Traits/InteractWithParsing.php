<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractWithParsing
{
    protected string $scrapingUrl = 'https://planetofhotels.com/guide';

    protected string $scrapingPureUrl = 'https://planetofhotels.com';

    public function geolocationsMainPath(): string
    {
        return storage_path('app/public/planet-hotel-geolocations/geolocations.json');
    }

    public function countriesInformationMainPath(): string
    {
        return storage_path('app/public/planet-hotel-countries-information/countries-information.json');
    }

    public function citiesInformationMainPath(): string
    {
        return storage_path('app/public/planet-hotel-cities-information/cities-information.json');
    }

    public function saveInFile(string $path, array $data): void
    {
        File::put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function attachMedia($model, array|null $gallery, string $collectionName = 'default'): void
    {
        if(!empty($gallery)){

            $this->clearOldMedia($model, $collectionName);

            foreach($gallery as $imagePath) {
                $model->addMediaFromUrl($imagePath)
                    ->withCustomProperties(['parsed' => true])
                    ->toMediaCollection($collectionName);
            }
        }

    }

    private function clearOldMedia($model, $collectionName): void
    {
        $model->clearMediaCollectionExcept($collectionName, $model->getMedia(filters: function (Media $media) {
            return !isset($media->custom_properties['parsed']);
        }));
    }

    public function prependParsingSource(array|null $images): Collection|null
    {
        if(empty($images)){
            return null;
        }

        return collect($images)->map(function($item){
            return $this->scrapingPureUrl . $item;
        });

    }

}
