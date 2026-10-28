<?php

namespace App\Http\Requests\InternalApi\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // role:admin sudah dari middleware
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_token' => ['required', 'string', 'max:64'],
        ];
    }
}
