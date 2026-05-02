<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Services\HistorialAccionService;
use App\Models\Fardo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Exception;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class FardoService
{

    public function __construct(private  CargarArchivoService $cargarArchivoService) {}

    public function listado($tipo_venta = ""): Collection
    {
        $fardos = Fardo::select("fardos.*")->where("status", 1);
        if (is_array($tipo_venta)) {
            $fardos->whereIn("tipo_venta", $tipo_venta);
        }

        if (is_string($tipo_venta) && $tipo_venta != '') {
            $fardos->where("tipo_venta", $tipo_venta);
        }

        $fardos = $fardos->get();
        return $fardos;
    }
    /**
     * Lista de fardos paginado con filtros
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
        $fardos = Fardo::select("fardos.*")->where("status", 1);

        // Filtros exactos
        foreach ($columnsFilter as $key => $value) {
            if (!is_null($value)) {
                $fardos->where("fardos.$key", $value);
            }
        }

        // Filtros por rango
        foreach ($columnsBetweenFilter as $key => $value) {
            if (isset($value[0], $value[1])) {
                $fardos->whereBetween("fardos.$key", $value);
            }
        }

        // Búsqueda en múltiples columnas con LIKE
        if (!empty($search) && !empty($columnsSerachLike)) {
            $fardos->where(function ($query) use ($search, $columnsSerachLike) {
                foreach ($columnsSerachLike as $col) {
                    $query->orWhere("$col", "LIKE", "%$search%");
                }
            });
        }

        // Ordenamiento
        foreach ($orderBy as $value) {
            if (isset($value[0], $value[1])) {
                $fardos->orderBy($value[0], $value[1]);
            }
        }


        $fardos = $fardos->paginate($length, ['*'], 'page', $page);
        return $fardos;
    }

    /**
     * Crear fardo
     *
     * @param array $datos
     * @return Fardo
     */
    public function crear(array $datos): Fardo
    {

        $fardo = Fardo::create([
            "nombre" => $datos["nombre"] ? mb_strtoupper($datos["nombre"]) : NULL,
            "tipo_venta" => $datos["tipo_venta"],
            "fecha_registro" => date("Y-m-d"),
            "hora_registro" => date("H:i:s"),
        ]);

        // COMPLETO
        if ($fardo->tipo_venta == 'COMPLETO') {
            $fardo->precio = $datos["precio"];
            $fardo->codigo_barras = $datos["codigo_barras"];
        } else {
            $fardo->stock = 0;
        }
        $fardo->save();

        return $fardo;
    }

    private function getCodigoFardo(): string
    {
        $ultimoFardo = Fardo::orderBy("id", "desc")->first();
        $codigo = "PROD-0001";
        if ($ultimoFardo) {
            $codigo = "PROD-" . str_pad($ultimoFardo->id + 1, 4, "0", STR_PAD_LEFT);
        }
        return $codigo;
    }

    /**
     * Actualizar fardo
     *
     * @param array $datos
     * @param Fardo $fardo
     * @return Fardo
     */
    public function actualizar(array $datos, Fardo $fardo): Fardo
    {
        $fardo->update([
            "nombre" => $datos["nombre"] ? mb_strtoupper($datos["nombre"]) : NULL,
            "tipo_venta" => $datos["tipo_venta"],
        ]);
        $existe_productos = DetalleVenta::whereHas("producto", function ($q) use ($fardo) {
            $q->where("fardo_id", $fardo->id);
        })->count();

        if ($existe_productos > 0) {
            throw new Exception("No es posible actualizar el registro porque tiene productos asociados");
        }

        // COMPLETO
        if ($fardo->tipo_venta == 'COMPLETO') {
            $fardo->precio = $datos["precio"];
            $fardo->codigo_barras = $datos["codigo_barras"];
        } else {
            $fardo->precio = NULL;
            $fardo->codigo_barras = NULL;
        }

        $fardo->save();

        return $fardo;
    }

    /**
     * Eliminar fardo
     *
     * @param Fardo $fardo
     * @return boolean
     */
    public function eliminar(Fardo $fardo): bool|Exception
    {
        $existe_productos = DetalleVenta::whereHas("producto", function ($q) use ($fardo) {
            $q->where("fardo_id", $fardo->id);
        })->count();
        if ($existe_productos > 0) {
            throw new Exception("No es posible eliminar el registro porque tiene productos asociados");
        }
        $fardo->status = 0;
        $fardo->save();
        return true;
    }

    /**
     * Cargar foto
     *
     * @param Fardo $fardo
     * @param UploadedFile $foto
     * @return void
     */
    public function cargarFoto(Fardo $fardo, UploadedFile $foto): void
    {
        if ($fardo->foto) {
            \File::delete(public_path("imgs/fardos/" . $fardo->foto));
        }

        $nombre = $fardo->id . time();
        $fardo->foto = $this->cargarArchivoService->cargarArchivo($foto, public_path("imgs/fardos"), $nombre);
        $fardo->save();
    }

    public function incrementaStock($fardo_id)
    {
        $fardo = Fardo::findOrFail($fardo_id);
        $fardo->stock = (int)$fardo->stock + 1;
        $fardo->save();
    }



    public function reduceStock($fardo_id)
    {
        $fardo = Fardo::find($fardo_id);
        if ($fardo) {
            $fardo->stock = (int)$fardo->stock - 1;
            $fardo->save();
        }
    }
}
