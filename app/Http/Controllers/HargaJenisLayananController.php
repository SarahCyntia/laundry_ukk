<?php

namespace App\Http\Controllers;

use App\Models\HargaJenisLayanan;
use App\Models\JenisItem;
use App\Models\JenisLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HargaJenisLayananController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = HargaJenisLayanan::with(['jenisLayanan', 'jenisItem'])
            ->when($request->search, function ($query, $search) {
                $query->where('harga', 'like', "%$search%");
                $query->where('jenis_satuan', 'like', "%$search%");
            })
            ->latest()
            ->paginate($per);

        $no = ($data->currentPage() - 1) * $per + 1;
        foreach ($data as $item) {
            $item->no = $no++;
        }

        return response()->json($data);
    }

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'harga' => 'required|numeric|min:0',
            'jenis_satuan' => 'required|string|max:255',
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
            'jenis_item_id' => 'required|exists:jenis_item,id',
        ]);

        $harga = HargaJenisLayanan::create($validated);

        return response()->json([
            'success' => true,
            'data' => $harga
        ]);
    }

    // 🔹 Detail
    public function show(HargaJenisLayanan $hargaJenisLayanan)
    {
        $hargaJenisLayanan->load(['jenisLayanan', 'jenisItem']);
        return response()->json($hargaJenisLayanan);
    }

    // 🔹 Update
    public function update(Request $request, HargaJenisLayanan $harga)
    {
        $validated = $request->validate([
            'harga' => 'required|numeric|min:0',
            'jenis_satuan' => 'required|string|max:255',
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
            'jenis_item_id' => 'required|exists:jenis_item,id',
        ]);

        $harga->update($validated);

        return response()->json([
            'success' => true,
            'data' => $harga
        ]);
    }

    // 🔹 Soft Delete
    public function destroy($id)
    {
        $data = HargaJenisLayanan::findOrFail($id);
        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data harga jenis layanan berhasil dihapus'
        ]);
    }

    // 🔹 Data yang sudah dihapus
    public function trash()
    {
        $data = HargaJenisLayanan::onlyTrashed()
            ->with(['jenisLayanan', 'jenisItem'])
            ->paginate(10)
            ->through(fn($item) => [
                'id' => $item->id,
                'harga' => $item->harga,
                'jenis_satuan' => $item->jenis_satuan,
                'jenis_layanan' => $item->jenisLayanan?->nama_layanan,
                'jenis_item' => $item->jenisItem?->nama_item,
                'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
                'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
            ]);

        return response()->json($data);
    }

    // 🔹 Restore
    public function restore($id)
    {
        HargaJenisLayanan::onlyTrashed()->where('id', $id)->restore();
        return response()->json(['success' => true]);
    }

    // 🔹 Hapus permanen
    public function forceDelete($id)
    {
        HargaJenisLayanan::onlyTrashed()->where('id', $id)->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus permanen'
        ]);
    }

    // 🔹 Dropdown sederhana
    public function list()
    {
        $data = HargaJenisLayanan::select('id', 'jenis_satuan as text')->get();
        return response()->json(['harga_jenis_layanan' => $data]);
    }


}
