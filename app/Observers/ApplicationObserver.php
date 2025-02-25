<?php

namespace App\Observers;

use App\Models\Application;
use App\Notifications\ApplicationCreatedNotification;
use Illuminate\Support\Facades\Notification;

class ApplicationObserver
{
    public function created(Application $application): void
    {
        # уведомляем менеджера о заявках
        Notification::route('mail', 'manager@ventourio.com')
            ->notify(new ApplicationCreatedNotification($application));
    }
}
