<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class addressesRequest extends FormRequest
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
            "full_name" => "required|string",
            "phone" => "required|numeric",
            "country" => "required|string",
            "city" => "required|string",
            "area" => "required|string",
            "street" => "required|string",
            "building" => "required|string",
            "floor" => "nullable|string",
            "apartment" => "nullable|string",
            "additional_info" => "nullable|string",
            "is_default" => "sometimes|boolean",
        ];
    }
}