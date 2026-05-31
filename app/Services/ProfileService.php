<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Create a new class instance.
     */
    //auth user $user
    public function updateProfile(User $user, array $data) :void
    {
        $user -> name = $data['name'];
        $user ->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        
    }
}
