<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        "venta_id",
        "modulo",
        "registro_id",
    ];

    protected $appends = ["item"];

    public function getItemAttribute()
    {
        return $this->modulo === "Producto"
            ? $this->producto
            : $this->fardo;
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'registro_id');
    }

    public function fardo()
    {
        return $this->belongsTo(Fardo::class, 'registro_id');
    }
}
