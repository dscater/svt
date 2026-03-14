<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        "codigo",
        "nombre",
        "foto",
        "marca",
        "modelo",
        "precio",
        "talla",
        "fecha_registro",
        "hora_registro",
        "status",
    ];

    protected $appends = ["fecha_registro_t", "fecha_hora", "url_foto"];

    public function getUrlFotoAttribute()
    {
        if ($this->foto) {
            return asset("imgs/productos/" . $this->foto);
        }
        return null;
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
