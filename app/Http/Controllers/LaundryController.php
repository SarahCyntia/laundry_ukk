<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;

class LaundryController extends Controller
{
    public function cariLaundry(Request $request)
    {
        $request->validate([
            'kecamatan_id' => 'required|exists:kecamatan,id'
        ]);

        $laundries = Mitra::with('kecamatan')
            ->where('kecamatan_id', $request->kecamatan_id)
            ->where('status_toko', true)
            ->get();

        return response()->json($laundries);
    }
}
