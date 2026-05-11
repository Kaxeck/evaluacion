<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CentroController;

Route::get('/', function () {
    return view('inicio_container'); // Vista temporal para cargar layout + inicio
})->name('home');

// Rutas de importación / Inicio
Route::get('/tabs/inicio', [DashboardController::class, 'index'])->name('tabs.inicio');

Route::get('/importar', [ImportController::class, 'index'])->name('import.index');
Route::post('/importar', [ImportController::class, 'store'])->name('import.store');

// Rutas
Route::get('/centros', [CentroController::class, 'index'])->name('centros.index');
Route::get('/alumnos', function () {
    return 'Modulo Alumnos'; })->name('alumnos.index');
Route::get('/calificaciones', function () {
    return 'Modulo Calificaciones'; })->name('calificaciones.index');

