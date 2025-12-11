<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
{
    $userId = auth()->id();

    return [
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $userId,
        'phone' => 'required|string|max:20',

        'alamat' => 'nullable|string',
        'kecamatan_id' => 'nullable|exists:kecamatan,id',
    ];
}

    // public function rules(): array
    // {
    //     return [
    //         // 'nama' => 'required|string|max:255',
    //         // 'phone' => 'required|string|max:15',
    //         // 'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //         // 'email' => 'required|email|unique:kurir,email,' . $this->kurir->id,
    //         'alamat' => 'required|string|max:500',
    //         'kode_pos' => 'nullable|string|max:500',
    //         'kecamatan_id' => 'nullable|string|max:500',
    //     ];
    // }

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
            'status.required' => 'Status harus diisi.',
            'status.in' => 'Status harus "aktif" atau "nonaktif".'
            // 'id_card_number.required' => 'Nomor KTP harus diisi.',
            // 'id_card_number.unique' => 'Nomor KTP sudah digunakan.',
        ];
    }
}