<?php

namespace App\Http\Controllers;

use App\Models\JenisItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JenisItemController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
    {
        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = JenisItem::when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%$search%")
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

    // 🔹 Simpan data baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $jenis = JenisItem::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $jenis
        ]);
    }

    // 🔹 Detail
    public function show(JenisItem $jenisitem)
    {
        return response()->json($jenisitem);
    }

    // 🔹 Update
    public function update(Request $request, JenisItem $jenisitem)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $jenisitem->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $jenisitem
        ]);
    }

    // 🔹 Delete
    public function destroy($id)
{
    $jenis = JenisItem::findOrFail($id); // cari modelnya
    $jenis->delete();                    // hapus model
    return response()->json([
        'success' => true,
        'message' => 'Data jenis item berhasil dihapus'
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
        $data = JenisItem::onlyTrashed()
    ->paginate(10)
    ->through(fn ($item) => [
        'id'         => $item->id,
        'nama'       => $item->nama,
        'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
        'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
    ]);

    return response()->json($data);

    }

    public function restore($id)
{
    JenisItem::onlyTrashed()->where('id', $id)->restore();
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
    JenisItem::onlyTrashed()->where('id', $id)->forceDelete();
    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dihapus permanen'
    ]);
}


    // 🔹 Ambil semua untuk dropdown/list simple
    public function list()
    {
        $data = JenisItem::select('id','nama as text')->get();
        return response()->json(['jenis_item' => $data]);
    }
    public function all()
    {
        Log::info("masuk all");   
        return JenisItem::get();
    }
}
