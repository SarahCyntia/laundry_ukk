<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pelanggan;
use App\Models\Pegawai;
use App\Models\LayananPrioritas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiLayananController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = Transaksi::with(['pelanggan', 'pegawai', 'layananPrioritas'])
            ->when($request->search, function ($query, $search) {
                $query->where('nota_layanan', 'like', "%$search%")
                    ->orWhere('nota_pelanggan', 'like', "%$search%")
                    ->orWhereHas('pelanggan', fn($q) => $q->where('nama', 'like', "%$search%"));
            })
            ->latest()
            ->paginate($per);

        $no = ($data->currentPage() - 1) * $per + 1;
        foreach ($data as $item) {
            $item->no = $no++;
        }

        return response()->json($data);
    }

    // 🔹 Simpan data transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'layanan_prioritas' => 'nullable|exists:layanan_prioritas,id',
            'total_biaya_layanan' => 'required|numeric|min:0',
            'total_biaya_prior' => 'nullable|numeric|min:0',
            'total_biaya_tambahan' => 'nullable|numeric|min:0',
            'total_bayar_akhir' => 'required|numeric|min:0',
            'jenis_pembayaran' => 'required|string|max:255',
            'bayar' => 'required|numeric|min:0',
            'kembalian' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:255',
        ]);

        $transaksi = Transaksi::create([
            'id' => Str::uuid(),
            'nota_layanan' => 'NOTA-' . now()->format('Ymd-His'),
            // 'nota_pelanggan' => 'PEL-' . strtoupper(Str::random(6)),
            'nota_pelanggan' => 'PEL-' . now()->format('Ymd') . '-' . str_pad(Transaksi::count() + 1, 3, '0', STR_PAD_LEFT),

            'waktu' => now(),
            ...$validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil disimpan',
            'data' => $transaksi
        ]);
    }

    // 🔹 Detail transaksi
    public function show($id)
    {
        $data = Transaksi::with(['dataPelanggan', 'pegawai', 'layananPrioritas'])
            ->findOrFail($id);

        return response()->json($data);
    }

    // 🔹 Update transaksi
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'pegawai_id' => 'nullable|exists:pegawai,id',
            'layanan_prioritas' => 'nullable|exists:layanan_prioritas,id',
            'total_biaya_layanan' => 'required|numeric|min:0',
            'total_biaya_prior' => 'nullable|numeric|min:0',
            'total_biaya_tambahan' => 'nullable|numeric|min:0',
            'total_bayar_akhir' => 'required|numeric|min:0',
            'jenis_pembayaran' => 'required|string|max:255',
            'bayar' => 'required|numeric|min:0',
            'kembalian' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:255',
        ]);

        $transaksi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diperbarui',
            'data' => $transaksi
        ]);
    }

    // 🔹 Hapus transaksi
    public function destroy($id)
    {
        $data = Transaksi::findOrFail($id);
        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus'
        ]);
    }

    // 🔹 List dropdown (untuk form)
    public function list()
    {
        $pelanggan = Pelanggan::select('id', 'nama as text')->get();
        $prioritas = LayananPrioritas::select('id', 'nama_prioritas as text')->get();

        return response()->json([
            'pelanggan' => $pelanggan,
            'layanan_prioritas' => $prioritas,
        ]);
    }



    // 🔹 Data yang sudah dihapus
    public function trash()
    {
        $data = Transaksi::onlyTrashed()
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
        Transaksi::onlyTrashed()->where('id', $id)->restore();
        return response()->json(['success' => true]);
    }

    // 🔹 Hapus permanen
    public function forceDelete($id)
    {
        Transaksi::onlyTrashed()->where('id', $id)->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus permanen'
        ]);
    }

    // 🔹 Dropdown sederhana
    // public function list()
    // {
    //     $data = Transaksi::select('id', 'jenis_satuan as text')->get();
    //     return response()->json(['harga_jenis_layanan' => $data]);
    // }


}
