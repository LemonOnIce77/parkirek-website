<?php

namespace App\Http\Requests\InternalApi\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class FilterViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'   => ['nullable', 'in:OPEN,CLOSED'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
