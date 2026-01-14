<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cache;
use App\Mail\SendOtpMail; // ✅ ini penting!
use App\Models\Mitra;
use App\Models\MitraPendaftaran;
use App\Models\Pelanggan;
use App\Models\Role;
use App\Models\User;
use Dotenv\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
public function cekStatusMitra($id)
{
    $user = User::where('id', $id)->first();
    if (!$user) {
        return response()->json(['status_validasi' => 'ditolak'], 200);
    }

    $mitra = Mitra::where('user_id', $user->id)->first();

    if (!$mitra) {
        return response()->json(['status_validasi' => 'ditolak'], 200);
    }

    return response()->json(['status_validasi' => $mitra->status_validasi]);
}


        public function index()
    {
        $mitra = Mitra::with('user')
            ->where('status_validasi', 'menunggu')
            ->get();

        return response()->json($mitra);
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

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string|max:15|unique:users,phone',
        'password' => 'required|min:6|confirmed',
        'alamat' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422);
    }

    // Buat user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => bcrypt($request->password),
    ]);

    $user->assignRole('pelanggan');

    // Buat pelanggan
    Pelanggan::create([
        'user_id' => $user->id,
        'alamat' => $request->alamat ?? '-',
    ]);

    $user->load('roles', 'pelanggan');

    return response()->json([
        'success' => true,
        'message' => 'Akun pelanggan berhasil dibuat.',
        'user' => $user
    ]);
}

// public function registerMitra(Request $request)
// {
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|unique:users,email',
//         'phone' => 'required|string|unique:users,phone',
//         'password' => 'required|min:6|confirmed',
//         'nama_laundry' => 'required|string',
//         'alamat_laundry' => 'required|string',
//         'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
//     ]);

//     // Simpan user
//     $user = User::create([
//         'name' => $validated['name'],
//         'email' => $validated['email'],
//         'phone' => $validated['phone'],
//         'password' => Hash::make($validated['password']),
//     ]);

//     $user->assignRole('mitra');

//     // Upload KTP jika ada
//     $path = null;
//     if ($request->hasFile('foto_ktp')) {
//         $path = $request->file('foto_ktp')->store('ktp', 'public');
//     }

//     // Simpan ke tabel mitra
//     $mitra = Mitra::create([
//         'user_id' => $user->id,
//         'nama_laundry' => $validated['nama_laundry'],
//         'alamat_laundry' => $validated['alamat_laundry'],
//         'foto_ktp' => $path,
//         'status_validasi' => 'menunggu',
//         'status_toko' => 'tutup',
//         'jam_buka' => null,
//         'jam_tutup' => null,
//     ]);

//     return response()->json([
//         'message' => 'Pendaftaran mitra berhasil!',
//         'user' => $user,
//         'mitra' => $mitra,
//         'role' => $user->getRoleNames()->first(),
//         'foto_url' => $path ? asset('storage/'.$path) : null,
//     ]);
// }

//sebelumnya

