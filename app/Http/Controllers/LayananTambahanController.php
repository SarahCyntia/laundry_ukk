<?php

namespace App\Http\Controllers;

use App\Models\JenisItem;
use App\Models\LayananTambahan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LayananTambahanController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
    {
        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = LayananTambahan::when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%$search%")
                      ->orWhere('harga', 'like', "%$search%");
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
            'nama'      => 'required|string|max:100',
            'harga' => 'nullable|string',
        ]);

        $jenis = LayananTambahan::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $jenis
        ]);
    }

    // 🔹 Detail
    public function show(LayananTambahan $layanantambahan)
    {
        return response()->json($layanantambahan);
    }

    // 🔹 Update
    public function update(Request $request,  $layanantambahan)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'harga' => 'nullable|string',
        ]);

        $layanantambahan->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $layanantambahan
        ]);
    }

    // 🔹 Delete
    public function destroy($id)
{
    $jenis = LayananTambahan::findOrFail($id); // cari modelnya
    $jenis->delete();                    // hapus model
    return response()->json([
        'success' => true,
        'message' => 'Data Layanan Tambahan berhasil dihapus'
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

    public function trash()      {
        $data = LayananTambahan::onlyTrashed()
    ->paginate(10)
    ->through(fn ($item) => [
        'id'         => $item->id,
        'nama'       => $item->nama,
        'harga'       => $item->harga,
        'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
        'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
    ]);

    return response()->json($data);

    }

    public function restore($id)
{
    LayananTambahan::onlyTrashed()->where('id', $id)->restore();
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
    LayananTambahan::onlyTrashed()->where('id', $id)->forceDelete();
    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dihapus permanen'
    ]);
}


    // 🔹 Ambil semua untuk dropdown/list simple
    public function list()
    {
        $data = LayananTambahan::select('id','nama as text')->get();
        return response()->json(['layanan_tambahan ' => $data]);
    }
    public function all()
    {
        Log::info("masuk all");   
        return LayananTambahan::get();
    }
}
