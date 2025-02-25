<?php

namespace App\Services\Web;

use App\Services\MainService;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService extends MainService
{

    public static function send(string $phone, string $code = null)
    {
        try {
            $accountSid   = config('sms_providers.twilio.account_sid');
            $authToken    = config('sms_providers.twilio.auth_token');
            $twilioNumber = config('sms_providers.twilio.twilio_number');

            $client = new Client( $accountSid, $authToken );

            $message = $client->messages->create('+' . $phone, [
                'from' => $twilioNumber,
                'body' => $code
            ]);

            Log::info(json_encode([
                'phone' => $phone,
                'sid' => $message->sid,
            ]));

            if($message->status !== 'sent'){
                throw new \Exception(__('Something went wrong!'));
            }

        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }

    public function generateCode(): int
    {
        return rand(1000, 9999);
    }
}
