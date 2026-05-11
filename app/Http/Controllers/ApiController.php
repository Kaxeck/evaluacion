<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Web Service: Obtener datos completos de un alumno (Perfil + Calificaciones)
     * Método: GET /api/alumnos/{matricula}
     */
    public function getAlumno($matricula)
    {
        // 1. Buscar al alumno por matrícula y traer datos del centro
        $alumnos = DB::select("
            SELECT 
                a.id, a.matricula, a.nombre, a.paterno, a.materno, a.genero, a.estatus,
                c.clave as centro_clave, c.nombre as centro_nombre
            FROM alumnos a
            LEFT JOIN centros c ON a.centro_id = c.id
            WHERE a.matricula = ?
            LIMIT 1
        ", [$matricula]);

        if (empty($alumnos)) {
            return response()->json([
                'success' => false,
                'message' => 'Alumno no encontrado',
                'data' => null
            ], 404);
        }

        $alumno = $alumnos[0];

        // 2. Traer las calificaciones con el nombre de la materia
        $calificaciones = DB::select("
            SELECT 
                m.nombre as materia,
                m.creditos,
                c.parcial1,
                c.parcial2,
                c.parcial3,
                c.promedio
            FROM calificaciones c
            INNER JOIN materias m ON c.materia_id = m.id
            WHERE c.alumno_id = ?
            ORDER BY m.id ASC
        ", [$alumno->id]);

        // 3. Calcular promedio general exacto
        $promedioGeneral = 0;
        if (count($calificaciones) > 0) {
            $suma = array_reduce($calificaciones, function($carry, $item) {
                return $carry + ($item->promedio !== null ? $item->promedio : 0);
            }, 0);
            
            // Contar solo las materias que tienen un promedio registrado
            $materiasConCalificacion = array_filter($calificaciones, function($item) {
                return $item->promedio !== null;
            });
            
            $divisor = count($materiasConCalificacion) > 0 ? count($materiasConCalificacion) : 1;
            $promedioGeneral = round($suma / $divisor, 2);
        }

        // 4. Armar el JSON de respuesta estructurado
        $payload = [
            'success' => true,
            'data' => [
                'perfil' => [
                    'matricula' => $alumno->matricula,
                    'nombre_completo' => trim("{$alumno->paterno} {$alumno->materno} {$alumno->nombre}"),
                    'genero' => $alumno->genero,
                    'estatus' => $alumno->estatus,
                ],
                'escuela' => [
                    'clave_centro' => $alumno->centro_clave,
                    'nombre_centro' => $alumno->centro_nombre,
                ],
                'academico' => [
                    'promedio_general' => count($calificaciones) > 0 ? $promedioGeneral : null,
                    'materias_evaluadas' => count($calificaciones),
                    'boleta' => $calificaciones
                ]
            ]
        ];

        // Retornar JSON con cabeceras CORS básicas (opcional pero buena práctica)
        return response()->json($payload, 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
