<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScholarshipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'superadmin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_gwa_required' => 'nullable|numeric|min:1.00|max:5.00',
            'deadline' => 'nullable|date',
            'max_renewals' => 'nullable|integer|min:1|max:12',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|in:text,number,textarea,select,file,date,email',
            'fields.*.required' => 'nullable',
            'fields.*.options' => 'nullable|string',
        ];
    }
}
