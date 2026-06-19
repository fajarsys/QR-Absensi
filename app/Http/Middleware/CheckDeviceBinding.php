<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckDeviceBinding
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $cookieDevice = $request->cookie('device_id');
            $currentUserAgent = $request->userAgent(); // Ambil Sidik Jari Browser Saat Ini

            $dbDevice = Auth::user()->device_id;
            $dbUserAgent = Auth::user()->user_agent;   // Ambil Sidik Jari Asli dari Database
            // Jika Device ID (Kunci) berbeda, ATAU User-Agent (Wajah) berbeda
            if ($dbDevice && ($cookieDevice !== $dbDevice || $currentUserAgent !== $dbUserAgent)) {
                Auth::logout();
                return redirect('/login')->withErrors(['email' => 'Perangkat atau Browser tidak dikenali. Celah Cookie Theft Diblokir!']);
            }
        }

        return $next($request);   
    }
}
