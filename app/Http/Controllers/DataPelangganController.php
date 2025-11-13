<?php

namespace App\Http\Controllers;

use App\Models\datapelanggan;
// use App\Models\datapelanggan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataPelangganController extends Controller
{
    /**
     * Ambil semua data datapelanggan (tanpa paginasi).
     */
    // public function get(Request $request)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'data' => datapelanggan::all()
    //     ]);
    // }

    /**
     * Ambil data datapelanggan dengan paginasi & pencarian.
     */
    public function index(Request $request)
    {

        $mitraId = $request->user()->mitra_id;
Model::where('mitra_id', $mitraId)->get();

        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = datapelanggan::when($request->search, function (Builder $query, string $search) {
                $query->where('nama', 'like', "%$search%")
                      ->orWhere('jenis_kelamina', 'like', "%$search%")
                      ->orWhere('telepon', 'like', "%$search%")
                      ->orWhere('alamat', 'like', "%$search%");
            })
            ->latest()
            ->paginate($per, ['*', DB::raw('@no := @no + 1 AS no')]);
            

        return response()->json($data);
    }

    /**
     * Simpan data datapelanggan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'telepon'       => 'required|string|max:20|unique:data_pelanggan,telepon',
            'alamat'        => 'required|string',
        ]);

        $datapelanggan = datapelanggan::create($validated);

        return response()->json([
            'success'  => true,
            'datapelanggan'=> $datapelanggan
        ]);
    }

    /**
     * Tampilkan detail datapelanggan.
     */
    public function show(datapelanggan $datapelanggan)
    {
        return response()->json([
            // 'datapelanggan' => $datapelanggan
             'datapelanggan'=> ['nama' => $datapelanggan->nama,
                                'jenis_kelamin' =>$datapelanggan->jenis_kelamin,
                                'telepon' =>$datapelanggan->telepon,
                                'alamat' =>$datapelanggan->alamat,
            ],
        ]);
    }

    /**
     * Update data datapelanggan.
     */
    public function update(Request $request, datapelanggan $datapelanggan)
    {
        $validatedData = $request->validated();
        $validatedData['id'] = $request->input('id');
        // $validated = $request->validate([
        //     'nama'          => 'sometimes|required|string|max:255',
        //     'jenis_kelamin' => 'sometimes|required|in:L,P',
        //     'telepon'       => 'sometimes|required|string|max:20|unique:datapelanggan,telepon,' . $datapelanggan->id,
        //     'alamat'        => 'sometimes|required|string',
        // ]);

        // $datapelanggan->update($validated);
         $datapelanggan->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
            // 'password' => $request->password,
            // 'photo' => $validatedData['photo'] ?? $datapelanggan->user->photo,
            // 'photo' => $request->photo,
        ]);

        return response()->json([
            'success'  => true,
            'datapelanggan'=> $datapelanggan
        ]);
    }

    /**
     * Hapus datapelanggan.
     */
    public function destroy(datapelanggan $datapelanggan)
    {
        $datapelanggan->delete();

        return response()->json([
            'success' => true
        ]);
    }

     public function all()
    {
        Log::info("masuk all");   
        return datapelanggan::get();
    }

}
