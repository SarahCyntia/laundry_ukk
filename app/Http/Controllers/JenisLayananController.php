<?php

namespace App\Http\Controllers;

use App\Models\JenisLayanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisLayananController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
{
    $mitraId = config('app.mitra_id'); // ambil dari middleware

    $per = $request->per ?? 10;
    $page = $request->page ? $request->page - 1 : 0;

    DB::statement('set @no=0+' . $page * $per);

    $data = JenisLayanan::where('mitra_id', $mitraId)
        ->when($request->search, function ($query, $search) {
            $query->where('nama_layanan', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%");
        })
        ->latest()
        ->paginate($per);

    $no = ($data->currentPage() - 1) * $per + 1;
    foreach ($data as $item) {
        $item->no = $no++;
    }

    return response()->json($data);
}

//     public function index(Request $request)
// {
//     // 🔹 ambil ID mitra dari user yang login
//     $mitraId = $request->user()->mitra_id;

//     $per = $request->per ?? 10;
//     $page = $request->page ? $request->page - 1 : 0;

//     DB::statement('set @no=0+' . $page * $per);

//     // 🔹 filter data berdasarkan mitra_id + pencarian
//     $data = JenisLayanan::where('mitra_id', $mitraId)
//         ->when($request->search, function ($query, $search) {
//             $query->where('nama_layanan', 'like', "%$search%")
//                   ->orWhere('deskripsi', 'like', "%$search%");
//         })
//         ->latest()
//         ->paginate($per);

//     // 🔹 tambahkan nomor urut
//     $no = ($data->currentPage() - 1) * $per + 1;
//     foreach ($data as $item) {
//         $item->no = $no++;
//     }

//     return response()->json($data);
// }


    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $jenis = JenisLayanan::create($validated);

        return response()->json([
            'success' => true,
            'data' => $jenis
        ]);
    }

    // 🔹 Detail
    public function show(JenisLayanan $jenisLayanan)
    {
        return response()->json($jenisLayanan);
    }

    // 🔹 Update
    public function update(Request $request, JenisLayanan $jenis)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $jenislayanan->update($validated);

        return response()->json([
            'success' => true,
            'data' => $jenislayanan
        ]);
    }

    // 🔹 Delete
    public function destroy($id)
    {
        $jenis = JenisLayanan::findOrFail($id); // cari modelnya
        $jenis->delete();                    // hapus model
        return response()->json([
            'success' => true,
            'message' => 'Data jenis Layanan berhasil dihapus'
        ]);
    }

    // public function destroy($id)
    // {
    //     $id->delete();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data jenis item berhasil dihapus'
    //     ]);
    // }

    public function trash()
    {
        $data = JenisLayanan::onlyTrashed()
            ->paginate(10)
            ->through(fn($item) => [
                'id' => $item->id,
                'nama_layanan' => $item->nama_layanan,
                'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
                'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
            ]);

        return response()->json($data);

    }

    public function restore($id)
    {
        JenisLayanan::onlyTrashed()->where('id', $id)->restore();
        return response()->json(['success' => true]);
    }

    //     public function restore($id)
// {
//     JenisItem::onlyTrashed()->where('id', $id)->restore();
//     return response()->json([
//         'success' => true,
//         'message' => 'Data berhasil direstore'
//     ]);
// }


    public function forceDelete($id)
    {
        JenisLayanan::onlyTrashed()->where('id', $id)->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus permanen'
        ]);
    }


    // 🔹 Ambil semua untuk dropdown/list simple
    public function list()
    {
        $data = JenisLayanan::select('id', 'nama as text')->get();
        return response()->json(['jenis_layanan' => $data]);
    }
    public function All()
    {
        return JenisLayanan::get();
    }

}
