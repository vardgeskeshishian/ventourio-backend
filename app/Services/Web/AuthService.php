<?php

namespace App\Services\Web;

use App\Events\RegisteredLazy;
use App\Exceptions\BusinessException;
use App\Helpers\PasswordGenerator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class AuthService extends WebService
{
    public function registerAndGetToken(array $data): string
    {
        return $this->register($data)
            ->createToken("API TOKEN")
            ->plainTextToken;
    }

    public function register(array $data): User
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new BusinessException(__('errors.app.user.email_used'));
        }

        return User::create([
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'email' => $data['email'],
            'country_id' => $data['country_id'] ?? null,
            'password' => Hash::make($data['password'])
        ]);
    }

    public function registerLazy(string $email): User
    {
        $password = PasswordGenerator::generate();

        $user = $this->register([
            'email' => $email,
            'password' => $password
        ]);

        RegisteredLazy::dispatch($user, $password);

        return $user;
    }

    public function login(array $data): string
    {
        if(!Auth::attempt($data)){
            throw new BusinessException(__('auth.failed'));
        }

        $user = User::where('email', $data['email'])->firstOrFail();

        return $user->createToken("API TOKEN")->plainTextToken;

    }
}
