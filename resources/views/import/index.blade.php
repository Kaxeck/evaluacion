@extends( (request()->ajax() || request()->has('ajax')) ? 'layouts.empty' : 'layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; text-align: center;">
    <div class="page-header" style="justify-content: center; margin-bottom: 2rem;">
      <div style="text-align: center;">
        <div class="page-title">Importar datos</div>
        <div class="page-subtitle">Carga de archivos Excel — centros y alumnos</div>
      </div>
    </div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin-bottom:0; padding-left:20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div style="display:flex; flex-wrap:wrap; gap:1.5rem; justify-content:center; margin-bottom:1.5rem;">
        <div class="card" style="padding:1.5rem; flex: 1 1 300px; max-width: 500px;">
            <div style="font-size:14px;font-weight:600;color:var(--blue-900);margin-bottom:1rem; text-align:center;">📊 Archivo de Centros</div>
            <div class="import-zone">
                <input type="file" name="centros_file" accept=".xlsx,.xls">
                <div class="import-zone-icon">📁</div>
                <div class="import-zone-text">Arrastra tu archivo Excel aquí<br>o haz click para seleccionar</div>
            </div>
        </div>
        
        <div class="card" style="padding:1.5rem; flex: 1 1 300px; max-width: 500px;">
            <div style="font-size:14px;font-weight:600;color:var(--blue-900);margin-bottom:1rem; text-align:center;">👥 Archivo de Alumnos</div>
            <div class="import-zone">
                <input type="file" name="alumnos_file" accept=".xlsx,.xls">
                <div class="import-zone-icon">📁</div>
                <div class="import-zone-text">Arrastra tu archivo Excel aquí<br>o haz click para seleccionar</div>
            </div>
        </div>
    </div>
    
    <div style="display:flex; justify-content:center;">
        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; font-size: 15px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Procesar Archivos Excel
        </button>
    </div>
</form>

<script>
    // Mostrar el nombre del archivo seleccionado en la zona de importación
    document.querySelectorAll('.import-zone input[type="file"]').forEach(input => {
        input.addEventListener('change', function(e) {
            let fileName = e.target.files[0] ? e.target.files[0].name : 'Arrastra tu archivo Excel aquí<br>o haz click para seleccionar';
            let textContainer = this.parentElement.querySelector('.import-zone-text');
            
            if (e.target.files[0]) {
                textContainer.innerHTML = `<span style="color:var(--blue-600); font-weight:600;">📄 ${fileName}</span><br><small style="color:var(--gray-600)">Archivo listo para procesar</small>`;
                this.parentElement.style.borderColor = 'var(--blue-600)';
                this.parentElement.style.background = '#eaf0ff';
            } else {
                textContainer.innerHTML = fileName;
                this.parentElement.style.borderColor = 'var(--blue-300)';
                this.parentElement.style.background = 'var(--blue-50)';
            }
        });
    });
</script>

<!-- Tarjeta de Logs (Errores) -->
@php
    $logs = \Illuminate\Support\Facades\DB::select('SELECT * FROM logs_importacion ORDER BY id DESC LIMIT 1000');
@endphp

@if(count($logs) > 0)
<div class="card" style="margin-top:2rem;">
    <div class="card-header" style="background:#fdf3f4; border-bottom: 1px solid #fad3d8;">
        <div class="card-title" style="color:#c0392b;">Registro de Inconsistencias (Últimas {{ count($logs) }})</div>
    </div>
    <div style="max-height: 400px; overflow-y: auto;">
        <table style="margin-bottom: 0;">
            <thead style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th>Archivo</th>
                    <th>Fila</th>
                    <th>Error detectado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            @foreach($logs as $log)
            <tr>
                <td><span class="td-badge badge-inactive">{{ $log->archivo }}</span></td>
                <td class="td-mono">Fila {{ $log->fila }}</td>
                <td style="color:#c0392b; font-weight:500;">{{ $log->error_mensaje }}</td>
                <td style="color:var(--gray-400); font-size:12px;">{{ $log->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif
</div>
@endsection
