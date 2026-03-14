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

            "ventas.paginado",
            "ventas.index",
            "ventas.listado",
            "ventas.create",
            "ventas.store",
            "ventas.edit",
            "ventas.show",
            "ventas.update",
            "ventas.destroy",

            "configuracions.index",
            "configuracions.create",
            "configuracions.edit",
            "configuracions.update",
            "configuracions.destroy",

            "reportes.usuarios",
            "reportes.r_usuarios",
            "reportes.productos",
            "reportes.r_productos",

        ],
        "VENDEDOR" => [],
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
