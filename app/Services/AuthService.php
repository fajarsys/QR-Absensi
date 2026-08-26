<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthService
{
    public function authenticate(array $credentials): array
    {
        if (auth::attempt($credentials)) {
            $user = Auth::user();
            $role = $user->role;
            $route = $role === 'admin' ? 'admin.dashboard' : 'dashboard';

            $deviceIdToCookie = null;

            // Fitur Kunci Perangkat hanya berlaku untuk karyawan (bukan admin)
            if ($role !== 'admin') {
                if (is_null($user->device_id)) {
                    // Karyawan baru: buat UUID baru dan kunci perangkat
                    $user->device_id = (string) Str::uuid();
                    $user->user_agent = request()->userAgent();
                    $user->save();
                }

                // SELALU tanam cookie setiap login (mencegah bug clear history)
                $deviceIdToCookie = $user->device_id;
            }

            return [
                'success' => true,
                'redirect' => $route,
                'device_id' => $deviceIdToCookie // Berisi UUID baru atau null
            ];
        }

        return [
            'success' => false
        ];
    }
}
