<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        // Ambil data ringkasan
        $totalMitra = User::where('role', 'mitra')->count();
        $totalCalon = User::where('role', 'calon_mitra')->count();
        $totalPelanggan = User::where('role', 'pelanggan')->count();

        return response()->json([
            'total_mitra' => $totalMitra,
            'total_calon_mitra' => $totalCalon,
            'total_pelanggan' => $totalPelanggan,
        ]);
    }
}
