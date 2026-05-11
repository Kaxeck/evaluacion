<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentroController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtener parámetros de la URL
        $search = $request->input('search', '');
        $municipioFiltro = $request->input('municipio', '');
        $page = (int) $request->input('page', 1);
        if ($page < 1) $page = 1;
        
        $perPage = 10; // Resultados por página
        
        // 2. Obtener lista de municipios para el select
        $municipios = DB::select('SELECT DISTINCT municipio FROM centros WHERE municipio IS NOT NULL AND municipio != "" ORDER BY municipio ASC');

        // 3. Construir la consulta base
        $whereClause = [];
        $bindings = [];

        if (!empty($search)) {
            $whereClause[] = "(nombre LIKE ? OR clave LIKE ? OR municipio LIKE ? OR clave_cct LIKE ? OR encargado LIKE ? OR correo_encargado LIKE ?)";
            $bindings = array_merge($bindings, array_fill(0, 6, "%{$search}%"));
        }

        if (!empty($municipioFiltro)) {
            $whereClause[] = "municipio = ?";
            $bindings[] = $municipioFiltro;
        }

        $whereSql = count($whereClause) > 0 ? 'WHERE ' . implode(' AND ', $whereClause) : '';

        $countQuery = "SELECT COUNT(*) as total FROM centros " . $whereSql;
        $dataQuery  = "SELECT * FROM centros " . $whereSql . " ORDER BY nombre ASC LIMIT ? OFFSET ?";

        // 4. Contar el total de registros para las matemáticas de paginación
        $totalResult = DB::select($countQuery, $bindings);
        $total = $totalResult[0]->total;

        // 4. Matemáticas del Paginador Manual
        $totalPages = ceil($total / $perPage);
        if ($page > $totalPages && $totalPages > 0) $page = $totalPages; // No pasar de la última página
        
        $offset = ($page - 1) * $perPage;

        // 5. Agregar parámetros de paginación al array de bindings
        $dataBindings = $bindings;
        $dataBindings[] = $perPage;
        $dataBindings[] = $offset;

        // 6. Ejecutar consulta de los registros
        $centros = DB::select($dataQuery, $dataBindings);

        $data = compact('centros', 'search', 'page', 'totalPages', 'total', 'municipios', 'municipioFiltro');

        // Retorna la vista parcial si es AJAX, o el contenedor completo si es carga directa
        if ($request->ajax() || $request->has('ajax')) {
            return view('centros.index', $data);
        }

        // Si la abren directo por URL, la envolvemos en el layout
        return view('centros.container', $data);
    }
}
