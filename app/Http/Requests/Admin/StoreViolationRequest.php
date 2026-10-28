<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // sudah dilindungi middleware role:admin
        return true;
    }

    public function rules(): array
    {
        return [
            // dipilih dari dropdown kendaraan
            'kendaraan_id' => ['required', 'integer', 'exists:kendaraans,id'],

            // input admin
            'jenis_pelanggaran' => ['required', 'string', 'max:100'],
            'deskripsi'         => ['nullable', 'string'],
            'denda'             => ['nullable', 'integer', 'min:0'],

            // foto bukti
            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:3072', // 3MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'kendaraan_id.required' => 'Kendaraan wajib dipilih.',
            'kendaraan_id.exists'   => 'Kendaraan tidak valid.',

            'jenis_pelanggaran.required' => 'Jenis pelanggaran wajib diisi.',
            'jenis_pelanggaran.max'      => 'Jenis pelanggaran maksimal 100 karakter.',

            'denda.integer' => 'Denda harus berupa angka.',
            'denda.min'     => 'Denda tidak boleh negatif.',

            'foto.image' => 'File bukti harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max'   => 'Ukuran foto maksimal 3MB.',
        ];
    }
}
