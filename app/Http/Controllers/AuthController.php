<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cache;
use App\Mail\SendOtpMail; // ✅ ini penting!
use App\Models\Mitra;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function cekStatusMitra($email)
    {
        $user = User::find($email);
        if (!$user) {
            return response()->json(['status' => 'tidak_ditemukan'], 404);
        }

        // $mitra = Mitra::where('name', $user->id)->first();
        $mitra = Mitra::where('user_id', $user->id)->first();

        if (!$mitra) {
            return response()->json(['status' => 'tidak_ditemukan'], 404);
        }

        return response()->json(['status_validasi' => $mitra->status_validasi]);
    }

    //   public function registerMitra(Request $request)
// {
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',     // nama pemilik laundry
//         'email' => 'required|email|unique:users',
//         'phone' => 'required|string|max:20',
//         'password' => 'required|string|min:6|confirmed',
//         'nama_laundry' => 'required|string|max:255',
//         'status_validasi' => 'required|string|max:255',
//         'alamat_laundry' => 'required|string',
//         'foto_ktp' => 'required|string',
//         'status_toko' => 'required|string',
//     ]);

    //     // Simpan ke tabel users
//     $user = User::create([
//         'name' => $validated['name'],
//         'email' => $validated['email'],
//         'phone' => $validated['no_hp'],
//         'password' => Hash::make($validated['password']),
//         'role_id' => '4',      // langsung role mitra
//         'status' => 'pending',  // menunggu validasi admin
//     ]);

    //     // Simpan ke tabel mitra
//     $mitra = Mitra::create([
//         'name'    => $user->id, // relasi user_id / pemilik_id
//         'nama_laundry' => $validated['nama_laundry'],
//         'status_validasi' => 'menunggu', // misalnya enum di tabel mitra
//         'alamat_laundry'     => $validated['alamat_laundry'],
//         'foto_ktp'     => $validated['foto_ktp'],
//         'status_toko' => 'buka', // misalnya enum di tabel mitra
//     ]);

    //     return response()->json([
//         'message' => 'Pendaftaran mitra berhasil! Silakan tunggu persetujuan admin.',
//         'user' => $user,
//         'mitra' => $mitra
//     ], 201);
// }


    public function registerMitra(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'password' => 'required|min:6',
            'nama_laundry' => 'required|string',
            'alamat_laundry' => 'required|string',
            'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // ✅ validasi file
        ]);

        // Simpan user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign role mitra
        $user->assignRole('mitra');

        // ✅ Simpan file KTP ke folder storage/app/public/ktp
        $path = $request->file('foto_ktp')->store('ktp', 'public');
        // $path = $request->file('foto_ktp')->store('ktp', 'public');

        // Simpan data mitra
        $mitra = Mitra::create([
            'user_id' => $user->id,
            'nama_laundry' => $validated['nama_laundry'],
            'alamat_laundry' => $validated['alamat_laundry'],
            'foto_ktp' => $path, // hanya simpan path-nya
            'status_validasi' => 'menunggu',
            'status_toko' => 'buka',
        ]);

        $user->load('roles');

        return response()->json([
            'message' => 'Pendaftaran berhasil, menunggu validasi admin.',
            'user' => $user,
            'mitra' => $mitra,
            'role' => $user->getRoleNames()->first(),
            'foto_url' => asset('storage/' . $path), // ✅ URL akses langsung ke gambar
        ]);
    }

    public function updateStatusMitra(Request $request, $id)
{
    $request->validate([
        'status_validasi' => 'required|in:menunggu,diterima,ditolak',
    ]);

    $mitra = Mitra::findOrFail($id);

    // Update kolom status_validasi
    $mitra->status_validasi = $request->status_validasi;
    $mitra->save();

    return response()->json(['message' => 'Status mitra berhasil diubah']);
}



    // public function updateStatus($id, Request $request)
    // {
    //     $request->validate([
    //         'status_validasi' => 'required|in:menunggu,diterima,ditolak',
    //     ]);

    //     $mitra = Mitra::findOrFail($id);
    //     $mitra->status_validasi = $request->status_validasi;
    //     $mitra->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Status mitra berhasil diperbarui.',
    //         'data' => $mitra
    //     ]);
    // }

    //   public function registerMitra(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:users',
//             'password' => 'required|confirmed|min:6',
//             'phone' => 'required',
//             'nama_laundry' => 'required',
//             'alamat_laundry' => 'required',
//         ]);

    //         $user = User::create([
//             'name' => $validated['name'],
//             'email' => $validated['email'],
//             'phone' => $validated['phone'],
//             'nama_laundry' => $validated['nama_laundry'],
//             'alamat_laundry' => $validated['alamat_laundry'],
//             'password' => Hash::make($validated['password']),
//             'role' => 'calon_mitra', // default role calon mitra
//             'status' => 'pending', // menunggu disetujui admin
//         ]);

    //         return response()->json([
//             'message' => 'Pendaftaran mitra berhasil! Menunggu persetujuan admin.',
//             'user' => $user
//         ]);
//     }


    public function approveMitra($id)
    {
        $user = User::findOrFail($id);
        $user->update(['role' => 'mitra', 'status' => 'aktif']);

        return response()->json(['message' => 'Mitra telah disetujui dan diaktifkan.']);
    }




    public function me()
    {
        return response()->json([
            'user' => auth()->user()
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                'status' => false,
                'message' => 'Email / Password salah!'
            ], 401);
        }

        $user = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'user' => auth()->user(),
            'token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    public function register(Request $request)
    {
        Log::info('Register request: ', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;

        // ✅ Pastikan OTP sudah diverifikasi sebelumnya
        // (bisa juga simpan flag di Cache setelah verify OTP)
        // Di sini kita lanjutkan langsung ke pembuatan user

        // 🔹 Cek apakah user sudah ada
        $existing = User::where('email', $email)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah terdaftar!'
            ], 400);
        }

        // 🔹 Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dibuat. Silakan login.',
            'user' => $user
        ]);
    }



    // public function login(Request $request)
    // {
    //     $validator = Validator::make($request->post(), [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first()
    //         ]);
    //     }

    //     if (!$token = auth()->attempt($validator->validated())) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Email / Password salah!'
    //         ], 401);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'user' => auth()->user(),
    //         'token' => $token
    //     ]);
    // }



    public function getEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $otp = rand(100000, 999999);
        $email = $request->email;

        // Simpan OTP sementara (5 menit)
        Cache::put('otp_' . $email, $otp, now()->addMinutes(5));

        // Kirim email
        Mail::to($email)->send(new SendOtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'Kode OTP berhasil dikirim ke email!'
        ]);
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric'
        ]);

        $email = $request->email;
        $otp = $request->otp;

        $storedOtp = Cache::get('otp_' . $email);

        if (!$storedOtp) {
            return response()->json([
                'status' => false,
                'message' => 'Kode OTP sudah kadaluarsa atau tidak ditemukan.'
            ], 400);
        }

        if ($storedOtp != $otp) {
            return response()->json([
                'status' => false,
                'message' => 'Kode OTP salah!'
            ], 400);
        }

        // Hapus OTP setelah verifikasi sukses
        Cache::forget('otp_' . $email);

        return response()->json([
            'status' => true,
            'message' => 'Verifikasi OTP berhasil!'
        ]);
    }



    public function logout()
    {
        auth()->logout();
        return response()->json(['success' => true]);
    }
}