public function registerMitra(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|string',
        'password' => 'required|min:6',
        'nama_laundry' => 'required|string',
        'alamat_laundry' => 'required|string',
        'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'kecamatan_id' => 'required|exists:kecamatan,id',
    ]);

    // Simpan user
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'],
        'password' => Hash::make($validated['password']),
    ]);

    // role mitra
    $user->assignRole('mitra');

    // upload foto KTP
    $path = $request->hasFile('foto_ktp')
        ? $request->file('foto_ktp')->store('ktp', 'public')
        : null;

    // Simpan mitra (status menunggu)
    $mitra = Mitra::create([
        'user_id' => $user->id,
        'nama_laundry' => $validated['nama_laundry'],
        'alamat_laundry' => $validated['alamat_laundry'],
        'foto_ktp' => $path,
        'status_validasi' => 'menunggu', // default
        'status_toko' => 'buka',
         'kecamatan_id' => $validated['kecamatan_id'],
    ]);

    return response()->json([
        'message' => 'Pendaftaran berhasil! Akun menunggu verifikasi admin.',
        'user' => $user,
        'mitra' => $mitra,
        'foto_url' => $path ? asset('storage/' . $path) : null,
    ]);
}
public function verifikasiDiterima($id)
{
    $mitra = Mitra::findOrFail($id);
    $mitra->status_validasi = 'diterima';
    $mitra->save();

    return response()->json(['message' => 'Mitra berhasil diverifikasi!']);
}
public function verifikasiDitolak($id)
{
    $mitra = Mitra::findOrFail($id);

    // Hapus user juga
    $user = $mitra->user;
    
    $mitra->delete();  // hapus data mitra
    $user->delete();   // hapus akun agar tidak bisa login

    return response()->json(['message' => 'Pendaftaran mitra ditolak dan akun dihapus.']);
}


    // public function registerMitra(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email',
    //         'phone' => 'required|string',
    //         'password' => 'required|min:6',
    //         'nama_laundry' => 'required|string',
    //         'alamat_laundry' => 'required|string',
    //         'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // ✅ validasi file
    //     ]);

    //     // Simpan user
    //     $user = User::create([
    //         'name' => $validated['name'],
    //         'email' => $validated['email'],
    //         'phone' => $validated['phone'],
    //         'password' => Hash::make($validated['password']),
    //         // 'mitra_id' => null, // akan diupdate setelah mitra dibuat
    //     ]);

    //     // Assign role mitra
    //     $user->assignRole('mitra');

    //     // ✅ Simpan file KTP ke folder storage/app/public/ktp
    //     $path = $request->file('foto_ktp')->store('ktp', 'public');
    //     // $path = $request->file('foto_ktp')->store('ktp', 'public');

    //     // Simpan data mitra
    //     $mitra = Mitra::create([
    //         'user_id' => $user->id,
    //         'nama_laundry' => $validated['nama_laundry'],
    //         'alamat_laundry' => $validated['alamat_laundry'],
    //         'foto_ktp' => $path, // hanya simpan path-nya
    //         'status_validasi' => 'menunggu',
    //         'status_toko' => 'buka',
    //     ]);

    //     $user->load('roles');

    //     return response()->json([
    //         'message' => 'Pendaftaran berhasil, menunggu validasi admin.',
    //         'user' => $user,
    //         'mitra' => $mitra,
    //         'role' => $user->getRoleNames()->first(),
    //         'foto_url' => asset('storage/' . $path), // ✅ URL akses langsung ke gambar
    //     ]);
    // }

//buat besok
//     public function registerMitra(Request $request)
// {
//     $validated = $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|unique:mitra_pendaftaran,email',
//         'phone' => 'required|string|unique:mitra_pendaftaran,phone',
//         'password' => 'required|min:6|confirmed',
//         'nama_laundry' => 'required|string',
//         'alamat_laundry' => 'required|string',
//         'foto_ktp' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
//     ]);

//     $path = null;
//     if ($request->hasFile('foto_ktp')) {
//         $path = $request->file('foto_ktp')->store('ktp', 'public');
//     }

//     $pendaftaran = \App\Models\MitraPendaftaran::create([
//         'name' => $validated['name'],
//         'email' => $validated['email'],
//         'phone' => $validated['phone'],
//         'password' => Hash::make($validated['password']),
//         'nama_laundry' => $validated['nama_laundry'],
//         'alamat_laundry' => $validated['alamat_laundry'],
//         'foto_ktp' => $path,
//         'status_validasi' => 'menunggu',
//     ]);

//     return response()->json([
//         'message' => 'Pendaftaran berhasil! Menunggu verifikasi admin.',
//         'pendaftaran' => $pendaftaran
//     ]);
// }


