<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportController extends Controller
{
    /**
     * Muestra la vista de importación.
     */
    public function index()
    {
        return view('import.index');
    }

    /**
     * Procesa la importación de Centros y Alumnos usando sentencias SQL en crudo.
     */
    public function store(Request $request)
    {
        // Validar que se haya subido al menos uno de los archivos
        $request->validate([
            'centros_file' => 'nullable|mimes:xlsx,xls',
            'alumnos_file' => 'nullable|mimes:xlsx,xls',
        ]);

        if (!$request->hasFile('centros_file') && !$request->hasFile('alumnos_file')) {
            return redirect()->route('import.index')->withErrors(['error' => 'Debes seleccionar al menos un archivo para importar.']);
        }

        try {
            DB::beginTransaction();
            $mensajes = [];

            // 1. Importar Centros
            if ($request->hasFile('centros_file')) {
                $centrosPath = $request->file('centros_file')->getRealPath();
                $resCentros = $this->importarCentros($centrosPath);
                $mensajes[] = "Centros: {$resCentros['exitos']} insertados, {$resCentros['fallos']} omitidos/con error.";
            }

            // 2. Importar Alumnos
            if ($request->hasFile('alumnos_file')) {
                $alumnosPath = $request->file('alumnos_file')->getRealPath();
                $resAlumnos = $this->importarAlumnos($alumnosPath);
                $mensajes[] = "Alumnos: {$resAlumnos['exitos']} insertados, {$resAlumnos['fallos']} omitidos/con error.";
            }

            DB::commit();

            return redirect()->route('import.index')->with('success', 'Importación finalizada. ' . implode(' | ', $mensajes));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('import.index')->with('error', 'Ocurrió un error fatal durante la importación: ' . $e->getMessage());
        }
    }

    /**
     * Lee el Excel de Centros y hace los inserts mediante RAW SQL.
     */
    private function importarCentros($filePath)
    {
        // Cargamos el archivo de Excel en memoria usando PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $exitos = 0;
        $fallos = 0;
        $isFirstRow = true;

        // Iteramos sobre cada fila del Excel
        foreach ($worksheet->getRowIterator() as $index => $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue; // Saltamos la primera fila porque son las cabeceras
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            // Extraemos y mapeamos los valores según las columnas del archivo CentrosTBC.xlsx
            // 0 => Clave, 1 => Telebachillerato (Nombre), 2 => Clave CCT, 3 => Municipio, etc.
            $clave = $cells[0] ?? null;
            $nombre = $cells[1] ?? null;
            $clave_cct = $cells[2] ?? null;
            $municipio = $cells[3] ?? null;
            $encargado = $cells[4] ?? null;
            $correo_encargado = $cells[5] ?? null;

            // Validación básica: Si faltan datos clave, registramos el error y continuamos
            if (!$clave || !$nombre) {
                $this->registrarLog('CentrosTBC.xlsx', $index, 'Faltan datos obligatorios (Clave o Nombre)', json_encode($cells));
                $fallos++;
                continue;
            }

            // Verificamos si el centro ya existe (Usamos DB::select porque no podemos usar Eloquent ORM)
            $existe = DB::select('SELECT id FROM centros WHERE clave = ?', [$clave]);
            
            if (empty($existe)) {
                try {
                    // Inserción limpia usando RAW SQL
                    DB::insert('INSERT INTO centros (clave, nombre, clave_cct, municipio, encargado, correo_encargado) VALUES (?, ?, ?, ?, ?, ?)', [
                        $clave, $nombre, $clave_cct, $municipio, $encargado, $correo_encargado
                    ]);
                    $exitos++;
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    if (strpos($errorMsg, '1062 Duplicate entry') !== false) {
                        $errorMsg = 'Error: Clave duplicada en la base de datos.';
                    } elseif (strpos($errorMsg, '1452 Cannot add or update a child row') !== false) {
                        $errorMsg = 'Error de referencia: un dato vinculado no existe.';
                    } else {
                        $errorMsg = 'Dato inválido o formato incorrecto (Error en celda).';
                    }
                    $this->registrarLog('CentrosTBC.xlsx', $index, $errorMsg, json_encode($cells));
                    $fallos++;
                }
            } else {
                // Si la clave ya existe, no detenemos el proceso, solo lo registramos
                $this->registrarLog('CentrosTBC.xlsx', $index, 'Centro duplicado (Clave ya existe)', json_encode($cells));
                $fallos++;
            }
        }
        
        return ['exitos' => $exitos, 'fallos' => $fallos];
    }

    /**
     * Lee el Excel de Alumnos, busca el centro_id y guarda los datos mediante RAW SQL.
     */
    private function importarAlumnos($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $exitos = 0;
        $fallos = 0;
        $isFirstRow = true;

        foreach ($worksheet->getRowIterator() as $index => $row) {
            if ($isFirstRow) {
                $isFirstRow = false;
                continue; // Saltar cabeceras
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }

            // Mapeo de celdas del archivo AlumnosTBC.xlsx
            // 0=>Matrícula, 1=>Centro(Nombre), 2=>Estatus, 3=>Nombre, 4=>Paterno, 5=>Materno, 6=>Genero, 7=>Generación, 8=>Municipio, 9=>País, 10=>FechaNac
            $matricula = $cells[0] ?? null;
            $nombreCentro = $cells[1] ?? null;
            $estatus = $cells[2] ?? null;
            $nombre = $cells[3] ?? null;
            $paterno = $cells[4] ?? null;
            $materno = $cells[5] ?? null;
            $genero = $cells[6] ?? null;
            $generacion = $cells[7] ?? null;
            $municipio = $cells[8] ?? null;
            $pais = $cells[9] ?? null;
            $fecha_nac = $cells[10] ?? null;

            // Validación de campos clave
            if (!$matricula || !$nombreCentro) {
                $this->registrarLog('AlumnosTBC.xlsx', $index, 'Faltan datos obligatorios (Matrícula o Centro)', json_encode($cells));
                $fallos++;
                continue;
            }

            // Convertir la fecha de Excel (formato numérico) a formato YYYY-MM-DD para MySQL
            $fechaNormalizada = null;
            if (!empty($fecha_nac)) {
                if (is_numeric($fecha_nac)) {
                    try {
                        $fechaNormalizada = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha_nac)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $fechaNormalizada = null;
                    }
                } else {
                    $parsed = strtotime($fecha_nac);
                    if ($parsed !== false) {
                        $fechaNormalizada = date('Y-m-d', $parsed);
                    }
                }
            }

            // Validar que tengamos una fecha válida para evitar el error de MySQL Strict Mode
            if (!$fechaNormalizada || $fechaNormalizada === '0000-00-00') {
                $this->registrarLog('AlumnosTBC.xlsx', $index, 'Fecha de nacimiento inválida o vacía', json_encode($cells));
                $fallos++;
                continue;
            }

            // Aquí está la solución al problema de que el Excel solo trae el nombre del Centro:
            // Buscamos el ID del centro a partir de su nombre usando LIMIT 1
            $centro = DB::select('SELECT id FROM centros WHERE nombre = ? LIMIT 1', [$nombreCentro]);
            
            if (empty($centro)) {
                // Si el centro no existe, es una inconsistencia mayor, la registramos y omitimos al alumno
                $this->registrarLog('AlumnosTBC.xlsx', $index, 'Centro no encontrado en BD: ' . $nombreCentro, json_encode($cells));
                $fallos++;
                continue;
            }
            $centro_id = $centro[0]->id;

            // Verificamos si la matrícula ya existe para no duplicar datos
            $existe = DB::select('SELECT id FROM alumnos WHERE matricula = ?', [$matricula]);

            if (empty($existe)) {
                try {
                    // Usamos sentencias SQL puras para cumplir con la regla de no usar ORMs
                    DB::insert('
                        INSERT INTO alumnos (centro_id, matricula, estatus, nombre, paterno, materno, genero, generacion, municipio_residencia, pais_nacimiento, fecha_nacimiento) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ', [
                        $centro_id, $matricula, $estatus, $nombre, $paterno, $materno, $genero, $generacion, $municipio, $pais, $fechaNormalizada
                    ]);
                    $exitos++;
                } catch (\Exception $e) {
                    $errorMsg = $e->getMessage();
                    if (strpos($errorMsg, '1062 Duplicate entry') !== false) {
                        $errorMsg = 'Error: Matrícula duplicada en la base de datos.';
                    } elseif (strpos($errorMsg, '1292 Incorrect date value') !== false) {
                        $errorMsg = 'Error: Fecha de nacimiento inválida o mal formateada.';
                    } elseif (strpos($errorMsg, '1406 Data too long') !== false) {
                        $errorMsg = 'Error: Texto demasiado largo para la columna.';
                    } else {
                        $errorMsg = 'Dato inválido o formato incorrecto (Error en celda).';
                    }
                    $this->registrarLog('AlumnosTBC.xlsx', $index, $errorMsg, json_encode($cells));
                    $fallos++;
                }
            } else {
                $this->registrarLog('AlumnosTBC.xlsx', $index, 'Alumno duplicado (Matrícula ya existe)', json_encode($cells));
                $fallos++;
            }
        }
        
        return ['exitos' => $exitos, 'fallos' => $fallos];
    }

    /**
     * Registra un error de inconsistencia en la tabla logs_importacion usando RAW SQL.
     */
    private function registrarLog($archivo, $fila, $mensaje, $datos)
    {
        DB::insert('INSERT INTO logs_importacion (archivo, fila, error_mensaje, datos_originales) VALUES (?, ?, ?, ?)', [
            $archivo, $fila, $mensaje, $datos
        ]);
    }
}
