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
            $dbDevice = Auth::user()->device_id;

            if ($dbDevice && $cookieDevice !== $dbDevice) {
                Auth::logout();
                return redirect('/login')->withErrors(['email' => 'Perangkat tidak dikenali. Akses ditolak!']);
            }
        }
        return $next($request);
    }
}
