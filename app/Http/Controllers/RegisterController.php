<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // public function registerMitra(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users',
    //         'phone' => 'required|string|max:20',
    //         'password' => 'required|string|min:6|confirmed',
    //         'nama_laundry' => 'required|string|max:255',
    //         'alamat_laundry' => 'required|string',
    //         'role' => 'string|in:pelanggan,mitra,admin',
    //         'status' => 'string|in:pending,aktif,ditolak',
    //     ]);


    //     User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'password' => Hash::make($request->password),
    //         'nama_laundry' => $request->nama_laundry,
    //         'alamat_laundry' => $request->alamat_laundry,
    //         'role' => $request->role ?? 'pelanggan',
    //         'status' => $request->status ?? 'pending',
    //     ]);


    //     return response()->json(['message' => 'Pendaftaran mitra berhasil! Silakan tunggu persetujuan admin.']);
    // }
}
