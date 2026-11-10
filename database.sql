CREATE DATABASE IF NOT EXISTS odontologia;
USE odontologia;

-- Tabla de Usuarios (Médicos / Administradores)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    clave VARCHAR(50) NOT NULL
);

-- Insertar usuario por defecto (usuario: admin / clave: 1234)
INSERT INTO usuarios (usuario, clave) VALUES ('admin', '1234');

-- Tabla de Pacientes
CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    fecha_nacimiento DATE
);

-- Tabla de Procedimientos Odontológicos
CREATE TABLE procedimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    costo DECIMAL(10,2) NOT NULL
);

-- Insertar procedimientos básicos por defecto
INSERT INTO procedimientos (nombre, costo) VALUES 
('Limpieza dental', 25.00),
('Blanqueamiento de dentadura', 50.00),
('Extracción de muelas / cordales', 40.00),
('Tratamiento de conducto', 80.00),
('Calza de muela', 30.00);

-- Tabla de Historia Clínica
CREATE TABLE historias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    procedimiento_id INT NOT NULL,
    diagnostico TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (procedimiento_id) REFERENCES procedimientos(id)
);