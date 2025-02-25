<?php

namespace App\Services\Admin;

use App\Enums\Gender;
use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class UserService
{
    public function index(array $data): array
    {
        $users = User::query();

        $page = $data['page'] ?? 1;
        $take = $data['count'] ?? 8;
        $count = $users->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $users = $users->take($take)->skip($skip);
        } else {
            $users = $users->take($take)->skip(0);
        }

        return [
            'success' => true,
            'data' => UserResource::collection($users->with('country')->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ];
    }

    public function store(array $data): User
    {
        $gender = Gender::tryFrom($data['gender'] ?? null);

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'country_id' => $data['country_id'],
            'password' => Hash::make($data['password']),
            'gender' => $gender
        ]);
    }

    public function update(array $data, User &$user): void
    {
        $gender = Gender::tryFrom($data['gender'] ?? null);

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'country_id' => $data['country_id'],
            'gender' => $gender
        ]);
    }
}
