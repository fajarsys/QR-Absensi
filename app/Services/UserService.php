<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new class instance.
     */
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            // Hash password dilakukan di dalam Service
            'password' => Hash::make($data['password']), 
            'role' => $data['role'],
            'jabatan' => $data['jabatan'],
        ]);
    }

    
    public function updateUser($user, array $data)
    {
        $dataUpdate = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'jabatan' => $data['jabatan'],
        ];

        // Jika password diisi pada form, lakukan hash dan masukkan ke array update
        if (!empty($data['password'])) {
            $dataUpdate['password'] = Hash::make($data['password']);
        }

        return $user->update($dataUpdate);
    }
}
