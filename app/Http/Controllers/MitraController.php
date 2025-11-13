<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMitraRequest;
use App\Http\Requests\UpdateMitraRequest;
use App\Models\Mitra;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MitraController extends Controller
{
    // Ambil semua calon mitra (status pending)
    public function index(Request $request)
    {
        $per  = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Mitra::select('id', 'name', 'email', 'phone', 'nama_laundry', 'status_validasi', 'photo', 'alamat_laundry', 'foto_ktp', 'status_toko');
        $data = Mitra::with('user')->select('id', 'user_id','nama_laundry', 'status_validasi', 'alamat_laundry', 'foto_ktp', 'status_toko') 
            ->when($request->search, function ($query, $search) {
                $query->where('id', 'like', "%$search%")
                    ->orWhere('nama_laundry', 'like', "%$search%")
                    ->orWhere('status_validasi', 'like', "%$search%")
                    ->orWhere('alamat_laundry', 'like', "%$search%")
                    ->orWhere('foto_ktp', 'like', "%$search%")
                    ->orWhere('status_toko', 'like', "%$search%");
            })->latest()->paginate($per);

        $no = ($data->currentPage() - 1) * $per + 1;
        foreach ($data as $item) {
            $item->no = $no++;
        }

        return response()->json($data);
    }


     public function store(StoreMitraRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            if ($mitra->user->photo) {
                Storage::disk('public')->delete($mitra->user->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        }

        $mitra = Mitra::create($validatedData);
        $mitra->load('user'); // load relasi user

        return response()->json([
            'success' => true,
            'mitra' => [
                'id' => $mitra->id,
                'nama_laundry' => $mitra->nama_laundry,
                'status_validasi' => $mitra->status_validasi,
                'alamat_laundry' => $mitra->alamat_laundry,
                'foto_ktp' => $mitra->foto_ktp,
                'status_toko' => $mitra->status_toko,
                'user' => [
                    'name' => $mitra->user->name,
                    'email' => $mitra->user->email,
                    'phone' => $mitra->user->phone,
                    'photo' => $mitra->user->photo,
                ],
            ],
        ]);
    }


     public function show(Mitra $mitra)
    {
        $mitra->load('user');

        return response()->json([
            'user' => [
                'name' => $mitra->user->name,
                'email' => $mitra->user->email,
                'phone' => $mitra->user->phone,
                'photo' => $mitra->user->photo,
                'nama_laundry' => $mitra->nama_laundry,
                'status_validasi' => $mitra->status_validasi,
                'alamat_laundry' => $mitra->alamat_laundry,
                'foto_ktp' => $mitra->foto_ktp,
                'status_toko' => $mitra->status_toko,
            ],
        ]);
    }

    public function update(UpdateMitraRequest $request, Mitra $mitra)
    {
        $validatedData = $request->validated();

        $validatedData['id'] = $request->input('id');

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }


        if ($request->hasFile('photo')) {
            if ($mitra->user->photo) {
                Storage::disk('public')->delete($mitra->user->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        }


        $mitra->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo' => $validatedData['photo'] ?? $mitra->user->photo,
        ]);

        $mitra->update($validatedData);
        return response()->json([
            'success' => true,
            'mitra' => [
                'id' => $mitra->id,
                'nama_laundry' => $mitra->nama_laundry,
                'status_validasi' => $mitra->status_validasi,
                'alamat_laundry' => $mitra->alamat_laundry,
                'foto_ktp' => $mitra->foto_ktp,
                'status_toko' => $mitra->status_toko,
            ]
        ]);
    }

     public function get()
    {
        return response()->json([
            'success' => true,
            'data' => Mitra::select('id', 'nama_laundry', 'status_validasi', 'alamat_laundry', 'foto_ktp', 'status_toko')->get()
        ]);
    }

    public function list()
    {
        $mitra = Mitra::with('user:id,name')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->user->name,
            ];
        });

        return response()->json([
            'mitra' => $mitra,
        ]);
    }


    public function updateStatus(Request $request)
{
    $user = auth()->user();

    if (!$user || !$user->mitra) {
        return response()->json(['message' => 'Mitra tidak ditemukan'], 404);
    }

    $validated = $request->validate([
        'status_toko' => 'required|in:buka,tutup',
    ]);

    $mitra = $user->mitra;
    $mitra->status = $validated['status'];
    $mitra->save();

    return response()->json([
        'message' => 'Status berhasil diperbarui',
        'status' => $mitra->status,
    ]);
}


 public function destroy(Mitra $mitra)
    {
        // Hapus foto dari storage jika user memiliki foto
        if ($mitra->user && $mitra->user->photo) {
            Storage::disk('public')->delete($mitra->user->photo);
        }

        // Hapus data user yang terkait
        if ($mitra->user) {
            $mitra->user->delete();
        }

        // Hapus data mitra
        $mitra->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data mitra berhasil dihapus'
        ]);
    }





    // Setujui mitra
    public function approve($id)
    {
        $user = Mitra::findOrFail($id);

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'User ini bukan calon mitra.'], 400);
        }

        $user->update([
            'role' => 'mitra',
            'status_validasi' => 'diterima',
        ]);

        return response()->json(['message' => 'Mitra telah disetujui dan diaktifkan.']);
    }

    // Tolak mitra
    public function reject($id)
    {
        $user = Mitra::findOrFail($id);

        if ($user->role !== 'mitra') {
            return response()->json(['message' => 'User ini bukan calon mitra.'], 400);
        }

        $user->update([
            'status_validasi' => 'ditolak',
        ]);

        return response()->json(['message' => 'Pendaftaran mitra telah ditolak.']);
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'nama_mitra' => 'required|string|max:100',
    //         'pemilik'    => 'required|string|max:100',
    //         'email'      => 'required|email|unique:mitra,email',
    //         'no_hp'      => 'nullable|string|max:20',
    //         'alamat'     => 'nullable|string',
    //         'password'   => 'required|string|min:6',
    //         'status'     => 'in:aktif,nonaktif'
    //     ]);

    //     $validated['password'] = bcrypt($validated['password']);
    //     $validated['status']   = $validated['status'] ?? 'aktif';

        // $user = User::create([
        //     'name' => $validated['pemilik'],
        //     'email' => $validated['email'],
        //     'phone' => $validated['no_hp'],
        //     'password' => $validated['password'],
        // ]);

        // $mitra = Mitra::create([
        //     'nama_mitra' => $validated['nama_mitra'],
        //     'pemilik'    => $user->id,
        //     'alamat'     => $validated['alamat'],
        //     'status'     => $validated['status'],
        // ]);

    //     return response()->json([
    //         'success' => true,
    //         'data'    => $mitra
    //     ]);
    // }

    // public function destroy(Mitra $mitra)
    // {
    //     if ($mitra->photo) {
    //         Storage::disk('public')->delete($mitra->photo);
    //     }

    //     $mitra->delete();

    //     return response()->json([
    //         'success' => true
    //     ]);
    // }
}
