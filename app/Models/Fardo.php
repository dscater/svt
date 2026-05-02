<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fardo extends Model
{
    use HasFactory;

    protected $fillable = [
        "nombre",
        "tipo_venta", // POR UNIDADES | COMPLETO
        "precio",
        "codigo_barras",
        "stock",
        "fecha_registro",
        "hora_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t", "fecha_hora", "url_foto"];

    public function getUrlFotoAttribute()
    {
        return asset("imgs/productos/producto_default.png");
    }

    public function getFechaRegistroTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_registro));
    }

    public function getFechaHoraAttribute()
    {
        return date("d/m/Y H:i:s", strtotime($this->fecha_registro . " " . $this->hora_registro));
    }
}
