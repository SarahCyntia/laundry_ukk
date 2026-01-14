<?php

use App\Http\Controllers\DashboardAdminController;
use App\Models\Penjemputan;
use App\Models\JenisLayanan;
use App\Models\LayananPrioritas;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\JemputController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisItemController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\AntarJemputController;
use App\Http\Controllers\Api\LaundryController;
use App\Http\Controllers\PenjemputanController;
use App\Http\Controllers\JenisLayananController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\LayananPrioritasController;
use App\Http\Controllers\LayananTambahanController;
use App\Http\Controllers\Mitra\TransaksiMitraController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\MitraDashboardController;
use App\Http\Controllers\MitraOrderController;
use App\Http\Controllers\MitraPendaftaranController;
use App\Http\Controllers\MitraTransaksiController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PelangganTransaksiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TransaksiLayananController;
use App\Http\Controllers\WilayahController;
use Illuminate\Http\Request;
use App\Http\Controllers\MidtransWebhookController; // if separate
use App\Http\Controllers\PelangganOrderController;
use Illuminate\Support\Facades\Password;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::get('/mitra', function (Request $request) {
//     $status = $request->query('status');
//     $query = \App\Models\User::where('role', 'mitra');

//     if ($status) {
//         $query->where('status', $status);
//     }

//     return response()->json([
//         'success' => true,
//         'data' => $query->latest()->get()
//     ]);
// });



Route::get('kecamatan', [KecamatanController::class, 'get']);
Route::post('kecamatan/store', [KecamatanController::class, 'store']);
Route::delete('kecamatan/{id}', [KecamatanController::class, 'destroy']);
Route::apiResource('kecamatan', KecamatanController::class)->except(['index', 'store']);

Route::post('kecamatan/trash', [KecamatanController::class, 'trash']);
Route::post('kecamatan/{id}/restore', [KecamatanController::class, 'restore']);
Route::delete('kecamatan/{id}/force-delete', [KecamatanController::class, 'forceDelete']);
// Route::post('/kecamatan/all', [KecamatanController::class, 'all']);
Route::post('/kecamatan', [KecamatanController::class, 'index']);
Route::get('/kecamatan', [KecamatanController::class, 'get']);


    // Route::get('/mitra-by-kecamatan/{kecamatan}', [KecamatanController::class, 'mitraByKecamatan']);
    Route::get('/mitra-by-kecamatan/{id}', [MitraController::class, 'mitraByKecamatan']);


// routes/api.php
Route::get('/mitra/me', [MitraController::class, 'me']);


// Route::get('/mitra/status/{id}', [MitraController::class, 'checkStatus']);

// Route::post('/mitra/{id}/status',  [AuthController::class, 'registerMitra']);
Route::post('/mitra/{id}/update-status', [AuthController::class, 'updateStatus']);
Route::post('/mitra/{id}/approve', [AuthController::class, 'approveMitra']);

Route::prefix('auth')->group(function () {
    Route::post('register/get/email/otp', [AuthController::class, 'getEmailOtp']);
    Route::post('/register/get/email/otp', [AuthController::class, 'getEmailOtp']);
    Route::post('/register/check/email/otp', [AuthController::class, 'verifyEmailOtp']);
    // Route::post('auth/register/verify-otp ', [AuthController::class, 'verifyEmailOtp']);
    // Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/register/verify/email/otp', [AuthController::class, 'verifyEmailOtp']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/register-mitra', [AuthController::class, 'registerMitra']);
});
Route::get('/cek-status-mitra/{id}', [AuthController::class, 'cekStatusMitra']);
Route::put('/cek-status-mitra/{id}', [AuthController::class, 'updateStatusMitra']);


Route::post('/mitra/auth', [authController::class, 'index']);



Route::post('/mitra/verifikasi/{id}/diterima', [authController::class, 'verifikasiDiterima']);
Route::post('/mitra/verifikasi/{id}/ditolak', [authController::class, 'verifikasiDitolak']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/mitra/pending', [MitraController::class, 'index']); // daftar calon mitra
    Route::post('/mitra/{id}/approve', [MitraController::class, 'approve']); // setujui
    Route::post('/mitra/{id}/reject', [MitraController::class, 'reject']);   // tolak
});

// Authentication Route
Route::middleware(['auth', 'json'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth');
    Route::delete('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::prefix('setting')->group(function () {
    Route::get('', [SettingController::class, 'index']);
});

// Route::middleware('auth')->group(function () {

// List semua layanan milik mitra
// Route::get('/jenis-layanan', [JenisLayananController::class, 'get']);
Route::post('/jenis-layanan', [JenisLayananController::class, 'index']);

// Simpan layanan baru
Route::post('/jenis-layanan/store', [JenisLayananController::class, 'store']);

// Tampilkan 1 layanan
Route::get('/jenis-layanan/{id}', [JenisLayananController::class, 'show']);
Route::get('/layanan/{id}', [JenisLayananController::class, 'showi']);

// Update layanan
Route::put('/jenis-layanan/{id}', [JenisLayananController::class, 'update']);

// Hapus layanan
Route::delete('/jenis-layanan/{id}', [JenisLayananController::class, 'destroy']);
// });
Route::post('/jenis-layanan/list', [JenisLayananController::class, 'list']);




Route::middleware(['auth', 'verified', 'json'])->group(function () {
    Route::prefix('setting')->middleware('can:setting')->group(function () {
        Route::post('', [SettingController::class, 'update']);
    });

    Route::prefix('master')->group(function () {
        Route::middleware('can:master-user')->group(function () {
            Route::get('users', [UserController::class, 'get']);
            Route::post('users', [UserController::class, 'index']);
            Route::post('users/store', [UserController::class, 'store']);
            Route::apiResource('users', UserController::class)
                ->except(['index', 'store'])->scoped(['user' => 'uuid']);
        });
        // Route::post('register-mitra', [UserController::class, 'registerMitra']);

        Route::middleware('can:master-role')->group(function () {
            Route::get('roles', [RoleController::class, 'get'])->withoutMiddleware('can:master-role');
            Route::post('roles', [RoleController::class, 'index']);
            Route::post('roles/store', [RoleController::class, 'store']);
            Route::apiResource('roles', RoleController::class)
                ->except(['index', 'store']);
        });
    });


    Route::get('/dashboard', [DashboardController::class, 'index']);
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard-data', [DashboardController::class, 'getData']);
    Route::post('/update-status', [DashboardController::class, 'updateStatus']);
    


Route::get('/cek-status/{kode_order}', [OrderController::class, 'cekStatus']);






    // Transaksi Layanan
    Route::get('transaksi_layanan', [TransaksiLayananController::class, 'get']);
    Route::post('transaksi_layanan', [TransaksiLayananController::class, 'index']);
    Route::post('transaksi_layanan/store', [TransaksiLayananController::class, 'store']);
    Route::delete('transaksi_layanan/{id}', [TransaksiLayananController::class, 'destroy']);
    Route::apiResource('transaksi_layanan', TransaksiLayananController::class)->except(['index', 'store']);

    Route::post('transaksi_layanan/trash', [TransaksiLayananController::class, 'trash']);
    Route::post('transaksi_layanan/{id}/restore', [TransaksiLayananController::class, 'restore']);
    Route::delete('transaksi_layanan/{id}/force-delete', [TransaksiLayananController::class, 'forceDelete']);



    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::post('/transaksi', [TransaksiController::class, 'store']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::put('/transaksi/{id}', [TransaksiController::class, 'update']);
    Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy']);







    // Route::get('mitra', [MitraController::class, 'get']);

    Route::post('mitra', [MitraController::class, 'index'])->withoutMiddleware('can:mitra');
    Route::get('/profile', [MitraController::class, 'profile'])->withoutMiddleware('can:mitra');

    Route::post('mitra/store', [MitraController::class, 'store']);
    Route::delete('mitra/{id}', [MitraController::class, 'destroy']);
    Route::apiResource('mitra', MitraController::class)->except(['index', 'store']);

    Route::post('mitra/trash', [MitraController::class, 'trash']);
    Route::post('mitra/{id}/restore', [MitraController::class, 'restore']);
    Route::delete('mitra/{id}/force-delete', [MitraController::class, 'forceDelete']);


    // routes/api.php
    Route::get('/mitra/{id}', [MitraController::class, 'show']);
    // Route::get('mitra', [MitraController::class, 'get']);
    Route::get('mitra', [MitraController::class, 'index']);
    Route::put('/mitra/{id}', [MitraController::class, 'update']);

    // Route::get('/mitra', [MitraController::class, 'publicList']); // untuk halaman pilih laundry
    Route::get('/mitra/all', [MitraController::class, 'all']);
    Route::get('/deteksi-kecamatan', [KecamatanController::class, 'deteksi']);

;

    // webhook midtrans (public)
    Route::post('/midtrans/callback', [PaymentController::class, 'callback']);


    Route::get('/payment/token/{id}', [PaymentController::class, 'getSnapToken']);
Route::post('/midtrans/notification', [PaymentController::class, 'handleNotification']);
Route::post('/manual-update-status', [Paymentcontroller::class, 'manualUpdateStatus']);





Route::get('/dashboard/admin-data', [DashboardAdminController::class, 'index']);

Route::get('/laundry/cari', [LaundryController::class, 'cariLaundry']);



    // Route::prefix('mitra')->middleware('auth:sanctum')->group(function () {

    // Order Masuk
    Route::post('/order-masuk', [OrderController::class, 'index']);
    Route::post('/order/{id}/accept', [MitraOrderController::class, 'accept']);
    Route::post('/order/{id}/reject', [MitraOrderController::class, 'reject']);
    // Route::post('/order-masuk', [OrderController::class, 'orderMitra']);

    // Order Diproses
    Route::post('/order-proses', [OrderController::class, 'index']);
    Route::post('/order/{id}/update-status', [MitraOrderController::class, 'updateStatus']);
    Route::get('/order/siap-diambil', [MitraOrderController::class, 'orderSiapDiambil']);
    Route::post('/order/{id}/selesai', [MitraOrderController::class, 'TandaiSebagaiSelesai']);

    Route::get('/order/selesai', [MitraOrderController::class, 'orderSelesai']);
    // Summary Dashboard
    Route::get('/summary', [MitraDashboardController::class, 'summary']);






    Route::prefix('pelanggan')->group(function () {
        Route::post('/order/{order}/sudah-antar', [PelangganOrderController::class, 'sudahAntar']);
        Route::post('/order/{order}/sudah-ambil', [PelangganOrderController::class, 'sudahAmbil']);
    });




    Route::get('/dashboard', [MitraDashboardController::class, 'index'])->withoutMiddleware('can:mitra');
    Route::get('/notif-order', [MitraOrderController::class, 'notifOrderBaru'])->withoutMiddleware('can:mitra');




    Route::post('/mitra/pelanggan-datang', [MitraOrderController::class, 'pelangganDatang']);

    //    Route::post('order', [OrderController::class, 'store']);
    Route::post('order', [OrderController::class, 'index']);
    // Route::post('order', [OrderController::class, 'show']);
    Route::get('/order/pelanggan', [OrderController::class, 'getOrderPelanggan']);
    Route::post('/order/store', [OrderController::class, 'store']);


    Route::put('/order/{id}', [OrderController::class, 'update']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
    Route::post('/order/{id}/konfirmasi', [OrderController::class, 'konfirmasi']);
    Route::post('/order/{id}/tolak', [OrderController::class, 'tolak']);

    // Route::patch('order/{order}/status', [OrderController::class, 'updateStatus']);
// Route::get('order/{id}/accept', [OrderController::class, 'accept']);
// Route::put('order/{id}/reject', [OrderController::class, 'reject']);



    // Pelanggan melihat detail laundry
    Route::get('/pelanggan/mitra/{id}', [PelangganController::class, 'detail']);

    Route::post('/pelanggan/order', [PelangganController::class, 'tambahOrder']);
    Route::get('/pelanggan/order', [PelangganController::class, 'orderanSaya']);


    Route::prefix('pelanggan')->group(function () {
        Route::get('/status', [PelangganController::class, 'orderStatus']);
    });

    Route::get('/pelanggan/order/{id}', [PelangganController::class, 'detail']);
    Route::get('pelanggan', [PelangganController::class, 'get']);
    // Route::put('/pelanggan/update', [PelangganController::class, 'update']);



    Route::put('/pelanggan/update/{id}', [PelangganController::class, 'update']);


    Route::post('pelanggan', [PelangganController::class, 'index'])->withoutMiddleware('can:pelanggan');
    Route::post('pelanggan/store', [PelangganController::class, 'store']);
    Route::post('pelanggan/show', [PelangganController::class, 'show']);


    Route::get('/pelanggan/profile', [PelangganController::class, 'profile']);
    Route::put('/pelanggan/{id}', [PelangganController::class, 'updateProfile']);
    Route::post('/profile/{id}/upload-photo', [PelangganController::class, 'uploadPhoto']);

    // Password
    Route::post('/user/change-password', [PelangganController::class, 'changePassword']);

    // Statistics
    // Route::get('/pelanggan/order/status', [PelangganController::class, 'orderStatus']);


    Route::put('/order/{id}/status', [OrderController::class, 'updateStatus']);


    Route::post('/payment/{order}', [PaymentController::class, 'pay']);
    Route::post('/midtrans/callback', [PaymentController::class, 'callback']);



});
Route::get('/mitra', [MitraController::class, 'listMitra'])->withoutMiddleware('can:mitra');
Route::get('/mitra/search', [MitraController::class, 'search']);
Route::get('/mitraa/{id}', [MitraController::class, 'showdashboard']);




Route::get('/transaksi/user', [TransaksiController::class, 'getUserTransaksi'])
    ->middleware('auth:sanctum');



    Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
// Route::post('/reset-password', [AuthController::class, 'reset']);

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Password berhasil direset'])
        : response()->json(['message' => 'Token tidak valid'], 422);
});
