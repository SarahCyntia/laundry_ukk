<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMitraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'nama.required' => 'Nama harus diisi.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'email.required' => 'Email harus diisi.',
            'email.unique' => 'Email sudah digunakan oleh kurir lain.',
            'alamat.required' => 'Alamat harus diisi.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
            'foto_ktp.max' => 'Ukuran gambar maksimal 2MB.',
            'status_validasi'=> 'Status harus "menunggu", "diterima" dan "ditolak.',
            'status.toko' => 'Status harus "aktif" atau "nonaktif".',
            'alamat_laundry' => 'Alamat Laundry harus diisi.',
            'nama_laundry' => 'Nama Laundry harus diisi.',
            // 'id_card_number.required' => 'Nomor KTP harus diisi.',
            // 'id_card_number.unique' => 'Nomor KTP sudah digunakan.',
        ];
    }
}