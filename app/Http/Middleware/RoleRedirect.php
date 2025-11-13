<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleRedirect
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        switch ($user->role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'mitra':
                return redirect('/mitra/dashboard');
            case 'pelanggan':
                return redirect('/pelanggan/dashboard');
        }

        return $next($request);
    }
}
