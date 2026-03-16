<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "usuario" => "required|min:2",
            "password" => "nullable|min:6",
            "tipo" => "required",
        ];
    }

    /**
     * Mensages validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "usuario.required" => "Este campo es obligatorio",
            "usuario.min" => "Debes ingresar al menos :min caracteres",
            "password.required" => "Este campo es obligatorio",
            "password.min" => "Debes ingresar al menos :min caracteres",
            "tipo.required" => "Este campo es obligatorio",
        ];
    }
}
