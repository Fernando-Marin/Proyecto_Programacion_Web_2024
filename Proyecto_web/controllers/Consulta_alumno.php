<?php
// Obtener los alumnos para la tabla
try {
    $query = $db->query("SELECT a.Numero_de_control, a.Nombre, a.Primer_Apellido, a.Segundo_Apellido, a.Semestre, c.Nombre_carrera
                         FROM alumno a
                         LEFT JOIN carrera c ON a.ID_carrera = c.ID_carrera");

    $alumnos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    $error_message = "Error al conectar o consultar la base de datos: " . htmlspecialchars($exception->getMessage());
}
