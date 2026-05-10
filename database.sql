CREATE DATABASE IF NOT EXISTS evaluacion;
USE evaluacion;

CREATE TABLE IF NOT EXISTS centros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(255) NOT NULL,
    clave_cct VARCHAR(50) NOT NULL,
    municipio VARCHAR(100) NOT NULL,
    encargado VARCHAR(255) NOT NULL,
    correo_encargado VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    centro_id INT NOT NULL,
    matricula VARCHAR(50) NOT NULL UNIQUE,
    estatus VARCHAR(50) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    paterno VARCHAR(100) NOT NULL,
    materno VARCHAR(100) NOT NULL,
    genero CHAR(1) NOT NULL,
    generacion VARCHAR(50) NOT NULL,
    municipio_residencia VARCHAR(100) NOT NULL,
    pais_nacimiento VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (centro_id) REFERENCES centros(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS materias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    creditos INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumno_id INT NOT NULL,
    materia_id INT NOT NULL,
    parcial1 DECIMAL(5,2) DEFAULT NULL,
    parcial2 DECIMAL(5,2) DEFAULT NULL,
    parcial3 DECIMAL(5,2) DEFAULT NULL,
    promedio DECIMAL(5,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id) ON DELETE CASCADE,
    FOREIGN KEY (materia_id) REFERENCES materias(id) ON DELETE CASCADE,
    UNIQUE KEY (alumno_id, materia_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS logs_importacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    archivo VARCHAR(100) NOT NULL,
    fila INT NOT NULL,
    error_mensaje TEXT NOT NULL,
    datos_originales TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunas materias por defecto para poder registrar calificaciones
INSERT INTO materias (nombre, creditos) VALUES
('Matemáticas I', 5),
('Taller de Lectura y Redacción I', 4),
('Química I', 5),
('Geografía', 4),
('Informática I', 3),
('Ética y Valores I', 3),
('Inglés I', 4)
ON DUPLICATE KEY UPDATE nombre=nombre;
