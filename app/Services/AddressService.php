<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Facades\DB;

class AddressService
{

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAddresses($user)
    {
        return $user->addresses()->get();
    }

    public function store($user, $data)
    {
        return DB::transaction(function () use ($user, $data) {

            if ($data['is_default'] ?? false) {
                $user->addresses()->where('is_default', true)->update([
                    'is_default' => false,
                ]);
            }

            $address = $user->addresses()->create($data);

            return $address;
        });
    }

    public function setDefault($user, $id)
    {
        return DB::transaction(function () use ($user, $id) {

            $address = $user->addresses()
                ->where('id', $id)
                ->firstOrFail();

            $user->addresses()
                ->where('is_default', true)
                ->update([
                    'is_default' => false
                ]);

            $address->update([
                'is_default' => true
            ]);

            return true;
        });
    }
    public function show_address($user, $id)
    {
        $address = $user->addresses()->where('id', $id)->firstOrFail();
        return $address;
    }

    public function updateAddress($user, $data, $id)
    {
        return DB::transaction(function () use ($user, $data, $id) {
            $address = $user->addresses()
                ->where('id', $id)
                ->firstOrFail();
            $address->update($data);
            return $address->fresh();
        });
    }

    public function delete_address ($user , $id){
        return DB::transaction(function () use ($user , $id){
           $address= $user->addresses()->where('id',$id)->firstOrFail();
            $address->delete();
            return true;
        });
    }
}
