<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el resumen de estadísticas en el panel de inicio.
     */
    public function index(Request $request)
    {
        // Obtener estadísticas reales de la base de datos con SQL puro
        $totalCentros = DB::select('SELECT COUNT(*) as total FROM centros')[0]->total;
        $totalAlumnos = DB::select('SELECT COUNT(*) as total FROM alumnos')[0]->total;
        
        // Tablas que aún no tienen módulo en esta fase
        $totalMaterias = DB::select('SELECT COUNT(*) as total FROM materias')[0]->total;
        $totalCalificaciones = DB::select('SELECT COUNT(*) as total FROM calificaciones')[0]->total;

        $data = compact('totalCentros', 'totalAlumnos', 'totalMaterias', 'totalCalificaciones');

        // Retorna la vista parcial si es AJAX, o el layout completo si es carga directa
        if ($request->ajax() || $request->has('ajax')) {
            return view('inicio', $data);
        }

        return view('inicio_container', $data);
    }
}
