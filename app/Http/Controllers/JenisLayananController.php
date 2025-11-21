<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisLayanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JenisLayananController extends Controller
{
    public function index(Request $request)
{
    $per = $request->per ?? 10;
    $page = $request->page ? $request->page - 1 : 0;

    $mitra_id = Auth::user()->mitra->id;

    DB::statement('SET @no = 0 + ' . ($page * $per));

    $data = JenisLayanan::where('mitra_id', $mitra_id)
        ->when($request->search, function ($query, $search) {
            $query->where('nama_layanan', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%");
        })
        ->latest()
        ->paginate(
            $per,
            ['*', DB::raw('@no := @no + 1 AS no')]
        );

    return response()->json($data);
}


    public function store(Request $request)
    {
        $mitra = Auth::user()->mitra;

        if (!$mitra) {
            return response()->json(['message' => 'Anda bukan mitra'], 403);
        }

        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
        ]);

        $layanan = JenisLayanan::create([
            'mitra_id' => $mitra->id,
            'nama_layanan' => $request->nama_layanan,
            'deskripsi' => $request->deskripsi,
            'satuan' => $request->satuan,
            'harga' => $request->harga,
        ]);

        return response()->json($layanan);
    }

    public function show($id)
{
    $mitra = Auth::user()->mitra;

    if (!$mitra) {
        return response()->json(['message' => 'Anda bukan mitra'], 403);
    }

    // Cari layanan milik mitra
    $layanan = JenisLayanan::where('mitra_id', $mitra->id)->find($id);

    if (!$layanan) {
        return response()->json(['message' => 'Layanan tidak ditemukan'], 404);
    }

    return response()->json($layanan);
}

    public function update(Request $request, $id)
    {
        $mitra = Auth::user()->mitra;

        if (!$mitra) {
            return response()->json(['message' => 'Anda bukan mitra'], 403);
        }

        $layanan = JenisLayanan::where('mitra_id', $mitra->id)->findOrFail($id);

        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'harga' => 'required|numeric|min:0',
        ]);

        $layanan->update($request->only(['nama_layanan', 'deskripsi', 'satuan', 'harga']));

        return response()->json($layanan);
    }

    public function destroy($id)
    {
        $mitra = Auth::user()->mitra;

        if (!$mitra) {
            return response()->json(['message' => 'Anda bukan mitra'], 403);
        }

        $layanan = JenisLayanan::where('mitra_id', $mitra->id)->findOrFail($id);

        $layanan->delete();

        return response()->json(['message' => 'Layanan berhasil dihapus']);
    }
}
