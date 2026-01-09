<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;
use App\Models\Mitra;
use App\Models\Order;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Config\Exception\ValidationException;

use function Laravel\Prompts\select;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $per = $request->per ?? 10;
        $page = $request->page ? $request->page - 1 : 0;

        DB::statement('set @no=0+' . $page * $per);
        $data = Pelanggan::select('id', 'alamat');
        $data = Pelanggan::with('user')->select('id', 'user_id', 'alamat') // Tambahkan relasi user
            ->when($request->search, function ($query, $search) {
                $query->where('id', 'like', "%$search%")
                    ->orWhere('alamat', 'like', "%$search%");
            })->latest()->paginate($per);

        $no = ($data->currentPage() - 1) * $per + 1;
        foreach ($data as $item) {
            $item->no = $no++;
        }

        return response()->json($data);
    }

    /**
     * Store a newly created pelanggan
     */
    public function store(StorePelangganRequest $request)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('photo')) {
            if ($pelanggan->user->photo) {
                Storage::disk('public')->delete($pelanggan->user->photo);
            }
            $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
        }

        $pelanggan = Pelanggan::create($validatedData);
        $pelanggan->load('user'); // load relasi user

        return response()->json([
            'success' => true,
            'pelanggan' => [
                'id' => $pelanggan->id,
                'alamat' => $pelanggan->alamat,
                'user' => [
                    'name' => $pelanggan->user->name,
                    'email' => $pelanggan->user->email,
                    'phone' => $pelanggan->user->phone,
                    'photo' => $pelanggan->user->photo,
                ],
            ],
        ]);
    }






