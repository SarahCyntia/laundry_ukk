<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Mitralama extends Controller
{
    // 🔹 List + Pencarian + Pagination (untuk admin)
    public function index(Request $request)
    {
        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);

        $data = Mitra::when($request->search, function ($query, $search) {
                $query->where('nama_mitra', 'like', "%$search%")
                      ->orWhere('pemilik', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('no_hp', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%")
                      ->orWhere('password', 'like', "%$search%")
                      ->orWhere('status', 'like', "%$search%");
            })
            ->latest()
            ->paginate($per);

        $no = ($data->currentPage() - 1) * $per + 1;
        foreach ($data as $item) {
            $item->no = $no++;
        }

        return response()->json($data);
    }

    // 🔹 Simpan data baru (admin menambahkan mitra baru)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:100',
            'pemilik'    => 'required|string|max:100',
            'email'      => 'required|email|unique:mitra,email',
            'no_hp'      => 'nullable|string|max:20',
            'alamat'     => 'nullable|string',
            'password'   => 'required|string|min:6',
            'status'     => 'in:aktif,nonaktif'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['status']   = $validated['status'] ?? 'aktif';

        $mitra = Mitra::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $mitra
        ]);
    }

    // 🔹 Detail mitra (admin)
    // public function show(Mitra $mitra)
    // {
    //     return response()->json($mitra);
    // }

    public function show(Mitra $mitra)
    {
        $mitra = Mitra::findOrFail($mitra->id);
        // $riwayat = Riwayat::where('no_resi', $no_resi)->orderBy('created_at')->get();

        return response()->json([
            'mitra' => $mitra,
        ]);
    }

    // 🔹 Update mitra
    public function update(Request $request, Mitra $mitra)
    {
        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:100',
            'pemilik'    => 'required|string|max:100',
            'email'      => 'required|email|unique:mitra,email,' . $mitra->id,
            'no_hp'      => 'nullable|string|max:20',
            'alamat'     => 'nullable|string',
            'password'   => 'nullable|string|min:6',
            'status'     => 'in:aktif,nonaktif',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $mitra->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $mitra
        ]);
    }

    // 🔹 Soft Delete
    public function destroy($id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data mitra berhasil dihapus'
        ]);
    }

    // 🔹 Data yang sudah dihapus (Trash)
    public function trash()
    {
        $data = Mitra::onlyTrashed()
            ->paginate(10)
            ->through(fn ($item) => [
                'id'         => $item->id,
                'nama_mitra' => $item->nama_mitra,
                'email'      => $item->email,
                'created_at' => $item->created_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
                'deleted_at' => $item->deleted_at->timezone('Asia/Jakarta')->format('d F Y H:i'),
            ]);

        return response()->json($data);
    }

    // 🔹 Restore dari trash
    public function restore($id)
    {
        Mitra::onlyTrashed()->where('id', $id)->restore();
        return response()->json(['success' => true]);
    }

    // 🔹 Hapus permanen
    public function forceDelete($id)
    {
        Mitra::onlyTrashed()->where('id', $id)->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'Data mitra berhasil dihapus permanen'
        ]);
    }

    // 🔹 Untuk dropdown / select2
    public function list()
    {
        $data = Mitra::select('id', 'nama_mitra as text')->get();
        return response()->json(['mitra' => $data]);
    }

    // 🔹 Untuk daftar mitra publik (Frontend Vue)
    public function publicList(Request $request)
    {
        Log::info("🔥 API PublicList Mitra dipanggil");

        $data = Mitra::where('status', 'aktif')
            ->when($request->search, function ($query, $search) {
                $query->where('nama_mitra', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%");
            })
            ->select('id', 'nama_mitra', 'alamat', 'no_hp', 'foto')
            ->orderBy('nama_mitra')
            ->get();

        return response()->json(['data' => $data]);
    }

    public function all()
{
    return Mitra::get();
}
}
