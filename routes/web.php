<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CentroController;
use App\Http\Controllers\AlumnoController;

Route::get('/', [DashboardController::class, 'index'])->name('home');

// Rutas de importación / Inicio
Route::get('/tabs/inicio', [DashboardController::class, 'index'])->name('tabs.inicio');

Route::get('/importar', [ImportController::class, 'index'])->name('import.index');
Route::post('/importar', [ImportController::class, 'store'])->name('import.store');

// Rutas
Route::get('/centros', [CentroController::class, 'index'])->name('centros.index');
Route::get('/centros/{id}', [CentroController::class, 'show'])->name('centros.show');

Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
Route::get('/alumnos/{id}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
Route::post('/alumnos/{id}', [AlumnoController::class, 'update'])->name('alumnos.update');
Route::get('/calificaciones', function () {
    return 'Modulo Calificaciones'; })->name('calificaciones.index');

