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
                // Jika di DB belum terkunci, buat UUID baru untuk dikunci
                if (is_null($user->device_id)) {
                    $newDeviceId = (string) Str::uuid();
                    $user->device_id = $newDeviceId;
                    $user->save();

                    // Kirim token ini agar Controller tahu harus menanam cookie baru
                    $deviceIdToCookie = $newDeviceId;
                }
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
