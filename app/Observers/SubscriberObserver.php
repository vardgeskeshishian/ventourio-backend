<?php

namespace App\Observers;

use App\Mail\SubscriberEmailVerifyMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;

class SubscriberObserver
{
    /**
     * Handle the Subscriber "created" event.
     *
     * @param  \App\Models\Subscriber  $subscriber
     * @return void
     */
    public function created(Subscriber $subscriber)
    {
        Mail::to($subscriber->email)
                ->send(new SubscriberEmailVerifyMail($subscriber->verify_token));
    }
}
