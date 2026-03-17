<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Services\HistorialAccionService;
use App\Models\Venta;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VentaService
{

    public function __construct(private  CargarArchivoService $cargarArchivoService) {}

    public function listado(): Collection
    {
        $ventas = Venta::select("ventas.*")->where("status", 1)->get();
        return $ventas;
    }
    /**
     * Lista de ventas paginado con filtros
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
        $ventas = DetalleVenta::with(["venta", "producto"])
            ->whereHas("venta", function ($query) {
                $query->where("status", 1);
            })
            ->join("ventas", "ventas.id", "detalle_ventas.venta_id")
            ->join("productos", "productos.id", "detalle_ventas.producto_id");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $ventas->where("$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $ventas->whereBetween("$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $ventas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $ventas->orderBy($value[0], $value[1]);
            }
        }


        $ventas = $ventas->paginate($length, ['*'], 'page', $page);
        return $ventas;
    }

    public function listadoPaginadoHistorial(
        string $search,
        array $columnsSerachLike = [],
        array $columnsFilter = [],
        array $columnsBetweenFilter = [],
        $fecha_ini = null,
        $fecha_fin = null,
        $modelo = "",
        $usuario = ""
    ) {

        $ventas = DetalleVenta::with(["venta.user", "producto"])
            ->select(
                "detalle_ventas.*",
                "ventas.fecha",
                "productos.nombre as producto_nombre",
                "users.usuario",
                DB::raw("ROW_NUMBER() OVER (ORDER BY detalle_ventas.id DESC) as nro_fila")
            )
            ->whereHas("venta", function ($query) {
                $query->where("status", 1);
            })
            ->join("ventas", "ventas.id", "detalle_ventas.venta_id")
            ->join("users", "users.id", "ventas.user_id")
            ->join("productos", "productos.id", "detalle_ventas.producto_id");

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $ventas->where($key, $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $ventas->whereBetween($key, $value);
            }
        }

        // Búsqueda LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $ventas->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere($col, "LIKE", "%$search%");
                }
            });
        }

        if ($fecha_ini && $fecha_fin) {
            $ventas->whereBetween("ventas.fecha", [$fecha_ini, $fecha_fin]);
        }

        if ($modelo) {
            $ventas->where("productos.modelo", "LIKE", "%$modelo%");
        }

        if ($usuario) {
            $ventas->where("users.usuario", "LIKE", "%$usuario%");
        }

        return $ventas->get();
    }

    /**
     * Crear venta
     *
     * @param array $datos
     * @return Venta
     */
    public function crear(array $datos): Venta
    {
        $venta = Venta::create([
            "tipo_pago" => mb_strtoupper($datos["tipo_pago"]),
            "user_id" => Auth::user()->id,
            "fecha" => date("Y-m-d"),
            "hora" => date("H:i:s"),
        ]);

        foreach ($datos["detalle_ventas"] as $item) {
            $venta->detalle_ventas()->create([
                "producto_id" => $item["producto_id"],
            ]);

            // cambiar estado del producto
            $producto = Producto::findOrFail($item["producto_id"]);
            $producto->status = 2;
            $producto->save();
        }

        return $venta;
    }

    /**
     * Actualizar venta
     *
     * @param array $datos
     * @param Venta $venta
     * @return Venta
     */
    public function actualizar(array $datos, Venta $venta): Venta
    {
        $venta->update([
            "nombre" => mb_strtoupper($datos["nombre"]),
            "marca" => mb_strtoupper($datos["marca"]),
            "modelo" => mb_strtoupper($datos["modelo"]),
            "precio" => $datos["precio"],
            "talla" => mb_strtoupper($datos["talla"]),
        ]);

        // cargar foto
        if ($datos["foto"] && !is_string($datos["foto"])) {
            $this->cargarFoto($venta, $datos["foto"]);
        }

        return $venta;
    }

    /**
     * Eliminar venta
     *
     * @param Venta $venta
     * @return boolean
     */
    public function eliminar(Venta $venta): bool|Exception
    {
        foreach ($venta->detalle_ventas as $item) {
            $producto = Producto::findOrFail($item->producto_id);
            $producto->status = 1;
            $producto->save();
        }

        $venta->status = 0;
        $venta->save();

        return true;
    }

    /**
     * Cargar foto
     *
     * @param Venta $venta
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(Venta $venta, UploadedFile $foto): void
    {
        if ($venta->foto) {
            \File::delete(public_path("imgs/ventas/" . $venta->foto));
        }

        $nombre = $venta->id . time();
        $venta->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/ventas"), $nombre);
        $venta->save();
    }
}
