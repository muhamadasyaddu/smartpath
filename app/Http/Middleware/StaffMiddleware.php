<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Akses baca untuk pengguna internal SmartPath.
     *
     * Administrator:
     * - dapat melihat
     * - dapat melakukan tindakan verifikasi
     *
     * Dinas:
     * - dapat melihat
     * - tidak mendapatkan permission administrator
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (
            !$user->isAdmin() &&
            !$user->isDinas()
        ) {
            abort(
                403,
                'Akses ditolak. Halaman ini hanya tersedia untuk pengguna internal SmartPath.'
            );
        }

        return $next($request);
    }
}