<?php

namespace App\Services\Web;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

final class CaptchaService extends WebService
{
    public function check(array $data): bool
    {
        $captcha = $data['captcha'] ?? null;
        if (empty($captcha)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('google.captcha.secret'),
                'response' => $captcha,
                'remoteip' => $data['ip'] ?? null,
            ])->throw();
        } catch (RequestException $e) {
            return false;
        }

        $result = $response->json();

        return $result['success'];
    }
}
