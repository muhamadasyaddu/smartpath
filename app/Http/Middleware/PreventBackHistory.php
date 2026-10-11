<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        // Simpan status autentikasi sebelum request diproses.
        $wasAuthenticated = Auth::check();

        $response = $next($request);

        // Terapkan header pada halaman sesi terautentikasi,
        // termasuk respons logout jika sebelumnya sudah login.
        if ($wasAuthenticated || Auth::check()) {
            $response->headers->set(
                'Cache-Control',
                'no-store, no-cache, must-revalidate, max-age=0'
            );

            $response->headers->set(
                'Pragma',
                'no-cache'
            );

            $response->headers->set(
                'Expires',
                '0'
            );
        }

        return $response;
    }
}