
<div class="page-header">
  <div>
    <div class="page-title">Panel de inicio</div>
    <div class="page-subtitle">Resumen general del sistema</div>
  </div>
</div>

<script>
    // En Inicio, ocultamos la barra de búsqueda global
    var topSearch = document.getElementById('top-search-container');
    if (topSearch) topSearch.style.display = 'none';
    window.currentModuleSearchUrl = null;
</script>

<div class="stat-row">
  <div class="stat-card">
    <div class="stat-label">Centros registrados</div>
    <div class="stat-value">{{ number_format($totalCentros) }}</div>
    <div class="stat-delta">Módulos activos</div>
    <div class="stat-icon"><svg width="48" height="48" fill="none" stroke="#0a1854" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Alumnos activos</div>
    <div class="stat-value">{{ number_format($totalAlumnos) }}</div>
    <div class="stat-delta">Ciclo actual</div>
    <div class="stat-icon"><svg width="48" height="48" fill="none" stroke="#0a1854" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Materias</div>
    <div class="stat-value">{{ number_format($totalMaterias) }}</div>
    <div class="stat-delta">Plan curricular</div>
    <div class="stat-icon"><svg width="48" height="48" fill="none" stroke="#0a1854" stroke-width="1.5" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Califs. capturadas</div>
    <div class="stat-value">{{ number_format($totalCalificaciones) }}</div>
    <div class="stat-delta">Evaluaciones en BD</div>
    <div class="stat-icon"><svg width="48" height="48" fill="none" stroke="#0a1854" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
  </div>
</div>

<div class="card" style="padding: 2rem; text-align: center; margin-top: 2rem;">
    <h3 style="color: var(--blue-900); margin-bottom: 1rem; font-family: var(--font-display);">¡Bienvenido al Sistema de Gestión!</h3>
    <p style="color: var(--gray-600); max-width: 600px; margin: 0 auto;">Usa el menú lateral para navegar entre la gestión de Centros y Alumnos, o para importar nuevos datos al sistema. Todos los datos se actualizan en tiempo real directamente desde MySQL.</p>
</div>
