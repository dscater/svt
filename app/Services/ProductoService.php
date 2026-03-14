<?php

namespace App\Services;

use App\Services\HistorialAccionService;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ProductoService
{

    public function __construct(private  CargarArchivoService $cargarArchivoService) {}

    public function listado(): Collection
    {
        $productos = Producto::select("productos.*")->where("status", 1)->get();
        return $productos;
    }
    /**
     * Lista de productos paginado con filtros
     *
     * @param integer $length
     * @param integer $page
     * @param string $search
     * @param array $columnsSerachLike
     * @param array $columnsFilter
     * @return LengthAwarePaginator
     */
    public function listadoPaginado(int $length, int $page, string $search, array $columnsSerachLike = [], array $columnsFilter = [], array $columnsBetweenFilter = [], array $orderBy = []): LengthAwarePaginator
    {
        $productos = Producto::select("productos.*")->where("status", 1);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $productos->where("productos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $productos->whereBetween("productos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $productos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $productos->orderBy($value[0], $value[1]);
            }
        }


        $productos = $productos->paginate($length, ['*'], 'page', $page);
        return $productos;
    }

    /**
     * Crear producto
     *
     * @param array $datos
     * @return Producto
     */
    public function crear(array $datos): Producto
    {
        $codigo = $this->getCodigoProducto();

        $producto = Producto::create([
            "codigo" => $codigo,
            "nombre" => mb_strtoupper($datos["nombre"]),
            "marca" => mb_strtoupper($datos["marca"]),
            "modelo" => mb_strtoupper($datos["modelo"]),
            "precio" => $datos["precio"],
            "talla" => mb_strtoupper($datos["talla"]),
            "fecha_registro" => date("Y-m-d"),
            "hora_registro" => date("H:i:s"),
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($producto, $datos["foto"]);
        }

        return $producto;
    }

    private function getCodigoProducto(): string
    {
        $ultimoProducto = Producto::orderBy("id", "desc")->first();
        $codigo = "PROD-0001";
        if ($ultimoProducto) {
            $codigo = "PROD-" . str_pad($ultimoProducto->id + 1, 4, "0", STR_PAD_LEFT);
        }
        return $codigo;
    }

    /**
     * Actualizar producto
     *
     * @param array $datos
     * @param Producto $producto
     * @return Producto
     */
    public function actualizar(array $datos, Producto $producto): Producto
    {
        $producto->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "marca" => mb_strtoupper($datos["marca"]),
            "modelo" => mb_strtoupper($datos["modelo"]),
            "precio" => $datos["precio"],
            "talla" => mb_strtoupper($datos["talla"]),
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($producto, $datos["foto"]);
        }

        return $producto;
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return boolean
     */
    public function eliminar(Producto $producto): bool|Exception
    {
        $producto->status = 0;
        $producto->save();

        return true;
    }

    /**
     * Cargar foto
     *
     * @param Producto $producto
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(Producto $producto, UploadedFile $foto): void
    {
        if ($producto->foto) {
            \File::delete(public_path("imgs/productos/" . $producto->foto));
        }

        $nombre = $producto->id . time();
        $producto->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/productos"), $nombre);
        $producto->save();
    }
}
