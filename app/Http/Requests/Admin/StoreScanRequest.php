<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) return false;

        // Tanpa package: asumsi ada kolom users.role = 'admin'
        // (kalau kamu pakai method hasRole sendiri, bisa diganti)
        return ($user->role ?? null) === 'admin';
    }

    public function rules(): array
    {
        return [
            'qr_token' => ['required', 'string', 'max:64'],
            'action'   => ['required', 'in:masuk,keluar'],
        ];
    }

    public function messages(): array
    {
        return [
            'qr_token.required' => 'QR token wajib diisi.',
            'action.required'   => 'Action wajib diisi.',
            'action.in'         => 'Action harus masuk atau keluar.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('qr_token')) {
            $this->merge([
                'qr_token' => trim((string) $this->input('qr_token')),
            ]);
        }

        if ($this->has('action')) {
            $this->merge([
                'action' => Str::lower(trim((string) $this->input('action'))),
            ]);
        }
    }
}
