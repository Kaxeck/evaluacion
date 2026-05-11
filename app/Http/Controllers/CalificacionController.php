<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalificacionController extends Controller
{
    /**
     * Lista de alumnos para seleccionar a quién calificar
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $centroFiltro = $request->input('centro', '');
        $page = (int) $request->input('page', 1);
        if ($page < 1) $page = 1;
        $perPage = 15;

        // Lista de centros para el filtro
        $listaCentros = DB::select('
            SELECT c.id, c.clave, c.nombre, COUNT(a.id) as alumnos_count 
            FROM centros c 
            INNER JOIN alumnos a ON a.centro_id = c.id 
            GROUP BY c.id, c.clave, c.nombre 
            ORDER BY c.nombre ASC
        ');

        $whereClause = [];
        $bindings = [];

        if (!empty($search)) {
            $whereClause[] = "(a.nombre LIKE ? OR a.paterno LIKE ? OR a.materno LIKE ? OR a.matricula LIKE ? OR c.nombre LIKE ?)";
            $bindings = array_merge($bindings, array_fill(0, 5, "%{$search}%"));
        }

        if (!empty($centroFiltro)) {
            $whereClause[] = "a.centro_id = ?";
            $bindings[] = $centroFiltro;
        }

        $whereSql = count($whereClause) > 0 ? 'WHERE ' . implode(' AND ', $whereClause) : '';

        // Conteo total
        $countQuery = "SELECT COUNT(a.id) as total FROM alumnos a LEFT JOIN centros c ON a.centro_id = c.id " . $whereSql;
        $totalResult = DB::select($countQuery, $bindings);
        $total = $totalResult[0]->total;
        $totalPages = ceil($total / $perPage);

        // Obtener alumnos
        $offset = ($page - 1) * $perPage;
        $dataBindings = array_merge($bindings, [$perPage, $offset]);
        
        $alumnosQuery = "
            SELECT 
                a.id, a.matricula, a.nombre, a.paterno, a.materno,
                c.clave as centro_clave, c.nombre as centro_nombre,
                (SELECT AVG(promedio) FROM calificaciones WHERE alumno_id = a.id) as promedio_general
            FROM alumnos a
            LEFT JOIN centros c ON a.centro_id = c.id
            $whereSql
            ORDER BY a.paterno ASC, a.materno ASC, a.nombre ASC
            LIMIT ? OFFSET ?
        ";
        
        $alumnos = DB::select($alumnosQuery, $dataBindings);

        $data = compact('alumnos', 'search', 'centroFiltro', 'listaCentros', 'page', 'totalPages', 'total');

        if ($request->ajax() || $request->has('ajax')) {
            return view('calificaciones.index', $data);
        }

        return view('calificaciones.container', $data);
    }

    /**
     * Muestra la matriz de captura (materias y promedios) para un alumno
     */
    public function show(Request $request, $id)
    {
        $alumno = DB::select('SELECT id, matricula, nombre, paterno, materno FROM alumnos WHERE id = ? LIMIT 1', [$id]);
        if (empty($alumno)) {
            return response('Alumno no encontrado', 404);
        }

        // Extraer materias
        $materias = DB::select('SELECT id, nombre, creditos FROM materias ORDER BY id ASC');

        // Extraer calificaciones existentes de este alumno
        $calificaciones = DB::select('SELECT materia_id, parcial1, parcial2, parcial3, promedio FROM calificaciones WHERE alumno_id = ?', [$id]);
        
        // Mapear calificaciones por materia_id para acceso rápido
        $califsMap = [];
        foreach ($calificaciones as $cal) {
            $califsMap[$cal->materia_id] = $cal;
        }

        return view('calificaciones.edit', [
            'alumno' => $alumno[0],
            'materias' => $materias,
            'calificaciones' => $califsMap
        ]);
    }

    /**
     * Guarda las calificaciones mediante SQL puro
     */
    public function store(Request $request, $id)
    {
        try {
            $grades = $request->input('grades'); // Array enviado desde JS

            DB::beginTransaction();

            foreach ($grades as $materiaId => $data) {
                // Validación básica de escala 0-10
                $p1 = isset($data['parcial1']) && is_numeric($data['parcial1']) ? floatval($data['parcial1']) : null;
                $p2 = isset($data['parcial2']) && is_numeric($data['parcial2']) ? floatval($data['parcial2']) : null;
                $p3 = isset($data['parcial3']) && is_numeric($data['parcial3']) ? floatval($data['parcial3']) : null;
                $promedio = isset($data['promedio']) && is_numeric($data['promedio']) ? floatval($data['promedio']) : null;

                // Verificamos si los parciales están en rango (0 a 10) si tienen valor
                if (($p1 !== null && ($p1 < 0 || $p1 > 10)) ||
                    ($p2 !== null && ($p2 < 0 || $p2 > 10)) ||
                    ($p3 !== null && ($p3 < 0 || $p3 > 10))) {
                    throw new \Exception("Las calificaciones deben estar en la escala de 0 a 10.");
                }

                // Check if row already exists
                $exists = DB::select('SELECT id FROM calificaciones WHERE alumno_id = ? AND materia_id = ? LIMIT 1', [$id, $materiaId]);

                if (!empty($exists)) {
                    // Actualizar
                    DB::update('
                        UPDATE calificaciones 
                        SET parcial1 = ?, parcial2 = ?, parcial3 = ?, promedio = ?, updated_at = NOW()
                        WHERE id = ?
                    ', [$p1, $p2, $p3, $promedio, $exists[0]->id]);
                } else {
                    // Si no hay valores en absoluto, evitamos insertar basura vacía
                    if ($p1 !== null || $p2 !== null || $p3 !== null) {
                        DB::insert('
                            INSERT INTO calificaciones (alumno_id, materia_id, parcial1, parcial2, parcial3, promedio, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
                        ', [$id, $materiaId, $p1, $p2, $p3, $promedio]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Calificaciones guardadas correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }
}
