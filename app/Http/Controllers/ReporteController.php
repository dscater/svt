<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\HistorialAccion;
use App\Models\Inscripcion;
use App\Models\User;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use PDF;
use Carbon\Carbon;
use FPDF;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReporteController extends Controller
{
    private $configuracion = null;
    public function __construct()
    {
        $this->configuracion = Configuracion::first();
        if (!$this->configuracion) {
            $this->configuracion = new Configuracion([
                "nombre_sistema" => "SISPRENDASOL S.A.",
                "alias" => "SP",
                "logo" => "logo.png",
                "fono" => "2222222",
                "dir" => "LOS OLIVOS",
            ]);
        }
    }

    public function usuarios()
    {
        return Inertia::render("Admin/Reportes/Usuarios");
    }

    public function r_usuarios(Request $request)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(-1);
        $tipo =  $request->tipo;
        $usuarios = User::select("users.*")
            ->where('id', '!=', 1)
            ->where('tipo', '!=', "POSTULANTE");

        if ($tipo != 'todos') {
            $request->validate([
                'tipo' => 'required',
            ]);
            $usuarios->where('role_id', $tipo);
        }

        $usuarios = $usuarios->orderBy("paterno", "ASC")->get();

        $pdf = PDF::loadView('reportes.usuarios', compact('usuarios'))->setPaper('legal', 'landscape');

        // ENUMERAR LAS PÁGINAS USANDO CANVAS
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf->get_canvas();
        $alto = $canvas->get_height();
        $ancho = $canvas->get_width();
        $canvas->page_text($ancho - 90, $alto - 25, "Página {PAGE_NUM} de {PAGE_COUNT}", null, 9, array(0, 0, 0));

        return $pdf->stream('usuarios.pdf');
    }
}
