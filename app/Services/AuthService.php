<?php

namespace App\Services;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function authenticate(array $credentials): array
    {
        if (auth::attempt($credentials)) {
            $role=Auth::user()->role;
            $route = $role === 'admin' ? 'admin.dashboard' : 'dashboard' ;
            
            return [
                'success' => true,
                'redirect' => $route
            ];
        }
        return [
            'success' => false
        ];
    }
}
