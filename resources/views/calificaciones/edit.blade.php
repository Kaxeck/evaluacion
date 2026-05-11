<div class="page-header" style="justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <div class="page-title">Boleta de Calificaciones</div>
        <div class="page-subtitle">{{ $alumno->paterno }} {{ $alumno->materno }} {{ $alumno->nombre }} | Matrícula: {{ $alumno->matricula }}</div>
    </div>
    
    <div style="display: flex; gap: 0.5rem;">
        <button onclick="loadTab(window.currentModuleSearchUrl || '{{ route('calificaciones.index') }}')" class="btn" style="background: white; border: 1px solid var(--gray-200); color: var(--gray-700); padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; cursor: pointer;">
            Cancelar
        </button>
        <button onclick="submitCalificaciones()" id="btn-save-califs" class="btn" style="background: var(--blue-600); border: 1px solid var(--blue-700); color: white; padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Guardar Boleta
        </button>
    </div>
</div>

<div id="form-alert" style="display: none; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-size: 14px; font-weight: 500;"></div>

<div class="card">
    <div class="table-container">
        <table class="data-table" id="grades-table">
            <thead>
                <tr>
                    <th style="width: 40%">Materia</th>
                    <th style="text-align: center; width: 15%">Parcial 1</th>
                    <th style="text-align: center; width: 15%">Parcial 2</th>
                    <th style="text-align: center; width: 15%">Parcial 3</th>
                    <th style="text-align: center; width: 15%">Promedio Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach($materias as $materia)
                    @php
                        $calif = isset($calificaciones[$materia->id]) ? $calificaciones[$materia->id] : null;
                    @endphp
                    <tr class="materia-row" data-id="{{ $materia->id }}">
                        <td style="font-weight: 500; color: var(--blue-900);">
                            {{ $materia->nombre }}
                            <div style="font-size: 11px; color: var(--gray-500); font-weight: 400; margin-top: 2px;">Créditos: {{ $materia->creditos }}</div>
                        </td>
                        <td style="text-align: center;">
                            <input type="number" step="0.1" min="0" max="10" class="grade-input p1" 
                                   value="{{ $calif ? $calif->parcial1 : '' }}" 
                                   oninput="calculateAverage(this)" 
                                   style="width: 70px; text-align: center; padding: 0.4rem; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); outline: none;">
                        </td>
                        <td style="text-align: center;">
                            <input type="number" step="0.1" min="0" max="10" class="grade-input p2" 
                                   value="{{ $calif ? $calif->parcial2 : '' }}" 
                                   oninput="calculateAverage(this)" 
                                   style="width: 70px; text-align: center; padding: 0.4rem; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); outline: none;">
                        </td>
                        <td style="text-align: center;">
                            <input type="number" step="0.1" min="0" max="10" class="grade-input p3" 
                                   value="{{ $calif ? $calif->parcial3 : '' }}" 
                                   oninput="calculateAverage(this)" 
                                   style="width: 70px; text-align: center; padding: 0.4rem; border: 1px solid var(--gray-300); border-radius: var(--radius-sm); outline: none;">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <div class="promedio-display" style="font-weight: 700; font-size: 16px; padding: 0.3rem 0.6rem; border-radius: 100px; display: inline-block; min-width: 60px;">
                                {{ $calif && $calif->promedio !== null ? number_format($calif->promedio, 2) : '--' }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .grade-input:focus { border-color: var(--blue-500); box-shadow: 0 0 0 2px var(--blue-100); }
    .grade-input:invalid { border-color: #ef4444; color: #b91c1c; background-color: #fef2f2; }
    
    .promedio-na { background: var(--gray-100); color: var(--gray-500); }
    .promedio-aprobado { background: #d1fae5; color: #065f46; }
    .promedio-reprobado { background: #fee2e2; color: #b91c1c; }
</style>

<script>
    // Ocultar buscador global
    var topSearch = document.getElementById('top-search-container');
    if (topSearch) topSearch.style.display = 'none';

    // Inicializar colores de los promedios al cargar
    document.querySelectorAll('.materia-row').forEach(row => {
        let display = row.querySelector('.promedio-display');
        let val = parseFloat(display.innerText);
        updatePromedioColor(display, val);
    });

    function calculateAverage(inputElement) {
        // Validación de rango instantánea
        if (inputElement.value !== "") {
            let val = parseFloat(inputElement.value);
            if (val < 0) inputElement.value = 0;
            if (val > 10) inputElement.value = 10;
        }

        const row = inputElement.closest('tr');
        const p1 = parseFloat(row.querySelector('.p1').value);
        const p2 = parseFloat(row.querySelector('.p2').value);
        const p3 = parseFloat(row.querySelector('.p3').value);
        const display = row.querySelector('.promedio-display');

        let count = 0;
        let sum = 0;

        if (!isNaN(p1)) { sum += p1; count++; }
        if (!isNaN(p2)) { sum += p2; count++; }
        if (!isNaN(p3)) { sum += p3; count++; }

        if (count === 0) {
            display.innerText = '--';
            updatePromedioColor(display, NaN);
        } else {
            let promedio = sum / count;
            // Para mostrar 2 decimales sin redondear hacia arriba falsamente en calificaciones limítrofes
            display.innerText = promedio.toFixed(2);
            updatePromedioColor(display, promedio);
        }
    }

    function updatePromedioColor(displayEl, value) {
        displayEl.classList.remove('promedio-na', 'promedio-aprobado', 'promedio-reprobado');
        
        if (isNaN(value)) {
            displayEl.classList.add('promedio-na');
        } else if (value >= 6.0) {
            displayEl.classList.add('promedio-aprobado');
        } else {
            displayEl.classList.add('promedio-reprobado');
        }
    }

    function submitCalificaciones() {
        const rows = document.querySelectorAll('.materia-row');
        let payload = {};
        let hasInvalidFields = false;

        rows.forEach(row => {
            const materiaId = row.getAttribute('data-id');
            const p1 = row.querySelector('.p1');
            const p2 = row.querySelector('.p2');
            const p3 = row.querySelector('.p3');
            
            // Check HTML5 validity
            if (!p1.checkValidity() || !p2.checkValidity() || !p3.checkValidity()) {
                hasInvalidFields = true;
            }

            const p1Val = p1.value;
            const p2Val = p2.value;
            const p3Val = p3.value;
            const promedioText = row.querySelector('.promedio-display').innerText;
            const promedio = promedioText === '--' ? null : parseFloat(promedioText);

            // Solo enviar materias que tengan al menos una calificación ingresada, o enviar vacíos si se borraron
            payload[materiaId] = {
                parcial1: p1Val !== "" ? p1Val : null,
                parcial2: p2Val !== "" ? p2Val : null,
                parcial3: p3Val !== "" ? p3Val : null,
                promedio: promedio
            };
        });

        const alertBox = document.getElementById('form-alert');

        if (hasInvalidFields) {
            alertBox.style.display = 'block';
            alertBox.style.backgroundColor = '#fee2e2';
            alertBox.style.color = '#b91c1c';
            alertBox.style.border = '1px solid #ef4444';
            alertBox.innerText = "Revisa los campos en rojo. Las calificaciones deben ser números entre 0 y 10.";
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        const btnSave = document.getElementById('btn-save-califs');
        btnSave.disabled = true;
        btnSave.innerHTML = 'Guardando...';
        alertBox.style.display = 'none';

        // Enviar mediante fetch JSON
        fetch('{{ route("calificaciones.store", $alumno->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ grades: payload })
        })
        .then(response => {
            if (!response.ok) throw new Error('Error de red al guardar.');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alertBox.style.display = 'block';
                alertBox.style.backgroundColor = '#d1fae5';
                alertBox.style.color = '#065f46';
                alertBox.style.border = '1px solid #10b981';
                alertBox.innerText = data.message;
                
                setTimeout(() => {
                    loadTab(window.currentModuleSearchUrl || '{{ route("calificaciones.index") }}');
                }, 1000);
            } else {
                throw new Error(data.message || 'Error del servidor.');
            }
        })
        .catch(error => {
            alertBox.style.display = 'block';
            alertBox.style.backgroundColor = '#fee2e2';
            alertBox.style.color = '#b91c1c';
            alertBox.style.border = '1px solid #ef4444';
            alertBox.innerText = error.message;
            
            btnSave.disabled = false;
            btnSave.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Guardar Boleta';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
</script>
