<?php

namespace App\Http\Requests\InternalApi\Mahasiswa;

use Illuminate\Foundation\Http\FormRequest;

class FilterScanLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'  => ['nullable', 'in:ACTIVE,DONE'],
            'from'    => ['nullable', 'date_format:Y-m-d'],
            'to'      => ['nullable', 'date_format:Y-m-d'],
            'plat_no' => ['nullable', 'string', 'max:20'],
            'per_page'=> ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
