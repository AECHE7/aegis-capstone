<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'scholarship_id' => 'required|exists:scholarships,id',
            'gwa' => 'nullable|numeric|min:1.00|max:5.00',
            'document' => 'nullable|file|mimes:jpeg,png,pdf|max:5120',
        ];

        $scholarshipId = $this->input('scholarship_id');
        if ($scholarshipId) {
            $scholarship = \App\Models\Scholarship::with('fields')->find($scholarshipId);
            if ($scholarship) {
                foreach ($scholarship->fields as $field) {
                    $fieldRule = [];
                    if ($field->is_required) {
                        $fieldRule[] = 'required';
                    } else {
                        $fieldRule[] = 'nullable';
                    }

                    if ($field->field_type === 'number') {
                        $fieldRule[] = 'numeric';
                    } elseif ($field->field_type === 'file') {
                        $fieldRule[] = 'file';
                        $fieldRule[] = 'max:5120';
                        $fieldRule[] = 'mimes:jpeg,png,pdf,doc,docx';
                    } else {
                        $fieldRule[] = 'string';
                    }

                    $rules['custom_fields.' . $field->field_name] = $fieldRule;
                }
            }
        }

        return $rules;
    }
}
