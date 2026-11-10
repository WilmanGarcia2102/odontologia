<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    die("Acceso no autorizado");
}

$paciente_id = $_GET['id'] ?? 0;

$stmtP = $conexion->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmtP->bind_param("i", $paciente_id);
$stmtP->execute();
$paciente = $stmtP->get_result()->fetch_assoc();

if (!$paciente) {
    die("Paciente no encontrado");
}

$stmtH = $conexion->prepare("
    SELECT h.fecha, p.nombre AS procedimiento, h.diagnostico, p.costo 
    FROM historias h 
    JOIN procedimientos p ON h.procedimiento_id = p.id 
    WHERE h.paciente_id = ? 
    ORDER BY h.fecha DESC
");
$stmtH->bind_param("i", $paciente_id);
$stmtH->execute();
$historias = $stmtH->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia Clínica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 30px; font-family: Arial, sans-serif; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary">Descargar / Imprimir PDF</button>
        <button onclick="window.close()" class="btn btn-secondary">Cerrar</button>
    </div>

    <div class="border p-4">
        <h2 class="text-center text-primary mb-4">HISTORIA CLÍNICA ODONTOLÓGICA</h2>
        
        <table class="table table-bordered mb-4">
            <tr>
                <th>Paciente:</th>
                <td><?= htmlspecialchars($paciente['nombre']) ?></td>
                <th>Cédula:</th>
                <td><?= htmlspecialchars($paciente['cedula']) ?></td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td><?= htmlspecialchars($paciente['telefono']) ?></td>
                <th>Fecha Nacimiento:</th>
                <td><?= htmlspecialchars($paciente['fecha_nacimiento']) ?></td>
            </tr>
        </table>

        <h4>Historial de Procedimientos</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Procedimiento</th>
                    <th>Diagnóstico / Observaciones</th>
                    <th>Costo ($)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                while ($h = $historias->fetch_assoc()) {
                    echo "<tr>
                        <td>{$h['fecha']}</td>
                        <td>" . htmlspecialchars($h['procedimiento']) . "</td>
                        <td>" . htmlspecialchars($h['diagnostico']) . "</td>
                        <td>$" . number_format($h['costo'], 2) . "</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>