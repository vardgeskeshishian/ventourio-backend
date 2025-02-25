<?php

namespace App\Services\Admin;

use App\Http\Resources\Admin\ApplicationResource;
use App\Http\Resources\Admin\ContactUsResource;
use App\Models\Application;
use App\Models\ContactUs;

final class ContactUsService
{
    public function index(): array
    {
        $contactUsRequests = ContactUs::orderBy('id', 'desc');

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $contactUsRequests->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $contactUsRequests = $contactUsRequests->take($take)->skip($skip);
        } else {
            $contactUsRequests = $contactUsRequests->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data'    => ContactUsResource::collection($contactUsRequests->get()),
            'pagination' => [
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }
}
