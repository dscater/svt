<?php

namespace App\Http\Controllers;


use App\Http\Requests\ProductoStoreRequest;
use App\Http\Requests\ProductoUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Producto;
use App\Models\User;
use App\Services\ProductoService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as ResponseInertia;
use PDF;

class ProductoController extends Controller
{
    public function __construct(private ProductoService $productoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Productos/Index");
    }

    public function barras(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);

        $productos = Producto::where("status", 1);
        if ($request->producto_id && $request->producto_id != "todos") {
            $producto_id = $request->producto_id;
            $productos->where("id", $producto_id);
        }

        $productos = $productos->get();
        $pdf = PDF::loadView('reportes.barras', compact('productos'));
        $pdf->setPaper([0, 0, $this->cmToPt(4), $this->cmToPt(3)]);
        $pdf->output();
        return $pdf->stream('productos.pdf');
    }


    function cmToPt($cm)
    {
        return $cm * 28.346;
    }

    /**
     * Listado de productos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "productos" => $this->productoService->listado()
        ]);
    }

    public function paginado(Request $request)
    {
        $perPage = $request->perPage;
        $page = (int)($request->input("page", 1));
        $search = (string)$request->input("search", "");
        $orderByCol = $request->orderByCol;
        $desc = $request->desc;

        $columnsSerachLike = [
            "codigo",
            "modelo",
            "marca",
            "talla",
            "nombre"
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderByCol && $desc) {
            $arrayOrderBy = [
                [$orderByCol, $desc]
            ];
        }

        $productos = $this->productoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $productos->items(),
            "total" => $productos->total(),
            "lastPage" => $productos->lastPage()
        ]);
    }


    /**
     * Endpoint para obtener la lista de productos paginado para datatable
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {

        return response()->JSON([]);
    }

    /**
     * Registrar un nuevo producto
     *
     * @param ProductoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(ProductoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Producto
            $this->productoService->crear($request->validated());
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un producto
     *
     * @param Producto $producto
     * @return JsonResponse
     */
    public function show(Producto $producto): JsonResponse
    {
        return response()->JSON($producto);
    }

    public function byCodigo(Request $request): JsonResponse
    {
        try {
            $codigo = $request->codigo;
            $producto = Producto::where("codigo", $codigo)->get()->first();

            if (!$producto) {
                throw new Exception("No hay ningún producto con ese código");
            }

            if ($producto->status == 0) {
                throw new Exception("No se encontró el producto");
            }

            if ($producto->status == 2) {
                throw new Exception("El producto ya fue vendido");
            }

            return response()->JSON($producto);
        } catch (\Exception $e) {
            Log::debug("BB");
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }


    public function update(Producto $producto, ProductoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar producto
            $this->productoService->actualizar($request->validated(), $producto);
            DB::commit();
            return redirect()->route("productos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar producto
     *
     * @param Producto $producto
     * @return JsonResponse|Response
     */
    public function destroy(Producto $producto): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->productoService->eliminar($producto);
            DB::commit();
            return response()->JSON([
                'sw' => true,
                'message' => 'El registro se eliminó correctamente'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
