<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ProductoUpdateRequest extends FormRequest
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
            // "nombre" => "required|unique:productos,nombre," . $this->producto->id,
            "nombre" => "required",
            "foto" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:8048",
            "marca" => "nullable",
            "modelo" => "nullable",
            "precio" => "nullable|decimal:0,2",
            "talla" => "nullable",
        ];
    }

    public function messages(): array
    {
        return [
            "nombre.required" => "El nombre es obligatorio",
            "nombre.unique" => "El nombre ya existe",
            "foto.required" => "La foto es obligatoria",
            "foto.image" => "El archivo debe ser una imagen",
            "foto.mimes" => "La foto debe ser un archivo de tipo: jpeg, png, jpg, gif, svg",
            "foto.max" => "La foto no debe ser mayor a 8MB",
            "precio.decimal" => "El precio debe ser un número decimal con máximo 2 decimales",
        ];
    }
}