public function update(Request $request, $id)
{
    $pelanggan = Pelanggan::with('user')->find($id);

    if (!$pelanggan) {
        return response()->json([
            'success' => false,
            'message' => 'Data pelanggan tidak ditemukan'
        ], 404);
    }

    // $validatedData = $request->validated();
    $validatedData = $request->validate([
    'name'      => 'required|string|max:255',
    'email'     => 'required|email|unique:users,email,' . $pelanggan->user->id,
    'phone'     => 'required|string|max:20',
    'alamat'    => 'nullable|string',
    'kecamatan_id' => 'nullable|integer|exists:kecamatan,id',
    'kode_pos'  => 'nullable|string|max:10',
    'photo'     => 'nullable|image|max:2048'
]);


    // =============================
    // UPDATE FOTO USER
    // =============================
    if ($request->hasFile('photo')) {

        // Hapus foto lama
        if ($pelanggan->user->photo) {
            Storage::disk('public')->delete($pelanggan->user->photo);
        }

        // Upload foto baru
        $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
    }

    // =============================
    // UPDATE DATA USER
    // =============================
    $pelanggan->user->update([
        'name'  => $validatedData['name'],
        'email' => $validatedData['email'],
        'phone' => $validatedData['phone'],
        'photo' => $validatedData['photo'] ?? $pelanggan->user->photo,
    ]);

    // =============================
    // UPDATE DATA PELANGGAN
    // =============================
    $pelanggan->update([
        'alamat' => $validatedData['alamat'],
        'kecamatan_id' => $validatedData['kecamatan_id'],
        'kode_pos' => $validatedData['kode_pos'] ?? null,
    ]);

    // Reload data relasi
    $pelanggan->load('user');

    return response()->json([
        'success' => true,
        'message' => 'Profil berhasil diperbarui',
        'pelanggan' => [
            'id' => $pelanggan->id,
            'alamat' => $pelanggan->alamat,
            'kecamatan_id' => $pelanggan->kecamatan_id,
            'kode_pos' => $pelanggan->kode_pos,
            'user' => [
                'name' => $pelanggan->user->name,
                'email' => $pelanggan->user->email,
                'phone' => $pelanggan->user->phone,
                'photo' => $pelanggan->user->photo,
            ],
        ],
    ]);







}





























    /**
     * Show a specific pelanggan
     */
    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load('user');

        return response()->json([
            // 'pelanggan'=> ['no_hp' => $pelanggan->no_hp],
            'user' => [
                'name' => $pelanggan->user->name,
                'email' => $pelanggan->user->email,
                'phone' => $pelanggan->user->phone,
                'photo' => $pelanggan->user->photo,
                'alamat' => $pelanggan->alamat,
                'kecamatan_id' => $pelanggan->kecamatan_id,
                'kode_pos' => $pelanggan->kode_pos,
                // 'password' => $pengguna->user->password,
            ],
        ]);

    }

    /**
     * Update an existing pelanggan
     */
    //sebelumnya
    // public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan)
    // {
    //     $validatedData = $request->validated();

    //     $validatedData['id'] = $request->input('id');

    //     if ($request->filled('password')) {
    //         $validatedData['password'] = Hash::make($validatedData['password']);
    //     } else {
    //         unset($validatedData['password']);
    //     }

    //     // if ($request->filled('jenis_kendaraan')) {
    //     //     $validatedData['jenis_kendaraan'] = max(1, min(5, $validatedData['jenis_kendaraan']));
    //     // }

    //     if ($request->hasFile('photo')) {
    //         if ($pelanggan->user->photo) {
    //             Storage::disk('public')->delete($pelanggan->user->photo);
    //         }
    //         $validatedData['photo'] = $request->file('photo')->store('photo', 'public');
    //     }


    //     $pelanggan->user->update([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         // 'password' => $request->password,
    //         'photo' => $validatedData['photo'] ?? $pelanggan->user->photo,
    //         // 'photo' => $request->photo,
    //     ]);

    //     $pelanggan->update($validatedData);
    //     return response()->json([
    //         'success' => true,
    //         'pelanggan' => [
    //             // 'id' => $pelanggan->id,
    //             'alamat' => $pelanggan->alamat,
    //         ]
    //     ]);
    // }
    public function get()
    {
        return response()->json([
            'success' => true,
            'data' => Pelanggan::select('id', 'alamat', 'kecamatan_id', 'kode_pos')->get()
        ]);
    }

    public function list()
    {
        $pelanggan = Pelanggan::with('user:id,name')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->user->name,
            ];
        });

        return response()->json([
            'pelanggan' => $pelanggan,
        ]);
    }


    public function destroy(Pelanggan $pelanggan)
    {
        // Hapus foto dari storage jika user memiliki foto
        if ($pelanggan->user && $pelanggan->user->photo) {
            Storage::disk('public')->delete($pelanggan->user->photo);
        }

        // Hapus data user yang terkait
        if ($pelanggan->user) {
            $pelanggan->user->delete();
        }

        // Hapus data pelanggan
        $pelanggan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil dihapus'
        ]);
    }




    public function listMitra()
    {
        return Mitra::select('id', 'nama_laundry', 'alamat_laundry', 'rating', 'harga_per_kilo')
            ->where('status_validasi', 'diterima')
            ->orderBy('nama_laundry')
            ->get();
    }

    public function tambahOrder(Request $request)
    {
        $request->validate([
            'mitra_id' => 'required|exists:mitra,id',
            'berat' => 'required|numeric|min:1'
        ]);

        $mitra = Mitra::find($request->mitra_id);

        $total = $request->berat * $mitra->harga_per_kilo;

        $trx = Transaksi::create([
            'user_id' => auth()->id(),
            'mitra_id' => $mitra->id,
            'berat' => $request->berat,
            'total_harga' => $total,
            'status' => 'menunggu_konfirmasi'
        ]);

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'data' => $trx
        ], 201);
    }

    // List pesanan pelanggan
    public function orderanSaya()
    {
        return Transaksi::with('mitra')
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();
    }

    // Detail pesanan
    public function detail($id)
    {
        return Transaksi::with('mitra')
            ->where('user_id', auth()->id())
            ->findOrFail($id);
    }










public function profile()
{
    try {
        $user = auth()->user()->load('pelanggan', 'roles');

        // Pastikan user punya role
        if (!$user->roles || $user->roles->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki role.'
            ], 403);
        }

        // Ambil nama role pertama
        $role = strtolower($user->roles[0]->name);

        Log::info("User Role:", ['role' => $role]);

        // Hanya pelanggan yang boleh akses
        if ($role !== 'pelanggan') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Anda bukan pelanggan.',
            ], 403);
        }

        // Ambil data pelanggan sesuai user_id
        $pelanggan = Pelanggan::where('user_id', $user->id)->first();

        // Jika pelanggan belum ada → buat baru
        if (!$pelanggan) {
            Log::info("Membuat pelanggan baru untuk user ID " . $user->id);

            $pelanggan = Pelanggan::create([
                'user_id' => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
            ]);
        }

        // Load relasi lengkap
        $pelanggan->load('kecamatan');
        // $pelanggan->load('kecamatan.kota.provinsi');

        return response()->json([
            'success' => true,
            'user' => $user,
            'pelanggan' => $pelanggan
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data profil.',
            'error' => $e->getMessage()
        ], 500);
    }
}



    /**
     * Update profile pelanggan
     */
