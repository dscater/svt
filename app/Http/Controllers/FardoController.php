<?php

namespace App\Http\Controllers;

use App\Http\Requests\FardoStoreRequest;
use App\Http\Requests\FardoUpdateRequest;
use App\Models\HistorialAccion;
use App\Models\Modulo;
use App\Models\Permiso;
use App\Models\Fardo;
use App\Models\User;
use App\Services\FardoService;
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

class FardoController extends Controller
{
    public function __construct(private FardoService $fardoService) {}

    /**
     * Página index
     *
     * @return Response
     */
    public function index(): ResponseInertia
    {
        return Inertia::render("Admin/Fardos/Index");
    }

    public function barras(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);

        $fardos = Fardo::where("tipo_venta", "COMPLETO");
        if ($request->fardo_id && $request->fardo_id != "todos") {
            $fardo_id = $request->fardo_id;
            $fardos->where("id", $fardo_id);
        }

        $fardos = $fardos->get();
        $pdf = PDF::loadView('reportes.barras_fardos', compact('fardos'));
        $pdf->setPaper([0, 0, $this->cmToPt(4), $this->cmToPt(3)]);
        $pdf->output();
        return $pdf->stream('fardos.pdf');
    }


    function cmToPt($cm)
    {
        return $cm * 28.346;
    }

    /**
     * Listado de fardos sin ids: 1 y 2
     *
     * @return JsonResponse
     */
    public function listado(Request $request): JsonResponse
    {
        return response()->JSON([
            "fardos" => $this->fardoService->listado($request->tipo_venta)
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
            "codigo_barras",
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

        $fardos = $this->fardoService->listadoPaginado($perPage, $page, $search, $columnsSerachLike, $columnsFilter, $columnsBetweenFilter, $arrayOrderBy);
        return response()->JSON([
            "data" => $fardos->items(),
            "total" => $fardos->total(),
            "lastPage" => $fardos->lastPage()
        ]);
    }


    /**
     * Endpoint para obtener la lista de fardos paginado para datatable
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function api(Request $request): JsonResponse
    {

        return response()->JSON([]);
    }

    /**
     * Registrar un nuevo fardo
     *
     * @param FardoStoreRequest $request
     * @return RedirectResponse|Response
     */
    public function store(FardoStoreRequest $request): RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            // crear el Fardo
            $this->fardoService->crear($request->validated());
            DB::commit();
            return redirect()->route("fardos.index")->with("bien", "Registro realizado");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Mostrar un fardo
     *
     * @param Fardo $fardo
     * @return JsonResponse
     */
    public function show(Fardo $fardo): JsonResponse
    {
        return response()->JSON($fardo);
    }

    public function byCodigo(Request $request): JsonResponse
    {
        try {
            $codigo = $request->codigo;
            $fardo = Fardo::where("codigo", $codigo)->get()->first();

            if (!$fardo) {
                throw new Exception("No hay ningún fardo con ese código");
            }

            if ($fardo->status == 0) {
                throw new Exception("No se encontró el fardo");
            }

            if ($fardo->status == 2) {
                throw new Exception("El fardo ya fue vendido");
            }

            return response()->JSON($fardo);
        } catch (\Exception $e) {
            Log::debug("BB");
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }


    public function update(Fardo $fardo, FardoUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            // actualizar fardo
            $this->fardoService->actualizar($request->validated(), $fardo);
            DB::commit();
            return redirect()->route("fardos.index")->with("bien", "Registro actualizado");
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::debug($e->getMessage());
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Eliminar fardo
     *
     * @param Fardo $fardo
     * @return JsonResponse|Response
     */
    public function destroy(Fardo $fardo): JsonResponse|Response
    {
        DB::beginTransaction();
        try {
            $this->fardoService->eliminar($fardo);
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
