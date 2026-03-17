<?php

namespace App\Services;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class PermisoService
{
    protected $arrayPermisos = [
        "ADMINISTRADOR" => [
            "usuarios.paginado",
            "usuarios.index",
            "usuarios.listado",
            "usuarios.create",
            "usuarios.store",
            "usuarios.edit",
            "usuarios.show",
            "usuarios.update",
            "usuarios.destroy",
            "usuarios.password",

            "productos.paginado",
            "productos.index",
            "productos.listado",
            "productos.create",
            "productos.store",
            "productos.edit",
            "productos.show",
            "productos.update",
            "productos.destroy",
            "productos.barras",
            "productos.byCodigo",

            "ventas.paginado",
            "ventas.index",
            "ventas.listado",
            "ventas.create",
            "ventas.store",
            "ventas.edit",
            "ventas.show",
            "ventas.update",
            "ventas.destroy",
            "ventas.historial",
            "ventas.paginadoHistorial",
            "ventas.exportarPDF",

            "configuracions.index",
            "configuracions.create",
            "configuracions.edit",
            "configuracions.update",
            "configuracions.destroy",

            "qrs.index",
            "qrs.create",
            "qrs.edit",
            "qrs.update",
            "qrs.destroy",
            "qrs.getQr",

            // "reportes.usuarios",
            // "reportes.r_usuarios",
            "reportes.ventas",
            "reportes.r_ventas",

        ],
        "VENDEDOR" => [

            "ventas.paginado",
            "ventas.index",
            "ventas.listado",
            "ventas.create",
            "ventas.store",
            "ventas.show",
            "ventas.historial",
            "ventas.paginadoHistorial",
            "ventas.exportarPDF",

            "qrs.index",
            "qrs.getQr",

            "reportes.ventas",
            "reportes.r_ventas",
        ],
    ];


    public function middleWarePostulante()
    {
        return $this->arrayPermisos["POSTULANTE"];
    }

    /**
     * Obtener permisos de usuario logeado
     *
     * @return array
     */
    public function getPermisosUser(): array|string
    {
        $user = Auth::user();
        $permisos = [];
        if ($user) {
            return $this->arrayPermisos[$user->tipo];
        }

        return $permisos;
    }
}
