<?php

namespace App\Http\Requests;

use App\Rules\DetalleVentaRule;
use Illuminate\Foundation\Http\FormRequest;

class VentaStoreRequest extends FormRequest
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
            "tipo_pago" => "required",
            "detalle_ventas" => ["required", "array", "min:1", new DetalleVentaRule()],
        ];
    }
}
