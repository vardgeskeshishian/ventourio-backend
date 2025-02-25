<?php

namespace App\Services\Web;

use App\Models\Continent;

use Illuminate\Database\Eloquent\Collection;


final class PopularDestinationsService extends WebService
{
    public function index(): Collection
    {
        return Continent::query()->select([
            'id',
            'title_l->' . $this->locale . ' as title'
        ])
        ->with([
            'countries' => function ($query) {
                $query->select([
                    'countries.id',
                    'countries.continent_id',
                    'countries.title_l->' . $this->locale . ' as title',
                    'countries.iso_code',
                ])
                ->withCount('hotels')
                ->with(['page' => function ($query) {
                    $query->select([
                        'pages.id',
                        'pages.slug',
                        'pages.type',
                        'pages.instance_id',
                        'pages.instance_type',
                    ]);
                }]);
            },
            'page' => function ($query) {
                $query->select([
                    'pages.id',
                    'pages.slug',
                    'pages.type',
                    'pages.instance_id',
                    'pages.instance_type',
                ]);
            }
        ])
        ->get();
    }
}
