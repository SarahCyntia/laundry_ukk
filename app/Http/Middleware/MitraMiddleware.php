<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MitraMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->mitra_id) {
            // simpan ID mitra ke konfigurasi global
            config(['app.mitra_id' => auth()->user()->mitra_id]);
        }

        return $next($request);
    }
}
