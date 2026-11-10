<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    exit(json_encode(['status' => 'error', 'message' => 'No autorizado']));
}

$paciente_id      = $_POST['paciente_id'] ?? '';
$procedimiento_id = $_POST['procedimiento_id'] ?? '';
$diagnostico      = $_POST['diagnostico'] ?? '';

$stmt = $conexion->prepare("INSERT INTO historias (paciente_id, procedimiento_id, diagnostico) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $paciente_id, $procedimiento_id, $diagnostico);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Consulta registrada']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar consulta']);
}
?>