<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    exit(json_encode(['status' => 'error', 'message' => 'No autorizado']));
}

$nombre = $_POST['nombre'] ?? '';
$costo  = $_POST['costo'] ?? 0;

$stmt = $conexion->prepare("INSERT INTO procedimientos (nombre, costo) VALUES (?, ?)");
$stmt->bind_param("sd", $nombre, $costo);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Procedimiento guardado']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar procedimiento']);
}
?>