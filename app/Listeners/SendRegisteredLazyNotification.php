<?php

namespace App\Listeners;

use App\Events\RegisteredLazy;
use App\Mail\RegisteredLazyMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendRegisteredLazyNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RegisteredLazy  $event
     * @return void
     */
    public function handle(RegisteredLazy $event): void
    {
        $email = $event->user->email;
        $generatedPassword = $event->generatedPassword;

        if (empty($email)) {
            return;
        }

        Mail::to($email)
            ->queue(new RegisteredLazyMail($email, $generatedPassword));
    }
}
