<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KecamatanController extends Controller
{
    // 🔹 List + Pencarian + Pagination
    public function index(Request $request)
    {
        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data =Kecamatan  ::when($request->search, function ($query, $search) {
                $query->where('nama', 'like', "%$search%");
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
        ]);

        $kecamatan = Kecamatan ::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $kecamatan
        ]);
    }


    public function get()
{
    return Kecamatan::all();
}
    // 🔹 Detail
    public function show(Kecamatan  $kecamatan )
    {
        return response()->json($kecamatan);
    }

    // 🔹 Update
    public function update(Request $request, Kecamatan $kecamatan)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:100',
        ]);

        $kecamatan->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $kecamatan
        ]);
    }

    // 🔹 Delete
    public function destroy($id)
{
    $kecamatan = Kecamatan::findOrFail($id); // cari modelnya
    $kecamatan->delete();                    // hapus model
    return response()->json([
        'success' => true,
        'message' => 'Data kecamatan$kecamatan item berhasil dihapus'
    ]);
}

    // public function destroy($id)
    // {
    //     $id->delete();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data kecamatan$kecamatan item berhasil dihapus'
    //     ]);
    // }

public function trash()
{
    $data = Kecamatan::onlyTrashed()
        ->paginate(10)
        ->through(function ($item) {
            return [
                'id'   => $item->id,
                'nama' => $item->nama,

                'created_at' => optional($item->created_at)
                    ->timezone('Asia/Jakarta')
                    ->format('d F Y H:i'),

                'deleted_at' => optional($item->deleted_at)
                    ->timezone('Asia/Jakarta')
                    ->format('d F Y H:i'),
            ];
        });

    return response()->json($data);
}


 public function deteksi(Request $request)
    {
        $alamat = strtolower($request->alamat);

        foreach (Kecamatan::all() as $kecamatan) {
            if (str_contains($alamat, strtolower($kecamatan->nama))) {
                return response()->json([
                    'id' => $kecamatan->id,
                    'nama' => $kecamatan->nama
                ]);
            }
        }

        return response()->json(null);
    }
    // public function trash()      {
    //     $data = Kecamatan::onlyTrashed()
    // ->paginate(10)
    // ->through(fn ($item) => [
    //     'id'         => $item->id,
    //     'nama'       => $item->nama,
    //     'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
    //     'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
    // ]);

    // return response()->json($data);

    // }

    public function restore($id)
{
    Kecamatan::onlyTrashed()->where('id', $id)->restore();
    return response()->json(['success' => true]);
}




public function forceDelete($id)
{
    Kecamatan::onlyTrashed()->where('id', $id)->forceDelete();
    return response()->json([
        'success' => true,
        'message' => 'Data berhasil dihapus permanen'
    ]);
}


    // 🔹 Ambil semua untuk dropdown/list simple
    public function list()
    {
        $data = Kecamatan::select('id','nama as text')->get();
        return response()->json(['kecamatan' => $data]);
    }
    public function all()
    {
        Log::info("masuk all");   
        return Kecamatan::get();
    }
}
