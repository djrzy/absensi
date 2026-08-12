<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Jika belum login, tendang ke halaman login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Cek apakah role user saat ini ada di dalam daftar role yang diizinkan
        if (!in_array($request->user()->role, $roles)) {
            // Jika tidak punya akses, kembalikan ke dashboard default masing-masing
            if ($request->user()->role === 'Admin') return redirect('/admin/dashboard');
            if ($request->user()->role === 'Guru') return redirect('/dashboard');
            return redirect('/'); // Wali murid
        }

        return $next($request);
    }
}
