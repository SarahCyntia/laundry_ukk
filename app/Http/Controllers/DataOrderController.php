<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DataOrderController extends Controller
{
  // app/Http/Controllers/OrderController.php

public function index(Request $request)
{
    $user = auth()->user();
    
    $query = Order::with(['pelanggan.user', 'mitra', 'jenis_layanan']);
    
    // Filter berdasarkan role
    if ($user->role === 'admin') {
        // Admin bisa filter berdasarkan mitra_id dari request
        if ($request->has('mitra_id') && $request->mitra_id) {
            $query->where('mitra_id', $request->mitra_id);
        }
        // Jika tidak ada filter, tampilkan semua
        
    } elseif ($user->role === 'mitra') {
        // Mitra hanya lihat order di laundry mereka
        $query->where('mitra_id', $user->mitra_id);
        
    } elseif ($user->role === 'pelanggan') {
        // Pelanggan hanya lihat order mereka sendiri
        $query->where('pelanggan_id', $user->pelanggan_id);
    }
    
    // Urutkan berdasarkan terbaru
    $query->orderBy('created_at', 'desc');
    
    return $query->paginate($request->per_page ?? 10);
}
}
