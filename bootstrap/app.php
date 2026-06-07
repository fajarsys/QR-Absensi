<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // Pasang Anti-Cache untuk semua halaman Web
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
        //penguncian perangkat
        $middleware->alias([
            'kunci.perangkat' => \App\Http\Middleware\CheckDeviceBinding::class,
        ]);

        // Menerima semua header proxy (untuk Cloudflare Tunnel / HTTPS)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
