<div class="page-header" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <div class="page-title">Detalles del Plantel</div>
        <div class="page-subtitle">{{ $centro->clave }} - {{ $centro->nombre }}</div>
    </div>
    
    <div>
        <button onclick="loadTab('{{ route('centros.index') }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--gray-700); padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Volver
        </button>
    </div>
</div>

<div class="card" style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            
            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Clave del Centro</div>
                <div style="font-size: 16px; font-weight: 500; color: var(--blue-900);">{{ $centro->clave }}</div>
            </div>

            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Nombre del Plantel</div>
                <div style="font-size: 16px; font-weight: 500; color: var(--blue-900);">{{ $centro->nombre }}</div>
            </div>

            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Clave CCT</div>
                <div style="font-size: 16px; font-weight: 500; color: var(--blue-900);">{{ $centro->clave_cct }}</div>
            </div>

            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Municipio</div>
                <div style="font-size: 16px; font-weight: 500; color: var(--blue-900);">{{ $centro->municipio }}</div>
            </div>

            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Encargado(a)</div>
                <div style="font-size: 16px; font-weight: 500; color: var(--blue-900);">{{ $centro->encargado ?? 'No registrado' }}</div>
            </div>

            <div style="background: var(--gray-50); padding: 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--gray-200);">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Correo Electrónico</div>
                <div style="font-size: 16px; font-weight: 500; color: var(--blue-900);">
                    @if($centro->correo_encargado)
                        <a href="mailto:{{ $centro->correo_encargado }}" style="color: var(--blue-600); text-decoration: none;">{{ $centro->correo_encargado }}</a>
                    @else
                        No registrado
                    @endif
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<script>
    // Ocultar buscador global
    var topSearch = document.getElementById('top-search-container');
    if (topSearch) topSearch.style.display = 'none';
</script>
