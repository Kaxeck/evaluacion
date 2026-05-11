<div class="page-header" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
  <div>
    <div class="page-title">Padrón de Alumnos</div>
    <div class="page-subtitle">Gestión de la base de datos de estudiantes</div>
  </div>
  
  <div class="filters-container">
      <label for="filter-centro" style="font-size: 13px; color: var(--gray-600); font-weight: 500;">Plantel:</label>
      <select id="filter-centro" onchange="applyAlumnoFilters()" style="max-width: 200px; padding: 0.4rem 2rem 0.4rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 13px; color: var(--blue-900); background-color: var(--white); cursor: pointer; outline: none; text-overflow: ellipsis;">
          <option value="">Todos los planteles</option>
          @foreach($listaCentros as $centro)
              <option value="{{ $centro->id }}" {{ $centroFiltro == $centro->id ? 'selected' : '' }}>
                  {{ $centro->clave }} - {{ $centro->nombre }} ({{ $centro->alumnos_count }} alumnos)
              </option>
          @endforeach
      </select>

      <label for="filter-estatus" style="font-size: 13px; color: var(--gray-600); font-weight: 500; margin-left: 0.5rem;">Estatus:</label>
      <select id="filter-estatus" onchange="applyAlumnoFilters()" style="padding: 0.4rem 2rem 0.4rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 13px; color: var(--blue-900); background-color: var(--white); cursor: pointer; outline: none;">
          <option value="">Todos</option>
          <option value="ALUMNO ACTIVO" {{ $estatusFiltro == 'ALUMNO ACTIVO' ? 'selected' : '' }}>Activo</option>
          <option value="ALUMNO INACTIVO" {{ $estatusFiltro == 'ALUMNO INACTIVO' ? 'selected' : '' }}>Inactivo</option>
      </select>

      <label for="filter-genero" style="font-size: 13px; color: var(--gray-600); font-weight: 500; margin-left: 0.5rem;">Género:</label>
      <select id="filter-genero" onchange="applyAlumnoFilters()" style="padding: 0.4rem 2rem 0.4rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 13px; color: var(--blue-900); background-color: var(--white); cursor: pointer; outline: none;">
          <option value="">Ambos</option>
          <option value="H" {{ $generoFiltro == 'H' ? 'selected' : '' }}>Hombre (H)</option>
          <option value="M" {{ $generoFiltro == 'M' ? 'selected' : '' }}>Mujer (M)</option>
      </select>
  </div>
</div>

<script>
    function applyAlumnoFilters() {
        var estatus = document.getElementById('filter-estatus').value;
        var genero = document.getElementById('filter-genero').value;
        var centro = document.getElementById('filter-centro').value;
        var search = document.getElementById('global-search') ? document.getElementById('global-search').value : '';
        
        var url = '{{ route("alumnos.index") }}?search=' + encodeURIComponent(search);
        if (estatus) url += '&estatus=' + encodeURIComponent(estatus);
        if (genero) url += '&genero=' + encodeURIComponent(genero);
        if (centro) url += '&centro=' + encodeURIComponent(centro);
        url += '&ajax=1';
        
        loadTab(url);
    }

    // Configurar buscador global
    var topSearch = document.getElementById('top-search-container');
    if (topSearch) {
        topSearch.style.display = '';
        var searchInput = document.getElementById('global-search');
        if (searchInput) {
            searchInput.placeholder = 'Buscar por nombre, matrícula o plantel...';
            searchInput.value = '{{ $search }}';
        }
    }
    
    // Configurar la URL actual del módulo para retener filtros
    var currentEstatus = '{{ $estatusFiltro }}';
    var currentGenero = '{{ $generoFiltro }}';
    var currentCentro = '{{ $centroFiltro }}';
    var baseUrl = '{{ route("alumnos.index") }}?';
    if (currentEstatus) baseUrl += 'estatus=' + encodeURIComponent(currentEstatus) + '&';
    if (currentGenero) baseUrl += 'genero=' + encodeURIComponent(currentGenero) + '&';
    if (currentCentro) baseUrl += 'centro=' + encodeURIComponent(currentCentro) + '&';
    window.currentModuleSearchUrl = baseUrl.slice(0, -1); // quita el último & o ?
</script>

<div class="card">
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre Completo</th>
                    <th>Plantel (Centro)</th>
                    <th>Género</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
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
                    <td style="color: var(--gray-600);">{{ $alumno->genero }}</td>
                    <td>
                        @if(trim(strtoupper($alumno->estatus)) == 'ALUMNO ACTIVO')
                            <span class="td-badge badge-active">Activo</span>
                        @else
                            <span class="td-badge badge-inactive">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <button onclick="loadTab('{{ route('alumnos.edit', $alumno->id) }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-600); padding: 0.3rem 0.6rem; border-radius: var(--radius-sm); font-size: 13px; font-weight: 500; cursor: pointer;">
                            Editar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                        No se encontraron alumnos con los filtros seleccionados.
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
            Mostrando página <strong>{{ $page }}</strong> de <strong>{{ $totalPages }}</strong> ({{ number_format($total) }} registros totales)
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            @php
                $urlParams = "&search=" . urlencode($search);
                if (!empty($estatusFiltro)) $urlParams .= "&estatus=" . urlencode($estatusFiltro);
                if (!empty($generoFiltro)) $urlParams .= "&genero=" . urlencode($generoFiltro);
                if (!empty($centroFiltro)) $urlParams .= "&centro=" . urlencode($centroFiltro);
                $urlParams .= "&ajax=1";
            @endphp
            
            @if($page > 1)
                <button onclick="loadTab('{{ route('alumnos.index') }}?page={{ $page - 1 }}{{ $urlParams }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-900); padding: 0.4rem 1rem; font-size: 13px;">
                    Anterior
                </button>
            @else
                <button disabled class="btn" style="background: transparent; border: 1px solid var(--gray-200); color: var(--gray-400); padding: 0.4rem 1rem; font-size: 13px; cursor: not-allowed;">
                    Anterior
                </button>
            @endif

            @if($page < $totalPages)
                <button onclick="loadTab('{{ route('alumnos.index') }}?page={{ $page + 1 }}{{ $urlParams }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-900); padding: 0.4rem 1rem; font-size: 13px;">
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
