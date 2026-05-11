<div class="page-header" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <div class="page-title">Captura de Calificaciones</div>
        <div class="page-subtitle">Selecciona a un alumno para capturar sus evaluaciones por materia.</div>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <select id="filter-centro" onchange="applyCalificacionFilters()" style="max-width: 250px; padding: 0.4rem 2rem 0.4rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 13px; color: var(--blue-900); background-color: var(--white); cursor: pointer; outline: none; text-overflow: ellipsis;">
            <option value="">Todos los planteles</option>
            @foreach($listaCentros as $centro)
                <option value="{{ $centro->id }}" {{ $centroFiltro == $centro->id ? 'selected' : '' }}>
                    {{ $centro->clave }} - {{ $centro->nombre }} ({{ $centro->alumnos_count }} alumnos)
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                    <th>Plantel (Centro)</th>
                    <th style="text-align: center;">Promedio</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alumnos as $alumno)
                <tr>
                    <td><span class="td-badge" style="background: var(--blue-50); color: var(--blue-700); border-color: var(--blue-100);">{{ $alumno->matricula }}</span></td>
                    <td style="font-weight: 500; color: var(--blue-900);">
                        {{ $alumno->paterno }} {{ $alumno->materno }} {{ $alumno->nombre }}
                    </td>
                    <td style="color: var(--gray-600); font-size: 13px;">
                        <strong>{{ $alumno->centro_clave }}</strong> - {{ $alumno->centro_nombre }}
                    </td>
                    <td style="text-align: center;">
                        @if($alumno->promedio_general !== null)
                            <span style="font-weight: 700; color: {{ $alumno->promedio_general >= 6 ? 'var(--green-600)' : 'var(--red-600)' }};">
                                {{ number_format($alumno->promedio_general, 2) }}
                            </span>
                        @else
                            <span style="color: var(--gray-400); font-style: italic; font-size: 13px;">S/C</span>
                        @endif
                    </td>
                    <td>
                        <button onclick="loadTab('{{ route('calificaciones.show', $alumno->id) }}')" class="btn" style="background: var(--blue-50); border: 1px solid var(--blue-200); color: var(--blue-700); padding: 0.3rem 0.6rem; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.3rem;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            Capturar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                        No se encontraron alumnos con los filtros de búsqueda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Paginador Manual -->
    @if($totalPages > 1)
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid var(--gray-200); background: var(--gray-50); border-radius: 0 0 var(--radius-md) var(--radius-md);">
        <div style="font-size: 13px; color: var(--gray-600);">
            Mostrando página <strong>{{ $page }}</strong> de <strong>{{ $totalPages }}</strong> ({{ $total }} alumnos totales)
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            @php
                $urlParams = "&search=" . urlencode($search);
                if (!empty($centroFiltro)) $urlParams .= "&centro=" . urlencode($centroFiltro);
                $urlParams .= "&ajax=1";
            @endphp
            
            @if($page > 1)
                <button onclick="loadTab('{{ route('calificaciones.index') }}?page={{ $page - 1 }}{{ $urlParams }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-900); padding: 0.4rem 1rem; font-size: 13px; cursor:pointer;">
                    Anterior
                </button>
            @else
                <button disabled class="btn" style="background: transparent; border: 1px solid var(--gray-200); color: var(--gray-400); padding: 0.4rem 1rem; font-size: 13px; cursor: not-allowed;">
                    Anterior
                </button>
            @endif

            @if($page < $totalPages)
                <button onclick="loadTab('{{ route('calificaciones.index') }}?page={{ $page + 1 }}{{ $urlParams }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-900); padding: 0.4rem 1rem; font-size: 13px; cursor:pointer;">
                    Siguiente
                </button>
            @else
                <button disabled class="btn" style="background: transparent; border: 1px solid var(--gray-200); color: var(--gray-400); padding: 0.4rem 1rem; font-size: 13px; cursor: not-allowed;">
                    Siguiente
                </button>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
    function applyCalificacionFilters() {
        let search = document.getElementById('global-search') ? document.getElementById('global-search').value : '{{ $search }}';
        let centro = document.getElementById('filter-centro').value;
        
        let url = '{{ route("calificaciones.index") }}?search=' + encodeURIComponent(search);
        if (centro) url += '&centro=' + encodeURIComponent(centro);
        url += '&ajax=1';
        
        loadTab(url);
    }

    // Configurar URL base para la barra de búsqueda global
    var baseUrl = '{{ route("calificaciones.index") }}?centro={{ urlencode($centroFiltro) }}&search=';
    window.currentModuleSearchUrl = baseUrl.slice(0, -1); // quita el '=' para que la funcion principal lo arme
    
    // Restaurar buscador global si estaba oculto
    var topSearch = document.getElementById('top-search-container');
    var topSearchInput = document.getElementById('global-search');
    if (topSearch) {
        topSearch.style.display = 'flex';
        if (topSearchInput) {
            topSearchInput.value = '{{ $search }}';
            topSearchInput.placeholder = 'Buscar alumno por matrícula o nombre...';
        }
    }
</script>
