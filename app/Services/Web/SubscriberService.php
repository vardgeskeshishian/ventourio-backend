<?php

namespace App\Services\Web;

use App\Models\Subscriber;

final class SubscriberService extends WebService
{
    public function __construct(public $token){
        parent::__construct();
    }

    public function verify(): bool
    {
        $subscriber = Subscriber::unverified()
            ->where('verify_token', $this->token)
            ->firstOrFail();

        $subscriber->update([
            'email_verified_at' => now(),
            'verify_token'      => null
        ]);

        return true;
    }
}
