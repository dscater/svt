<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FardoStoreRequest extends FormRequest
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
        $rules = [
            "nombre" => "nullable",
            "tipo_venta" => "required",
        ];

        if ($this->tipo_venta == 'COMPLETO') {
            $rules["precio"] = ["required", "decimal:0,2"];
            $rules["codigo_barras"] = "required";
        }
        return $rules;
    }

    public function messages()
    {
        return [
            "tipo_venta.required" => "Debes completar este campo",
            "precio.required" => "Debes completar este campo",
            "codigo_barras.required" => "Debes completar este campo",
        ];
    }
}
