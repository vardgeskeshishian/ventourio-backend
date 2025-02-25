<?php

namespace App\Services\Web\Filter;

use Illuminate\Database\Eloquent\Builder;

interface FilterServiceInterface
{
    public function __construct(array $filterData);

    public function filter(Builder &$builder);
}
