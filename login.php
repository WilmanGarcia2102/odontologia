<?php
session_start();
require_once 'conexion.php';

$usuario = $_POST['usuario'] ?? '';
$clave   = $_POST['clave'] ?? '';

$stmt = $conexion->prepare("SELECT id FROM usuarios WHERE usuario = ? AND clave = ?");
$stmt->bind_param("ss", $usuario, $clave);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $_SESSION['usuario'] = $usuario;
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Usuario o clave incorrectos']);
}
?>