<?php

namespace App\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function createAddress(
        User $user,
        array $data
    ): Address {
        return DB::transaction(function () use ($user, $data) {
            $hasAddresses = $user->addresses()->exists();

            return $user->addresses()->create([
                ...$data,
                'is_default' => ! $hasAddresses,
            ]);
        });
    }

    public function getAddresses(User $user): Collection
    {
        return $user->addresses()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get();
    }

    public function getAddress(
        User $user,
        int $addressId
    ): Address {
        return $user->addresses()
            ->findOrFail($addressId);
    }

    public function updateAddress(
        User $user,
        int $addressId,
        array $data
    ): Address {
        return DB::transaction(function () use ($user, $addressId, $data) {
            $address = $this->getAddress($user, $addressId);

            $address->update($data);

            return $address->refresh();
        });
    }

    public function deleteAddress(
        User $user,
        int $addressId
    ): void {
        DB::transaction(function () use ($user, $addressId) {
            $address = $this->getAddress($user, $addressId);
            $wasDefault = $address->is_default;

            $address->delete();

            if ($wasDefault) {
                $newDefault = $user->addresses()
                    ->orderBy('id')
                    ->first();

                if ($newDefault) {
                    $newDefault->update([
                        'is_default' => true,
                    ]);
                }
            }
        });
    }

    public function setDefaultAddress(
        User $user,
        int $addressId
    ): Address {
        return DB::transaction(function () use ($user, $addressId) {
            $address = $this->getAddress($user, $addressId);

            $user->addresses()
                ->where('id', '!=', $address->id)
                ->update([
                    'is_default' => false,
                ]);

            $address->update([
                'is_default' => true,
            ]);

            return $address->refresh();
        });
    }
}
