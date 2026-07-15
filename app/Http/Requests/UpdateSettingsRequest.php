<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'app_name' => 'required|string|max:255',
            'university_name' => 'required|string|max:255',
            'ai_fraud_threshold' => 'required|numeric|min:0|max:100',
            'gwa_discrepancy_tolerance' => 'required|numeric|min:0|max:5',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'mfa_enforcement' => 'required|in:all,students,none',
            'auto_approval_enabled' => 'nullable|string|in:0,1',
            'auto_approval_min_confidence' => 'required|numeric|min:0|max:100',
            'auto_approval_max_anomalies' => 'required|integer|min:0',
        ];
    }
}
