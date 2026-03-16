<?php

namespace App\Services;

use App\Models\Qr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrService
{
    public function __construct(private CargarArchivoService $cargarArchivoService) {}

    /**
     * Actualizar qr
     *
     * @param array $datos
     * @param Qr $qr
     * @return Qr
     */
    public function actualizar(array $datos, Qr $qr): Qr
    {
        $qr = Qr::first();

        $qr->update([
            "remitente" => $datos["remitente"],
            "fecha_vencimiento" => $datos["fecha_vencimiento"],
        ]);

        // cargar qr
        if ($datos["qr"] && !is_string($datos["qr"])) {
            $this->cargarLogo($qr, $datos["qr"]);
        }

        return $qr;
    }

    public function cargarLogo(Qr $qr, UploadedFile $imagen_qr): void
    {
        if ($qr->qr) {
            \File::delete(public_path("imgs/" . $qr->qr));
        }
        $nombre = $qr->id . time();
        $qr->qr = $this->cargarArchivoService->cargarArchivo($imagen_qr, public_path("imgs"), $nombre);
        $qr->save();
    }

    public function verificarQr()
    {
        $qr = Qr::first();

        $fecha_actual = date("Y-m-d");
        $estado_sw = true;
        $estado = "NO SE CARGÓ NINGÚN QR";
        if (!$qr) {
            Qr::create([
                "qr" => null,
                "remitente" => null,
                "fecha_vencimiento" => null,
            ]);
            $estado_sw = false;
        }

        if ($qr && ($qr->fecha_vencimiento == null)) {
            $estado = "NO SE CARGÓ NINGÚN QR";
            $estado_sw = false;
        }

        if ($qr && $qr->fecha_vencimiento != null && $qr->fecha_vencimiento < $fecha_actual) {
            $estado = "EL QR ACTUAL ESTA VENCIDO";
            $estado_sw = false;
        }

        return [$qr, $estado, $estado_sw];
    }
}
