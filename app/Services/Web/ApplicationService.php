<?php

namespace App\Services\Web;

use App\Enums\ApplicationType;
use App\Models\Application;

final class ApplicationService extends WebService
{
    public function store(array $data): array
    {
        $application = Application::create([
            'type'  => ApplicationType::Default,
            'name'  => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'body'  => $data['body']
        ]);

        return $application->only('id');
    }
}
