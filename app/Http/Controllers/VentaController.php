<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentaStoreRequest;
use App\Http\Requests\VentaUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Venta;
use App\Models\User;
use App\Services\VentaService;
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

class VentaController extends Controller
{
    public function __construct(private VentaService $ventaService) {}

    public function historial(): ResponseInertia
    {
        return Inertia::render("Admin/Ventas/Historial");
    }

    public function paginadoHistorial(Request $request)
    {
        $search = (string)$request->input("search", "");
        $fecha_ini = (string)$request->input("fecha_ini", "");
        $fecha_fin = (string)$request->input("fecha_fin", "");
        $modelo = (string)$request->input("modelo", "");
        $usuario = (string)$request->input("usuario", "");

        $columnsSerachLike = [
            "productos.codigo",
            "productos.nombre",
            "productos.marca",
            "productos.precio",
            "productos.talla",
            "ventas.id",
            "ventas.tipo_pago",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];

        $ventas = $this->ventaService->listadoPaginadoHistorial($search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $fecha_ini, $fecha_fin, $modelo, $usuario);
        return response()->JSON([
            "registros" => $ventas
        ]);
    }

    public function exportarPDF(Request $request)
    {
        $search = (string)$request->input("search", "");
        $fecha_ini = (string)$request->input("fecha_ini", "");
        $fecha_fin = (string)$request->input("fecha_fin", "");
        $modelo = (string)$request->input("modelo", "");
        $usuario = (string)$request->input("usuario", "");

        $columnsSerachLike = [
            "productos.codigo",
            "productos.nombre",
            "productos.marca",
            "productos.precio",
            "productos.talla",
            "ventas.id",
            "ventas.tipo_pago",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];

        $ventas = $this->ventaService->listadoPaginadoHistorial($search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $fecha_ini, $fecha_fin, $modelo, $usuario);
        $pdf = PDF::loadView('reportes.historialVentas', compact('ventas'))->setPaper('letter', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('historialVentas.pdf');
    }

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Ventas/Index");
    }

    /**
     * Listado de ventas sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(): JsonResponse
    {
        return response()->JSON([
            "ventas" => $this->ventaService->listado()
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
            "productos.codigo",
            "productos.nombre",
            "productos.precio",
            "productos.talla",
            "ventas.id",
            "ventas.tipo_pago",
        ];
        $columnsFilter = [];
        $columnsBetweenFilter = [];
        $arrayOrderBy = [];
        if ($orderByCol && $desc) {
            $arrayOrderBy = [
                [$orderByCol, $desc]
            ];
        }

        $ventas = $this->ventaService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $ventas->items(),
            "total" => $ventas->total(),
            "lastPage" => $ventas->lastPage()
        ]);
    }


    /**
     * Endpoint para obtener la lista de ventas paginado para datatable
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {

        return response()->JSON([]);
    }

    public function create(): ResponseInertia
    {
        return Inertia::render("Admin/Ventas/Create");
    }

    /**
     * Registrar un nuevo venta
     *
     * @param VentaStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(VentaStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Venta
            $this->ventaService->crear($request->validated());
            DB::commit();
            return redirect()->route("ventas.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un venta
     *
     * @param Venta $venta
     * @return JsonResponse
     */
    public function show(Venta $venta): JsonResponse
    {
        return response()->JSON($venta);
    }

    public function edit(Venta $venta): ResponseInertia
    {
        $venta = $venta->load(["detalle_ventas.producto"]);
        return Inertia::render("Admin/Ventas/Edit", compact("venta"));
    }


    public function update(Venta $venta, VentaUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar venta
            $this->ventaService->actualizar($request->validated(), $venta);
            DB::commit();
            return redirect()->route("ventas.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar venta
     *
     * @param Venta $venta
     * @return JsonResponse|Response
     */
    public function destroy(Venta $venta): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->ventaService->eliminar($venta);
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
