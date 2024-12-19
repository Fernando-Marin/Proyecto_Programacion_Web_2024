<?php
// Procesar el cambio (actualización) de un alumno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_alumno'])) {
    $numero_control = $_POST['numero_control'];
    $nombre = $_POST['nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $semestre = $_POST['semestre'];
    $id_carrera = $_POST['id_carrera'];

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE alumno SET Nombre = :nombre, Primer_Apellido = :primer_apellido, Segundo_Apellido = :segundo_apellido, Semestre = :semestre, ID_carrera = :id_carrera WHERE Numero_de_control = :numero_control");

        $stmt->bindParam(':numero_control', $numero_control);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':primer_apellido', $primer_apellido);
        $stmt->bindParam(':segundo_apellido', $segundo_apellido);
        $stmt->bindParam(':semestre', $semestre);
        $stmt->bindParam(':id_carrera', $id_carrera);

        if ($stmt->execute()) {
            $success_message = "Alumno actualizado exitosamente.";
        } else {
            $error_message = "Error al actualizar el alumno.";
        }
    } catch (PDOException $exception) {
        $error_message = "Error en la base de datos: " . htmlspecialchars($exception->getMessage());
    }
}
