<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMitraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan ini 'true' agar request bisa diproses
    }

    public function rules(): array
    {
        return [
            'nama_laundry' => 'required|string|max:500',
            'status_validasi' => 'required|in:menuggu,diterima,ditolak',  
            'alamat_laudry' => 'required|string|max:500',
            'foto_ktp' => 'required|string|max:15',
            'status_toko' => 'required|in:buka,tutup',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'address.required' => 'Alamat harus diisi.',
            'id_card_number.required' => 'Nomor KTP harus diisi.',
            'id_card_number.unique' => 'Nomor KTP sudah terdaftar.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status harus "buka" atau "tutup".'
        ];
    }
}