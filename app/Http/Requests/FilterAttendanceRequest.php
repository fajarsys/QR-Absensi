<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FilterAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // nullable berarti boleh kosong. dan hanya bisa diisi berupa angka 1-12
            'month' => 'nullable|numeric|between:1,12',
            // Wajib berupa angka, minimal 2000
            'year'  => 'nullable|numeric|min:2000'
        ];
    }
}
