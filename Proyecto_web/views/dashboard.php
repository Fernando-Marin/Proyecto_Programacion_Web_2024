<?php

require_once '../conexionBD/Database.php'; // Incluir la clase Database


require_once '../controllers/login.php';
require_once '../controllers/alta_alumno.php';
require_once '../controllers/baja_alumno.php';
require_once '../controllers/cambio_alumno.php';


// Obtener todas las carreras para el selector
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM carrera");
    $carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    $error_message = "Error al obtener las carreras: " . htmlspecialchars($exception->getMessage());
}

// Obtener los alumnos para la tabla
try {
    $query = $db->query("SELECT a.Numero_de_control, a.Nombre, a.Primer_Apellido, a.Segundo_Apellido, a.Semestre, c.Nombre_carrera
                         FROM alumno a
                         LEFT JOIN carrera c ON a.ID_carrera = c.ID_carrera");

    $alumnos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    $error_message = "Error al conectar o consultar la base de datos: " . htmlspecialchars($exception->getMessage());
}


// Obtener los registros de auditoria de alumnos para la tabla
try {
    $query = $db->query("SELECT aa.Numero_de_control, aa.Nombre, aa.Primer_Apellido, aa.Segundo_Apellido, 
                                aa.Semestre, c.Nombre_carrera, aa.Fecha_eliminacion, aa.Usuario_eliminacion
                         FROM auditoria_alumno aa
                         LEFT JOIN carrera c ON aa.ID_carrera = c.ID_carrera");

    $alumnos_auditoria = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    $error_message = "Error al conectar o consultar la base de datos de auditoría: " . htmlspecialchars($exception->getMessage());
}




?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestión de Alumnos</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1>Bienvenido al sistema de tutorias</h1>
        <p>Gestión de Alumnos</p>

        <!-- Mostrar mensaje de éxito o error -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para Alta de Alumnos -->
        <h2>Registrar Nuevo Alumno</h2>
        <form method="POST" action="dashboard.php">
            <div class="mb-3">
                <label for="numero_control" class="form-label">Número de Control</label>
                <input
                    type="text"
                    class="form-control"
                    id="numero_control"
                    name="numero_control"
                    required
                    oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '')"
                    title="Solo se permiten letras y números.">
            </div>

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input
                    type="text"
                    class="form-control"
                    id="nombre"
                    name="nombre"
                    required
                    oninput="this.value = this.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                    title="Solo se permiten letras y espacios.">
            </div>
            <div class="mb-3">
                <label for="primer_apellido" class="form-label">Primer Apellido</label>
                <input
                    type="text"
                    class="form-control"
                    id="primer_apellido"
                    name="primer_apellido"
                    required
                    oninput="this.value = this.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                    title="Solo se permiten letras y espacios.">
            </div>
            <div class="mb-3">
                <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                <input
                    type="text"
                    class="form-control"
                    id="segundo_apellido"
                    name="segundo_apellido"
                    required
                    oninput="this.value = this.value.replace(/[^a-zA-ZÁÉÍÓÚáéíóúÑñ\s]/g, '')"
                    title="Solo se permiten letras y espacios.">
            </div>

            <div class="mb-3">
                <label for="semestre" class="form-label">Semestre</label>
                <input
                    type="number"
                    class="form-control"
                    id="semestre"
                    name="semestre"
                    required
                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                    min="1"
                    max="12"
                    title="Solo se permiten números entre 1 y 12.">
            </div>


            <div class="mb-3">
                <label for="id_carrera" class="form-label">Carrera</label>
                <select class="form-select" id="id_carrera" name="id_carrera" required>
                    <option value="">Selecciona una carrera</option>
                    <?php foreach ($carreras as $carrera): ?>
                        <option value="<?php echo $carrera['ID_carrera']; ?>"><?php echo $carrera['Nombre_carrera']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="alta_alumno" class="btn btn-success">Registrar Alumno</button>
        </form>
    </div>

    <!-- Mostrar la tabla de alumnos -->
    <div class="mt-4">
        <h2>Lista de Alumnos Registrados</h2>
        <input type="text" id="search" class="form-control" placeholder="Buscar por nombre o número de control"
            onkeyup="filterTable()">
        <table class="table table-bordered table-striped mt-2" id="alumnosTable">
            <thead>
                <tr>
                    <th>Número de Control</th>
                    <th>Nombre</th>
                    <th>Primer Apellido</th>
                    <th>Segundo Apellido</th>
                    <th>Semestre</th>
                    <th>Carrera</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alumnos as $alumno): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($alumno['Numero_de_control']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Primer_Apellido']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Segundo_Apellido']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Semestre']); ?></td>
                        <td><?php echo htmlspecialchars($alumno['Nombre_carrera']); ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="<?php echo $alumno['Numero_de_control']; ?>"
                                data-nombre="<?php echo $alumno['Nombre']; ?>"
                                data-apellido="<?php echo $alumno['Primer_Apellido']; ?>"
                                data-second-apellido="<?php echo $alumno['Segundo_Apellido']; ?>"
                                data-semestre="<?php echo $alumno['Semestre']; ?>"
                                data-carrera="<?php echo $alumno['Nombre_carrera']; ?>">Editar</button>
                            <!-- Botón para eliminar -->
                            <a href="dashboard.php?delete_id=<?php echo $alumno['Numero_de_control']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Seguro que deseas eliminar este alumno?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


    <div class="mt-4">
        <button id="toggleAuditTable" class="btn btn-secondary mb-3" onclick="toggleAuditTable()">
            Mostrar Alumnos Eliminados
        </button>

        <div id="auditTableContainer" style="display: none;">
            <h2>Historial de Alumnos Eliminados</h2>
            <input type="text" id="search" class="form-control" placeholder="Buscar por nombre o número de control"
                onkeyup="filterTable()">
            <table class="table table-bordered table-striped mt-2" id="alumnosAuditoriaTable">
                <thead>
                    <tr>
                        <th>Número de Control</th>
                        <th>Nombre</th>
                        <th>Primer Apellido</th>
                        <th>Segundo Apellido</th>
                        <th>Semestre</th>
                        <th>Carrera</th>
                        <th>Fecha de Eliminación</th>
                        <th>Usuario que Eliminó</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos_auditoria as $alumno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($alumno['Numero_de_control']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Nombre']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Primer_Apellido']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Segundo_Apellido']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Semestre']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Nombre_carrera']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Fecha_eliminacion']); ?></td>
                            <td><?php echo htmlspecialchars($alumno['Usuario_eliminacion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleAuditTable() {
            var tableContainer = document.getElementById('auditTableContainer');
            var toggleButton = document.getElementById('toggleAuditTable');

            if (tableContainer.style.display === 'none') {
                tableContainer.style.display = 'block';
                toggleButton.textContent = 'Ocultar Alumnos Eliminados';
                toggleButton.classList.remove('btn-secondary');
                toggleButton.classList.add('btn-primary');
            } else {
                tableContainer.style.display = 'none';
                toggleButton.textContent = 'Mostrar Alumnos Eliminados';
                toggleButton.classList.remove('btn-primary');
                toggleButton.classList.add('btn-secondary');
            }
        }
    </script>

    <script>
        function filterTable() {
            // Search function similar to the original one
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search");
            filter = input.value.toUpperCase();
            table = document.getElementById("alumnosAuditoriaTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                // Search across multiple columns
                var show = false;
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        show = true;
                        break;
                    }
                }
                tr[i].style.display = show ? "" : "none";
            }
        }
    </script>

    <!-- Modal para Editar Alumno -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Alumno</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="dashboard.php">
                        <div class="mb-3">
                            <label for="numero_control_edit" class="form-label">Número de Control</label>
                            <input type="text" class="form-control" id="numero_control_edit" name="numero_control"
                                readonly required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_edit" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre_edit" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="primer_apellido_edit" class="form-label">Primer Apellido</label>
                            <input type="text" class="form-control" id="primer_apellido_edit" name="primer_apellido"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="segundo_apellido_edit" class="form-label">Segundo Apellido</label>
                            <input type="text" class="form-control" id="segundo_apellido_edit" name="segundo_apellido"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="semestre_edit" class="form-label">Semestre</label>
                            <input type="number" class="form-control" id="semestre_edit" name="semestre" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_carrera_edit" class="form-label">Carrera</label>
                            <select class="form-select" id="id_carrera_edit" name="id_carrera" required>
                                <?php foreach ($carreras as $carrera): ?>
                                    <option value="<?php echo $carrera['ID_carrera']; ?>">
                                        <?php echo $carrera['Nombre_carrera']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="editar_alumno" class="btn btn-primary">Actualizar Alumno</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <script>
        // Filtrar la tabla de alumnos por nombre o número de control
        function filterTable() {
            const input = document.getElementById("search");
            const filter = input.value.toLowerCase();
            const table = document.getElementById("alumnosTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }
                rows[i].style.display = found ? "" : "none";
            }
        }

        // Llenar el formulario de edición con los datos del alumno
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('numero_control_edit').value = button.getAttribute('data-id');
            document.getElementById('nombre_edit').value = button.getAttribute('data-nombre');
            document.getElementById('primer_apellido_edit').value = button.getAttribute('data-apellido');
            document.getElementById('segundo_apellido_edit').value = button.getAttribute('data-second-apellido');
            document.getElementById('semestre_edit').value = button.getAttribute('data-semestre');
            document.getElementById('id_carrera_edit').value = button.getAttribute('data-carrera');
        });
    </script>

    <form method="POST" action="logout.php">
        <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
    </form>

</body>

</html>