public function updateProfile(UpdatePelangganRequest $request, $id)
{
    $user = Auth::user();

    // Ambil data pelanggan milik user
    $pelanggan = $user->pelanggan()->where('id', $id)->first();

    if (!$pelanggan) {
        return response()->json([
            'message' => 'Data pelanggan tidak ditemukan'
        ], 404);
    }

    // Ambil data terverifikasi dari FormRequest
    $validated = $request->validated();

    // Update user fields
    $user->update([
        'name'   => $validated['name'],
        'email'  => $validated['email'],
        'phone'  => $validated['phone'],
    ]);

    // Update pelanggan fields
    $pelanggan->update([
        'alamat'       => $validated['alamat'] ?? null,
        'kecamatan_id' => $validated['kecamatan_id'] ?? null,
    ]);

    // Load relasi lengkap
    $pelanggan->load([
        'user',
        'kecamatan'
    ]);

    return response()->json([
        'message' => 'Profil berhasil diperbarui',
        'data'    => $pelanggan
    ]);
}


//    public function updateProfile(Request $request, $id)
// {
//     try {
//         $user = Auth::user();
//         $pelanggan = $user->pelanggan()->where('id', $id)->firstOrFail();

//         // Validasi
//         $validated = $request->validate([
//             'name'       => 'required|string|max:255',
//             'email'      => 'required|email|unique:users,email,' . $user->id,
//             'phone'      => 'required|string|max:20',

//             'alamat'     => 'nullable|string',
//             'kecamatan_id' => 'nullable|exists:kecamatan,id',
//         ]);

//         // Update user
//         $user->update([
//             'name'  => $validated['name'],
//             'email' => $validated['email'],
//             'phone' => $validated['phone'],
//         ]);

//         // Update pelanggan
//         $pelanggan->update([
//             'alamat'       => $validated['alamat'] ?? null,
//             'kecamatan_id' => $validated['kecamatan_id'] ?? null,
//         ]);

//         $pelanggan->load(['user', 'kecamatan.kota.provinsi']);

//         return response()->json([
//             'message' => 'Profil berhasil diperbarui',
//             'data' => $pelanggan
//         ]);

//     } catch (ValidationException $e) {
//         return response()->json([
//             'message' => 'Validasi gagal',
//             'errors' => $e->errors()
//         ], 422);

//     } catch (\Exception $e) {
//         return response()->json([
//             'message' => 'Gagal memperbarui profil',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }


    /**
     * Upload foto profil
     */
public function uploadPhoto(Request $request, $id)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // pastikan user yang login == user yang diupdate
    $user = Auth::user();

    if ($user->id != $id) {
        return response()->json([
            'message' => 'Tidak diizinkan'
        ], 403);
    }

    // hapus foto lama
    if ($user->photo && Storage::disk('public')->exists($user->photo)) {
        Storage::disk('public')->delete($user->photo);
    }

    // simpan foto baru
    $path = $request->file('photo')->store('photos/profile', 'public');

    // update user
    $user->update([
        'photo' => $path
    ]);

    return response()->json([
        'message' => 'Foto berhasil diupdate',
        'photo' => $path
    ]);
}

    /**
     * Ubah password
     */
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|min:6',
            ]);

            // Cek password lama
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'message' => 'Password lama tidak sesuai'
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Password berhasil diubah'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'error' => $e->error()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengubah password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistik pesanan pelanggan
     */
    public function orderStatus()
    {
        try {
            // $user = Auth::user();
            // $pelanggan = $user->pelanggan()->first();
            $pelangganId = auth()->user()->pelanggan->id;
            $pelanggan = Pelanggan::find($pelangganId);

            if (!$pelanggan) {
                return response()->json([
                    'totalOrder' => 0,
                    'selesaiOrder' => 0,
                    'menungguOrder' => 0
                ]);
            }

            // Hitung statistik berdasarkan pesanan
            $totalOrder = $pelanggan->order()->count();
            $selesaiOrder = $pelanggan->order()->where('status', 'selesai')->count();
            $menungguOrder = $pelanggan->order()->whereIn('status', ['menunggu_konfirmasi_mitra','diterima','dicuci','dikeringkan','disetrika','siap_ambil',  'diproses', 'dikirim'])->count();

            return response()->json([
                'totalOrder' => $totalOrder,
                'selesaiOrder' => $selesaiOrder,
                'menungguOrder' => $menungguOrder
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
