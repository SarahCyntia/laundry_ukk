<?php

namespace App\Http\Controllers;

use App\Models\LayananPrioritas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananPrioritasController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
    {
        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = LayananPrioritas::when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%$search%")
                      ->orWhere('deskripsi', 'like', "%$search%")
                      ->orWhere('harga', 'like', "%$search%")
                      ->orWhere('prioritas', 'like', "%$search%");
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
            'deskripsi' => 'nullable|string',
            'harga' => 'nullable|numeric',
            'prioritas' => 'nullable|integer',
        ]);

        $layanan = LayananPrioritas::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $layanan
        ]);
    }

    // 🔹 Detail
    public function show(LayananPrioritas $layananprioritas)
    {
        return response()->json($layananprioritas);
    }

    // 🔹 Update
    public function update(Request $request, LayananPrioritas $layananprioritas)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'nullable|double',
            'prioritas' => 'nullable|integer',
        ]);

        $layananprioritas->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $layananprioritas
        ]);
    }

    // 🔹 Delete
    public function destroy($id)
{
    $layanan = LayananPrioritas::findOrFail($id); // cari modelnya
    $layanan->delete();                    // hapus model
    return response()->json([
        'success' => true,
        'message' => 'Data layanan prioritas item berhasil dihapus'
    ]);
}
    public function trash()      {
        $data = LayananPrioritas::onlyTrashed()
    ->paginate(10)
    ->through(fn ($item) => [
        'id'         => $item->id,
        'nama'       => $item->nama,
        'deskripsi'  => $item->deskripsi,
        'harga'      => $item->harga,
        'prioritas'      => $item->prioritas,
        'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
        'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
    ]);

    return response()->json($data);

    }

    public function restore($id)
{
    LayananPrioritas::onlyTrashed()->where('id', $id)->restore();
    return response()->json(['success' => true]);
}


public function forceDelete($id)
{
    LayananPrioritas::onlyTrashed()->where('id', $id)->forceDelete();
    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dihapus permanen'
    ]);
}


    // 🔹 Ambil semua untuk dropdown/list simple
    public function list()
    {
        $data = LayananPrioritas::select('id','nama as text')->get();
        return response()->json(['layanan_prioritas' => $data]);
    }
}
