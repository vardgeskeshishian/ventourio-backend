<?php

namespace App\Services\Web;

use App\Mail\SendCodeResetPassword;
use App\Models\PasswordReset;
use App\Services\MainService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Helpers\TokenGenerator;

final class PasswordResetService extends MainService
{
    public function sendToEmail(array $data): void
    {
        // Удаляем старые коды
        PasswordReset::where('email', $data['email'])->delete();

        // Сгенерируем новый случайный токен
        $data['token'] = TokenGenerator::generate();

        // Сахраняем новый токен для проверки
        $tokenData = PasswordReset::create($data);

        // Отправляем ссылку на почту
        Mail::to($data['email'])->send(new SendCodeResetPassword($tokenData->token));
    }

    public function validateToken(array $data): bool
    {
        // Находим токен в таблице
        $token = PasswordReset::query()
                                ->where('token', $data['token'])
                                ->firstOrFail();

        // Если прoшло больше часа удаляем токен
//        if ($token->isExpire()) {
//            return false;
//        }

        $user = $token->user;

        $user->update([
            'password' => Hash::make($data['password'])
        ]);

        // Удаляем данный токен проверки
        $token->delete();

        return true;
    }
}
