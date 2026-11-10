<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    exit(json_encode(['status' => 'error', 'message' => 'No autorizado']));
}

$cedula = $_POST['cedula'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fecha = $_POST['fecha_nacimiento'] ?? '';

$stmt = $conexion->prepare("INSERT INTO pacientes (cedula, nombre, telefono, fecha_nacimiento) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $cedula, $nombre, $telefono, $fecha);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Paciente registrado con éxito']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar (Cédula duplicada)']);
}
?>