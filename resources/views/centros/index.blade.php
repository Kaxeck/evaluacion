<div class="page-header" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
  <div>
    <div class="page-title">Catálogo de Centros</div>
    <div class="page-subtitle">Listado oficial de planteles Telebachillerato</div>
  </div>
  
  <div style="display: flex; gap: 0.5rem; align-items: center;">
      <label for="filter-municipio" style="font-size: 13px; color: var(--gray-600); font-weight: 500;">Filtrar por Municipio:</label>
      <select id="filter-municipio" onchange="applyFilters()" style="padding: 0.4rem 2rem 0.4rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 13px; color: var(--blue-900); background-color: var(--white); cursor: pointer; outline: none;">
          <option value="">Todos los municipios</option>
          @foreach($municipios as $mun)
              <option value="{{ $mun->municipio }}" {{ $municipioFiltro == $mun->municipio ? 'selected' : '' }}>{{ $mun->municipio }}</option>
          @endforeach
      </select>
  </div>
</div>

<script>
    function applyFilters() {
        let municipio = document.getElementById('filter-municipio').value;
        let search = document.getElementById('global-search') ? document.getElementById('global-search').value : '';
        let url = '{{ route("centros.index") }}?search=' + encodeURIComponent(search) + '&municipio=' + encodeURIComponent(municipio) + '&ajax=1';
        loadTab(url);
    }

    // Configurar buscador global para el módulo Centros
    var topSearch = document.getElementById('top-search-container');
    if (topSearch) {
        topSearch.style.display = 'flex';
        var searchInput = document.getElementById('global-search');
        if (searchInput) {
            searchInput.placeholder = 'Buscar por nombre, CCT, correo...';
            searchInput.value = '{{ $search }}';
        }
    }
    
    // Configuramos la URL actual para que el buscador global mantenga el filtro
    let currentMunicipio = '{{ $municipioFiltro }}';
    window.currentModuleSearchUrl = '{{ route("centros.index") }}' + (currentMunicipio ? '?municipio=' + encodeURIComponent(currentMunicipio) : '');
</script>

<div class="card">
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Clave</th>
                    <th>Nombre del Plantel</th>
                    <th>CCT</th>
                    <th>Municipio</th>
                    <th>Encargado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($centros as $centro)
                <tr>
                    <td><span class="td-badge badge-active">{{ $centro->clave }}</span></td>
                    <td style="font-weight: 500; color: var(--blue-900);">{{ $centro->nombre }}</td>
                    <td class="td-mono">{{ $centro->clave_cct }}</td>
                    <td style="color: var(--gray-600);">{{ $centro->municipio }}</td>
                    <td style="font-size: 13px; color: var(--gray-600);">
                        {{ $centro->encargado }}<br>
                        <small style="color: var(--blue-400);">{{ $centro->correo_encargado }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--gray-400);">
                        No se encontraron centros registrados.
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
            Mostrando página <strong>{{ $page }}</strong> de <strong>{{ $totalPages }}</strong> ({{ $total }} registros totales)
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            @if($page > 1)
                <button onclick="loadTab('{{ route('centros.index') }}?page={{ $page - 1 }}&search={{ urlencode($search) }}&municipio={{ urlencode($municipioFiltro) }}&ajax=1')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-900); padding: 0.4rem 1rem; font-size: 13px;">
                    Anterior
                </button>
            @else
                <button disabled class="btn" style="background: transparent; border: 1px solid var(--gray-200); color: var(--gray-400); padding: 0.4rem 1rem; font-size: 13px; cursor: not-allowed;">
                    Anterior
                </button>
            @endif

            @if($page < $totalPages)
                <button onclick="loadTab('{{ route('centros.index') }}?page={{ $page + 1 }}&search={{ urlencode($search) }}&municipio={{ urlencode($municipioFiltro) }}&ajax=1')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--blue-900); padding: 0.4rem 1rem; font-size: 13px;">
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


