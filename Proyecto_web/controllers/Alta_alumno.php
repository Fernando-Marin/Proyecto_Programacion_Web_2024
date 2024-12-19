<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['alta_alumno'])) {
    $numero_control = $_POST['numero_control'];
    $nombre = $_POST['nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $semestre = $_POST['semestre'];
    $id_carrera = $_POST['id_carrera'];

    try {
        $db = Database::getInstance()->getConnection();

        // Iniciar la transacción
        $db->beginTransaction();

        $stmt = $db->prepare("INSERT INTO alumno (Numero_de_control, Nombre, Primer_Apellido, Segundo_Apellido, Semestre, ID_carrera) 
                              VALUES (:numero_control, :nombre, :primer_apellido, :segundo_apellido, :semestre, :id_carrera)");
        $stmt->bindParam(':numero_control', $numero_control);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':primer_apellido', $primer_apellido);
        $stmt->bindParam(':segundo_apellido', $segundo_apellido);
        $stmt->bindParam(':semestre', $semestre);
        $stmt->bindParam(':id_carrera', $id_carrera);

        // Ejecutar la inserción
        if ($stmt->execute()) {
            // Si la inserción fue exitosa, confirmamos la transacción
            $db->commit();
            $success_message = "Alumno registrado exitosamente.";
        } else {
            // Si ocurre un error, hacemos un rollback
            $db->rollBack();
            $error_message = "Error al registrar el alumno.";
        }
    } catch (PDOException $exception) {
        // En caso de excepción, hacemos un rollback
        $db->rollBack();
        $error_message = "Error en la base de datos: " . htmlspecialchars($exception->getMessage());
    }
}
