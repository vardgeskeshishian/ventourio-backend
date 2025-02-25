<?php

namespace App\Services\Web;

use App\Exceptions\BusinessException;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

final class UserService extends WebService
{
    public function data(): User
    {
        return auth()->user()->load([
            'bookings',
            'favorites' => function ($query) {
                $query->select([
                    "hotels.id",
                    "hotels.address",
                    "title_l->{$this->locale} as title"
                ])
                    ->with('media')
                    ->with('page');
            },
            'socialAccounts',
            'media'
        ]);
    }

    public function update(array $data, User $user): User
    {
        $this->unsetNotUsed($data);

        if ( ! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['password_updated_at'] = now();
        }

        if ( ! empty($data['avatar'])) {

            try {
                $media = $user->addMedia($data['avatar'])->toMediaCollection('avatar');
            } catch (FileDoesNotExist|FileIsTooBig $e) {
                throw new BusinessException($e->getMessage());
            }

            $user->clearMediaCollectionExcept('avatar', $media);

        } unset($data['avatar']);

        $user->update($data);

        return $user;
    }

    private function unsetNotUsed(array &$data)
    {
        if (empty($data['first_name'])) {
            unset($data['first_name']);
        }

        if (empty($data['last_name'])) {
            unset($data['last_name']);
        }

        if (empty($data['email'])) {
            unset($data['email']);
        }

        if (empty($data['phone'])) {
            unset($data['phone']);
        }

        if (empty($data['country_id'])) {
            unset($data['country_id']);
        }

        if (empty($data['gender'])) {
            unset($data['gender']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }
    }
}
