<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Parámetros de URL
        $search = $request->input('search', '');
        $estatusFiltro = $request->input('estatus', '');
        $generoFiltro = $request->input('genero', '');
        $centroFiltro = $request->input('centro', '');
        $page = (int) $request->input('page', 1);
        if ($page < 1)
            $page = 1;

        $perPage = 15;

        // 2. Obtener lista de centros con su conteo de alumnos para el filtro
        // Solo mostramos los que tienen alumnos (INNER JOIN)
        $listaCentros = DB::select('
            SELECT c.id, c.clave, c.nombre, COUNT(a.id) as alumnos_count 
            FROM centros c 
            INNER JOIN alumnos a ON a.centro_id = c.id 
            GROUP BY c.id, c.clave, c.nombre 
            ORDER BY c.nombre ASC
        ');

        // 3. Construir condiciones WHERE dinámicas
        $whereClause = [];
        $bindings = [];

        if (!empty($search)) {
            $whereClause[] = "(alumnos.nombre LIKE ? OR alumnos.paterno LIKE ? OR alumnos.materno LIKE ? OR alumnos.matricula LIKE ? OR centros.nombre LIKE ?)";
            $bindings = array_merge($bindings, array_fill(0, 5, "%{$search}%"));
        }

        if (!empty($estatusFiltro)) {
            $whereClause[] = "alumnos.estatus = ?";
            $bindings[] = $estatusFiltro;
        }

        if (!empty($generoFiltro)) {
            $whereClause[] = "alumnos.genero = ?";
            $bindings[] = $generoFiltro;
        }

        if (!empty($centroFiltro)) {
            $whereClause[] = "alumnos.centro_id = ?";
            $bindings[] = $centroFiltro;
        }

        $whereSql = count($whereClause) > 0 ? 'WHERE ' . implode(' AND ', $whereClause) : '';

        // 3. Consultas SQL Puras con JOIN
        // Conteo optimizado
        $countQuery = "SELECT COUNT(*) as total FROM alumnos LEFT JOIN centros ON alumnos.centro_id = centros.id " . $whereSql;

        // Consulta de datos con JOIN
        $dataQuery = "
            SELECT 
                alumnos.*, 
                centros.nombre as centro_nombre,
                centros.clave as centro_clave
            FROM alumnos 
            LEFT JOIN centros ON alumnos.centro_id = centros.id 
            {$whereSql} 
            ORDER BY alumnos.paterno ASC, alumnos.materno ASC, alumnos.nombre ASC 
            LIMIT ? OFFSET ?
        ";

        // 4. Calcular Paginación
        $totalResult = DB::select($countQuery, $bindings);
        $total = $totalResult[0]->total;

        $totalPages = ceil($total / $perPage);
        if ($page > $totalPages && $totalPages > 0)
            $page = $totalPages;

        $offset = ($page - 1) * $perPage;

        // 5. Agregar parámetros de paginación
        $dataBindings = $bindings;
        $dataBindings[] = $perPage;
        $dataBindings[] = $offset;

        // 6. Ejecutar Query Principal
        $alumnos = DB::select($dataQuery, $dataBindings);

        $data = compact('alumnos', 'search', 'page', 'totalPages', 'total', 'estatusFiltro', 'generoFiltro', 'centroFiltro', 'listaCentros');

        if ($request->ajax() || $request->has('ajax')) {
            return view('alumnos.index', $data);
        }

        return view('alumnos.container', $data);
    }

    public function edit(Request $request, $id)
    {
        $alumno = DB::select('SELECT * FROM alumnos WHERE id = ? LIMIT 1', [$id]);
        
        if (empty($alumno)) {
            return response('Alumno no encontrado', 404);
        }

        $centros = DB::select('SELECT id, clave, nombre FROM centros ORDER BY nombre ASC');

        return view('alumnos.edit', [
            'alumno' => $alumno[0],
            'centros' => $centros
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $nombre = $request->input('nombre');
            $paterno = $request->input('paterno', '');
            $materno = $request->input('materno', '');
            $genero = $request->input('genero');
            $estatus = $request->input('estatus');
            $centro_id = $request->input('centro_id');

            // Actualización por SQL puro
            $affected = DB::update('
                UPDATE alumnos 
                SET nombre = ?, paterno = ?, materno = ?, genero = ?, estatus = ?, centro_id = ? 
                WHERE id = ?
            ', [
                $nombre, $paterno, $materno, $genero, $estatus, $centro_id, $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Los datos del alumno se actualizaron correctamente.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
