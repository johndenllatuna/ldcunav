<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'fullName' => ['required', 'string', 'max:255'],
            'studentId' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'ends_with:@liceo.edu.ph'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
        ];
    }
}