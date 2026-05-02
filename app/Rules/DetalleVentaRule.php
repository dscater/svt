<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DetalleVentaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_array($value)) {
            $fail("Debes enviar un array de productos");
            return;
        }

        foreach ($value as $key => $item) {
            if (trim($item["registro_id"]) == '') {
                $fail("No se pudo reconocer el producto de la fila " . ($key + 1));
                return;
            }
        }
    }
}
