<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminProfileRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'sometimes|string|max:255',
            'birthOfDate' => 'sometimes|date',
            'city' => 'sometimes|string|max:255',
            'profilePhoto' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ];
    }
}
