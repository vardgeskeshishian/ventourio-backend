<?php

namespace App\Services\Admin;

use App\Exceptions\BusinessException;
use App\Mail\SendCodeResetPassword;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use App\Services\MainService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

final class AuthService extends MainService
{
    public function register(array $data): string
    {
        $user = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return $user->createToken("API TOKEN")->plainTextToken;
    }

    /**
     * @throws BusinessException
     */
    public function login(array $data): string
    {
        if(!Auth::guard('admin')->attempt($data)){
            throw new BusinessException(__('auth.failed'));
        }

        $user = Admin::where('email', $data['email'])->firstOrFail();

        return $user->createToken("API TOKEN")->plainTextToken;

    }

    public function sendToEmail(array $data): void
    {
        // Удаляем старые коды
        AdminPasswordReset::where('email', $data['email'])->delete();

        // Сгенерируем новый случайный код
        $data['code'] = mt_rand(100000, 999999);

        // Сахраняем новый код для проверки
        $codeData = AdminPasswordReset::create($data);

        // Отправляем код на почту
        Mail::to($data['email'])->send(new SendCodeResetPassword($codeData->code));
    }

    public function passwordCodeCheck(array $data): bool
    {
        // Находим код в таблице
        $passwordReset = AdminPasswordReset::where('code', $data['code'])->firstOrFail();

        // Если прoшло больше часа удаляем токен
        if ($passwordReset->isExpire()) {
            return false;
        }

        $user = Admin::where('email', $passwordReset->email)->firstOrFail();

        $user->update(['password' => $data['password']]);

        // Удаляем данный код для проверки
        $passwordReset->delete();

        return true;
    }


}
