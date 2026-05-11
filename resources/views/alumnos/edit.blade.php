<div class="page-header" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <div class="page-title">Detalle y Edición de Alumno</div>
        <div class="page-subtitle">Matrícula: {{ $alumno->matricula }}</div>
    </div>
    
    <div>
        <button onclick="loadTab(window.currentModuleSearchUrl || '{{ route('alumnos.index') }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--gray-700); padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Cancelar y Volver
        </button>
    </div>
</div>

<div class="card" style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    
    <div id="form-alert" style="display: none; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 14px; font-weight: 500;"></div>

    <form id="edit-alumno-form" onsubmit="submitAlumnoEdit(event)">
        <!-- CSRF Token para Laravel -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="nombre" style="font-size: 13px; font-weight: 500; color: var(--gray-700);">Nombre(s)</label>
                <input type="text" id="nombre" name="nombre" value="{{ $alumno->nombre }}" required style="padding: 0.6rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 14px; outline: none; width: 100%;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="paterno" style="font-size: 13px; font-weight: 500; color: var(--gray-700);">Apellido Paterno</label>
                <input type="text" id="paterno" name="paterno" value="{{ $alumno->paterno }}" style="padding: 0.6rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 14px; outline: none; width: 100%;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="materno" style="font-size: 13px; font-weight: 500; color: var(--gray-700);">Apellido Materno</label>
                <input type="text" id="materno" name="materno" value="{{ $alumno->materno }}" style="padding: 0.6rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 14px; outline: none; width: 100%;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="genero" style="font-size: 13px; font-weight: 500; color: var(--gray-700);">Género</label>
                <select id="genero" name="genero" style="padding: 0.6rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 14px; outline: none; width: 100%; cursor: pointer;">
                    <option value="H" {{ $alumno->genero == 'H' ? 'selected' : '' }}>Hombre (H)</option>
                    <option value="M" {{ $alumno->genero == 'M' ? 'selected' : '' }}>Mujer (M)</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="estatus" style="font-size: 13px; font-weight: 500; color: var(--gray-700);">Estatus</label>
                <select id="estatus" name="estatus" style="padding: 0.6rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 14px; outline: none; width: 100%; cursor: pointer;">
                    <option value="ALUMNO ACTIVO" {{ trim(strtoupper($alumno->estatus)) == 'ALUMNO ACTIVO' ? 'selected' : '' }}>Activo</option>
                    <option value="ALUMNO INACTIVO" {{ trim(strtoupper($alumno->estatus)) == 'ALUMNO INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem; grid-column: 1 / -1;">
                <label for="centro_id" style="font-size: 13px; font-weight: 500; color: var(--gray-700);">Plantel Asignado</label>
                <select id="centro_id" name="centro_id" required style="padding: 0.6rem 1rem; border: 1px solid var(--gray-200); border-radius: var(--radius-sm); font-family: var(--font-body); font-size: 14px; outline: none; width: 100%; cursor: pointer;">
                    @foreach($centros as $centro)
                        <option value="{{ $centro->id }}" {{ $alumno->centro_id == $centro->id ? 'selected' : '' }}>
                            {{ $centro->clave }} - {{ $centro->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div style="display: flex; justify-content: flex-end; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
            <button type="submit" id="btn-save" class="btn" style="background: var(--blue-600); color: white; padding: 0.6rem 1.5rem; border: none; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
    // Ocultar buscador global
    var topSearch = document.getElementById('top-search-container');
    if (topSearch) topSearch.style.display = 'none';

    function submitAlumnoEdit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const btnSave = document.getElementById('btn-save');
        const alertBox = document.getElementById('form-alert');
        
        btnSave.disabled = true;
        btnSave.innerHTML = 'Guardando...';
        alertBox.style.display = 'none';
        
        fetch('{{ route("alumnos.update", $alumno->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en el servidor al guardar los datos.');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alertBox.style.display = 'block';
                alertBox.style.backgroundColor = '#d1fae5';
                alertBox.style.color = '#065f46';
                alertBox.style.border = '1px solid #10b981';
                alertBox.innerText = data.message;
                
                // Retornar al index después de 1 segundo para mostrar el mensaje de éxito
                setTimeout(() => {
                    loadTab(window.currentModuleSearchUrl || '{{ route("alumnos.index") }}');
                }, 1000);
            } else {
                throw new Error(data.message || 'Error desconocido.');
            }
        })
        .catch(error => {
            alertBox.style.display = 'block';
            alertBox.style.backgroundColor = '#fee2e2';
            alertBox.style.color = '#b91c1c';
            alertBox.style.border = '1px solid #ef4444';
            alertBox.innerText = error.message;
            
            btnSave.disabled = false;
            btnSave.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Guardar Cambios';
        });
    }
</script>
