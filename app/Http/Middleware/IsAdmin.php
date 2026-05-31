<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek: Apakah User Login?
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek: Apakah Role-nya Admin?
        if (Auth::user()->role === 'admin') {
            return $next($request); // Silakan masuk
        }

        // 3. Jika Karyawan nekat masuk halaman Admin:
        // Kembalikan ke Dashboard Karyawan
        return redirect()->route('dashboard'); 
    }
}