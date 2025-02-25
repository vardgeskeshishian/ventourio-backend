<?php

namespace App\Services\Web;

use App\Exceptions\BusinessException;
use App\Http\Resources\Web\ArticleResource;
use App\Http\Resources\Web\ArticleSlidesResource;
use App\Http\Resources\Web\DestinationResource;
use App\Models\Article;
use App\Models\City;
use App\Models\Continent;
use App\Models\Country;
use App\Models\Page;
use App\Services\Web\Hotel\BestDealsService;
use App\Services\Web\Hotel\HotelsBlockService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;
use App\Enums\HotelStars;

final class GuideBookService extends WebService
{
    private const SlIDES_LIMIT = 3;

    private const CITIES_LIMIT = 24;

    public function index(): Collection
    {
        $select = [
            'id',
            'title_l->' . $this->locale . ' as title'
        ];

        $defaultContinent = Continent::select($select)
            ->with([

                'countries' => function ($query) {
                    $query->select([
                        'countries.id',
                        'countries.continent_id',
                        'countries.title_l->' . $this->locale . ' as title',
                        'countries.iso_code',
                    ])
                        ->with([
                            'media' => function ($query) {
                                $query->where('collection_name', 'flag');
                            }
                        ]);
                },
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.type',
                        'pages.instance_id',
                        'pages.instance_type',
                        'pages.content_l->' . $this->locale . ' as content',
                        'pages.heading_title_l->' . $this->locale . ' as heading_title',
                        'pages.meta_title_l->' . $this->locale . ' as meta_title',
                        'pages.meta_description_l->' . $this->locale . ' as meta_description',
                    ]);
                }
            ])
            ->first();

        $defaultContinent->page->makeHidden(['instance_type', 'instance_id']);

        if ($defaultContinent->countries->isNotEmpty()) {

            $defaultContinent->countries->each(function (Country $country) {
                $country->setAttribute('flag', $country->getFirstMediaUrl('flag'));
                $country->makeHidden(['media']);
            });
        }

        $continents = Continent::select($select)
            ->where('id', '!=', $defaultContinent->id)
            ->get();

        $continents->prepend($defaultContinent);

        return $continents;
    }

    //TODO change this function place
    public function latestArticles($request, $currentArticle = null, int $limit = 8)
    {

        $articles = Article::select([
            'articles.id',
            'articles.article_category_id',
            'articles.title_l->'.$this->locale.' as title',
            'articles.content_l',
            ])
            ->with([
                'category' => function ($query) {
                    $query->select([
                        'id',
                        'title_l->' . $this->locale . ' as title',
                        'color_hex'
                    ]);
                }
            ])
            ->with([
                'tags' => function ($query) {
                    $query->select([
                        'tags.id',
                        'tags.title_l->'.$this->locale.' as title',
                    ]);
                }
            ])
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.instance_id',
                        'pages.instance_type',
                    ]);
                }
            ])
            ->with('media')
            ->where(function($query) use ($currentArticle){
                if($currentArticle){
                    $query->where('articles.id', '!=', $currentArticle->id);
                }
            })
            ->latest()
            ->take($limit)
            ->get();

        $request->merge([
            'locale' => $this->locale,
        ]);

        return ArticleResource::collection($articles);
    }

    //TODO change this function place
    public function latestArticlesSlides(): AnonymousResourceCollection
    {

        $articles = Article::select([
            'articles.id',
            'articles.article_category_id',
            'articles.title_l->'.$this->locale.' as title',
            'articles.author_l->'.$this->locale.' as author',
            'articles.created_at',
            ])
            ->with([
                'category' => function ($query) {
                    $query->select([
                        'id',
                        'title_l->' . $this->locale . ' as title',
                        'color_hex'
                    ]);
                }
            ])
            ->with([
                'page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.instance_id',
                        'pages.instance_type',
                    ]);
                }
            ])
            ->with('media')
            ->latest()
            ->whereNotNull('parsing_source')
            ->take(self::SlIDES_LIMIT)
            ->get();

        return ArticleSlidesResource::collection($articles);
    }

    public function getContinentCountryCity(): array
    {
        $continents = $this->getContinents();

        $continentSlug = $continents->first()->page->slug;

        $countries = $this->indexCountriesByContinent($continentSlug);

        $cities = $this->indexCitiesByContinent($continentSlug);

        return [
            'continents'  => $continents ?? collect(),
            'countries'   => $countries ?? collect(),
            'cities'      => $cities ?? collect(),
        ];
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

    public function indexCountriesByContinent(string $continentSlug): Collection
    {
        $countries = Country::select([
                'id',
                'continent_id',
                'title_l->' . $this->locale . ' as title',
                'description_l->' . $this->locale . ' as description',
                'geography_l->' . $this->locale . ' as geography',
                'article_l->' . $this->locale . ' as article',
            ])
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

        foreach ($countries as $country) {

            $country->setAttribute('image', $country->getFirstMedia()->getUrl('original'));
            $country->makeHidden('media');
        }

        return $countries;
    }

    public function indexCitiesByContinent(string $continentSlug): Collection
    {

        $cities = City::select([
                'id',
                'title_l->' . $this->locale . ' as title',
                'description_l->' . $this->locale . ' as description',
                'geography_l->' . $this->locale . ' as geography',
                'article_l->' . $this->locale . ' as article',
            ])
            ->whereHas('continent', function ($query) use ($continentSlug) {
                $query->whereHas('page', function ($query) use ($continentSlug) {
                    $query->where('slug', $continentSlug);
                });
            })
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
            ->whereHas('defaultMedia')
            ->withCount('sights')
            ->take(self::CITIES_LIMIT)
            ->get();

        foreach ($cities as $city) {

            $city->setAttribute('image', $city->getFirstMedia()->getUrl('original'));
            $city->makeHidden('media');
        }

        return $cities;
    }

    public function indexCountries(array $data): SupportCollection
    {
        $countriesQuery = Country::query()
            ->select([
                'id',
                'title_l->' . $this->locale . ' as title',
                'description_l->' . $this->locale . ' as description',
                'geography_l->' . $this->locale . ' as geography',
                'article_l->' . $this->locale . ' as article',
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
            ->orderBy('title');

        if ( ! empty($data['search'])) {
            $countriesQuery->whereRaw("LOWER(title_l->'$.{$this->locale}') like ?", '%'.strtolower($data['search']).'%');
        }

        $countries = $countriesQuery->get();

        if ($countries->isEmpty()) {
            throw new BusinessException(__('errors.app.country.search.not_found'));
        }

        return $countries->groupBy(function (Country $country) {
            return Str::upper(Str::limit($country->title, 1, ''));
        });
    }

    public function indexCities(array $data): SupportCollection
    {
        $citiesQuery = City::select([
                'id',
                'title_l->' . $this->locale . ' as title',
                'description_l->' . $this->locale . ' as description',
                'geography_l->' . $this->locale . ' as geography',
                'article_l->' . $this->locale . ' as article',
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
            ->orderBy('title');

        if ( ! empty($data['search'])) {
            $citiesQuery->whereRaw("LOWER(title_l->'$.{$this->locale}') like ?", '%'.strtolower($data['search']).'%');
        }

        $cities = $citiesQuery->get();

        if ($cities->isEmpty()) {
            throw new BusinessException(__('errors.app.city.search.not_found'));
        }

        return $cities->groupBy(function (City $city) {
            return Str::upper(Str::limit($city->title, 1, ''));
        });
    }

    public function destination($request, Page $page)
    {
        $destination = Page::query()->select([
            'pages.id',
            'pages.instance_id',
            'pages.instance_type',
            'pages.slug',
            'pages.heading_title_l->' . $this->locale . ' as heading_title',
            'pages.meta_title_l->' . $this->locale . ' as meta_title',
            'pages.meta_description_l->' . $this->locale . ' as meta_description'
        ])
        ->with([
            'instance' => function ($query) {
                $query->select([
                    'id',
                    'geo',
                    'title_l->' . $this->locale . ' as title',
                    'description_l->' . $this->locale . ' as description',
                    'geography_l->' . $this->locale . ' as geography',
                    'article_l->' . $this->locale . ' as article',
                ])
                ->with('media');
            },
        ])
        ->findOrFail($page->id);

        $destination->instance->append('breadcrumbs');

        $destination->setAttribute('breadcrumbs', $page->instance->breadcrumbs);

        $continent = $page->instance->continent;

        $continentSlug = $continent->page->slug;

        $countries = $this->indexCountriesByContinent($continentSlug);

        $instanceClassName = class_basename($page->instance);

        $instanceId = $page->instance->id;

        $isCity = ($instanceClassName === 'City');

        if (!$isCity) {
            $cities = $this->getCities($page->slug);
        }

        $cityId = $isCity ? $instanceId : $cities->first()->id;

        return [
            'stars'  => $this->getStars() ?? collect(),
            'current_continent'  => $continentSlug ?? '',
            'continents'  => $this->getContinents() ?? collect(),
            'countries'   => $countries ?? collect(),
            'destination' => new DestinationResource($destination),
            'hotels' => HotelsBlockService::byCity($cityId, $this->locale, $this->currency),
            'latestArticlesSlides' => $this->latestArticlesSlides(),
            'latestArticles' => $this->latestArticles($request),
        ];

    }

    public function getCities(?string $countrySlug): Collection
    {
        $cities = City::query()
            ->select(['id', 'title_l->' . $this->locale . ' as title'])
            ->whereHas('districts', function ($query) {
                $query->whereHas('hotels', function ($query) {
                    $query->has('page');
                });
            });

        if ($countrySlug) {
            $cities->whereHas('region', function ($query) use ($countrySlug) {
                $query->whereHas('country', function ($query) use ($countrySlug) {
                    $query->whereHas('page', function ($query) use ($countrySlug) {
                        $query->where('slug', $countrySlug);
                    });
                });
            });
        }

        $cities = $cities->get();

        if ($cities->isEmpty()) {
            throw new BusinessException('No results');
        }

        return $cities;
    }

    public function getPageBySlug(Page $page): Model
    {
        return Page::query()->select([
            'pages.id',
            'pages.instance_id',
            'pages.instance_type',
            'pages.slug',
            'pages.heading_title_l->' . $this->locale . ' as heading_title',
            'pages.meta_title_l->' . $this->locale . ' as meta_title',
            'pages.meta_description_l->' . $this->locale . ' as meta_description'
        ])
        ->with([
            'instance' => function ($query) {
                $query->select([
                    'id',
                    'geo',
                    'title_l->' . $this->locale . ' as title',
                    'description_l->' . $this->locale . ' as description',
                    'geography_l->' . $this->locale . ' as geography',
                    'article_l->' . $this->locale . ' as article',
                ]);
            },
        ])
        ->findOrFail($page->id);
    }

    public function filterHotelsByStar($request, $page)
    {
        $instance = $page->instance;

        $star = $request->query('star');

        $className = 'by'. class_basename($instance);

        if(!method_exists(HotelsBlockService::class, $className))
        {
            throw new \Error(__('Undefined instance!'));
        }

        return HotelsBlockService::$className($instance->id, $this->locale, $this->currency,$star);
    }

    private function getStars(): array
    {
        $stars = HotelStars::cases();

        $result = [];

        foreach ($stars as $star) {

            $value = $star->value;
            $result[] = [
                'title' => Str::lower($star->name),
                'value' => $value,
            ];
        }

        return $result;
    }


}
