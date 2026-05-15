<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }
        
        // Cek apakah user adalah admin (is_admin = true)
        if (!auth()->user()->is_admin) {
            abort(403, 'Akses ditolak! Hanya untuk admin.');
        }
        
        return $next($request);
    }
}