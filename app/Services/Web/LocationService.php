<?php

namespace App\Services\Web;

use App\Exceptions\BusinessException;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Page;
use App\Models\QuestionAnswer;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Stevebauman\Location\Facades\Location;
use Stevebauman\Location\Position;

final class LocationService extends WebService
{
    public function search(array $data): array
    {
        if (!isset($data['without_continents'])) {
            $data['without_continents'] = false;
        }
        if (!isset($data['with_page'])) {
            $data['with_page'] = false;
        }

        $continentSlug = $data['continent'] ?? null;
        $countrySlug = $data['country'] ?? null;

        if (empty($continentSlug) && empty($countrySlug)) {
            $continents = $this->getContinents();
            $continentSlug = $continents->first()->page->slug;
        } else {
            $page = $this->getPage($data);
        }

        if (empty($countrySlug)) {

            if (empty($continents) && ! $data['without_continents']) {
                $continents = $this->getContinents();
            }

            $countries = $this->getCountries($continentSlug);
        } else {
            $regions = $this->getRegionsWithCities($countrySlug);
            $cities = $regions->pluck('cities')
                ->collapse()
                ->sortBy('title')
                ->values();
            $regions = $regions->makeHidden('cities');
        }

        return [
            'page'        => $page ?? null,
            'title'       => $page->instance->title ?? null,
            'continents'  => $continents ?? collect(),
            'countries'   => $countries ?? collect(),
            'regions'     => $regions ?? collect(),
            'cities'      => $cities ?? collect(),
        ];
    }

    public static function getPosition(string $ip): bool|Position
    {
        return Location::get($ip);
    }

    private function getContinents(): Collection
    {
        $continents = Continent::select(['id', 'title_l->' . $this->locale . ' as title'])
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.instance_id',
                        'pages.instance_type',
                        'pages.slug'
                    ]);
                }
            ])
            ->get();

        if ($continents->isEmpty()) {
            throw new BusinessException('Empty continents');
        }

        return $continents;
    }

    private function getCountries(string $continentSlug): Collection
    {
        $countries = Country::select(['id', 'continent_id', 'title_l->' . $this->locale . ' as title'])
            ->whereHas('continent', function ($query) use ($continentSlug) {
                $query->whereHas('page', function ($query) use ($continentSlug) {
                    $query->where('slug', $continentSlug);
                });
            })
            ->with([
                'media',
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.instance_id',
                        'pages.instance_type',
                        'pages.slug'
                    ]);
                }
            ])
            ->get();

        if ($countries->isEmpty()) {
            throw new BusinessException('Empty countries');
        }

        foreach ($countries as $country) {

            $country->setAttribute('image', $country->getFirstMediaUrl('flag'));
            $country->makeHidden('media');
        }

        return $countries;
    }

    private function getRegionsWithCities(string $countrySlug): Collection
    {
        $regions = Region::select(['id', 'country_id', 'title_l->' . $this->locale . ' as title'])
            ->whereHas('country', function ($query) use ($countrySlug) {
                $query->whereHas('page', function ($query) use ($countrySlug) {
                    $query->where('slug', $countrySlug);
                });
            })
            ->where('is_common', 0)
            ->orderBy('title')
            ->with([
                'cities' => function (Relation $query) {
                    $query->select([
                        'cities.id', 'region_id',
                        'title_l->' . $this->locale . ' as title'
                    ])
                        ->with([
                            'page' => function ($query) {
                                $query->select([
                                    'pages.id',
                                    'pages.instance_id',
                                    'pages.instance_type',
                                    'pages.slug'
                                ]);
                            }
                        ])
                        ->whereHas('hotels')
                        ->withCount('hotels');
                },
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.instance_id',
                        'pages.instance_type',
                        'pages.slug'
                    ]);
                }
            ])
            ->get();

        if ($regions->isEmpty()) {
            throw new BusinessException('Empty regions');
        }

        return $regions;
    }

    private function getPage(array $data): ?Page
    {
        if ( ! $data['with_page']) {
            return null;
        }

        $page = Page::query();

        $instanceSelect = [
            'id',
            'title_l->' . $this->locale . ' as title'
        ];

        if ( ! empty($data['country'])) {
            $page->where([
                ['slug', $data['country']],
                ['instance_type', Country::class]
            ]);

            $instanceSelect[] = 'continent_id';
        } else {
            $page->where([
                ['slug', $data['continent']],
                ['instance_type', Continent::class]
            ]);
        }

        $page->select([
            'pages.id',
            'pages.instance_type',
            'pages.instance_id',
            'pages.slug',
            'pages.type',
            'pages.heading_title_l->' . $this->locale . ' as heading_title',
            'pages.meta_title_l->' . $this->locale . ' as meta_title',
            'pages.meta_description_l->' . $this->locale . ' as meta_description',
            'pages.content_l->' . $this->locale . ' as content'
        ])
            ->with([
                'instance' => function ($query) use ($instanceSelect) {
                    $query->select($instanceSelect);
                },
                'qa' => function ($query) {
                    $query->select([
                        'question_answers.id',
                        'question_answers.page_id',
                        'question_answers.question_l->' . $this->locale . ' as question',
                        'question_answers.answer_l->' . $this->locale . ' as answer',
                    ]);
                }
            ]);

        $page = $page->first();


        if (empty($page)) {
            throw new BusinessException('Empty page');
        }

        if (empty($page->qa) || $page->qa->isEmpty()) {

            $commonQa = QuestionAnswer::whereNull('page_id')->select([
                'question_answers.id',
                'question_answers.page_id',
                'question_answers.question_l->' . $this->locale . ' as question',
                'question_answers.answer_l->' . $this->locale . ' as answer',
            ])->get();

            $page->setRelation('qa', $commonQa);
        }

        $page->instance->append('breadcrumbs');

        $page->setAttribute('breadcrumbs', $page->instance->breadcrumbs);

        $page->makeHidden('instance');

        return $page;
    }
}
