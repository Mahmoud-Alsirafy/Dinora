<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'full_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:100',
            'city' => 'sometimes|required|string|max:100',
            'area' => 'sometimes|required|string|max:100',
            'street' => 'sometimes|required|string|max:255',
            'building' => 'sometimes|required|string|max:100',

            'floor' => 'sometimes|nullable|string|max:50',
            'apartment' => 'sometimes|nullable|string|max:50',
            'additional_info' => 'sometimes|nullable|string',

            'is_default' => 'sometimes|boolean',
        ];
    }
}
