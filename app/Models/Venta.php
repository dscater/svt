<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        "tipo_pago",
        "user_id",
        "fecha",
        "hora",
        "status"
    ];

    protected $appends = ["fecha_t", "fecha_hora", "total_productos"];

    public function getTotalProductosAttribute()
    {
        return DetalleVenta::where("venta_id", $this->id)->count();
    }

    public function getFechaTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha));
    }

    public function getFechaHoraAttribute()
    {
        return date("d/m/Y H:i:s", strtotime($this->fecha . " " . $this->hora));
    }

    public function detalle_ventas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
