<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AuthloginRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Tampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(AuthloginRequest $request, AuthService $service)
    {
        // 1. Jalankan proses autentikasi via Service
        $hasil = $service->authenticate($request->validated());

        if ($hasil['success']) {
            $request->session()->regenerate();

            // 2. Siapkan object redirect ke route tujuannya
            $response = redirect()->route($hasil['redirect']);

            // 3. JIKA service menginstruksikan untuk menanam cookie baru (karyawan baru)
            if (!empty($hasil['device_id'])) {
                // Tanam cookie 'device_id' dengan masa aktif 1 tahun (525600 menit)
                $response->withCookie(cookie('device_id', $hasil['device_id'], 525600));
            }

            return $response;
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
