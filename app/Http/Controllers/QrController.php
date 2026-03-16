<?php

namespace App\Http\Controllers;

use App\Http\Requests\QrRequest;
use App\Models\Qr;
use App\Services\QrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class QrController extends Controller
{
    public function __construct(private QrService $qrService) {}

    public function index(Request $request)
    {
        $array = $this->qrService->verificarQr();
        $qr = $array[0];
        $estado = $array[1];
        $estado_sw = $array[2];

        return Inertia::render("Admin/Qrs/Index", compact("qr", "estado", "estado_sw"));
    }

    public function getQr()
    {
        $array = $this->qrService->verificarQr();
        $qr = $array[0];
        $estado = $array[1];
        $estado_sw = $array[2];
        return response()->JSON([
            "qr" => $qr,
            "estado" => $estado,
            "estado_sw" => $estado_sw,
        ], 200);
    }

    public function update(Qr $qr, QrRequest $request)
    {
        Log::debug("ASDSD");
        DB::beginTransaction();
        try {
            $this->qrService->actualizar($request->validated(), $qr);
            DB::commit();
            return redirect()->route("qrs.index")->with("success", "Registro correcto");
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }

    public function show(Qr $qr) {}
}
