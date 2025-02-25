<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\ApplicationResource;
use App\Models\Application;

final class ApplicationService
{
    public function index(): array
    {
        $applications = Application::orderBy('id', 'desc');

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $applications->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $applications = $applications->take($take)->skip($skip);
        } else {
            $applications = $applications->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data'    => ApplicationResource::collection($applications->get()),
            'pagination' => [
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
