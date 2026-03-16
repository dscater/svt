<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class QrRequest extends FormRequest
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
        Log::debug($this);
        return [
            "remitente" => "required",
            "fecha_vencimiento" => "required",
            "qr" => "required",
        ];
    }

    /**
     * Mensajes validacion
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "remitente.required" => "Debes completar este campo",
            "fecha_vencimiento.required" => "Debes completar este campo",
            "qr.required" => "Debes completar este campo",
            "fono.required" => "Debes completar este campo",
            "dir.required" => "Debes completar este campo",
        ];
    }
}
