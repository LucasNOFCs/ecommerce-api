<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function getProfile(User $user): User
    {
        return $user;
    }

    public function updateProfile(
        User $user,
        array $data
    ): User {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            return $user->refresh();
        });
    }
}
