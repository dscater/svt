<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qr extends Model
{
    use HasFactory;

    protected $fillable = [
        "qr",
        "remitente",
        "fecha_vencimiento",
    ];

    protected $appends = ["fecha_vencimiento_t", "url_qr"];

    public function getUrlQrAttribute()
    {
        if ($this->qr) {
            return asset("imgs/" . $this->qr);
        }
        return "";
    }

    public function getFechaVencimientoTAttribute()
    {
        return date("d/m/Y", strtotime($this->fecha_vencimiento));
    }
}