//buat besok
public function verifikasi($id, Request $request)
{
    $pendaftar = MitraPendaftaran::findOrFail($id);

    if ($request->status == 'diterima') {

        // Buat user baru
        $user = User::create([
            'name' => $pendaftar->name,
            'email' => $pendaftar->email,
            'phone' => $pendaftar->phone,
            'password' => $pendaftar->password, // sudah hash
        ]);

        $user->assignRole('mitra');

        // Masukkan ke tabel mitra
        Mitra::create([
            'user_id' => $user->id,
            'nama_laundry' => $pendaftar->nama_laundry,
            'alamat_laundry' => $pendaftar->alamat_laundry,
            'foto_ktp' => $pendaftar->foto_ktp,
            'status_validasi' => 'diterima',
            'status_toko' => 'tutup',
            'kecamatan_id' => $pendaftar->kecamatan_id,
        ]);

        $pendaftar->status_validasi = 'diterima';
        $pendaftar->save();

        return response()->json([
            "message" => "Mitra berhasil diverifikasi & dibuatkan akun",
        ]);
    }

    // jika ditolak
    $pendaftar->status_validasi = 'ditolak';
    $pendaftar->save();

    return response()->json([
        "message" => "Pendaftaran mitra ditolak"
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

    //     $user = auth()->user();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login berhasil',
    //         'user' => auth()->user(),
    //         'token' => $token,
    //         'token_type' => 'bearer',
    //     ]);
    // }

    // public function register(Request $request)
    // {
    //     Log::info('Register request: ', $request->all());

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:100',
    //         'email' => 'required|email',
    //         'phone' => 'required|string|max:15',
    //         'password' => 'required|min:6|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'Validasi gagal',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $email = $request->email;

    //     // ✅ Pastikan OTP sudah diverifikasi sebelumnya
    //     // (bisa juga simpan flag di Cache setelah verify OTP)
    //     // Di sini kita lanjutkan langsung ke pembuatan user

    //     // 🔹 Cek apakah user sudah ada
    //     $existing = User::where('email', $email)->first();
    //     if ($existing) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Email sudah terdaftar!'
    //         ], 400);
    //     }

    //     // 🔹 Buat user baru
    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $email,
    //         'phone' => $request->phone,
    //         'password' => bcrypt($request->password),
    //     ]);


    //     $user->assignRole('pelanggan');

    //     // reload roles
    //     $user->load('roles');


    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Akun berhasil dibuat. Silakan login.',
    //         'user' => $user
    //     ]);
    // }




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
        ], 422);
    }

    // Coba login
    if (!$token = auth()->attempt($validator->validated())) {
        return response()->json([
            'status' => false,
            'message' => 'Email / Password salah!'
        ], 401);
    }

    // Ambil user dan relasinya
    $user = auth()->user()->load('roles', 'pelanggan', 'mitra');

    /**
     * 🔥 CEK STATUS VERIFIKASI MITRA
     * Kalau role = mitra dan status_validasi ≠ diterima → tolak login
     */
    if ($user->hasRole('mitra')) {

        // Jika mitra belum dibuat (NULL), langsung tolak
        if (!$user->mitra) {
            return response()->json([
                "status" => false,
                "message" => "Akun mitra belum diproses oleh admin."
            ], 403);
        }

        // Jika status bukan 'diterima'
        if ($user->mitra->status_validasi !== 'diterima') {
            return response()->json([
                "status" => false,
                "message" => "Akun Anda belum diverifikasi admin."
            ], 403);
        }
    }

    // Jika lolos semua → login sukses
    return response()->json([
        'status' => true,
        'message' => 'Login berhasil',
        'user' => $user,
        'role' => $user->getRoleNames()->first(),
        'token' => $token,
        'token_type' => 'bearer',
    ]);
}


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









public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link reset password sudah dikirim ke email Anda.'])
            : response()->json(['message' => 'Email tidak ditemukan atau gagal mengirim link reset password.'], 400);
    }
     public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'min:8',
                'confirmed', // cek otomatis password_confirmation
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password berhasil direset. Silakan login dengan password baru.'])
            : response()->json(['message' => 'Token reset tidak valid atau sudah kedaluwarsa.'], 400);
    }
}
