<?php
session_start();
require_once 'conexion.php';

$logueado = isset($_SESSION['usuario']);

$pacientes = [];
$procedimientos = [];
$historias = [];

if ($logueado) {
    $pacientes = $conexion->query("SELECT * FROM pacientes ORDER BY nombre ASC");
    $procedimientos = $conexion->query("SELECT * FROM procedimientos ORDER BY nombre ASC");
    
    $queryH = "SELECT h.id, p.nombre AS paciente, pr.nombre AS procedimiento, h.diagnostico, h.fecha, p.id AS paciente_id 
              FROM historias h 
              JOIN pacientes p ON h.paciente_id = p.id 
              JOIN procedimientos pr ON h.procedimiento_id = pr.id 
              ORDER BY h.fecha DESC";
    $historias = $conexion->query($queryH);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Odontológico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if (!$logueado): ?>
    <!-- INICIO DE SESIÓN -->
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 350px;">
            <h4 class="text-center mb-3">Iniciar Sesión</h4>
            <div id="login-alert" class="alert alert-danger d-none"></div>
            <form id="form-login">
                <div class="mb-3">
                    <label class="form-label">Usuario</label>
                    <input type="text" name="usuario" class="form-control" required value="admin">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="clave" class="form-control" required value="1234">
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- PANEL PRINCIPAL CON NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Consultorio Odontológico</a>
            <span class="navbar-text text-white">
                Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong> | 
                <a href="logout.php" class="btn btn-sm btn-danger ms-2">Cerrar Sesión</a>
            </span>
        </div>
    </nav>

    <div class="container">
        <!-- MENÚ DE OPCIONES (TABULADORES) -->
        <ul class="nav nav-tabs mb-4" id="menuTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pacientes">Pacientes</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-procedimientos">Procedimientos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-historias">Historia Clínica</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- PESTAÑA: PACIENTES -->
            <div class="tab-pane fade show active" id="tab-pacientes">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card p-3">
                            <h5>Registrar Paciente</h5>
                            <form id="form-paciente">
                                <input type="text" name="cedula" class="form-control mb-2" placeholder="Cédula" required>
                                <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre completo" required>
                                <input type="text" name="telefono" class="form-control mb-2" placeholder="Teléfono" required>
                                <input type="date" name="fecha_nacimiento" class="form-control mb-2" required>
                                <button type="submit" class="btn btn-success w-100">Guardar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card p-3">
                            <h5>Lista de Pacientes</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Cédula</th><th>Nombre</th><th>Teléfono</th><th>Acción</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($pacientes && $pacientes->num_rows > 0) {
                                        while ($p = $pacientes->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$p['cedula']}</td>
                                                <td>{$p['nombre']}</td>
                                                <td>{$p['telefono']}</td>
                                                <td><a href='pdf_historia.php?id={$p['id']}' target='_blank' class='btn btn-sm btn-danger'>Ver PDF</a></td>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: PROCEDIMIENTOS -->
            <div class="tab-pane fade" id="tab-procedimientos">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card p-3">
                            <h5>Nuevo Procedimiento</h5>
                            <form id="form-procedimiento">
                                <input type="text" name="nombre" class="form-control mb-2" placeholder="Ej: Limpieza dental" required>
                                <input type="number" step="0.01" name="costo" class="form-control mb-2" placeholder="Costo ($)" required>
                                <button type="submit" class="btn btn-success w-100">Guardar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card p-3">
                            <h5>Catálogo de Procedimientos</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>ID</th><th>Procedimiento</th><th>Costo</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($procedimientos && $procedimientos->num_rows > 0) {
                                        while ($pr = $procedimientos->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$pr['id']}</td>
                                                <td>{$pr['nombre']}</td>
                                                <td>$" . number_format($pr['costo'], 2) . "</td>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA: HISTORIAS CLÍNICAS -->
            <div class="tab-pane fade" id="tab-historias">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card p-3">
                            <h5>Registrar Consulta</h5>
                            <form id="form-historia">
                                <select name="paciente_id" class="form-select mb-2" required>
                                    <option value="">Seleccione Paciente</option>
                                    <?php 
                                    $pacientes->data_seek(0);
                                    while ($p = $pacientes->fetch_assoc()) {
                                        echo "<option value='{$p['id']}'>{$p['nombre']} ({$p['cedula']})</option>";
                                    }
                                    ?>
                                </select>

                                <select name="procedimiento_id" class="form-select mb-2" required>
                                    <option value="">Seleccione Procedimiento</option>
                                    <?php 
                                    $procedimientos->data_seek(0);
                                    while ($pr = $procedimientos->fetch_assoc()) {
                                        echo "<option value='{$pr['id']}'>{$pr['nombre']}</option>";
                                    }
                                    ?>
                                </select>

                                <textarea name="diagnostico" class="form-control mb-2" placeholder="Diagnóstico u observaciones" required></textarea>
                                <button type="submit" class="btn btn-success w-100">Guardar Consulta</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card p-3">
                            <h5>Historial Clínico Registrado</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr><th>Fecha</th><th>Paciente</th><th>Procedimiento</th><th>Diagnóstico</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($historias && $historias->num_rows > 0) {
                                        while ($h = $historias->fetch_assoc()) {
                                            echo "<tr>
                                                <td>{$h['fecha']}</td>
                                                <td>{$h['paciente']}</td>
                                                <td>{$h['procedimiento']}</td>
                                                <td>{$h['diagnostico']}</td>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const formLogin = document.getElementById("form-login");
    if(formLogin) {
        formLogin.addEventListener("submit", async (e) => {
            e.preventDefault();
            const data = new FormData(formLogin);
            const res = await fetch("login.php", { method: "POST", body: data });
            const json = await res.json();
            if(json.status === "success") {
                location.reload();
            } else {
                const alert = document.getElementById("login-alert");
                alert.textContent = json.message;
                alert.classList.remove("d-none");
            }
        });
    }

    const enviarFormularioAjax = (idForm, url) => {
        const form = document.getElementById(idForm);
        if(form) {
            form.addEventListener("submit", async (e) => {
                e.preventDefault();
                const data = new FormData(form);
                const res = await fetch(url, { method: "POST", body: data });
                const json = await res.json();
                alert(json.message);
                if(json.status === "success") location.reload();
            });
        }
    };

    enviarFormularioAjax("form-paciente", "guardar_paciente.php");
    enviarFormularioAjax("form-procedimiento", "guardar_procedimiento.php");
    enviarFormularioAjax("form-historia", "guardar_historia.php");
});
</script>
</body>
</html>