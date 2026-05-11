# Sistema de Evaluación TBC

🌐 **Demo:** [https://sistema-de-evaluacion-production.up.railway.app/](https://sistema-de-evaluacion-production.up.railway.app/)

Sistema de gestión escolar desarrollado en Laravel enfocado en alto rendimiento mediante el uso estricto de SQL puro (Arquitectura No-ORM) e interfaces de usuario dinámicas tipo Single Page Application (SPA) usando AJAX nativo.

## Características Principales

*   **Gestión de Planteles (Centros):** Visualización y detalle de planteles educativos.
*   **Gestión de Alumnos:** Padrón completo con filtros dinámicos cruzados (Plantel, Género, Estatus) y barra de búsqueda inteligente.
*   **Captura de Calificaciones:** Matriz de captura rápida (tipo Excel) con cálculo automático de promedios en tiempo real y validaciones estrictas.
*   **Módulo de Importación CSV:** Carga masiva de datos normalizando inconsistencias al vuelo.
*   **Arquitectura No-ORM:** Cero uso de Eloquent para consultas de datos, utilizando exclusivamente el Facade `DB` y sentencias preparadas para un máximo rendimiento y prevención de inyecciones SQL.
*   **UI Dinámica:** Navegación "Zero-Reload" mediante API Fetch.

## 📱 Aplicación Móvil & Web Service

El sistema expone un **Web Service / API RESTful** en la ruta `/api/alumnos/{matricula}` que retorna un JSON estructurado y anidado con la información completa de la escuela, el perfil del alumno y sus calificaciones.

En conjunto con esto, se desarrolló una **App Móvil Nativa en Flutter** que consume dicha API. La aplicación:
* Réplica la estética y la paleta de colores del portal web.
* Muestra de forma dinámica los promedios y desglose de parciales con manejo de estados visuales.
* Está lista para compilarse en APK y conectarse a la base de datos en tiempo real.

## Requisitos Previos

*   PHP >= 8.1
*   Composer
*   MySQL / MariaDB
*   Servidor Web (Apache/Nginx o servidor integrado de PHP)

## Instrucciones de Instalación

1.  **Clonar el repositorio o descomprimir el proyecto:**
    ```bash
    cd evaluacion
    ```

2.  **Instalar dependencias de PHP:**
    ```bash
    composer install
    ```

3.  **Configurar el entorno:**
    Duplica el archivo de configuración base:
    ```bash
    cp .env.example .env
    ```
    Genera la llave de la aplicación:
    ```bash
    php artisan key:generate
    ```

4.  **Configurar la Base de Datos:**
    Abre el archivo `.env` en tu editor de texto y configura las credenciales de tu base de datos MySQL local:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nombre_de_tu_base_de_datos
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_contraseña
    ```
    *(Asegúrate de crear la base de datos vacía en tu gestor MySQL antes de continuar)*.

5.  **Ejecutar Migraciones Básicas:**
    Corre las migraciones base de Laravel (usuarios, sesiones, cache):
    ```bash
    php artisan migrate
    ```

6.  **Construir Estructura de Datos (No-ORM):**
    La estructura de las tablas maestras NO se encuentra en migraciones de Laravel. Se debe importar el archivo `database.sql` en tu gestor de base de datos para crear las tablas de `centros`, `alumnos`, `materias` y `calificaciones`.

7.  **Poblar Datos Iniciales:**
    El archivo `database.sql` que importaste en el paso anterior ya incluye las inserciones base para el catálogo de **Materias** (Matemáticas, Química, etc.). 
    Para poblar los **Centros** y **Alumnos**, utiliza el Módulo de Importación CSV dentro de la propia aplicación web.

8.  **Levantar el Servidor de Desarrollo:**
    ```bash
    php artisan serve
    ```
    El sistema estará disponible en `http://localhost:8000`.

## Uso del Sistema

*   **Paso 1:** Navega al módulo de importación y carga tus archivos CSV para poblar los catálogos de Centros y Alumnos.
*   **Paso 2:** Utiliza la barra lateral para navegar a **Alumnos** y verificar la carga de datos usando los filtros.
*   **Paso 3:** Dirígete a **Calificaciones**, busca a un alumno y haz clic en "Capturar" para asentar sus notas parciales. El promedio se calculará y guardará automáticamente.

