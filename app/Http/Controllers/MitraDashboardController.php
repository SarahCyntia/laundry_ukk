<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MitraDashboardController extends Controller
{
    public function summary(Request $request)
    {
        // ID mitra dari token login
        $mitraId = auth()->user()->mitra_id ?? auth()->id();

        return response()->json([
            "menunggu_konfirmasi" => Order::where('mitra_id', $mitraId)
                ->where('status', 'menunggu_konfirmasi')
                ->count(),

            "diproses" => Order::where('mitra_id', $mitraId)
                ->whereIn('status', ['diproses','dicuci','dikeringkan','disetrika'])
                ->count(),

            "siap_diambil" => Order::where('mitra_id', $mitraId)
                ->where('status', 'siap_diambil')
                ->count(),

            "selesai" => Order::where('mitra_id', $mitraId)
                ->where('status', 'selesai')
                ->whereDate('updated_at', now()->toDateString())
                ->count(),
        ]);
    }
}
