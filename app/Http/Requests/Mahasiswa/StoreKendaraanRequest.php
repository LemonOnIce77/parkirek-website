<?php

namespace App\Http\Requests\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreKendaraanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) return false;

        // Jika pakai spatie/role system:
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('student');
        }

        // Fallback jika pakai kolom role di users:
        return ($user->role ?? null) === 'student';
    }

    public function rules(): array
    {
        return [
            'jenis_kendaraan' => ['required', 'in:motor,mobil'],
            'plat_no' => ['required', 'string', 'max:20', 'unique:kendaraans,plat_no'],
            'stnk_number' => ['required', 'string', 'max:50'],


            // ✅ foto STNK opsional
            'stnk_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_kendaraan.in' => 'Jenis kendaraan harus motor atau mobil.',
            'plat_no.unique' => 'Plat nomor sudah terdaftar.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normalisasi sederhana
        if ($this->has('plat_no')) {
            $this->merge([
                'plat_no' => Str::upper(trim((string) $this->input('plat_no'))),
            ]);
        }
        if ($this->has('stnk_number')) {
            $this->merge([
                'stnk_number' => trim((string) $this->input('stnk_number')),
            ]);
        }
    }
}
