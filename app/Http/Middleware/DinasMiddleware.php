<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DinasMiddleware
{
    /**
     * Memastikan hanya petugas Dinas
     * yang dapat mengakses area Dinas.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isDinas()) {
            abort(
                403,
                'Akses ditolak. Halaman ini khusus untuk petugas Dinas.'
            );
        }

        return $next($request);
    }
}