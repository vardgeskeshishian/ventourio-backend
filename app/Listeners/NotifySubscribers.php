<?php

namespace App\Listeners;

use App\Events\InstanceWithSubscribersCreated;
use App\Mail\NotifySubscriberMail;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifySubscribers implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param InstanceWithSubscribersCreated $event
     * @return void
     */
    public function handle(InstanceWithSubscribersCreated $event): void
    {
        $model = $event->model;

        $subscribers = Subscriber::pluck('email');
        if ($subscribers->isEmpty()) {
            return;
        }

        Mail::to($subscribers)
            ->queue(new NotifySubscriberMail($model));
    }
}
