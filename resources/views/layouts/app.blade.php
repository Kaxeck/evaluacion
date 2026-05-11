<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema TBC — Evaluación</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <!-- Estilos principales del sistema -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <!-- TOP HEADER -->
    <header class="top-header">
        <button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
        <div class="header-logo">
            <div class="logo-mark">U</div>
            <div>
                <div class="logo-text">Sistema</div>
                <div class="logo-sub">Gestión escolar</div>
            </div>
        </div>
        <div class="header-search" id="top-search-container" style="display: none;">
            <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <path d="m21 21-4.35-4.35" />
            </svg>
            <input id="global-search" onkeypress="performGlobalSearch(event)" placeholder="Buscar...">
        </div>
        <div class="header-right">
            <button class="mobile-search-toggle" onclick="document.querySelector('.header-search').classList.toggle('open')" style="background:none; border:none; color:white; display:none;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
            <span class="header-pill">Ciclo 2024–2</span>
            <div class="avatar">AD</div>
        </div>
    </header>

    <!-- LAYOUT -->
    <div class="layout">

        <!-- SIDEBAR -->
        <nav class="sidebar">
            <div class="nav-section-label">Principal</div>
            <a class="nav-item active" id="nav-inicio" onclick="loadTab('{{ route('tabs.inicio') }}', this)">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                Inicio
            </a>

            <a class="nav-item" id="nav-centros" onclick="loadTab('{{ route('centros.index') }}', this)">
                <img src="{{ asset('images/centros.png') }}" alt="Centros"
                    style="width:24px; height:24px; object-fit:contain;">
                Centros
            </a>
            <a class="nav-item" id="nav-alumnos" onclick="loadTab('{{ route('alumnos.index') }}', this)">
                <img src="{{ asset('images/alumnos.png') }}" alt="Alumnos"
                    style="width:24px; height:24px; object-fit:contain;">
                Alumnos
            </a>
            <a class="nav-item" id="nav-calificaciones" onclick="loadTab('{{ route('calificaciones.index') }}', this)">
                <img src="{{ asset('images/calificaciones.png') }}" alt="Calificaciones"
                    style="width:24px; height:24px; object-fit:contain;">
                Calificaciones
            </a>

            <div class="nav-section-label">Herramientas</div>
            <a class="nav-item" id="nav-importar" onclick="loadTab('{{ route('import.index') }}', this)">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                    <polyline points="17 8 12 3 7 8" />
                    <line x1="12" y1="3" x2="12" y2="15" />
                </svg>
                Importar Excel
            </a>

        </nav>

        <!-- MAIN -->
        <main class="main" id="main-content">
            <!-- El contenido cargado por defecto -->
            @yield('content')
        </main>
    </div>

    <script>
        window.currentModuleSearchUrl = null;

        function performGlobalSearch(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                if (window.currentModuleSearchUrl) {
                    let term = document.getElementById('global-search').value;
                    let url = window.currentModuleSearchUrl;
                    url += (url.includes('?') ? '&' : '?') + 'search=' + encodeURIComponent(term) + '&ajax=1';
                    loadTab(url);
                }
            }
        }

        function loadTab(url, element = null) {
            // Cerrar menú lateral en móviles al hacer clic
            let sidebar = document.querySelector('.sidebar');
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }

            // Actualizar URL del navegador sin recargar la página
            if (url && !url.includes('ajax=1')) {
                window.history.pushState({}, '', url);
            }

            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');

            document.getElementById('main-content').innerHTML = '<div style="text-align:center; padding: 3rem; color: var(--gray-400);">Cargando...</div>';

            let fetchUrl = url + (url.includes('?') ? '&' : '?') + 'ajax=1';
            fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Error de red');
                    return response.text();
                })
                .then(html => {
                    document.getElementById('main-content').innerHTML = html;
                    let scripts = document.getElementById('main-content').querySelectorAll('script');
                    scripts.forEach(script => {
                        let newScript = document.createElement('script');
                        newScript.text = script.innerHTML;
                        document.body.appendChild(newScript).parentNode.removeChild(newScript);
                    });
                })
                .catch(error => {
                    document.getElementById('main-content').innerHTML = '<div class="alert alert-danger">Error cargando el contenido. Inténtalo nuevamente.</div>';
                    console.error(error);
                });
        }
    </script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>